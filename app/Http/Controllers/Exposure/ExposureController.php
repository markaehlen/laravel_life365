<?php

namespace App\Http\Controllers\Exposure;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\System;
use App\Models\Location;
use App\Models\Exposure;
use App\Models\Sublocation;
use Inertia\Inertia;
use App\Models\Unit;
use App\Models\Ecoparameter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\Helper;
use App\Models\HelpTip;
use App\Models\Project;
use App\Models\UserProject;
use Session;

class ExposureController extends Controller
{
    public function index(Request $request)
    {      

        $project = $this->getProject();
        
        $saved_defaults = json_decode($project->inputs);
        
        if (session('project')) {
            
            // dd(session('project'));
            $inputJSON=session('project');
        }else
        {
          $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
         
        }
        
        
        
        
        $projectData = $inputJSON;
        

        $locations_query = Location::isActive();
        
        
        $locations = $locations_query->whereHas('sublocations', function ($q) {
            $q->isActive()->whereHas('exposures', function ($query) {
                $query->isActive();
            });
        })->pluck('name', 'name')->toArray();
        
        $colors = [];

        array_push($colors, $saved_defaults->base_alt_color, $saved_defaults->alt1_color, $saved_defaults->alt2_color, $saved_defaults->alt3_color, $saved_defaults->alt5_color);

        $raw_temp_data = $projectData['project']['projectData']['temperatureHistory']['temp'];

        $temperature_entries = [];
        for ($i = 0; $i < sizeof($raw_temp_data); $i++) {
            $temperature_entries[$raw_temp_data[$i]['calendarMonth']] = $raw_temp_data[$i]['temperature'];
        }
        //dd($projectData);
        $loginResults = Helper::authenticateAPI();
        // dd($inputJSON);
        Helper::callAPI($loginResults, $inputJSON, 'astm');
        $helping_tips = HelpTip::where('category', 'exposure')->pluck('content', 'slug');
        
        return Inertia::render('Exposure/Exposure', [
            'isProjectScreen' => true,
            'locationOptions' => $locations,
            'projectData' => $projectData,
            'temperatureEntries' => $temperature_entries,
            //Units
            'baseUnits' => $inputJSON['project']['projectData']['baseUnits'],
            "concUnits" => $inputJSON['project']['projectData']['concentrationUnits'],
            //Structure Details
            'structures' => Config::get('defaults.structures'),
            "centimeterMetric" => Config::get('units.sUnitsCentM'),
            "usUnits" => Config::get('units.sUnitsUS'),
            "siUnits" => Config::get('units.sUnitsSI'),
            //Colors
            'colors' => $colors,
            'helping_tips'=>$helping_tips,
        ]);
    }

    public function getProject()
    {
        $user = Auth::guard('user')->user();
        $project = null;

        if ($user) {
            $project = UserProject::where('unique_id', session('projectID'))->first();
        }

        if (!$project) {
            $project = Project::where('unique_id', session('projectID'))->first();
        }
        return $project;
    }

    public function getDefaultEcoparameters()
    {
        $response_array = [];
        $ecoparameters = Ecoparameter::isActive()->get();
        foreach ($ecoparameters as $ep) {
            $response_array[$ep->identifier] = [
                'value' => (float)$ep->default_value,
                'type' => $ep->type
            ];
        }
        return $response_array;
    }

    public function getRedefinedRatesAndUnits($baseUnits)
    {
        $units = Unit::isActive()->whereHas('system', function ($query) use ($baseUnits) {
            $query->where('identifier', $baseUnits);
        })->get();

        foreach ($units as $unit) {
            switch ($unit->type) {
                case ('area'):
                    $area_unit = $unit->short_hand;
                    break;
                case ('volume'):
                    $volume_unit = $unit->short_hand;
                    break;
                case ('capacity'):
                    $capacity_unit = $unit->short_hand;
                    break;
                case ('weight'):
                    $weight_unit = $unit->short_hand;
                    break;
                case ('length'):
                    $length_unit = $unit->short_hand;
                    break;
                default:
                    break;
            }
        }
        return response()->json([
            'area_unit' => $area_unit,
            'volume_unit' => $volume_unit,
            'weight_unit' => $weight_unit,
            'capacity_unit' => $capacity_unit,
            'length_unit' => $length_unit,
        ]);
    }

    public function convertConcentrationBasedOnConcUnitAndBaseUnit(Request $request)
    {
        $conc = Helper::convertConcentrationFromOldToNewConcentrationUnits($request->concentration, $request->baseUnits, Config::get('units.sConcWtConc'), $request->concUnits);

        return response()->json([
            'concentration' => $conc
        ]);
    }

