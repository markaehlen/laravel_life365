<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\UserProject;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Storage;
use DB;
use Session;

class Helper
{
    public static function authenticateAPI()
    {
    return true;
        $response = Http::post(env('API_LOGIN_URL'), [
            'user' => env('API_USERNAME'),
            'password' => env('API_PASSWORD'),
        ]);

        $decoded_response = $response->object();

        if ($response->ok() && $decoded_response->message == "ok") {
            return $decoded_response;
        } else if ($response->status() == '200') {
            abort(400, $decoded_response->message);
        } else if ($response->status() == '400') {
            abort(400, $response->getReasonPhrase());
        }
    }

    // public static function callAPI($loginDetails, $inputJSON, $procedure)
    // {
        
    //     ini_set('memory_limit', '-1');
    //     ini_set('max_execution_time', 300);
    //     File::put(public_path('/uploads/apicalls/input.json'), json_encode($inputJSON));
    //     // my own
    //     // File::put(public_path('/uploads/apicalls/input.result'), json_encode($inputJSON));

	//     $path = Storage::disk('stag_file_for_api')->path($procedure);
            
	//         $files = scandir($path, SCANDIR_SORT_DESCENDING);
           
    //     	$filename = $files[1];
            
	// 	$file_array = explode(".", $filename);
    //     // $get=DB::table('ids')->lastInsertId();
    //     // dd($get);
    //     $id = DB::table('ids')->insertGetId([
    //         'id_junk' => 'x',           
    //     ]);
	// 	//$fileid = (int) $file_array[0] + 1;        
	// 	$fileid=$id+ 1;
        
    //     if ($loginDetails && $inputJSON && $procedure) {

            
    //         Storage::disk('stag_file_for_api')->put($procedure.'/'.$fileid.'.json', json_encode($inputJSON),'public');
    //         // my own
    //         // Storage::disk('stag_file_for_api')->append($procedure.'/'.$fileid.'.results', json_encode($inputJSON),'public');
    //         // $path = Storage::disk('stag_file_for_api')->path('');
    //         $path="../../data/";
            
    //         $cmd="java -jar ../../Life365_CLI.jar verbose=false id=$fileid proc=$procedure path=$path";
    //          //java -jar ../../Life365_CLI.jar verbose=false id=100000 proc=astm path=../../data/
             
    //         exec($cmd . " 2>&1",$output);
    //         $api_proc_res=(object) array(
    //              'message' => 'ok',
    //              'id' => $fileid,
    //              'systemerror'=>$output,
    //              );
            
    //         if (Storage::disk('stag_file_for_api')->exists($procedure.'/'.$fileid.'.results')) {
    //             //my own
    //             // $a=Storage::disk('stag_file_for_api')->get($procedure.'/'.$fileid.'.json');
                
    //             $fileapi_result = Storage::disk('stag_file_for_api')->get($procedure.'/'.$fileid.'.results');
    //             //dd($fileapi_result);
    //             $api_proc_res->results=$fileapi_result;
    //         }
            
    //         if (Storage::disk('stag_file_for_api')->exists($procedure.'/'.$fileid.'.error')) {
               
    //             $api_proc_res->error = Storage::disk('stag_file_for_api')->get($procedure.'/'.$fileid.'.error');
    //         }
            
