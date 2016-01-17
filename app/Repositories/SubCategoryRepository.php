<?php

namespace App\Repositories;

use App\Models\SubCategory;

class SubCategoryRepository extends BaseRepository {
	
	/**
	 * Create a new CategoryRepository instance.
	 *
	 * @param App\Models\Category $category        	
	 * @return void
	 */
	public function __construct(SubCategory $subcategory) {
		$this->model = $subcategory;
	}
	
	/**
	 * Create or update a post.
	 *
	 * @param App\Models\Cat $cat        	
	 * @param array $inputs        	
	 * @param bool $user_id        	
	 * @return App\Models\Category
	 */
	private function savePost($cat, $inputs, $user_id = null) {
		$cat->title = $inputs ['title'];
		$cat->summary = $inputs ['summary'];
		$cat->slug = $inputs ['slug'];
		$cat->active = isset ( $inputs ['active'] );
		$cat->cat_id = $inputs ['cat_id'];
		if ($user_id) {
			$cat->user_id = $user_id;
		}
		$cat->save ();
		
		return $cat;
	}
	
	/**
	 * Update "active" in post.
	 *
	 * @param array $inputs        	
	 * @param int $id        	
	 * @return void
	 */
	public function updateActive($inputs, $id) {
		$cat = $this->getById ( $id );
		
		$cat->active = $inputs ['active'] == 'true';
		
		$cat->save ();
	}
	
	/**
	 * Get Sub Categorys collection.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function index($n, $user_id = null, $orderby = 'created_at', $direction = 'desc') {
		$query = $this->model->select ( config ( "constants.SUB_CATEGORY_TABLE" ) . '.id', config ( "constants.SUB_CATEGORY_TABLE" ) . '.created_at as created_at', config ( "constants.SUB_CATEGORY_TABLE" ) . '.title as title', config ( "constants.SUB_CATEGORY_TABLE" ) . '.summary', 
										config ( "constants.SUB_CATEGORY_TABLE" ) . '.slug', 'username', config ( "constants.SUB_CATEGORY_TABLE" ) . '.active', config ( "constants.CATEGORY_TABLE" ) . '.title as catname' )
										->join ( 'users', 'users.id', '=', config ( "constants.SUB_CATEGORY_TABLE" ) . '.user_id' )
										->join ( config ( "constants.CATEGORY_TABLE" ), config ( "constants.CATEGORY_TABLE" ) . '.id', '=', config ( "constants.SUB_CATEGORY_TABLE" ) . '.cat_id' )
							->orderBy ( $orderby, $direction );
		
	
		if ($user_id) {
			$query->where ( 'user_id', $user_id );
		}
		return $query->paginate ( $n );
	}
	
	/**
	 * Get cat collection.
	 *
	 * @param string $slug        	
	 * @return array
	 */
	// public function show($slug = null)
	// {
	// $post = $this->model->with('user', 'tags')->whereSlug($slug)->firstOrFail();
	
	/*
	 * $comments = $this->comment
	 * ->wherePost_id($post->id)
	 * ->with('user')
	 * ->whereHas('user', function($q) {
	 * $q->whereValid(true);
	 * })
	 * ->get();
	 */
	
	// return compact('post', 'comments');
	// }
	
	/**
	 * Update a cat.
	 *
	 * @param array $inputs        	
	 * @param App\Models\Post $post        	
	 * @return void
	 */
	public function update($inputs, $post) {
		$post = $this->savePost ( $post, $inputs );
	}
	/**
	 * Destroy a post.
	 *
	 * @param App\Models\Post $post        	
	 * @return void
	 */
	public function destroy($post) {
		$post->delete ();
	}
	/**
	 * Create a post.
	 *
	 * @param array $inputs        	
	 * @param int $user_id        	
	 * @return void
	 */
	public function store($inputs, $user_id) {
		$post = $this->savePost ( new $this->model (), $inputs, $user_id );
		
		// Maybe purge orphan tags...
	}
	
	/**
	 * Get post collection.
	 *
	 * @param App\Models\Post $post        	
	 * @return array
	 */
	public function edit($post) {
		$tags = [ ];
		
		return compact ( 'post', 'tags' );
	}
	
	/**
	 * Get post collection.
	 *
	 * @param int $id        	
	 * @return array
	 */
	public function GetById($id) {
		return $this->model->findOrFail ( $id );
	}
	
	/**
	 * Get all category.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function all()
	{
		return $this->model->all();
	}
	
	/**
	 * Get sub category collection.
	 *
	 * @param  App\Models\SubCategory
	 * @return Array
	 */
	public function getAllSelect()
	{
		$select = $this->all()->lists('title', 'id');
	
		return compact('select');
	}
	
	/**
	 * Get sub category collection.
	 *
	 * @param  App\Models\SubCategory
	 * @return Array
	 */
	public function getAllByFilmSelect()
	{
		$select = $this->model->select('id', 'created_at', 'updated_at', 'title', 'slug', 'user_id', 'summary')
						->where('cat_id', config('constants.FILM_CAT_ID'))
		->lists('title', 'id');
	
		return compact('select');
	}
	
	public function getAllByMusicSelect()
	{
		$select = $this->model->select('id', 'created_at', 'updated_at', 'title', 'slug', 'user_id', 'summary')
						->where('cat_id', config('constants.MUSIC_CAT_ID'))
		->lists('title', 'id');
	
		return compact('select');
	}
}
