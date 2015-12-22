<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;

class Film extends Model
{
	
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'films';
    
    /**
     * One to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }
    
    
    
}
