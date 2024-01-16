<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Ecoparameter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\UserProject;
use App\Models\HelpTip;
use App\Models\Location;
use App\Models\Sublocation;
use App\Models\System;
use App\Models\Exposure;
use App\Models\Unit;
use Illuminate\Support\Facades\Redirect;
use File;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Session;

class ProjectController extends Controller
{
  public function index()
  {
    $project = $this->getProject(); 
    
    $saved_defaults = json_decode($project->inputs);
    
    if (session('project')) {
      $inputJSON=session('project'); 
      
    }else{
      $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
      
    }
    
    
    $colors = [];
    if ($saved_defaults) {
      array_push($colors, $saved_defaults->base_alt_color, $saved_defaults->alt1_color, $saved_defaults->alt2_color, $saved_defaults->alt3_color, $saved_defaults->alt4_color, $saved_defaults->alt5_color);
    }
    if($saved_defaults!=null){
        if ($saved_defaults->baseUnits == Config::get('units.sUnitsSI')) {
          $acceptableValues = Config::get('defaults.si_acceptable_values');
          
        } else if ($saved_defaults->baseUnits == Config::get('units.sUnitsCentM')) {
          $acceptableValues = Config::get('defaults.cm_acceptable_values');
         
        } else if ($saved_defaults->baseUnits == Config::get('units.sUnitsUS')) {
          $acceptableValues = Config::get('defaults.us_acceptable_values');
          
        }
      }else{
        $acceptableValues=Config::get('defaults.us_acceptable_values');
      }
    if(!session('project')) {
       $inputJSON['project']['projectData']['date']=date('m/d/Y');
    }
    $helping_tips = HelpTip::where('category', 'project-details')->pluck('content', 'slug');
    
    return Inertia::render('Project/Project', [
      'isProjectScreen' => true,
      'structures' => Config::get('defaults.structures'),
      "usUnits" => Config::get('units.sUnitsUS'),
      "siUnits" => Config::get('units.sUnitsSI'),
      "centimeterMetric" => Config::get('units.sUnitsCentM'),
      'projectData' => $inputJSON,
      'baseUnits' => $inputJSON['project']['projectData']['baseUnits'],
      "concUnits" => $inputJSON['project']['projectData']['concentrationUnits'],
      'colors' => $colors,
      'acceptableValues' => $acceptableValues,
      'helping_tips' => $helping_tips,
    ]);
  }


