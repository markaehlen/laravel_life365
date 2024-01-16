<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\EmailTemplate;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailTemplatePolicy
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
            config('authorization.MODULES.EMAIL_TEMPLATE'),
            [
                config('authorization.PERMISSIONS.FAQ'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access email templates.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\EmailTemplate $emailTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $user, EmailTemplate $emailTemplate = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.EMAIL_TEMPLATE'),
            [
                config('authorization.PERMISSIONS.FAQ'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access email templates.');
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
            config('authorization.MODULES.EMAIL_TEMPLATE'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to create email templates.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\EmailTemplate $emailTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, EmailTemplate $emailTemplate = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.EMAIL_TEMPLATE'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to update email templates.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\EmailTemplate $emailTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, EmailTemplate $emailTemplate = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.EMAIL_TEMPLATE'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to delete email templates.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $user
     * @param  \App\Models\EmailTemplate $emailTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, EmailTemplate $emailTemplate = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.EMAIL_TEMPLATE'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to restore email templates.');
    }
}
