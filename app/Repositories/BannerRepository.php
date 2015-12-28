<?php

namespace App\Repositories;

use App\Models\Banner;


class BannerRepository extends BaseRepository {
	
	/**
	 * Create a new FilmRepository instance.
	 *
	 * @param App\Models\Banner $banner        	
	 * @return void
	 */
	public function __construct(Banner $banner) {
		$this->model = $banner;
	}
	
	/**
	 * Create or update a post.
	 *
	 * @param App\Models\Film $film        	
	 * @param array $inputs        	
	 * @param bool $user_id        	
	 * @return App\Models\Film
	 */
	private function savePost($banner, $inputs, $user_id = null) {
		$banner->title = $inputs ['title'];
		$banner->summary = $inputs ['summary'];
		
		$banner->sub_cat_id = $inputs ['sub_cat_id'];
		$banner->publish = isset ( $inputs ['publish'] );
		
		if(!empty($inputs ['link']))
			$banner->link = $inputs ['link'];
		
		if(!empty($inputs ['btnImage']))			
			$banner->poster_path = $inputs ['btnImage'];
		
		
		//$cat->cat_id = $inputs ['cat_id'];
		
		$banner->save ();
		
		return $banner;
	}
	
	/**
	 * Update "Publish" in film
	 *
	 * @param array $inputs        	
	 * @param int $id        	
	 * @return void
	 */
	public function updatePublish($inputs, $id) {
		$banner = $this->getById ( $id );
		
		$banner->publish = $inputs ['publish'] == 'true';
		
		$banner->save ();
	}
	
	/**
	 * Get Sub Categorys collection.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function index($n, $user_id = null, $orderby = 'created_at', $direction = 'desc') {
		$query = $this->model->select ( config ( "constants.BANNER_TABLE" ) . '.id', config ( "constants.BANNER_TABLE" ) . '.created_at as created_at', config ( "constants.BANNER_TABLE" ) . '.title as title', config ( "constants.BANNER_TABLE" ) . '.summary', 
										 config ( "constants.BANNER_TABLE" ) . '.publish' )
										
							->orderBy ( $orderby, $direction );
			
		if ($user_id) {
			$query->where ( 'user_id', $user_id );
		}
		return $query->paginate ( $n );
	}
	
		
	/**
	 * Update a cat.
	 *
	 * @param array $inputs        	
	 * @param App\Models\Post $post        	
	 * @return void
	 */
	public function update($inputs, $post) {
		$post = $this->savePost ( $post, $inputs, null);
	}
	/**
	 * Destroy a post.
	 *
	 * @param App\Models\Post $post        	
	 * @return void
	 */
	public function destroy($banner) {
		$banner->delete ();
	}
	/**
	 * Create a post.
	 *
	 * @param array $inputs        	
	 * @param int $user_id        	
	 * @return void
	 */
	public function store($inputs, $user_id) {
		$banner = $this->savePost ( new $this->model (), $inputs, $user_id);
		
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
	 * Get post collection.
	 *
	 * @param int $id
	 * @return array
	 */
	public function GetAllPublishByCat($id) {
		$vartemp = $this->model->select (config ( "constants.BANNER_TABLE" ) . '.id', config ( "constants.BANNER_TABLE" ) . '.created_at as created_at', config ( "constants.BANNER_TABLE" ) . '.title as title', config ( "constants.BANNER_TABLE" ) . '.summary', 
										 config ( "constants.BANNER_TABLE" ) . '.publish' )
										->where("sub_cat_id", $id);
		return  $vartemp;		
	}
	
}