  public function getProject()
  {
    $user = Auth::guard('user')->user();
    $project = null;
    if ($user) {
      $project = UserProject::where('unique_id', session('projectID'))->first();
    }else{
      $project = Project::where('unique_id', session('projectID'))->first();
      
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

  public function saveProjectData(Request $request)
  {
    $inputJSON = $this->prepareJSON($request->all());
    
    $runPostPageDataSave = Helper::postPageDataSave($inputJSON);
    
    if ($runPostPageDataSave) {
      // dd($request->intendedUrl);
      if (is_array($request->intendedUrl)) {
        return Redirect::route($request->intendedUrl[0], $request->intendedUrl[1])->with('success', 'Project details saved successfully!');
      }
      return Redirect::route($request->intendedUrl ?: 'set-project')->with('success', 'Project details saved successfully!');
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

    $tempJSON['typeOfStructure'] = $inputData['structure'];
    $tempJSON['title'] = $inputData['title'];
    $tempJSON['doneByName'] = $inputData['analyst'];
    $tempJSON['description'] = $inputData['description'];
    $tempJSON['date'] = $inputData['date'];

    $tempJSON['structureDimensions']['overallThickness'] = $inputData['trueThickness'];
    $tempJSON['structureDimensions']['trueThickness'] = $inputData['trueThickness'];
    $tempJSON['structureDimensions']['clearCover'] = $inputData['clearCover'];
    $tempJSON['structureDimensions']['area'] = $inputData['structureAreaOrLength'];

    $tempJSON['baseYear'] = $inputData['baseYear'];
    $tempJSON['studyPeriod'] = $inputData['studyPeriod'];
    $tempJSON['inflationRate'] = $inputData['inflation'] / 100;
    $tempJSON['realDiscountRate'] = $inputData['realDiscountRate'] / 100;

    $tempJSON['alts']['alt'] = $inputData['alternatives'];

    $inputJSON['project']['projectData'] = $tempJSON;
    Session::put('project',$inputJSON);
    return $inputJSON;
  }
  
  public function saveProject()
  {     
        
        $inputJSON=session('project');
        $projectID =session('projectID');
        if($inputJSON==null)
        {
          return Redirect::route('login')->with('danger','Please Login!');
        }
        
        $user = Auth::guard('user')->user();
        $data=[
            'title' => $inputJSON['project']['projectData']['title'],
            'description' => $inputJSON['project']['projectData']['description'],
            'details' => json_encode($inputJSON),
        ];
        $inputInputs = null;
        if($inputInputs){
            $data['inputs'] = $inputInputs?json_encode($inputInputs):null;
        }
        if ($user) {
            
            $act = UserProject::updateOrInsert(
                ['user_id' => $user->id, 'unique_id' => $projectID], $data
            );
        } else {
            // dd( $projectID);
            $act = Project::updateOrInsert(
                ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP()], $data
            );
        }
    session()->forget(['project']);    
    return Redirect::route('my-projects.index')->with('success','Project Saved!');
  }

  public function newProject()
  {     
        $inputJSON=session('project');
         
        $projectID = (string) Str::uuid();
       
        $user = Auth::guard('user')->user();
        $data=[
            'title' => $inputJSON['project']['projectData']['title']??'Default',
            'description' => $inputJSON['project']['projectData']['description']??'Description',
            'details' => json_encode($inputJSON),
        ];
        $inputInputs = null;
        if($inputInputs){
            $data['inputs'] = $inputInputs?json_encode($inputInputs):null;
        }
        if ($user) {
            
            $act = UserProject::updateOrInsert(
                ['user_id' => $user->id, 'unique_id' => $projectID], $data
            );
        } else {
            // dd( $projectID);
            $act = Project::updateOrInsert(
                ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP()], $data
            );
        }
    session()->forget(['project']);    
    return Redirect::route('my-projects.index')->with('success','Project Saved!');
  }

  public function exportProject()
  {
    $user = Auth::guard('user')->user();
    
    if ($user) {
      if (session('project')) {
        $project=session('project');
      }else{
      $project = UserProject::where('unique_id', session('projectID'))->first();
      }
    } else {
      if (session('project')) {
        $project=session('project');
        
      }else{
      $project = Project::where('unique_id', session('projectID'))->first();
      }
    }
    $mytime = date('YmdHm');
    // dd($project);project_name-YYYYMMDDHHMM.json
    if (session('project')) {
      $filename =   $project['project']['projectData']['title']."-".$mytime.".json";
    
      File::put(public_path('/uploads/json/' . $filename), json_encode($project));
    }else{
    $filename =   $project->title."-".$mytime.".json";
    
    File::put(public_path('/uploads/json/' . $filename), $project->details);
    }
    return response()->download(public_path('/uploads/json/' . $filename))->deleteFileAfterSend();
  }

  public function closeProject()
  {
    session()->forget(['projectID']);
    session()->forget(['project']);
    session()->forget(['alt']);
    session()->forget(['exposure']);
    
    return Redirect::route('home')->with('success', 'Project closed!');
  }

  public function clearProjects()
  {
    Project::where('user_id', null)->delete();
    return Redirect::route('home')->with('success', 'Projects cleared successfully!');
  }

  public function openSavedProject($projectID)
  {
    session(['projectID' => $projectID]);
    return Redirect::route('set-project');
  }

  public function importProject(Request $request)
  {
    $validation = $request->validate([
      'file' => 'file|mimetypes:application/json,text/plain|max:5000'
    ]);

    $inputJSON = $request->file('file')->get();


    // $file      = $validation['file'];
    // $extension = $file->getClientOriginalExtension();
    // $filename  = 'importedJSON_' . time() . '.' . $extension;
    // $path      = $file->storeAs('file', $filename);

    // //extract json

    // $inputJSON = file_get_contents(storage_path() . "/app/${path}");

    if (Helper::isJson($inputJSON)) {
      $inputJSON = json_decode($inputJSON, true);
      $customRequest = new Request([
        'inputJSON' => $inputJSON,
      ]);

      $this->validate($customRequest, [
        // 'inputJSON' => ['bail','required','json','json_schema_validator:validJSONSchema.json',],
        'inputJSON' => ['bail', 'required', 'array'],
        'inputJSON.project' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData.baseUnits' => ['bail', 'required'],
        'inputJSON.project.projectData.concentrationUnits' => ['bail', 'required'],
        'inputJSON.project.projectData.date' => ['bail', 'required'],
        'inputJSON.project.projectData.description' => ['bail', 'required'],
        'inputJSON.project.projectData.doneByName' => ['bail', 'required'],
        'inputJSON.project.projectData.title' => ['bail', 'required'],
        'inputJSON.project.projectData.tradeNames' => ['bail', 'required'],
        'inputJSON.project.projectData.typeOfStructure' => ['bail', 'required'],
        'inputJSON.project.projectData.baseYear' => ['bail', 'required'],
        'inputJSON.project.projectData.inflationRate' => ['bail', 'required'],
        'inputJSON.project.projectData.realDiscountRate' => ['bail', 'required'],
        'inputJSON.project.projectData.studyPeriod' => ['bail', 'required'],
        'inputJSON.project.projectData.alts' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData.costs' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData.exposureConditions' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData.exposureLocale' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData.structureDimensions' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData.temperatureHistory' => ['bail', 'required', 'array'],
        'inputJSON.project.projectData.uncertainty' => ['bail', 'required', 'array'],

      ]);
      //create if doesn't exists
      $proj_inputs=$this->prepareInputsForImport($inputJSON);
      $projectID = (string) Str::uuid();
      
      //Prepare JSON       
        $user = Auth::guard('user')->user();
        $data=[
            'title' => $inputJSON['project']['projectData']['title'],
            'description' => $inputJSON['project']['projectData']['description'],
            'details' => json_encode($inputJSON),
        ];
        
        
        $data['inputs'] =json_encode($this->defaultValue());
        
        
        if ($user) {
            
            $act = UserProject::updateOrInsert(
                ['user_id' => $user->id, 'unique_id' => $projectID], $data
            );
        } else {
            // dd( $projectID);
            $act = Project::updateOrInsert(
                ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP()], $data
            );
        }
      $runPostPageDataSave = Helper::postPageDataSave($inputJSON, $proj_inputs, $projectID);
      if ($runPostPageDataSave) {
        session(['projectID' => $projectID]);
        return Redirect::route('set-project')->with('success', 'Project imported successfully!');
      }
    }
    return response(['message' => 'JSON Data Invalid'], 403);
  }

