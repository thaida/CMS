<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\MusicRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\MusicRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubCategoryRepository;
use Input;
use Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MusicController extends Controller {
	
	/**
	 * The BlogRepository instance.
	 *
	 * @var App\Repositories\SubCategoryRepository
	 */
	protected $sub_cat_gestion;
	
	/**
	 * The CategoryRepository instance.
	 *
	 * @var App\Repositories\MusicRepository
	 */
	protected $music_gestion;
	
	/**
	 * The pagination number.
	 *
	 * @var int
	 */
	protected $nbrPages;
	
	/**
	 * Create a new CategoryRepository instance.
	 *
	 * @param App\Repositories\CategoryRepository $blog_gestion        	
	 * @return void
	 */
	public function __construct(MusicRepository $music_gestion, SubCategoryRepository $sub_cat_gestion) {
		$this->sub_cat_gestion = $sub_cat_gestion;
		$this->music_gestion = $music_gestion;
		
		$this->nbrPages = 2;
		
		
		 $this->middleware('redac', ['except' => ['indexFront', 'show', 'tag', 'search', 'series', 'allmusic', 'musicbycat', 'single']]);
		 $this->middleware('admin', ['only' => ['updateSeen', 'updateActive']]);
		 $this->middleware('ajax', ['only' => ['updateSeen', 'updateActive']]);
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Redirection
	 */
	public function index() {
		return redirect ( route ( 'music.order', [ 
				'name' => 'created_at',
				'sort' => 'asc' 
		] ) );
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @param Illuminate\Http\Request $request        	
	 * @return Response
	 */
	public function indexOrder(Request $request) {
		
		// $statut = $this->user_gestion->getStatut();
		$musics = $this->music_gestion->index ( config("constants.LIMIT"), null, $request->name, $request->sort );
		
		$links = $musics->appends ( [ 
				'name' => $request->name,
				'sort' => $request->sort 
		] );
		
		if ($request->ajax ()) {
			return response ()->json ( [ 
					'view' => view ( 'back.music.table', compact ( 'musics' ) )->render (),
					'links' => $links->setPath ( 'order' )->render () 
			] );
		}
		
		$links->setPath ( '' )->render ();
		
		$order = ( object ) [ 
				'name' => $request->name,
				'sort' => 'sort-' . $request->sort 
		];
		
		return view ( 'back.music.index', compact ( 'musics', 'links', 'order' ) );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function edit($id) {
		$music = $this->music_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$url = config ( 'medias.url' );
		$img_host_url = config ( 'medias.image-host' );
		return view ( 'back.music.edit', array_merge ( $this->music_gestion->edit ( $music ), compact ( 'url' ), compact('img_host_url') ), $select = $this->sub_cat_gestion->getAllByMusicSelect () );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param App\Http\Requests\CategoryRequest $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function update(MusicRequest $request, $id) {
		$music = $this->music_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->music_gestion->update ( $request->all (), $music );
		
		return redirect ( 'music' )->with ( 'ok', trans ( 'back/music.updated' ) );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function destroy($id) {
		$music = $this->music_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->music_gestion->destroy ( $music );
		
		return redirect ( 'music' )->with ( 'ok', trans ( 'back/music.destroyed' ) );
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		$url = config ( 'medias.url' );
		return view ( 'back.music.create', array_merge ( compact ( 'url' ), $this->sub_cat_gestion->getAllByMusicSelect () ) );
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param App\Http\Requests\PostRequest $request        	
	 * @return Response
	 */
	public function store(MusicRequest $request) {

		$this->music_gestion->store ( $request->all (), $request->user ()->id );
		
		return redirect ( 'music' )->with ( 'ok', trans ( 'back/music.stored' ) );
	}
	
	/**
	 * Update "active" for the specified resource in storage.
	 *
	 * @param Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function updatePublish(Request $request, $id) {
		$this->music_gestion->updatePublish ( $request->all (), $id );
		
		return response ()->json ();
	}
	
	/**
	 * Update "active" for the specified resource in storage.
	 *
	 * @param Illuminate\Http\Request $request
	 * @param int $id
	 * @return Response
	 */
	public function updateFront(Request $request, $id) {
		$this->music_gestion->updateFront ( $request->all (), $id );
	
		return response ()->json ();
	}
	
	
	/**
	 * Display the specified resource.
	 *
	 * @param Illuminate\Contracts\Auth\Guard $auth        	
	 * @param string $slug        	
	 * @return Response
	 */
	public function show(Guard $auth, $slug) {
		$user = $auth->user ();
		$img_url = config('medias.image-host');
		$music_url = config('medias.music-host');
		
		$post = $this->music_gestion->show ( $slug );
	
		$musics_link = [];//$this->music_gestion->getMusicInSeries($post->first_episode_id)->get();

		//tim nhung Music lien quan toi Music nay de dua vao muc de xuat
		//$Musics = $this->Music_gestion->MusicRelated($slug);
		//$Musics_free = $this->Music_gestion->MusicFree($slug);
		
		return view ( 'front.music.show', array_merge ( $this->music_gestion->MusicRelated($slug), $this->music_gestion->MusicFree($slug), 
						compact ( 'user' ,'img_url', 'Music_url', 'Musics_link', 'post') ) );
	}
	
		
	/**
	 * Hien thi danh sach phim bo
	 * Phim bo la nhung phim co so tap > 1 (num > 1)
	 * @param Illuminate\Contracts\Auth\Guard $auth
	 * @param string $slug
	 * @return Response
	 */
	public function series() {
		$img_url = config('medias.image-host');
		$Music_url = config('medias.music-host');
		$Musics_most_view = $this->music_gestion->series();
		
		$Musics = $this->music_gestion->MusicBySubCatSlug('tam-ly');
		$links = $musics->appends ( [] );
		
		$links->setPath ( '' )->render ();
		//return "hello";		
		return view ( 'front.music.list', compact('Musics_most_view','img_url','Music_url', 'Musics', 'links'));
		//return view ( 'front.Music.list', array_merge ( $this->Music_gestion->series(),compact($this->Music_gestion->MusicBySubCatSlug(4, 'tam-ly', null)),  compact('img_url','Music_url') ) );
	}
	/**
	 * Hien thi danh sach phim (ca phim le va phim bo
	 * Phim bo la nhung phim co so tap > 1 (num > 1)
	 * @param Illuminate\Contracts\Auth\Guard $auth
	 * @param string $slug
	 * @return Response
	 */
	public function allMusic() {
		$img_url = config('medias.image-host');
		$music_url = config('medias.music-host');
		$musics_most_view = $this->music_gestion->allMusic();
		$musics = $this->music_gestion->MusicBySubCatSlug('tinh-cam');
		$links = $musics->appends ( [] );
	
		$links->setPath ( '' )->render ();
		//return "hello";
		return view ( 'front.music.list', compact('musics_most_view','img_url','music_url', 'musics', 'links'));
		//return view ( 'front.Music.list', array_merge ( $this->Music_gestion->series(),compact($this->Music_gestion->MusicBySubCatSlug(4, 'tam-ly', null)),  compact('img_url','Music_url') ) );
	}
	
	
	/* Hien thi danh sach phim theo subcat */
	public function Musicbycat($cat) {
		$img_url = config('medias.image-host');
		$music_url = config('medias.music-host');
		
		$musics = $this->music_gestion->getAllMusicBySubCat ( 4, $cat, null);
		
		$links = $musics->appends ( [] );
						
		$links->setPath ( '' )->render ();
			//return view ( 'back.Music.index', compact ( 'posts', 'links', 'order' ) );
		return view ( 'front.Music.index', ( compact('img_url', 'music_url', 'musics', 'links') ) );
	}
	/**
	 * Hien thi danh sach phim bo
	 * Phim bo la nhung phim co so tap > 1
	 * @param Illuminate\Contracts\Auth\Guard $auth
	 * @param string $slug
	 * @return Response
	 */
	public function single() {
		$img_url = config('medias.image-host');
		$Music_url = config('medias.music-host');
		//return "hello";
		$Musics_most_view = $this->Music_gestion->single();
		$Musics = $this->Music_gestion->MusicBySubCatSlug('tinh-cam');
		$links = $Musics->appends ( [] );
		
		$links->setPath ( '' )->render ();
		return view ( 'front.Music.list', compact('img_url','Music_url', 'links', 'Musics_most_view', 'Musics') );
	}
}

