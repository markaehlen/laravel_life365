<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
class NewsLetter extends Model {
	
	use Sortable; // Attach the Sortable trait to the model


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 protected $table = 'newsletters';
	 
    protected $fillable = ['name', 'email','is_mailchimp_subscribed','newsletter_group_id'];

    /**
     * Scope to filter data
     */
    public function scopeFilter($query, $keyword) {
        if (!empty($keyword)) {
            $query->where(function($query) use ($keyword) {
                $query->where('email', 'LIKE', '%' . $keyword . '%');
            });
        }
        return $query;
    }

}