    public function getSublocationConcAndTemperatures(Request $request)
    {
        $subLocation = $request->sublocation;
        $exposure = $request->exposure;
        $baseUnits = $request->baseUnits;
        $concUnits = $request->conUnits;


        $sublocation_data = Sublocation::isActive()->where('name', $subLocation)->first();

        $exposure_data = Exposure::isActive()->where('name', $exposure)->first();

        //calculate maxCS and TimeToBuildUp

        $dMaxConc = 1;
        $yearsToMax = 1;
        $yearlyIncrement = 1;

        if ($sublocation_data->max_cs) {
            $dMaxConc = $sublocation_data->max_cs;
        } else {
            $dMaxConc = 0.5;
        }

        if ($sublocation_data->build_up) {
            $yearlyIncrement = $sublocation_data->build_up;
        }

        if ($exposure === "Marine spray zone") {
            $dMaxConc = 1.0;
            $yearlyIncrement = 0.10;
        } else if ($exposure === "Marine tidal zone") {
            $dMaxConc = 0.80;
            $yearlyIncrement = 1.0;
        } else if ($exposure === "Within 800 m of the ocean") {
            $dMaxConc = 0.60;
            $yearlyIncrement = 0.04;
        } else if ($exposure === "Within 1.5km of the ocean") {
            $dMaxConc = 0.60;
            $yearlyIncrement = 0.02;
        } else if ($exposure === "Parking garages") {
            $dMaxConc *= 1;
            $yearlyIncrement *= 1;
        } else if ($exposure === "Urban highway bridges") {
            $dMaxConc *= 0.85;
            $yearlyIncrement *= 0.85;
        } else if ($exposure === "Rural highway bridges") {
            $dMaxConc *= 0.70;
            $yearlyIncrement *= 0.70;
        }

        $yearsToMax = max(1, $dMaxConc / $yearlyIncrement);

        $yearsToMax = round($yearsToMax / 10, 2) * 10;

        return response()->json([

            'max_cs' => $dMaxConc,

            'time_to_max' => $yearsToMax,

            'jan_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->jan_temp) : $sublocation_data->jan_temp),

            'feb_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->feb_temp) : $sublocation_data->feb_temp),

            'mar_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->mar_temp) : $sublocation_data->mar_temp),

            'apr_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->apr_temp) : $sublocation_data->apr_temp),

            'may_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->may_temp) : $sublocation_data->may_temp),

            'jun_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->jun_temp) : $sublocation_data->jun_temp),

            'jul_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->jul_temp) : $sublocation_data->jul_temp),

            'aug_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->aug_temp) : $sublocation_data->aug_temp),

            'sep_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->sep_temp) : $sublocation_data->sep_temp),

            'oct_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->oct_temp) : $sublocation_data->oct_temp),

            'nov_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->nov_temp) : $sublocation_data->nov_temp),

            'dec_temp' => ($baseUnits == Config::get('units.sUnitsUS') ? $this->convertCelsiusToFahrenheit($sublocation_data->dec_temp) : $sublocation_data->dec_temp),
        ]);
    }

    public function convertCelsiusToFahrenheit($temperature)
    {
        $fahrenheit = ($temperature * (9 / 5)) + 32;
        return round($fahrenheit, 2);
    }

    public function calculateASTMData(Request $request)
    {
        
        $inputJSON = $this->prepareASTMJSON($request->all());
        
        $loginResults = Helper::authenticateAPI();
        
        $inputJSON['project']['projectData']['']=["maxSurfaceConcentration" => 0.8];
        // dump(json_decode(json_encode($inputJSON)));
        // dd($loginResults);
        $apiResults = Helper::callAPI($loginResults, $inputJSON, 'astm');
       
        if ($apiResults) {
            if ($apiResults->results) {
                $decoded_results = json_decode($apiResults->results);
                $decoded_results = json_decode(json_encode($decoded_results), true);
                //converting into percentages
                $tempJSON = $decoded_results['project']['projectData'];
                $tempJSON['exposureConditions']['c1556Sets'] = $tempJSON['exposureConditions']['c1556Sets'];
                $decoded_results['project']['projectData'] = $tempJSON;

                session(['inputJSON' => $decoded_results]);
                return response()->json(['inputJSON' => session('inputJSON')]);
            } else {
                return response()->json(['error' => 'API endpoint broken'], 401);
            }
        } else {
            return response()->json(['error' => 'API endpoint broken'], 401);
        }
    }

    public function prepareASTMJSON($inputData, $isResult = null)
    {
        $project = $this->getProject();

        $inputJSON = json_decode(json_encode(json_decode($project->details)), true);

        $tempJSON = $inputJSON['project']['projectData'];

        for ($i = 0; $i < sizeof($inputData['sets']); $i++) {
            $inputData['sets'][$i]['astmResults'] = (object) array();
        }

        //setting variables
        $tempJSON['exposureConditions']['setManually'] = true;
        $tempJSON['exposureConditions']['useC1556'] = true;
        $tempJSON['exposureLocale']['useThis'] = false;

        //converting into decimals
        $tempJSON['exposureConditions']['c1556Sets'] = $inputData['sets'];

        $inputJSON['project']['projectData'] = $tempJSON;

        return  $inputJSON;
    }

