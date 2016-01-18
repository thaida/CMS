<?php

namespace App\Repositories;

use App\Models\Music;
use App\Models\SubCategory;


class MusicRepository extends BaseRepository {
	
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
	public function __construct(Music $film, SubCategory $subCat) {
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
		/* phim duoc su dung hay ko */
		$film->publish = isset ( $inputs ['publish'] );
		/* la phim noi bat hay ko */
		$film->isHot = isset ( $inputs ['isHot'] );
		/* phim mien phi hay ko */
		$film->isFree = isset ( $inputs ['isFree'] );
		/* poster image */
		if(!empty($inputs ['poster_path']))			
			$film->poster_path = str_replace("/filemanager", "", $inputs ['poster_path']);
		/* film path */
		if(!empty($inputs ['film_path']))
			$film->film_path = str_replace("/filemanager", "", $inputs ['film_path']);
		
		/* subtitle path */
		if(!empty($inputs ['subtitle_path']))
			$film->subtitle_path = str_replace("/filemanager", "", $inputs ['subtitle_path']);
		/* dao dien */
		if(isset($inputs ['director']))
			$film->director = $inputs ['director'];
		/* Dien vien */
		if(isset($inputs ['actor']))
			$film->actor = $inputs ['actor'];
		/* Ngay phat hanh */
		if(isset($inputs ['release_date']))
			$film->release_date = $inputs ['release_date'];
		
		/* thoi luong phim */
		if(isset($inputs ['running_time']))
			$film->running_time = $inputs ['running_time'];
		
		/* ngon ngu  */
		if(isset($inputs ['language']))
			$film->language = $inputs ['language'];
		
		
		/* so diem cham */
		if(isset($inputs ['star']))
			$film->star = $inputs ['star'];
		
		//$cat->cat_id = $inputs ['cat_id'];
		if ($user_id) {
			$film->user_id = $user_id;
		}
		$film->save ();
		
		//neu khong nhap phim lien quan, mac dinh phim nay lien quan den chinh no
		
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
		$query = $this->model->select ( 'musics.id', 'musics.created_at as created_at', 'musics.title as title', 'musics.summary', 
										'musics.slug', 'username', 'musics.publish', 'isHot' )
										->join ( 'users', 'users.id', '=', 'musics.user_id' )
							->orderBy ( $orderby, $direction );
			
		if ($user_id) {
			$query->where ( 'user_id', $user_id );
		}
		return $query->paginate ( $n );
	}
	
	/**
	 * Get film collection by subCat slug.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function getAllFilmBySubCat($n, $sub_cat_slug, $user_id = null) {
		$query = $this->model->select ( 'musics.id', 'musics.created_at as created_at', 'musics.title as title', 'musics.summary',
				'sub_categories.title as subCat', 'sub_categories.slug as catSlug', 'musics.release_date','musics.running_time',
				 'musics.poster_path', 'musics.slug', 'username', 'musics.publish', 'isHot' )
				->leftjoin ( 'sub_categories', function ($join) {
				$join->on('sub_categories.id' ,'=', 'musics.sub_cat_id');
				})
				->join ( 'users', 'users.id', '=', 'musics.user_id' )
				->where('sub_categories.slug', '=', $sub_cat_slug);
					
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
	public function edit($music) {
		$tags = [ ];
		
		return compact ( 'music', 'tags' );
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
	public function musicRelated($slug)
	{
		$post = $this->model->whereSlug($slug)->firstOrFail();
	
		//lay ra 15 phim co so luong xem nhieu nhat cung chuyen muc voi film nay, theo thu tu film moi nhat va khong mien phi
		$condition = array('sub_cat_id' => $post->sub_cat_id, 'isFree' => 0);
		$order = array('counter' => 'desc', 'created_at' => 'desc');
		$musics = $this->model->where($condition)
						->whereNotIn('id', array($post->id))
						->orderBy('counter', 'desc')
						->orderBy('created_at', 'desc')
						->take(15)->get();
			
		return compact('musics');
	}
	
	/**
	 * Get film free.
	 *
	 * @param  string  $slug
	 * @return array
	 */
	public function musicFree($slug)
	{
		$post = $this->model->whereSlug($slug)->firstOrFail();
	
		//lay ra 15 phim co so luong xem nhieu nhat cung chuyen muc voi film nay, theo thu tu film moi nhat va khong mien phi
		$condition = array('sub_cat_id' => $post->sub_cat_id, 'isFree' => 1);
		//$order = array('counter' => 'desc', 'created_at' => 'desc');
		$musics_free = $this->model->where($condition)
		->whereNotIn('id', array($post->id))
		->orderBy('counter', 'desc')
		->orderBy('created_at', 'desc')
		->take(15)->get();
			
		return compact('musics_free');
	}
	
