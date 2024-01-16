<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use Config;

class RoleController extends Controller
{
    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Roles/Index', [
            'filters' => Request::all('search', 'status', 'trashed', 'sort'),
            'roles' => Role::filter(Request::only('search', 'status', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($role, $index) {
                    return [
                        'id' => $role->id,
                        'srno' => ($index + 1),
                        'name' => $role->name,
                        'is_active' => $role->is_active,
                        'is_system' => $role->is_system,
                        'created_at' => $role->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                        'deleted_at' => $role->deleted_at,
                    ];
                }),
            'authorize' => [
                'view' => Auth::guard('admin')->user()->can('viewAny', Role::class),
                'create' => Auth::guard('admin')->user()->can('create', Role::class),
                'update' => Auth::guard('admin')->user()->can('update', Role::class),
                'delete' => Auth::guard('admin')->user()->can('delete', Role::class),
            ],
        ]);
    }

    /**
     * Show the resource create view.
     * @return Response
     */
    public function create()
    {
        $modules = Module::isActive()
            ->get()
            ->transform(function ($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name
                ];
            });
        $permissions = Permission::isActive()
            ->get()
            ->transform(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name
                ];
            });

        return Inertia::render('Roles/Create', [
            'modules' => $modules,
            'permissions' => $permissions,
            'initialPermissions' => $modules->map(function ($module) {
                return [
                    'module' => $module['id'],
                    'permission' => 1
                ];
            })
        ]);
    }

    /**
     * Save the specified resource.
     * @param RoleRequest $request
     * @return Response
     */
    public function store(RoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => Request::get('name'),
            ]);
            // Attach the modules
            foreach ($request->get('permissions') as $permission) {
                $role->modules()->attach($permission['module'], ['permission_id' => $permission['permission']]);
            }
            // Db commit
            DB::commit();
            return Redirect::route('admin.roles.index')->with('success', 'Role has been created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('admin.roles.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the resource edit view.
     * @param Role $role
     * @return Response
     */
    public function edit(Role $role)
    {
        return Inertia::render('Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'created_at' => $role->created_at->format('M/d/Y'),
                'deleted_at' => $role->deleted_at,
            ],
            'modules' => Module::isActive()
                ->get()
                ->transform(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                    ];
                }),
            'permissions' => Permission::isActive()
                ->get()
                ->transform(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name
                    ];
                }),
            'assignedPermissions' => $role->modules->transform(function ($module) {
                return [
                    'module' => $module->pivot->module_id,
                    'permission' => $module->pivot->permission_id
                ];
            }),
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', Role::class),
            ],
        ]);
    }
    /**
     * Update the specified resource.
     * @param RoleRequest $request
     * @param Role $role
     * @return Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        DB::beginTransaction();
        try {
            $role->update(Request::only('name'));
            // detach the modules
            $role->modules()->detach();
            // Attach the modules
            foreach ($request->get('permissions') as $permission) {
                $role->modules()->attach($permission['module'], ['permission_id' => $permission['permission']]);
            }
            // Db commit
            DB::commit();
            return Redirect::route('admin.roles.index')->with('success', 'Role has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('admin.roles.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Destroy the specified resource.
     * @param Role $role
     * @return Response
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return Redirect::back()->with('success', 'Role has been deleted successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Restore the specified resource.
     * @param Role $role
     * @return Response
     */
    public function restore(Role $role)
    {
        try {
            $role->restore();
            return Redirect::back()->with('success', 'Role has been successfully restored.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Toggle status of the specified resource.
     * @param Role $role
     * @return Response
     */
    public function toggleStatus(Role $role)
    {
        if (Auth::guard('admin')->user()->cannot('update', $role)) {
            abort(403);
        }
        try {
            $role->is_active = $role->is_active ? Role::IN_ACTIVE : Role::ACTIVE;
            $role->save();
            return Redirect::back()->with('success', 'Role status has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
