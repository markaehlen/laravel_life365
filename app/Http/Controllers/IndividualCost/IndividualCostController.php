<?php

namespace App\Http\Controllers\IndividualCost;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;
use App\Models\UserProject;
use App\Models\Project;
use Session;

class IndividualCostController extends Controller
{

    public function index(Request $request)
    {
        $project = $this->getProject();
        
        $saved_defaults = json_decode($project->inputs);
        if (session('project')) {
            $inputJSON=session('project');
             
        } else{
            $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
          }
        
        // $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
        
                    $inputJSON = Helper::exposureLocaleCorrection($inputJSON);
                   
                    $inputJSON = Helper::listenerASTMCorrection($inputJSON);
                    
                    $orignal=$inputJSON;
                if (isset($inputJSON['project']['projectData']['serviceLifeResults'])) {
                    unset($inputJSON['project']['projectData']['serviceLifeResults']);
                }
                  
                $inputJSON = Helper::prePageDataCalc($inputJSON, 'cost');
                $inputJSON['project']['projectData']['alts']=$orignal['project']['projectData']['alts'];
                $colors = [];
        array_push($colors, $saved_defaults->base_alt_color, $saved_defaults->alt1_color, $saved_defaults->alt2_color, $saved_defaults->alt3_color, $saved_defaults->alt4_color, $saved_defaults->alt5_color);
        
        //Get Project JSON
        return Inertia::render('IndividualCost/IndividualCost', [
            'isProjectScreen' => true,
            'projectData' => $inputJSON,
            'lccResults' => json_decode(gzdecode(base64_decode($inputJSON['project']['projectData']['lccResults']))),
            //Colors
            'colors' => $colors,
            'structures' => Config::get('defaults.structures'),
            "usUnits" => Config::get('units.sUnitsUS'),
            "siUnits" => Config::get('units.sUnitsSI'),
            "centimeterMetric" => Config::get('units.sUnitsCentM'),
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

    public function saveDefaults(Request $request)
    {
        if (true) {
            if (Auth::guard('user')->user()) {
                DB::table('inputs')
                    ->updateOrInsert(
                        ['user_id' => Auth::guard('user')->user()->id],
                        ['inputs' => json_encode($request->all())]
                    );
            }
            return Redirect::route('set-project')
                ->withCookie(cookie()->forever('defaults', json_encode($request->all())))
                ->withCookie(cookie()->forever('ecoparameters', json_encode($request->ecoparameters)))
                ->withCookie(cookie()->forever('baseUnits', json_encode($request->baseUnits)))
                ->with('success', 'Default settings saved successfully!');
        } else {
            return Redirect::route('defaults')->with('error', 'Something went wrong!');
        }
    }

    public function calculateIndividualCost(Request $request)
    {
       
        $inputJSON = $this->prepareJSON($request->all());
        
        $runPostPageDataSave = Helper::postPageDataSave($inputJSON);

        if ($runPostPageDataSave) {
            return Redirect::route('individual-cost')
                ->with('success', 'Individual costs computed successfully!');
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

        // $inputJSON = json_decode(json_encode(json_decode($project->details)), true);

        $tempJSON = $inputJSON['project']['projectData'];

        $tempJSON['alts']['alt'] = $inputData['alternatives'];

        $inputJSON['project']['projectData'] = $tempJSON;
        Session::put('project',$inputJSON);
        return  $inputJSON;
    }
}
