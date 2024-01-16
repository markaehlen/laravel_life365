<?php

namespace App\Http\Controllers\Defaults;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\System;
use App\Models\Location;
use App\Models\Exposure;
use App\Models\Sublocation;
use App\Models\HelpTip;
use App\Models\Project;
use App\Models\UserProject;
use Inertia\Inertia;
use App\Models\Unit;
use App\Models\Ecoparameter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\Input;
use App\Models\UserInput;
use Illuminate\Support\Str;
use App\Helpers\Helper;

class DefaultsController extends Controller
{

    public function index(Request $request)
    {

        $saved_defaults = $this->getExistingOrCreateNewDefaults();
        $ecoparameters = $saved_defaults ? $saved_defaults->ecoparameters : $this->getDefaultEcoParameters();
        //getting locations

        $locations_query = Location::isActive();
        $locations = $locations_query->whereHas('sublocations', function ($q) {
            $q->isActive()->whereHas('exposures', function ($query) {
                $query->isActive();
            });
        })->pluck('name', 'name')->toArray();

        $default_location = $locations_query->isDefault()->first();

        //getting sublocations

        $sublocations_query = Sublocation::isActive();
        $default_sublocation = $sublocations_query->isDefault()->first();

        //getting unit systems

        $systems_query = System::isActive();
        $systems = $systems_query->isDependent()->pluck('name', 'identifier')->toArray();
        $default_system = $systems_query->isBase()->first();
        
        //getting exposures

        $exposures_query = Exposure::isActive();
        $default_exposure = $exposures_query->isDefault()->first();

        //getting concentration units

        $conc_units_query = Unit::isActive();
        
        $default_conc_unit = $conc_units_query->whereHas('system', function ($query) {
            $query->isIndependent();
        })->where('type', 'concentration')->first();
        
        $helping_tips = HelpTip::where('category', 'default-settings')->pluck('content', 'slug');

        $colors = Config::get('defaults.colors');
        $colors['alt3_color']='#f97316';
        $colors['alt2_color']='#E84B39';
        // dd($ecoparameters);
        return Inertia::render('Defaults/Default', [
            'isProjectScreen' => true,
            'systems' => $systems,
            'locations' => $locations,
            'ecoparameters' => $ecoparameters,
            'baseUnits' => $saved_defaults ? $saved_defaults->baseUnits : $default_system->identifier,
            "concUnits" => $saved_defaults ? $saved_defaults->concUnits : $default_conc_unit->short_hand,
            "location" => $saved_defaults ? $saved_defaults->location : $default_location->name,
            "subLocation" => $saved_defaults ? $saved_defaults->subLocation : $default_sublocation->name,
            "exposureType" => $saved_defaults ? $saved_defaults->exposureType : $default_exposure->name,
            'base_alt_color' => $saved_defaults ? $saved_defaults->base_alt_color : $colors['base_alt_color'],
            'alt1_color' => $saved_defaults ? $saved_defaults->alt1_color : $colors['alt1_color'],
            'alt2_color' => $saved_defaults ? $saved_defaults->alt2_color : $colors['alt2_color'],
            'alt3_color' => $saved_defaults ? $saved_defaults->alt3_color : $colors['alt3_color'],
            'alt4_color' => $saved_defaults ? $saved_defaults->alt4_color : $colors['alt4_color'],
            'alt5_color' => $saved_defaults ? $saved_defaults->alt5_color : $colors['alt5_color'],
            'helping_tips' => $helping_tips,
        ]);
    }