    //         return $api_proc_res;
    //     } else {
    //         abort(500, 'Bad Request');
    //     }
    // }
    public static function callAPI($loginDetails, $inputJSON, $procedure)
    {   
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
        File::put(public_path('/uploads/apicalls/input.json'), json_encode($inputJSON));

	    $path = Storage::disk('stag_file_for_api')->path($procedure);

        $files = scandir($path, SCANDIR_SORT_DESCENDING);
             
        $filename = $files[1];
            
		$file_array = explode(".", $filename);
        
        $id = DB::table('ids')->insertGetId([
            'id_junk' => 'x',           
        ]);
		        
		$fileid=$id+ 1;
        
       
        if ($loginDetails && $inputJSON && $procedure) {
        
            Storage::disk('stag_file_for_api')->put($procedure.'/'.$fileid.'.json', json_encode($inputJSON),'public');
        
            $path = Storage::disk('stag_file_for_api')->path('');

            $cmd="java -jar /var/www/api.life-365.org/htdocs/api/Life365_CLI.jar verbose=false id=$fileid proc=$procedure path=$path";
            exec($cmd . " 2>&1",$output);
            $api_proc_res=(object) array(
                'message' => 'ok',
                'id' => $fileid,
                'systemerror'=>$output,
                );
                
            if (Storage::disk('stag_file_for_api')->exists($procedure.'/'.$fileid.'.results')) {
                $fileapi_result = Storage::disk('stag_file_for_api')->get($procedure.'/'.$fileid.'.results');
                $api_proc_res->results=$fileapi_result;

            }

            if (Storage::disk('stag_file_for_api')->exists($procedure.'/'.$fileid.'.error')) {
                
                $api_proc_res->error = Storage::disk('stag_file_for_api')->get($procedure.'/'.$fileid.'.error');
                $error['error']=$api_proc_res->error;
                $error['filename']=$procedure.'/'.$fileid.'.error';
                // Mail::to('ahsanshahzad920@gmail.com')->send(new \App\Mails\Admin\Errorlog($error));  
                              
                if($api_proc_res->error!='' && $api_proc_res->error!="[]"){
                    $jsonFilePath = Storage::disk('stag_file_for_api')->path($procedure . '/' . $fileid . '.json');
                    // Mail::to(['mark.ehlen.consulting@gmail.com','ahsanshahzad920@gmail.com'])->cc('david@webscope.com')->send(new \App\Mails\Admin\Errorlog($error,$jsonFilePath));
                }
            }
            
            return $api_proc_res;
        } else {
            abort(500, 'Bad Request');
        }
        
    }

    public static function getMAC()
    {
        return '';
        // return substr(exec('getmac'), 0, 17);
    }

    public static function getIP()
    {
        return \Request::ip();
    }

    public static function convertProjectDataFromOneConcentrationUnitToAnother($projectData, $lastBaseUnits, $newBaseUnits)
    {

        // environment changes
        $projectData['exposureConditions']['maxSurfaceConcentration'] = (new self)->convertConcentrationFromOldToNewConcentrationUnits($projectData['exposureConditions']['maxSurfaceConcentration'], $projectData['baseUnits'], $lastBaseUnits, $newBaseUnits);

        // mixes modifications
        for ($m = 0; $m < sizeof($projectData['alts']['alt']); $m++) {
            $cmd = $projectData['alts']['alt'][$m]['alternative']['concreteMixDesign'];
            if ($cmd['isUserDefineable']) {
                $cmd['ct'] = (new self)->convertConcentrationFromOldToNewConcentrationUnits($cmd['ct'], $projectData['baseUnits'], $lastBaseUnits, $newBaseUnits);
            }
            $projectData['alts']['alt'][$m]['alternative']['concreteMixDesign'] = $cmd;
        }

        $projectData['concentrationUnits'] = $newBaseUnits;
        return $projectData;
    }

    public static function convertConcentrationFromOldToNewConcentrationUnits($conc, $baseUnits, $oldUnits, $newUnits)
    {
        if ($oldUnits === $newUnits) {
            return $conc;
        }

        if ($oldUnits === Config::get('units.sConcKgM3')) {
            return (new self)->convertKgPerM3_to_PctConc($conc);
        } else if ($oldUnits === Config::get('units.sConcLbY3')) {
            return (new self)->convertLbsPerY3_to_PctConc($conc);
        } else {
            if ($baseUnits == Config::get('units.sUnitsCentM') || $baseUnits == Config::get('units.sUnitsSI')) {

                return (new self)->convertPctConc_to_KgPerM3($conc);
            }
            return (new self)->convertPctConc_to_LbsPerY3($conc);
        }
    }

    public function convertKgPerM3_to_PctConc($value)
    {

        return ($value / Config::get('conversions.dKgsPerM3Concrete')) * 100;
    }

    public function convertLbsPerY3_to_PctConc($value)
    {

        return ($value / Config::get('conversions.dLBsPerY3Concrete')) * 100.;
    }


