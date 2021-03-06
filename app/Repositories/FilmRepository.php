<?php

namespace App\Repositories;

use App\Models\Film;
use App\Models\SubCategory;

use Spatie\SearchIndex\SearchIndexFacade;
use Spatie\SearchIndex\Searchable;
use Spatie\SearchIndex\SearchIndexHandler;
use Spatie\SearchIndex\SearchIndexServiceProvider;


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
		/* so luong tap phim */
		if(isset($inputs ['num']))
			$film->num =  $inputs ['num'];
		/* tap thu may */
		if(isset($inputs ['episode']))
			$film->episode = $inputs ['episode'];
		/* ngon ngu  */
		if(isset($inputs ['language']))
			$film->language = $inputs ['language'];
		
		/* phim lien quan  */
		if(isset($inputs ['first_episode_id']))
			$film->first_episode_id = $inputs ['first_episode_id'];
		
		/* so diem cham */
		if(isset($inputs ['star']))
			$film->star = $inputs ['star'];
		
		//$cat->cat_id = $inputs ['cat_id'];
		if ($user_id) {
			$film->user_id = $user_id;
		}
		$film->save ();
		
		//neu khong nhap phim lien quan, mac dinh phim nay lien quan den chinh no
		$film->first_episode_id = $film->id;
		$film->update ();
		SearchIndexFacade::upsertToIndex($film);
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
		SearchIndexFacade::upsertToIndex($film);
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
		$query = $this->model->select ( 'films.id', 'films.created_at as created_at', 'films.title as title', 'films.summary', 
										'films.slug', 'username', 'films.publish', 'isHot' )
										->join ( 'users', 'users.id', '=', 'films.user_id' )
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
		$query = $this->model->select ( 'films.id', 'films.created_at as created_at', 'films.title as title', 'films.summary',
				'sub_categories.title as subCat', 'sub_categories.slug as catSlug', 'films.release_date','films.running_time',
				 'films.poster_path', 'films.slug', 'username', 'films.publish', 'isHot' )
				->leftjoin ( 'sub_categories', function ($join) {
				$join->on('sub_categories.id' ,'=', 'films.sub_cat_id');
				})
				->join ( 'users', 'users.id', '=', 'films.user_id' )
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
		SearchIndexFacade::removeFromIndex($post);
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
	 * Get film most view.(tat ca cac phim - bo va le,
	 * lay ra theo so luong view sap xep theo thoi gian tu moi nhat dong thoi khong hien thi tu tap 2
	 *
	 * @return array
	 */
	public function allFilm($keyword = null)
	{
		$condition = array('publish' => '1');
		$films_most_view = $this->model->where($condition)
		->where('episode', '<', '2')
		->orderBy('counter', 'desc')
		->orderBy('created_at', 'desc')
	;
		if(!empty($keyword))
			$films_most_view = $films_most_view->where('title', 'LIKE', '%'.$keyword.'%');
		//->get();
	
		return $films_most_view->paginate(5);//compact('films_most_view');
	}
	
	/**
	 * lay toan bo film la tap 1 va co title trung voi keyword.
	 *
	 * @return array of nation
	 */
	public function allFilmWithKeyword($keyword)
	{
		$condition = array('publish' => '1');
		$films = $this->model->where($condition);
	
		if(isset($keyword))
			$films = $films->where('name', 'LIKE', '%'.$keyword.'%');
		
		return $films->get();
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
		$films_most_view = $this->model->where($condition)
							->where('num', '>', '1')
							->where('episode', '<', '2')
							->orderBy('counter', 'desc')
							->orderBy('created_at', 'desc')
							//->take(15)
		;
							//->get();
	
		return $films_most_view->paginate(5);//compact('films_most_view');
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
		//->take(15)
		;
		//->get();
	
		return  $films->paginate(5);
	}
	
	/**
	 * Get film collection.
	 *
	 * @param  string  $slug
	 * @return array
	 */
	public function show($slug)
	{
				
		$post = $this->model->with('user')->select('films.id', 'films.created_at as created_at', 'films.title as title', 'films.summary',
				'sub_categories.title as subCat', 'sub_categories.slug as catSlug', 'films.release_date','films.running_time', 'films.counter',
				 'films.poster_path', 'films.slug', 'films.publish', 'isHot', 'num','episode', 'language', 'release_date', 'films.first_episode_id' )
				->leftjoin('sub_categories', 'sub_categories.id' ,'=', 'films.sub_cat_id')
				->where('films.slug', $slug)
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
	public function filmBySubCatSlug($slug)
	{
		$condition = array('publish' => '1');
		$films = $this->model->select( 'films.id', 'films.created_at as created_at', 'films.title as title', 'films.summary',
				'sub_categories.title as subCat', 'sub_categories.slug as catSlug', 'films.release_date','films.running_time',
				 'films.poster_path', 'films.slug',  'films.publish', 'isHot' )
		->leftjoin('sub_categories', 'sub_categories.id', '=', 'films.sub_cat_id')
		->where('sub_categories.slug', $slug)
		->orderBy('films.created_at', 'desc')
		->orderBy('counter', 'desc')
		->take(16);
		//->get();
	  
		/* $links = $films -> append([]);
		
		$links->setPath ( '' )->render (); */
		return $films->paginate(5);
	}
	
	/**
	 * Hien thi danh sach phim cung bo voi phim nay
	 *
	 * @param $id id cua tap 1 cua bo phim nay
	 * @return 
	 */
	public function getFilmInSeries($id) {
		$cond = array('publish' => '1', 'first_episode_id' => $id);
		$films = $this->model->where($cond)
				->orderBy('episode', 'asc');
		
		return $films;
	}
	
	/**
	 * Tim kiem danh sach phim phu hop voi tu khoa
	 * Su dung ElasticSearch Engine
	 * @param $keyword tu khoa muon tim kiem
	 * @return 
	 */
	public function search($keyword) {
	
		$client = SearchIndexFacade::getClient();
		
		//tim kiem tat ca cac cot trong index voi tu khoa trong query_string 
		//và điều kiện lọc bổ sung là name = "trò"
		/* "query": {
			"filtered": {
				"query": {
					"query_string": {
						"query": "vời"
					}
				},
				"filter": {
					"term": { "name": "trò" }
				}
			}
		} */
		$params = [
		    //"search_type" => "scan",    // use search_type=scan
		    //"scroll" => "30s",          // how long between scroll requests. should be small!
		    "size" => 50,               // how many results *per shard* you want back
		    "index" => "main",
			"type"=> "film",
		    "body" => [
		        "query" => [
		        	 'filtered' => [
		                'filter' => [		                		
	                		'term' => [ 'publish' => 'true' ]
		                ], 
		                'query' => [
		                    'match' => [ 'title' => $keyword]
		                ]
            		]
		        ]
		    ]
		];
		
		$results = $client->search($params);   // Execute the search
		
		//var_dump($doc["name"]);
		//echo $milliseconds. " " . $score . " " ;
		return $results;
	}
	
}
