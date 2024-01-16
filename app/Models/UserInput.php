<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInput extends Model
{
    protected $table = 'user_inputs';

    protected $fillable = array('user_id', 'inputs');
}