    public function convertPctConc_to_KgPerM3($value)
    {

        return Config::get('conversions.dKgsPerM3Concrete') * $value / 100.;
    }

    public function convertPctConc_to_LbsPerY3($value)
    {

        return Config::get('conversions.dLBsPerY3Concrete') * $value / 100.;
    }

    public static function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    //load data on page load

    public static function prePageDataCalc($inputJSON, $proc)
    {
        $loginResults = Helper::authenticateAPI();
        
        // dump($proc);
        // my original data
        $orignal=$inputJSON;
        
        $apiResults = Helper::callAPI($loginResults, $inputJSON, $proc);
        
        // dd(json_decode($apiResults->results));
        if (!empty($apiResults->results)) {
            
            $decoded_results = json_decode($apiResults->results);
           
            $decoded_results = json_decode(json_encode($decoded_results), true);
            
            
            // 9-1-2024
            $inputJSON['project']['projectData'] = $decoded_results['project']['projectData'];
           
            $inputJSON['project']['projectData']['exposureLocale'] = $orignal['project']['projectData']['exposureLocale'];
            // $inputJSON['project']['projectData']['serviceLifeResults']=$decoded_results['project']['projectData']['serviceLifeResults'];
            // $inputJSON['project']['projectData']['lccResults']=$decoded_results['project']['projectData']['lccResults'];
            
            if ($inputJSON) {
                $runPostPageDataSave = Helper::postPageDataSave($inputJSON);
                if ($runPostPageDataSave) {
                    return $inputJSON;
                }
            }
        } else {
            if ($apiResults->systemerror) {
                
                abort(403, json_encode($apiResults->systemerror));
            } else if ($apiResults->error) {
                
                abort(403, json_encode($apiResults->error));
            }
        }
    }

    // post page data save
    public static function postPageDataSave($inputJSON, $inputInputs = null, $projectID = null)
    {
        $projectID = $projectID ? $projectID : session('projectID');
        $user = Auth::guard('user')->user();
        // dd($inputJSON); 
        $data=[
            'title' => $inputJSON['project']['projectData']['title'],
            'description' => $inputJSON['project']['projectData']['description'],
            'details' => json_encode($inputJSON),
            
        ];
        if($inputInputs){
            $data['inputs'] = $inputInputs?json_encode($inputInputs):null;
        }
        // if ($user) {
        //     $act = UserProject::updateOrInsert(
        //         ['user_id' => $user->id, 'unique_id' => $projectID], $data
        //     );
        // } else {
        //     // dd( $projectID);
        //     $act = Project::updateOrInsert(
        //         ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP()], $data
        //     );
        // }
        // if ($act) {
        //     return true;
        // } else {
        //     return false;
        // }
       
        Session::put('project',$inputJSON);
        return true;
    }
    //save data on submit

    // public static function postPageDataSave($inputJSON, $inputInputs = null, $projectID = null)
    // {
        
    //     $projectID = $projectID ? $projectID : session('projectID');
    //     $user = Auth::guard('user')->user();
        
    //     $data=[
    //         'title' => $inputJSON['project']['projectData']['title'],
    //         'description' => $inputJSON['project']['projectData']['description'],
    //         'details' => json_encode($inputJSON),
    //     ];
        
    //     if($inputInputs){
       
    //         $data['inputs'] = $inputInputs?json_encode($inputInputs):null;
    //     }
      
    //     if ($user) {
    //         $project=UserProject::where('user_id',$user->id)->where('unique_id',$projectID)->first();
    //         if($project){
              
    //             $act = UserProject::where('user_id',$user->id)->where('unique_id',$projectID)->update(
    //                 ['user_id' => $user->id, 'unique_id' => $projectID,'title'=>$inputJSON['project']['projectData']['title'],
    //                  'description' => $inputJSON['project']['projectData']['description'],
    //                  'details' => json_encode($inputJSON),
    //             ], 
    //             );
    //         }else{
               