    public function getExistingOrCreateNewDefaults()
    {
        $user = Auth::guard('user')->user();
        $defaults = null;
        if (session('projectID')) {
            if ($user) {
                $fetchFromProject = UserProject::where('user_id', $user->id)->where('unique_id', session('projectID'))->first();
            } else {
                $fetchFromProject = Project::where('ip_address', Helper::getIP())->where('mac_address', Helper::getMAC())->where('unique_id', session('projectID'))->first();
            }
            if ($fetchFromProject) {
                $defaults = $fetchFromProject->inputs;
            }
        } 
        return $defaults ? json_decode($defaults) : null;
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

    public function getSubLocations($location)
    {
        $sublocations = Sublocation::isActive()->whereHas('location', function ($query) use ($location) {
            $query->where('name', $location);
        })->whereHas('exposures', function ($q) {
            $q->isActive();
        })->pluck('name', 'name')->toArray();
        //dd($sublocations);
        $default_selected_sublocation = array_key_first($sublocations);

        return response()->json(['sublocations' => $sublocations, 'default_value' => $default_selected_sublocation]);
    }

    public function getExposures($subLocation)
    {
        $exposures = Exposure::isActive()->whereHas('sublocations', function ($query) use ($subLocation) {
            $query->where('name', $subLocation);
        })->pluck('name', 'name')->toArray();

        $default_selected_exposure = array_key_first($exposures);

        return response()->json(['exposures' => $exposures, 'default_value' => $default_selected_exposure]);
    }

    public function resetDefaults()
    {
        session()->forget(['projectID']);
        return Redirect::route('defaults')->with('success', 'Default settings have been reset successfully.');
    }

    public function saveDefaults(Request $request)
    {
       
        $inputDefaults = $request->all();
        $inputDefaults['areaToRepairPct']=$inputDefaults['areaToRepairPct']/100;
        $inputDefaults['inflation']=$inputDefaults['inflation']/100;
        $inputDefaults['discount']=$inputDefaults['discount']/100;
        $project = $this->createOrGetProject($inputDefaults);

        if ($project) {
            if(is_array($request->intendedUrl)){
                return Redirect::route($request->intendedUrl[0],$request->intendedUrl[1])->with('success', 'Project details saved successfully!');
              }
        
            return Redirect::route($request->intendedUrl?:'defaults')->with('success', 'Default settings saved successfully!');
        } else {
            return Redirect::route('defaults')->with('error', 'Something went wrong!');
        }
    }

    public function createOrGetProject($inputDefaults)
    {
    try{
        $inputJSON = Config::get('defaults.defaultProjectData');
       
        $project = null;

        $user = Auth::guard('user')->user();

        if (session('projectID')) {
            
            //fetch if project exists

            if ($user) {
                $project = UserProject::where('unique_id', session('projectID'))->first();
            }
            if (!$project) {
                $project = Project::where('unique_id', session('projectID'))->where('ip_address', Helper::getIP())->where('mac_address', Helper::getMAC())->first();
                
            }

            $inputJSON = json_decode(json_encode(json_decode($project->details)), true);

            //conc changes for project data

            $inputJSON['project']['projectData'] = Helper::convertProjectDataFromOneConcentrationUnitToAnother($inputJSON['project']['projectData'], $inputJSON['project']['projectData']['concentrationUnits'], $inputDefaults['concUnits']);

            //Prepare JSON

            $inputJSON = $this->prepareJSON($inputJSON, $inputDefaults);

            //Rewriting JSON

            $project->details = json_encode($inputJSON);
            $project->inputs = json_encode($inputDefaults);
            $project->save();

            if ($project->save()) {
                return true;
            }
        } else {

            //create if doesn't exists

            $projectID = (string) Str::uuid();
            session(['projectID' => $projectID]);

            //conc changes for project data

            $inputJSON['project']['projectData'] = Helper::convertProjectDataFromOneConcentrationUnitToAnother($inputJSON['project']['projectData'], $inputJSON['project']['projectData']['concentrationUnits'], $inputDefaults['concUnits']);

            //Prepare JSON

            $inputJSON = $this->prepareJSON($inputJSON, $inputDefaults);
            if ($user) {
                UserProject::updateOrInsert(
                    ['user_id' => $user->id, 'unique_id' => $projectID],
                    [
                        'title' => $inputJSON['project']['projectData']['title'],
                        'description' => $inputJSON['project']['projectData']['description'],
                        'details' => json_encode($inputJSON),
                        'inputs' => json_encode($inputDefaults)
                    ]
                );
            } else {
                Project::updateOrInsert(
                    ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP()],
                    [
                        'title' => $inputJSON['project']['projectData']['title'],
                        'description' => $inputJSON['project']['projectData']['description'],
                        'details' => json_encode($inputJSON),
                        'inputs' => json_encode($inputDefaults)
                    ]
                );
            }
            return true;
        }
        return false;
     }
     catch (\Exception $e) {
        Helper::tryCatch($e);
     }
    }

