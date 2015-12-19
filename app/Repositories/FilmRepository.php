<?php

namespace App\Repositories;

use App\Models\Film;

class FilmRepository extends BaseRepository {
	
	/**
	 * Create a new FilmRepository instance.
	 *
	 * @param App\Models\Film $film        	
	 * @return void
	 */
	public function __construct(Film $film) {
		$this->model = $film;
	}
	
	/**
	 * Create or update a post.
	 *
	 * @param App\Models\Film $film        	
	 * @param array $inputs        	
	 * @param bool $user_id        	
	 * @return App\Models\Film
	 */
	private function savePost($film, $inputs, $user_id = null) {
		$film->title = $inputs ['title'];
		$film->summary = $inputs ['summary'];
		$film->slug = $inputs ['slug'];
		$film->active = isset ( $inputs ['active'] );
		//$cat->cat_id = $inputs ['cat_id'];
		if ($user_id) {
			$film->user_id = $user_id;
		}
		$film->save ();
		
		return $film;
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
		$query = $this->model->select ( config ( "constants.FILM_TABLE" ) . '.id', config ( "constants.FILM_TABLE" ) . '.created_at as created_at', config ( "constants.FILM_TABLE" ) . '.title as title', config ( "constants.FILM_TABLE" ) . '.summary', 
										config ( "constants.FILM_TABLE" ) . '.slug', 'username', config ( "constants.FILM_TABLE" ) . '.publish' )
										->join ( 'users', 'users.id', '=', config ( "constants.FILM_TABLE" ) . '.user_id' )
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
}
