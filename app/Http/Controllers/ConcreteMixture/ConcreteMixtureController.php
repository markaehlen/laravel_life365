<?php

namespace App\Http\Controllers\ConcreteMixture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;
use App\Models\UserProject;
use App\Models\Project;
use File;
use App\Models\HelpTip;
use Illuminate\Support\Facades\Cache;
use Session;


class ConcreteMixtureController extends Controller
{

    public function index(Request $request)
    {
        $project = $this->getProject();

        $saved_defaults = json_decode($project->inputs);

        if (session('project')) {
            $inputJSON = session('project');
        } else {
            $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
        }

        $inputJSON = Helper::exposureLocaleCorrection($inputJSON);

        $inputJSON = Helper::listenerASTMCorrection($inputJSON);

        if (isset($inputJSON['project']['projectData']['lccResults'])) {
            unset($inputJSON['project']['projectData']['lccResults']);
        }

        // 
        $inputJSON = Helper::prePageDataCalc($inputJSON, 'serv');

        $privious = $inputJSON['project']['projectData']['alts']['alt'];
        if (session('alt')) {
            $session = session('alt');
            $inputJSON['project']['projectData']['alts']['alt'] = session('alt');
        }

        // CODE TO FIX - WHY IS THIS ONLY ALT 0
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign'] = $privious['0']['alternative']['concreteMixDesign'];
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] = $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] * 100;
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] = $session['0']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] ?? '0.0' * 100;
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentSlag'] = $session['0']['alternative']['concreteMixDesign']['percentSlag'] ?? '0.0' * 100;
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentSilicaFume'] = $session['0']['alternative']['concreteMixDesign']['percentSilicaFume'] ?? '0.0' * 100;

        // CODE TO FX - WHY IS THIS ONLY FOR THE SECOND ALT? WHAT IF THERE ARE LESS THAN 2 ALTS? THIS WILL CRASH
        if (isset($inputJSON['project']['projectData']['alts']['alt']['1'])) {

            $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentSilicaFume'] = $session['1']['alternative']['concreteMixDesign']['percentSilicaFume'] ?? '0.0' * 100;
            $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign'] = $privious['1']['alternative']['concreteMixDesign'];
            $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] = $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] * 100;
            $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] = $session['1']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] ?? '0.0' * 100;
            $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentSlag'] = $session['1']['alternative']['concreteMixDesign']['percentSlag'] ?? '0.0' * 100;
        }
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['inhibitor'] = $privious['1']['alternative']['concreteMixDesign']['inhibitor'];
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['inhibitor'] = $privious['1']['alternative']['concreteMixDesign']['inhibitor'];

        $colors = [];
        array_push($colors, $saved_defaults->base_alt_color, $saved_defaults->alt1_color, $saved_defaults->alt2_color, $saved_defaults->alt3_color, $saved_defaults->alt4_color, $saved_defaults->alt5_color);
        $helping_tips = HelpTip::where('category', 'mixture')->pluck('content', 'slug');
        //Get Project JSON
        return Inertia::render('ConcreteMixture/ConcreteMixture', [
            'isProjectScreen' => true,
            'projectData' => $inputJSON,
            'serviceLifeResults' => json_decode(gzdecode(base64_decode($inputJSON['project']['projectData']['serviceLifeResults']))),
            "rebarSteelTypes" => Config::get('defaults.rebar_steel_types'),
            'inhibitors' => Config::get('defaults.inhibitors'),
            'barrierTypes' => Config::get('defaults.barrier_types'),
            //Colors
            'colors' => $colors,
            'helping_tips' => $helping_tips,
        ]);
    }

    public function exportSLReport(Request $request)
    {
        $project = $this->getProject();

        $saved_defaults = json_decode($project->inputs);
        if (session('project')) {
            $inputJSON = session('project');
        } else {
            $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
        }

        $inputJSON = Helper::exposureLocaleCorrection($inputJSON);
        $inputJSON = Helper::listenerASTMCorrection($inputJSON);
        $inputJSON = Helper::prePageDataCalc($inputJSON, 'serv');

        $colors = [];
        array_push($colors, $saved_defaults->base_alt_color, $saved_defaults->alt1_color, $saved_defaults->alt2_color, $saved_defaults->alt3_color, $saved_defaults->alt4_color, $saved_defaults->alt5_color);

        $raw_temp_data = $inputJSON['project']['projectData']['temperatureHistory']['temp'];
        $temperature_entries = [];
        for ($i = 0; $i < sizeof($raw_temp_data); $i++) {
            $temperature_entries[$raw_temp_data[$i]['calendarMonth']] = $raw_temp_data[$i]['temperature'];
        }

        //Get Project JSON
        return Inertia::render('ConcreteMixture/ServiceLifeReport', [
            'isProjectScreen' => true,
            'projectData' => $inputJSON,
            "rebarSteelTypes" => Config::get('defaults.rebar_steel_types'),
            'inhibitors' => Config::get('defaults.inhibitors'),
            'barrierTypes' => Config::get('defaults.barrier_types'),
            'baseUnits' => $inputJSON['project']['projectData']['baseUnits'],
            //Colors
            'colors' => $colors,
            'temperatureEntries' => $temperature_entries,
        ]);
    }

    public function getProject()
    {
        $user = Auth::guard('user')->user();
        $project = null;

        if ($user) {
            $project = UserProject::where('unique_id', session('projectID'))->where('user_id', $user->id)->first();
        }

        if (!$project) {
            $project = Project::where('unique_id', session('projectID'))->first();
        }
        return $project;
    }

    public function computeServiceLife(Request $request)
    {
        $inputJSON = $this->prepareJSON($request->all());

        // CODE TO FIX - THIS NEEDS TO BE FOR WHATEVER IS IN THE ALTS ARRAY; E.G., USE THIS
        // for ($i = 0; $i < sizeof($inputJSON['project']['projectData']['alts']['alt']); $i++) {
        //     $inputJSON['project']['projectData']['alts']['alt'][$i]['alternative']['concreteMixDesign']['detailedBarrier']['initialEfficiency'] = 1;
        // }
        // CURRENT CODE BELOW WILL CRASH IF THERE ARE LESS THAN 2 ALTS

        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] = $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] = $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['rebarType']['percentOfTotal'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] = $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] = $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentClassFFlyAsh'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentSlag'] = $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentSlag'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentSlag'] = $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentSlag'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentSilicaFume'] = $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['percentSilicaFume'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentSilicaFume'] = $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['percentSilicaFume'] / 100;
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['inhibitor'] = $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['inhibitor'];
        // dd($inputJSON);
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['detailedBarrier'] = $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['detailedBarrier'];
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['detailedBarrier'] = $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['detailedBarrier'];

        // For the first 'alt'
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['dRef'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['dRef']);
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['m'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['m']);
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['hydration'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['hydration']);
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['ct'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['ct']);
        $inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['propagationInYears'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['propagationInYears']);

        // For the second 'alt'
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['dRef'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['dRef']);
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['m'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['m']);
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['hydration'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['hydration']);
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['ct'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['ct']);
        $inputJSON['project']['projectData']['alts']['alt']['1']['alternative']['concreteMixDesign']['propagationInYears'] = floatval($inputJSON['project']['projectData']['alts']['alt']['0']['alternative']['concreteMixDesign']['propagationInYears']);

        $runPostPageDataSave = Helper::postPageDataSave($inputJSON);

        if ($runPostPageDataSave) {

            if (is_array($request->intendedUrl)) {

                return Redirect::route($request->intendedUrl[0], $request->intendedUrl[1]);
            }

            return Redirect::route($request->intendedUrl ?: 'concrete-mixtures');
        } else {

            return Redirect::route('set-project')->with('error', 'Something went wrong!');
        }
    }

    public function prepareJSON($inputData)
    {
        $project = $this->getProject();
        if (session('project')) {
            $inputJSON = session('project');
        } else {
            $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
        }

        $tempJSON = $inputJSON['project']['projectData'];

        $tempJSON['alts']['alt'] = $inputData['alternatives'];
        Session::put('alt', $inputData['alternatives']);
        if (session('uncertainty')) {

            $tempJSON['uncertainty'] = session('uncertainty');
            $tempJSON['uncertainty']['useUncertainty'] = $inputData['useUncertainty'];
        } else {

            $tempJSON['uncertainty']['useUncertainty'] = $inputData['useUncertainty'];
            $tempJSON['uncertainty']['useDefaults'] = $inputData['useUncertaintyDefaults'];

            $tempJSON['uncertainty']['coVd28'] = $inputData['coVd28'];
            $tempJSON['uncertainty']['coVm'] = $inputData['coVm'];
            $tempJSON['uncertainty']['covCs'] = $inputData['covCs'];
            $tempJSON['uncertainty']['covCt'] = $inputData['covCt'];
            $tempJSON['uncertainty']['covCover'] = $inputData['covCover'];
        }
        $inputJSON['project']['projectData'] = $tempJSON;
        Session::put('project', $inputJSON);
        return  $inputJSON;
    }

    public function updateUncertaintyForCurrentCalculation(Request $request)
    {
        $requestData = $request->all();

        $requestData['uncertainty']['useUncertainty'] = true;
        //dd($requestData['uncertainty']);
        Session::put('uncertainty', $requestData['uncertainty']);
        //dd(session('username'));
        //dd(session(['uncertainty', $request->uncertainty]));

    }
}
