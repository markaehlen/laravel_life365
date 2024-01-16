<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use DB;
use App\Mails\WelcomeEmail;
use App\Models\Project;
use Illuminate\Support\Facades\Mail;
use App\Models\UserProject;

use Illuminate\Support\Str;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:user');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create($data);
        $user->is_active = true;
        $user->password = $data['password'];
        $user->save();

        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(UserRequest $request)
    {
        $user = $this->create($request->all());

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('user');
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $this->syncProjectIfExist();

        // Send mail to user
        try {
            Mail::to($user)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            \Log::info("User:: password changed email not sent");
        }
    }

    public function syncProjectIfExist()
    {
        $user = Auth::guard('user')->user();
        if ($user) {
            if (session('projectID')) {
                $project = Project::where('unique_id', session('projectID'))->first();
                $inputJSON = json_decode(json_encode(json_decode($project->details)), true);
                UserProject::updateOrInsert(
                    ['user_id' => $user->id, 'unique_id' => $project->unique_id],
                    [
                        'title' => $inputJSON['project']['projectData']['title'],
                        'description' => $inputJSON['project']['projectData']['description'],
                        'details' => $project->details,
                        'inputs' => $project->inputs
                    ]
                );
            }
        }
        return true;
    }
}
