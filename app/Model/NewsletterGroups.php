<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class NewsletterGroups extends Model
{

    use Sortable; // Attach the Sortable trait to the model


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['title', "list_id"];
}
