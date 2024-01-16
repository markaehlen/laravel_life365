<?php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Defaults\DefaultsController;
use App\Http\Controllers\Conversion\ConversionController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Exposure\ExposureController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\UserProjectController;
use App\Http\Controllers\Profile\PasswordController;
use App\Http\Controllers\Page\PageController;
use App\Http\Controllers\ConcreteMixture\ConcreteMixtureController;
use App\Http\Controllers\LifeCycleCost\LifeCycleCostController;
use App\Http\Controllers\IndividualCost\IndividualCostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImagesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Frontend
Route::get('clear-session', function () {
    session()->flush();
    return redirect('/');
});
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/pages/{slug}', [PageController::class, 'show'])
    ->name('pages');

// Auth routes
Route::get('login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest:user');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])
    ->name('register')
    ->middleware('guest:user');

Route::post('register', [RegisterController::class, 'register'])
    ->name('register')
    ->middleware('guest:user');

Route::post('login', [LoginController::class, 'login'])
    ->name('login.attempt')
    ->middleware('guest:user');

// Forgot password routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request')
    ->middleware('guest:user');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email')
    ->middleware('guest:user');

// Reset password routes
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset')
    ->middleware('guest:user');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.reset.attempt')
    ->middleware('guest:user');

//SPA Routes
Route::get('defaults', [DefaultsController::class, 'index'])
    ->name('defaults');

Route::get('get-sublocations/{id}', [DefaultsController::class, 'getSublocations'])
    ->name('defaults.sublocations');

Route::get('get-exposures/{id}', [DefaultsController::class, 'getExposures'])
    ->name('defaults.sublocations');

Route::post('redefine-units', [ConversionController::class, 'getRedefinedRatesAndUnits'])
    ->name('defaults.redefine-units');

Route::post('save-defaults', [DefaultsController::class, 'saveDefaults'])
    ->name('save.defaults');

Route::get('reset-defaults', [DefaultsController::class, 'resetDefaults'])
    ->name('reset.defaults');

Route::get('help/{slug}', [PageController::class, 'getHelpForPage'])
    ->name('help-window');

Route::get('overview', [PageController::class, 'overview'])
    ->name('overview');

Route::get('clear-projects', [ProjectController::class, 'clearProjects'])
    ->name('clear.projects');

Route::post('import-project', [ProjectController::class, 'importProject'])
    ->name('import-project');

Route::get('open-saved-project/{projectID}', [ProjectController::class, 'openSavedProject'])
    ->name('open-saved-project'); 
       
Route::group(['middleware' => 'check.defaults'], function () {

    

    //Project Routes
    Route::get('set-project', [ProjectController::class, 'index'])
        ->name('set-project');

    Route::get('export-project', [ProjectController::class, 'exportProject'])
        ->name('export.project');

    //Calculate Volume
    Route::post('calculate-volume', [ProjectController::class, 'getVolumeOfConcrete'])->name('calculate-volume');
    
    //save project
    Route::get('save-project', [ProjectController::class, 'saveProject'])->name('save-project');
    Route::get('new-project', [ProjectController::class, 'newProject'])->name('new-project');
    //save project data
    Route::post('save/project-data', [ProjectController::class, 'saveProjectData'])->name('save.project-data');

    //save exposure data
    Route::post('save/exposure-data', [ExposureController::class, 'saveExposureData'])->name('save.exposure-data');

    //save exposure data
    Route::post('calculate-astm', [ExposureController::class, 'calculateASTMData'])->name('save.calculate-astm');

    Route::post('update-uncertainty', [ConcreteMixtureController::class, 'updateUncertaintyForCurrentCalculation'])->name('update.uncertainty');

    //get updated sets from session
    Route::get('get-astm-sets', function () {
        return session('inputJSON');
    });

    //Project Routes
    Route::get('set-exposure', [ExposureController::class, 'index'])
        ->name('set-exposure');

    Route::get('change-units-string/{baseUnits}', [ConversionController::class, 'changeUnitsString'])
        ->name('sitewide.change-units-string');

    Route::post('get-sublocation-maxconc-and-temperatures', [ExposureController::class, 'getSublocationConcAndTemperatures'])
        ->name('exposures.temperatures');

    Route::post('convert-conc-by-unit', [ExposureController::class, 'convertConcentrationBasedOnConcUnitAndBaseUnit'])
        ->name('exposures.convert-conc');

    Route::any('concrete-mixtures', [ConcreteMixtureController::class, 'index'])
        ->name('concrete-mixtures');

    Route::get('service-life-report', [ConcreteMixtureController::class, 'exportSLReport'])
        ->name('service-life-report');

    Route::post('compute-servicelife', [ConcreteMixtureController::class, 'computeServiceLife'])
        ->name('compute-servicelife');

    Route::get('life-cycle-cost', [LifeCycleCostController::class, 'index'])
        ->name('life-cycle-cost');

    Route::get('lcc-report', [LifeCycleCostController::class, 'exportLCCReport'])
        ->name('lcc-report');

    Route::get('individual-cost', [IndividualCostController::class, 'index'])
        ->name('individual-cost');

    Route::post('calculate-individual-cost', [IndividualCostController::class, 'calculateIndividualCost'])
        ->name('calculate-individual-cost');
});

Route::get('close-project', [ProjectController::class, 'closeProject'])
->name('close.project');


// Images
Route::group(['middleware' => ['auth:user', 'logoutUsers']], function () {
    // Logout
    Route::get('logout', [LoginController::class, 'logout'])
        ->name('logout');
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])
        ->name('dashboard');
    // Profile
    Route::get('profile', [ProfileController::class, 'index'])
        ->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    // Password
    Route::get('password', [PasswordController::class, 'index'])
        ->name('password');
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');
    //User Projects
    Route::resource('my-projects', UserProjectController::class);
});

Route::get('/spa/images/{path}', [ImagesController::class, 'show'])->where('path', '.*');
// 500 error
Route::get('500', function () {
    // Force debug mode for this endpoint in the demo environment
    if (App::environment('demo')) {
        Config::set('app.debug', true);
    }

    echo $fail;
});