  public function prepareInputsForImport($inputJSON)
  {
    $inputs=array(
      'intendedUrl'	=>	null,
      'baseUnits'	=>	$inputJSON['project']['projectData']['baseUnits'],
      'concUnits'	=>	$inputJSON['project']['projectData']['concentrationUnits'],
      'location'	=>	$inputJSON['project']['projectData']['exposureLocale']['location'] ?:'New York',
      'subLocation'	=>	$inputJSON['project']['projectData']['exposureLocale']['subLocation'],
      'exposureType'	=>	$inputJSON['project']['projectData']['exposureLocale']['exposure'],
      // 'ecoparameters'	=>	$inputJSON['project']['projectData']['ecoparameters'] ?:[],
      'ecoparameters'	=>	[
        'baseYear'=>[
          'value'=>$inputJSON['project']['projectData']['baseYear'],
          'type'=>null,
        ],
        'studyPeriod'=>[
          'value'=>$inputJSON['project']['projectData']['studyPeriod'],
          'type'=>null,
        ],
        'inflation'=>[
          'value'=>$inputJSON['project']['projectData']['inflationRate'],
          'type'=>'percentage',
        ],
        'discount'=>[
          'value'=>$inputJSON['project']['projectData']['realDiscountRate'],
          'type'=>'percentage',
        ],
        'baseMixCost'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['baseMixCost'],
          'type'=>'volume',
        ],
        'costBlackSteel'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['blackSteel'],
          'type'=>'weight',
        ],
        'costEpoxy'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['epoxySteel'],
          'type'=>'weight',
        ],
        'costStainless'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['stainlessSteel'],
          'type'=>'weight',
        ],
        'costMembrane'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['membrane'],
          'type'=>'area',
        ],
        'costSealer'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['sealant'],
          'type'=>'area',
        ],
        'costInhibitor'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['inhibitor'],
          'type'=>'capacity',
        ],
        'repairCost'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['repairCost'],
          'type'=>'area',
        ],
        'areaToRepairPct'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['areaToRepair'],
          'type'=>'percentage',
        ],
        'repairIntervalYrs'=>[
          'value'=>$inputJSON['project']['projectData']['costs']['repairInterval'],
          'type'=>null,
        ],
      ],

      // 'ecoparameters'	=>	$inputJSON['project']['projectData']['ecoparameters'] ?:[],
      'baseYear'	=>	$inputJSON['project']['projectData']['baseYear'],
      'studyPeriod'	=>	$inputJSON['project']['projectData']['studyPeriod'],
      'inflation'	=>	$inputJSON['project']['projectData']['inflationRate'],
      'discount'	=>	$inputJSON['project']['projectData']['realDiscountRate'],

      'baseMixCost'	=>	$inputJSON['project']['projectData']['costs']['baseMixCost'],
      'costBlackSteel'	=>	$inputJSON['project']['projectData']['costs']['blackSteel'],
      'costEpoxy'	=>	$inputJSON['project']['projectData']['costs']['epoxySteel'],
      'costStainless'	=>	$inputJSON['project']['projectData']['costs']['stainlessSteel'],
      'costMembrane'	=>	$inputJSON['project']['projectData']['costs']['membrane'],
      'costSealer'	=>	$inputJSON['project']['projectData']['costs']['sealant'],
      'costInhibitor'	=>	$inputJSON['project']['projectData']['costs']['inhibitor'],
      'repairCost'	=>	$inputJSON['project']['projectData']['costs']['repairCost'],
      'areaToRepairPct'	=>	$inputJSON['project']['projectData']['costs']['areaToRepair'],
      'repairIntervalYrs'	=>	$inputJSON['project']['projectData']['costs']['repairInterval'],

      'base_alt_color'	=>	'#1CA085',
      'alt1_color'	=>	'#2980B9',
      'alt2_color'	=>	'#222F3D',
      'alt3_color'	=>	'#F2C511',
      'alt4_color'	=>	'#E84B3C',
      'alt5_color'	=>	'#A463BF',

    );

    return $inputs;
  }

  public function defaultValue()
  {
        $saved_defaults = $this->getExistingOrCreateNewDefaults();
        
        $ecoparameters = $saved_defaults ? $saved_defaults->ecoparameters : $this->getDefaultEcoParameters();
        // dd($ecoparameters);
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

        $default=[
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
        ];
      return $default;
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
}
