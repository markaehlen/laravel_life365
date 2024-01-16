<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\UserRequest;
use App\Mails\Admin\NewUserRegistered;
use App\Mails\Admin\AccountActivated;
use App\Mails\Admin\AccountDeActivated;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use Exception;
use Config;

class UsersController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'status', 'trashed', 'sort'),
            'users' => User::notId(Auth::guard('admin')->id())
                ->filter(Request::only('search', 'status', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($user, $index) {
                    return [
                        'id' => $user->id,
                        'srno' => ($index + 1),
                        'name' => $user->name,
                        'email' => $user->email,
                        'is_active' => $user->is_active,
                        'created_at' => $user->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                        'deleted_at' => $user->deleted_at,
                    ];
                }),
            'authorize' => [
                'view' => Auth::guard('admin')->user()->can('viewAny', User::class),
                'create' => Auth::guard('admin')->user()->can('create', User::class),
                'update' => Auth::guard('admin')->user()->can('update', User::class),
                'delete' => Auth::guard('admin')->user()->can('delete', User::class),
            ],
        ]);
    }

    /**
     * Show the resource create view.
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Save the specified resource.
     * @param UserRequest $request
     * @return Response
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name' => Request::get('first_name'),
                'last_name' => Request::get('last_name'),
                'email' => Request::get('email'),
                'is_active' => true
            ]);
            
            // Uncomment this if want to receive password reset link and pass the token mail template and reeceive it.
            $token = app('auth.password.broker')->createToken($user);

            // Send mail to user
            try {
                Mail::to($user)->send(new NewUserRegistered($user, $token));
            } catch (\Exception $e) {
                Log::info("User:: welcome email not sent");
            }
            // Db commit
            DB::commit();
            return Redirect::route('admin.users.index')->with('success', 'User has been created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::route('admin.users.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the resource edit view.
     * @param User $user
     * @return Response
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'deleted_at' => $user->deleted_at,
            ],
            'authorize' => [
                'update' => Auth::guard('admin')->user()->can('update', $user),
                'restore' => Auth::guard('admin')->user()->can('restore', $user),
            ],
        ]);
    }
    /**
     * Update the specified resource.
     * @param UserRequest $request
     * @param User $user
     * @return Response
     */
    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            $user->update(Request::only('first_name', 'last_name', 'email'));

            // Db commit
            DB::commit();
            return Redirect::route('admin.users.index')->with('success', 'User has been updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return Redirect::route('admin.users.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the specified resource.
     * @param User $user
     * @return Response
     */
    public function show(User $user)
    {
        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'created_at' => $user->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            ],
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', $user),
            ],
        ]);
    }

    /**
     * Destroy the specified resource.
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return Redirect::back()->with('success', 'User has been deleted successfully.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Restore the specified resource.
     * @param User $user
     * @return Response
     */
    public function restore(User $user)
    {
        try {
            $user->restore();
            return Redirect::back()->with('success', 'User has been successfully restored.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Toggle status of the specified resource.
     * @param User $user
     * @return Response
     */
    public function toggleStatus(User $user)
    {
        if (Auth::guard('admin')->user()->cannot('update', User::class)) {
            abort(403);
        }
        try {
            $user->is_active = $user->is_active ? User::IN_ACTIVE : User::ACTIVE;
            $user->save();
            $user->refresh();

            try {
                if ($user->is_active == User::ACTIVE) {
                    Mail::to($user)->send(new AccountActivated($user));
                } else {
                    Mail::to($user)->send(new AccountDeActivated($user));
                }
            } catch (\Exception $e) {
                Log::info("User:: welcome email not sent");
            }
            return Redirect::back()->with('success', 'User status has been updated successfully.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
