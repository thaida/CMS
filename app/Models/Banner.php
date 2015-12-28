<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banners';
    
    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCats()
    {
    	return $this->belongsTo('App\Models\SubCategory');
    }     
    
}
