<?php

namespace App\Repositories;

use App\Models\Film;
use App\Models\SubCategory;


class FilmRepository extends BaseRepository {
	
	/**
	 * The Tag instance.
	 *
	 * @var App\Models\SubCategory
	 */
	protected $subCat;
	
	/**
	 * Create a new FilmRepository instance.
	 *
	 * @param App\Models\Film $film        	
	 * @return void
	 */
	public function __construct(Film $film, SubCategory $subCat) {
		$this->model = $film;
		$this->subCat = $subCat;
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
		$film->sub_cat_id = $inputs ['sub_cat_id'];
		$film->publish = isset ( $inputs ['publish'] );
		$film->isHot = isset ( $inputs ['isHot'] );
		if(isset($inputs ['btnImage']))			
			$film->poster_path = str_replace("/filemanager", "", $inputs ['btnImage']);
		
		
		//$cat->cat_id = $inputs ['cat_id'];
		if ($user_id) {
			$film->user_id = $user_id;
		}
		$film->save ();
		
		return $film;
	}
	
	/**
	 * Update "Publish" in film
	 *
	 * @param array $inputs        	
	 * @param int $id        	
	 * @return void
	 */
	public function updatePublish($inputs, $id) {
		$film = $this->getById ( $id );
		
		$film->publish = $inputs ['publish'] == 'true';
		
		$film->save ();
	}
	
	/**
	 * Update "Publish" in film
	 *
	 * @param array $inputs
	 * @param int $id
	 * @return void
	 */
	public function updateFront($inputs, $id) {
		$film = $this->getById ( $id );
	
		$film->isHot = $inputs ['ishot'] == 'true';
	
		$film->save ();
	}
	
	/**
	 * Get Sub Categorys collection.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function index($n, $user_id = null, $orderby = 'created_at', $direction = 'desc') {
		$query = $this->model->select ( config ( "constants.FILM_TABLE" ) . '.id', config ( "constants.FILM_TABLE" ) . '.created_at as created_at', config ( "constants.FILM_TABLE" ) . '.title as title', config ( "constants.FILM_TABLE" ) . '.summary', 
										config ( "constants.FILM_TABLE" ) . '.slug', 'username', config ( "constants.FILM_TABLE" ) . '.publish', 'isHot' )
										->join ( 'users', 'users.id', '=', config ( "constants.FILM_TABLE" ) . '.user_id' )
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
		$post = $this->savePost ( new $this->model (), $inputs, $user_id);
		
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
	 * Get film related.
	 *
	 * @param  string  $slug
	 * @return array
	 */
	public function filmRelated($slug)
	{
		$post = $this->model->whereSlug($slug)->firstOrFail();
	
		//lay ra 15 phim co so luong xem nhieu nhat cung chuyen muc voi film nay, theo thu tu film moi nhat va khong mien phi
		$condition = array('sub_cat_id' => $post->sub_cat_id, 'isFree' => 0);
		$order = array('counter' => 'desc', 'created_at' => 'desc');
		$films = $this->model->where($condition)
						->whereNotIn('id', array($post->id))
						->orderBy('counter', 'desc')
						->orderBy('created_at', 'desc')
						->take(15)->get();
			
		return compact('films');
	}
	
	/**
	 * Get film free.
	 *
	 * @param  string  $slug
	 * @return array
	 */
	public function filmFree($slug)
	{
		$post = $this->model->whereSlug($slug)->firstOrFail();
	
		//lay ra 15 phim co so luong xem nhieu nhat cung chuyen muc voi film nay, theo thu tu film moi nhat va khong mien phi
		$condition = array('sub_cat_id' => $post->sub_cat_id, 'isFree' => 1);
		//$order = array('counter' => 'desc', 'created_at' => 'desc');
		$films_free = $this->model->where($condition)
		->whereNotIn('id', array($post->id))
		->orderBy('counter', 'desc')
		->orderBy('created_at', 'desc')
		->take(15)->get();
			
		return compact('films_free');
	}
	
	/**
	 * Get series film most view.(film bo la nhung film co so tap > 1, 
	 * lay ra theo so luong view sap xep theo thoi gian tu moi nhat dong thoi khong hien thi tu tap 2
	 * (co van de neu film duoc xem nhieu la nhung tap khac khong phai tap 1)
	 *
	 * @return array
	 */
	public function series()
	{
		$condition = array('publish' => '1');
		$films = $this->model->where($condition)
							->where('num', '>', '1')
							->where('episode', '<', '2')
							->orderBy('counter', 'desc')
							->orderBy('created_at', 'desc')
							->take(15)
							->get();
	
		return compact('films');
	}
	
	/**
	 * Get single film most view.(film le la nhung film co so tap <= 1,
	 * lay ra theo so luong view sap xep theo thoi gian tu moi nhat 
	 *
	 * @return array
	 */
	public function single()
	{
		$condition = array('publish' => '1');
		$films = $this->model->where($condition)
		->where('num', '<', '2')
		->orderBy('counter', 'desc')
		->orderBy('created_at', 'desc')
		->take(15)
		->get();
	
		return compact('films');
	}
	
	/**
	 * Get film collection.
	 *
	 * @param  string  $slug
	 * @return array
	 */
	public function show($slug)
	{
		$post = $this->model->with('user')->whereSlug($slug)->firstOrFail();
	
		/*$comments = $this->comment
		->wherePost_id($post->id)
		->with('user')
		->whereHas('user', function($q) {
			$q->whereValid(true);
		})
		->get();*/
		//do khi xem phim thi film tu dong chay nen moi lan goi ham nay thi update bien dem luon
		if(!empty($post))
		{
			$post->counter = $post->counter + 1;
			$post->save();
		}
		
		$comments = [];
	
		return compact('post', 'comments');
	}
	
	/**
	 * Lay ra danh sach phim trong tung chuyen muc theo slug
	 *
	 * @param string $slug
	 * @return array
	 */
	public function filmBySubCatSlug($slug)
	{
		$subCat = $this->subCat->whereSlug($slug)->firstOrFail();
		
		$condition = array('publish' => '1', 'sub_cat_id' => $subCat->id);
		$films = $this->model->where($condition)
		->orderBy('created_at', 'desc')
		->take(15)
		->get();
	
		return array_merge(compact('films'), compact('subCat'));
	}
	
	
}
