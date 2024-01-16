<?php

namespace App\Policies;

use App\Models\Ecoparameter;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class EcoparameterPolicy
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
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Admin $user)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.ECO_PARAMETER'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access eco-parameters.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Ecoparameter $ecoparameter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $user, Ecoparameter $ecoparameter = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.ECO_PARAMETER'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access eco-parameters.');
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
            config('authorization.MODULES.ECO_PARAMETER'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to create eco-parameters.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Ecoparameter $ecoparameter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, Ecoparameter $ecoparameter = null)
    {

        return $user->hasModulePermission(
            config('authorization.MODULES.ECO_PARAMETER'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to update eco-parameters.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Ecoparameter $ecoparameter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, Ecoparameter $ecoparameter = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.ECO_PARAMETER'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to delete eco-parameters.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Ecoparameter $ecoparameter
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, Ecoparameter $ecoparameter = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.ECO_PARAMETER'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to restore eco-parameters.');
    }
}