	/**
	 * Get film most view.(tat ca cac phim - bo va le,
	 * lay ra theo so luong view sap xep theo thoi gian tu moi nhat dong thoi khong hien thi tu tap 2
	 *
	 * @return array
	 */
	public function allMusic($keyword = null)
	{
		$condition = array('publish' => '1');
		$musics_most_view = $this->model->where($condition)
		
		->orderBy('counter', 'desc')
		->orderBy('created_at', 'desc')
	;
		if(!empty($keyword))
			$musics_most_view = $musics_most_view->where('title', 'LIKE', '%'.$keyword.'%');
		//->get();
	
		return $musics_most_view->paginate(5);//compact('musics_most_view');
	}
	
	/**
	 * lay toan bo film la tap 1 va co title trung voi keyword.
	 *
	 * @return array of nation
	 */
	public function allFilmWithKeyword($keyword)
	{
		$condition = array('publish' => '1');
		$musics = $this->model->where($condition);
	
		if(isset($keyword))
			$musics = $musics->where('name', 'LIKE', '%'.$keyword.'%');
		
		return $musics->get();
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
		$musics_most_view = $this->model->where($condition)
							->where('num', '>', '1')
							->where('episode', '<', '2')
							->orderBy('counter', 'desc')
							->orderBy('created_at', 'desc')
							//->take(15)
		;
							//->get();
	
		return $musics_most_view->paginate(5);//compact('musics_most_view');
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
		$musics = $this->model->where($condition)
		->where('num', '<', '2')
		->orderBy('counter', 'desc')
		->orderBy('created_at', 'desc')
		//->take(15)
		;
		//->get();
	
		return  $musics->paginate(5);
	}
	
	/**
	 * Get film collection.
	 *
	 * @param  string  $slug
	 * @return array
	 */
	public function show($slug)
	{
				
		$post = $this->model->with('user')->select('musics.id', 'musics.created_at as created_at', 'musics.title as title', 'musics.summary',
				'sub_categories.title as subCat', 'sub_categories.slug as catSlug', 'musics.release_date','musics.running_time', 'musics.counter',
				 'musics.poster_path', 'musics.slug', 'musics.publish', 'isHot', 'language', 'release_date' )
				->leftjoin('sub_categories', 'sub_categories.id' ,'=', 'musics.sub_cat_id')
				->where('musics.slug', $slug)
				->firstOrFail();
	
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
				
	
		return $post;
	}
	
	/**
	 * Lay ra danh sach phim trong tung chuyen muc theo slug
	 *
	 * @param string $slug
	 * @return array
	 */
	public function musicBySubCatSlug($slug)
	{
		$condition = array('publish' => '1');
		$musics = $this->model->select( 'musics.id', 'musics.created_at as created_at', 'musics.title as title', 'musics.summary',
				'sub_categories.title as subCat', 'sub_categories.slug as catSlug', 'musics.release_date','musics.running_time',
				 'musics.poster_path', 'musics.slug',  'musics.publish', 'isHot' )
		->leftjoin('sub_categories', 'sub_categories.id', '=', 'musics.sub_cat_id')
		->where('sub_categories.slug', $slug)
		->orderBy('musics.created_at', 'desc')
		->orderBy('counter', 'desc')
		->take(16);
		//->get();
	  
		/* $links = $musics -> append([]);
		
		$links->setPath ( '' )->render (); */
		return $musics->paginate(5);
	}
	
	/**
	 * Hien thi danh sach phim cung bo voi phim nay
	 *
	 * @param $id id cua tap 1 cua bo phim nay
	 * @return 
	 */
	public function getFilmInSeries($id) {
		$cond = array('publish' => '1', 'first_episode_id' => $id);
		$musics = $this->model->where($cond)
				->orderBy('episode', 'asc');
		
		return $musics;
	}
	
}
