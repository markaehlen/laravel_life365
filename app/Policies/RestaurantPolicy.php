<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\UserManager\Entities\User;
use Illuminate\Auth\Access\Response;

class RestaurantPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    //only customer allowed to book restuarant
    public function bookOrder(User $user)
    {
        return ($user->isCustomer()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function listingAbility(User $user)
    {
        return ($user->plan->isListingPlan()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function menuAbility(User $user)
    {
        return ($user->plan->isMenuPlan()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function logoAbility(User $user)
    {
        return ($user->plan->isLogoPlan()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function hotDealAbility(User $user)
    {
        return ($user->plan->isHotdealPlan()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function rodAbility(User $user)
    {
        return ($user->plan->isRodPlan()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function bookAbility(User $user)
    {
        return ($user->plan->isBookTablePlan()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function linkAbility(User $user)
    {
        return ($user->plan->isWebsiteLinkPlan()) ? Response::allow() : Response::deny('Permission Denied');
    }

    public function photoAbility(User $user)
    {
        return ($user->plan->isPhotoPlan()) ? Response::allow() : Response::deny('Permission Denied');
    }
}
