<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PlanSync extends Model
{
    /**
     * Get the owning plansyncable model.
     */
    public function plansyncable()
    {
        return $this->morphTo();
    } 
}