    //             $act = UserProject::insert(
    //                 ['user_id' => $user->id, 'unique_id' => $projectID,'title'=>$inputJSON['project']['projectData']['title'],
    //                 'description' => $inputJSON['project']['projectData']['description'],
    //                 'details' => json_encode($inputJSON)]
    //             );
    //         }
    //         // $act = UserProject::updateOrInsert(
    //         //     ['user_id' => $user->id, 'unique_id' => $projectID], $data
    //         // );

    //     //  dd($act);  
    //     } else {
            
    //         $project=Project::where('mac_address',Helper::getMAC())->where('unique_id',$projectID)->where('ip_address',Helper::getIP())->first();
    //         if($project){
              
    //             $act = Project::where('mac_address',Helper::getMAC())->where('unique_id',$projectID)->where('ip_address',Helper::getIP())->update(
    //                 ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP(),'title'=>$inputJSON['project']['projectData']['title'],
    //                 'description' => $inputJSON['project']['projectData']['description'],
    //                 'details' => json_encode($inputJSON)], 
    //             );
    //         }else{
    //             $act = Project::insert(
    //                 ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP(),'title'=>$inputJSON['project']['projectData']['title'],
    //                 'description' => $inputJSON['project']['projectData']['description'],
    //                 'details' => json_encode($inputJSON)]
    //             );
    //         }

    //         // $act = Project::updateOrInsert(
    //         //     ['unique_id' => $projectID, 'mac_address' => Helper::getMAC(), 'ip_address' => Helper::getIP()], $data
    //         // );
    //     }
    //     if ($act) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    //listener Correction for ASTM

    public static function listenerASTMCorrection($inputJSON)
    {
        
        $inputData = $inputJSON['project']['projectData']['exposureConditions'];
        
        for ($i = 0; $i < sizeof($inputData['c1556Sets']); $i++) {
            if (isset($inputData['c1556Sets'][$i]['astmResults']) && is_array($inputData['c1556Sets'][$i]['astmResults']) && sizeof($inputData['c1556Sets'][$i]['astmResults'])) {
                if ($inputData['c1556Sets'][$i]['astmResults']['errorsHat'] == null) {
                    $inputData['c1556Sets'][$i]['astmResults']['errorsHat'] = 0;
                }

                // if ($inputData['c1556Sets'][$i]['astmResults']['dtm'] && sizeof($inputData['c1556Sets'][$i]['astmResults']['dtm']['listenerList']) == 0) {
                //     $inputData['c1556Sets'][$i]['astmResults']['dtm']['listenerList'] = (object) array();
                // }
                if (isset($inputData['c1556Sets'][$i]['astmResults']['dtm']) && 
                        is_array($inputData['c1556Sets'][$i]['astmResults']['dtm']['listenerList']) &&
                        count($inputData['c1556Sets'][$i]['astmResults']['dtm']['listenerList']) == 0) {
                        $inputData['c1556Sets'][$i]['astmResults']['dtm']['listenerList'] = (object) array();
                    }

                // if ($inputData['c1556Sets'][$i]['astmResults']['dtmSi'] && sizeof($inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList']) == 0) {
                //     $inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList'] = (object) array();
                // }
                if (isset($inputData['c1556Sets'][$i]['astmResults']['dtmSi']) &&
                    isset($inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList']) &&
                    is_array($inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList']) &&
                    count($inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList']) == 0) {

                    $inputData['c1556Sets'][$i]['astmResults']['dtmSi']['listenerList'] = (object) array();
                }

            } else {
                $inputData['c1556Sets'][$i]['astmResults'] = (object) array();
            }
        }
        
        $inputJSON['project']['projectData']['exposureConditions'] = $inputData;
        
        return $inputJSON;
    }

    //exposure Correction for Exposure Locale

    public static function exposureLocaleCorrection($inputJSON)
    {
        if (session('exposure')) {
            $inputJSON['project']['projectData']['exposureLocale']['exposure'] = session('exposure');
        }
        return $inputJSON;
    }

    public function tryCatch($error)
    {
        session()->forget(['projectID']);
        session()->forget(['project']);
        session()->forget(['alt']);
        session()->forget(['exposure']);
        \Log::error($error->getMessage());

        return Redirect::route('/')->with('danger', 'Something is Wrong!');
    }
}