    public function saveExposureData(Request $request)
    {
        session(['exposure' => $request->exposureType]);
      
        $inputJSON = $this->prepareJSON($request->all());
        
        $runPostPageDataSave = Helper::postPageDataSave($inputJSON);
       
        if ($runPostPageDataSave) {
            if(is_array($request->intendedUrl)){
               // dump('1');
                return Redirect::route($request->intendedUrl[0],$request->intendedUrl[1])->with('success', 'Project details saved successfully!');
              }
        
            return Redirect::route($request->intendedUrl?:'set-exposure')
                ->with('success', 'Exposure details saved successfully!');
        } else {
            return Redirect::route('set-project')->with('error', 'Something went wrong!');
        }
    }

    public function prepareJSON($inputData)
    {   
        $project = $this->getProject();
        if (session('project')) {
            $inputJSON=session('project');
        }else{
        $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
        }
        $tempJSON = $inputJSON['project']['projectData'];

        $tempJSON['temperatureHistory']['temp'][0]['temperature'] = (float) $inputData['jan_temp'];
        $tempJSON['temperatureHistory']['temp'][1]['temperature'] = (float) $inputData['feb_temp'];
        $tempJSON['temperatureHistory']['temp'][2]['temperature'] = (float) $inputData['mar_temp'];
        $tempJSON['temperatureHistory']['temp'][3]['temperature'] = (float) $inputData['apr_temp'];
        $tempJSON['temperatureHistory']['temp'][4]['temperature'] = (float) $inputData['may_temp'];
        $tempJSON['temperatureHistory']['temp'][5]['temperature'] = (float) $inputData['jun_temp'];
        $tempJSON['temperatureHistory']['temp'][6]['temperature'] = (float) $inputData['jul_temp'];
        $tempJSON['temperatureHistory']['temp'][7]['temperature'] = (float) $inputData['aug_temp'];
        $tempJSON['temperatureHistory']['temp'][8]['temperature'] = (float) $inputData['sep_temp'];
        $tempJSON['temperatureHistory']['temp'][9]['temperature'] = (float) $inputData['oct_temp'];
        $tempJSON['temperatureHistory']['temp'][10]['temperature'] = (float) $inputData['nov_temp'];
        $tempJSON['temperatureHistory']['temp'][11]['temperature'] = (float) $inputData['dec_temp'];

        $tempJSON['exposureLocale']['location'] = $inputData['location'];
        $tempJSON['exposureLocale']['subLocation'] = $inputData['subLocation'];
        $tempJSON['exposureLocale']['exposure'] = $inputData['exposureType'];

        //setting variables
        $tempJSON['exposureConditions']['setManually'] = $inputData['setManually'];
        $tempJSON['exposureConditions']['useC1556'] = $inputData['useC1556'];

        if ($tempJSON['exposureConditions']['setManually'] == true) {
            $tempJSON['exposureLocale']['useThis'] = false;
        } else {
            $tempJSON['exposureLocale']['useThis'] = true;
        }

        //astm related corrections

        //listenerList correction

        for ($i = 0; $i < sizeof($inputData['c1556Sets']); $i++) {
            if (isset($inputData['c1556Sets'][$i]['astmResults']) && sizeof($inputData['c1556Sets'][$i]['astmResults'])) {
                if ($inputData['c1556Sets'][$i]['astmResults']['errorsHat'] == null) {
                    $inputData['c1556Sets'][$i]['astmResults']['errorsHat'] = 0;
                }

                if ($inputData['c1556Sets'][$i]['astmResults']['dtm'] && sizeof($inputData['c1556Sets'][$i]['astmResults']['dtm']['listenerList']) == 0) {
                    $inputData['c1556Sets'][$i]['astmResults']['dtm']['listenerList'] = [];
                }
                if ($inputData['c1556Sets'][$i]['astmResults']['dtmSi'] && sizeof($inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList']) == 0) {
                    $inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList'] = [];
                }
            } else {
                $inputData['c1556Sets'][$i]['astmResults'] = [];
            }
        }
        
        //assigning astm sets and active set
        $tempJSON['exposureConditions']['c1556Sets'] = $inputData['c1556Sets'];
        $tempJSON['exposureConditions']['c1556SetUsed'] = $inputData['c1556SetUsed'];


        $tempJSON['exposureConditions']['maxSurfaceConcentration'] = $inputData['maxSurfaceConcentration'];
        $tempJSON['exposureConditions']['timeToBuildUp'] = $inputData['timeToBuildUp'];

        $inputJSON['project']['projectData'] = $tempJSON;
        
        Session::put('project',$inputJSON);
        return  $inputJSON;
    }
}
