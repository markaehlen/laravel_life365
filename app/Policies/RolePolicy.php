<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\Admin  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(Admin $user, $ability)
    {
        if ($user) {
            return Response::allow();
        }
    }

    /**
     * Determine whether the user can view the any model.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Admin $user)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.ROLE'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access roles.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $user, Role $role = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.ROLE'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access roles.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $user)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.ROLE'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to create roles.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, Role $role = null)
    {
        if (optional($role)->is_system) {
            return Response::deny('Sorry! system roles are not editable.');
        }
        return $user->hasModulePermission(
            config('authorization.MODULES.ROLE'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to update roles.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, Role $role = null)
    {
        if (optional($role)->is_system) {
            return Response::deny('Sorry! system roles are not deletable.');
        }
        return $user->hasModulePermission(
            config('authorization.MODULES.ROLE'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to delete roles.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, Role $role = null)
    {
        if (optional($role)->is_system) {
            return Response::deny('Sorry! system roles are not deletable.');
        }
        return $user->hasModulePermission(
            config('authorization.MODULES.USER'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to restore users.');
    }
}
