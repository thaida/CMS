<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;
use Log;
use Spatie\SearchIndex\Searchable;

class Film extends Model implements Searchable {
	
	/**
	 * Returns an array with properties which must be indexed
	 *
	 * @return array
	 */
	public function getSearchableBody()
	{
		$searchableProperties = [
				'title' => $this->title,
				'summary' => $this->summary,
				'actor' => $this->actor,
				'publish' => $this->publish,
				'slug' => $this->slug,
				'director' => $this->director,
				'poster' => $this->poster_path
		];
	
		return $searchableProperties;
	
	}
	
	/**
	 * Return the type of the searchable subject
	 *
	 * @return string
	 */
	public function getSearchableType()
	{
		return 'film';
	}
	
	/**
	 * Return the id of the searchable subject
	 *
	 * @return string
	 */
	public function getSearchableId()
	{
		return $this->id;
	}
	
	
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
	public function user() {
		return $this->belongsTo ( 'App\Models\User' );
	}
	
	/**
	 * tu dong set gia tri releasedate vao table trong DB voi dinh dang yy-mm-dd tu dinh dang dd/mm/yy
	 *
	 * @return 
	 */
	public function setReleaseDateAttribute($birthdate) {
		// thay the ky tu / de co the convert duoc strtotime
		$birthdate = str_replace ( '/', '-', $birthdate );
		if ($birthdate) {
			$this->attributes ['release_date'] = date ( 'Y/m/d', (strtotime ( $birthdate )) );
		} else {
			$this->attributes ['release_date'] = '';
		}
	}
	
	/**
	 * Tu dong lay ra gia tri tu trong db voi dinh dang yy-mm-dd de hien thi len voi dinh dang dd/mm/yy
	 *
	 * @return date format dd/mm/yy
	 */
	public function getReleaseDateAttribute() {
		$tmpdate = $this->attributes ['release_date'];
		if ($tmpdate == "0000-00-00" || $tmpdate == "") {
			return "";
		} else {
			return date ( 'd/m/Y', strtotime ( $tmpdate ) );
		}
	}
}
