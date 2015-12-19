<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SubCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sub_categories';
    
    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
    	return $this->belongsTo('App\Models\Category');
    }
}