    public function prepareJSON($inputJSON, $defaults)
    {
        $defaultProjectDataJSON = $inputJSON['project']['projectData'];
        $currentBaseUnits = $defaults['baseUnits'];

        if ($currentBaseUnits == 'US units') {
            $defaultProjectDataJSON['structureDimensions']['trueThickness'] = round($defaultProjectDataJSON['structureDimensions']['trueThickness'] / 24, 2);
            $defaultProjectDataJSON['structureDimensions']['clearCover'] = round($defaultProjectDataJSON['structureDimensions']['clearCover'] / 24, 2);
        } else if ($currentBaseUnits == 'Centimeter metric') {
            $defaultProjectDataJSON['structureDimensions']['trueThickness'] = round($defaultProjectDataJSON['structureDimensions']['trueThickness'] / 10, 2);
            $defaultProjectDataJSON['structureDimensions']['clearCover'] = round($defaultProjectDataJSON['structureDimensions']['clearCover'] / 10, 2);
        }

        $defaultProjectDataJSON['baseUnits'] = $defaults['baseUnits'];
        $defaultProjectDataJSON['concentrationUnits'] = $defaults['concUnits'];
        $defaultProjectDataJSON['baseYear'] = $defaults['ecoparameters']['baseYear']['value'];
        $defaultProjectDataJSON['studyPeriod'] = $defaults['ecoparameters']['studyPeriod']['value'];
        $defaultProjectDataJSON['inflationRate'] = $defaults['ecoparameters']['inflation']['value'] / 100;
        $defaultProjectDataJSON['realDiscountRate'] = $defaults['ecoparameters']['discount']['value'] / 100;

        $defaultProjectDataJSON['exposureLocale']['location'] = $defaults['location'];
        $defaultProjectDataJSON['exposureLocale']['subLocation'] = $defaults['subLocation'];
        $defaultProjectDataJSON['exposureLocale']['exposure'] = $defaults['exposureType'];

        $defaultProjectDataJSON['costs']['baseMixCost'] = $defaults['ecoparameters']['baseMixCost']['value'];
        $defaultProjectDataJSON['costs']['repairCost'] = $defaults['ecoparameters']['repairCost']['value'];
        $defaultProjectDataJSON['costs']['areaToRepair'] = $defaults['ecoparameters']['areaToRepairPct']['value']/100;
        $defaultProjectDataJSON['costs']['repairInterval'] = $defaults['ecoparameters']['repairIntervalYrs']['value'];

        $defaultProjectDataJSON['costs']['blackSteel'] = $defaults['ecoparameters']['costBlackSteel']['value'];
        $defaultProjectDataJSON['costs']['stainlessSteel'] = $defaults['ecoparameters']['costStainless']['value'];
        $defaultProjectDataJSON['costs']['epoxySteel'] = $defaults['ecoparameters']['costEpoxy']['value'];

        $defaultProjectDataJSON['costs']['membrane'] = $defaults['ecoparameters']['costMembrane']['value'];
        $defaultProjectDataJSON['costs']['sealant'] = $defaults['ecoparameters']['costSealer']['value'];
        $defaultProjectDataJSON['costs']['inhibitor'] = $defaults['ecoparameters']['costInhibitor']['value'];

        $inputJSON['project']['projectData'] = $defaultProjectDataJSON;

        return $inputJSON;
    }
}
