<?php

use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\EnquiryController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ExposureController;
use App\Http\Controllers\Admin\SublocationController;
use App\Http\Controllers\Admin\EcoparameterController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\HelpwindowController;
use App\Http\Controllers\Admin\HelpTipController;

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

// Admin

Route::group(["prefix" => "admin", "as" => "admin."], function () {


    // Auth routes
    Route::get("/", function () {
        return redirect()->route("admin.login");
    });

    // Login routes
    Route::get('login', [LoginController::class, 'showLoginForm'])
        ->name('login')
        ->middleware(['guest:admin']);
    Route::post('login', [LoginController::class, 'login'])
        ->name('login.attempt')
        ->middleware('guest:admin');

    // Forgot password routes
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request')
        ->middleware('guest:admin');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email')
        ->middleware('guest:admin');

    // Reset password routes
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset')
        ->middleware('guest:admin');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.reset.attempt')
        ->middleware('guest:admin');

    // Authenticated routes
    Route::group(['middleware' => ['auth:admin', 'revalidate']], function () {

        // Logout
        Route::get('logout', [LoginController::class, 'logout'])
            ->name('logout');
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/dashboard/user/statistics', [DashboardController::class, 'userStatistics'])
            ->name('dashboard.user.statistics');
        Route::get('/dashboard/enquiry/statistics', [DashboardController::class, 'enquiryStatistics'])
            ->name('dashboard.enquiry.statistics');

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

        // Users
        Route::put('users/{user}/restore', [UsersController::class, 'restore'])
            ->name('users.restore');
        Route::put('users/{user}/toggle-status', [UsersController::class, 'toggleStatus'])
            ->name('users.toggle.status');
        Route::resource('users', UsersController::class);

        // Roles
        Route::put('roles/{role}/restore', [RoleController::class, 'restore'])
            ->name('roles.restore');
        Route::put('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])
            ->name('roles.toggle.status');
        Route::resource('roles', RoleController::class)
            ->except('show');

        // Enquries
        Route::put('enquiries/{enquiry}/restore', [EnquiryController::class, 'restore'])
            ->name('enquiries.restore');
        Route::resource('enquiries', EnquiryController::class)
            ->except(['create', 'store', 'edit', 'update']);

        // Faqs
        Route::put('faqs/{faq}/restore', [FaqController::class, 'restore'])
            ->name('faqs.restore');
        Route::put('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus'])
            ->name('faqs.toggle.status');
        Route::resource('faqs', FaqController::class);

        // Testimonials
        Route::put('testimonials/{testimonial}/restore', [TestimonialController::class, 'restore'])
            ->name('testimonials.restore');
        Route::put('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])
            ->name('testimonials.toggle.status');
        Route::resource('testimonials', TestimonialController::class);

        // Pages
        Route::resource('pages', PageController::class)
            ->except(['restore']);

        // Helpwindows
        Route::resource('helpwindows', HelpwindowController::class)
            ->except(['create', 'store', 'destroy', 'restore']);

        // Helpwindows
        Route::resource('help-tips', HelpTipController::class)
            ->except(['destroy', 'restore']);

        // Email Templates
        Route::resource('email-templates', EmailTemplateController::class)
            ->except(['create', 'store']);

        // Settings
        Route::get('settings', [SettingController::class, 'index'])
            ->name('settings');
        Route::put('settings', [SettingController::class, 'update'])
            ->name('settings.update');

        // Reports
        Route::get('reports', [ReportsController::class, 'index'])
            ->name('reports');

        //Core Parameters

        //Unit Systems
        Route::put('systems/{system}/toggle-status', [SystemController::class, 'toggleStatus'])
            ->name('systems.toggle.status');
        Route::resource('systems', SystemController::class)
            ->except('create', 'store', 'destroy', 'restore');

        //Units
        Route::put('units/{unit}/restore', [UnitController::class, 'restore'])
            ->name('units.restore');
        Route::put('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])
            ->name('units.toggle.status');

        Route::resource('units', UnitController::class);

        // Locations
        Route::put('locations/{location}/restore', [LocationController::class, 'restore'])
            ->name('locations.restore');
        Route::put('locations/{location}/toggle-status', [LocationController::class, 'toggleStatus'])
            ->name('locations.toggle.status');
        Route::resource('locations', LocationController::class);

        //Sublocations
        Route::put('sublocations/{sublocation}/restore', [SublocationController::class, 'restore'])
            ->name('sublocations.restore');
        Route::put('sublocations/{sublocation}/toggle-status', [SublocationController::class, 'toggleStatus'])
            ->name('sublocations.toggle.status');

        Route::resource('sublocations', SublocationController::class);

        // Exposures
        Route::put('exposures/{exposure}/restore', [ExposureController::class, 'restore'])
            ->name('exposures.restore');
        Route::put('exposures/{exposure}/toggle-status', [ExposureController::class, 'toggleStatus'])
            ->name('exposures.toggle.status');
        Route::resource('exposures', ExposureController::class);

        //Ecoparameters
        // Locations
        Route::put('ecoparameters/{ecoparameter}/restore', [EcoparameterController::class, 'restore'])
            ->name('ecoparameters.restore');
        Route::put('ecoparameters/{ecoparameter}/toggle-status', [EcoparameterController::class, 'toggleStatus'])
            ->name('ecoparameters.toggle.status');
        Route::resource('ecoparameters', EcoparameterController::class);
    });
});

// Images

Route::get('/images/{path}', [ImagesController::class, 'show'])->where('path', '.*');

// 500 error
Route::get('500', function () {
    // Force debug mode for this endpoint in the demo environment
    if (App::environment('demo')) {
        Config::set('app.debug', true);
    }

    echo $fail;
});
