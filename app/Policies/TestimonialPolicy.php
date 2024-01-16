<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Testimonial;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestimonialPolicy
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
            config('authorization.MODULES.PAGE'),
            [
                config('authorization.PERMISSIONS.TESTIMONIAL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access testimonial.');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Testimonial $testimonial
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $user, Testimonial $testimonial = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.TESTIMONIAL'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
                config('authorization.PERMISSIONS.READ_ONLY')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to access testimonial.');
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
            config('authorization.MODULES.TESTIMONIAL'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER')
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to create testimonial.');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Testimonial $testimonial
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, Testimonial $testimonial = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.TESTIMONIAL'),
            [
                config('authorization.PERMISSIONS.FULL'),
                config('authorization.PERMISSIONS.WRITER'),
                config('authorization.PERMISSIONS.EDITOR'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to update testimonial.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Testimonial $testimonial
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, Testimonial $testimonial = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.TESTIMONIAL'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to delete testimonial.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Admin  $user
     * @param \App\Models\Testimonial $testimonial
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, Testimonial $testimonial = null)
    {
        return $user->hasModulePermission(
            config('authorization.MODULES.TESTIMONIAL'),
            [
                config('authorization.PERMISSIONS.FULL'),
            ]
        ) ? Response::allow() : Response::deny('You do not have permission to restore testimonial.');
    }
}
