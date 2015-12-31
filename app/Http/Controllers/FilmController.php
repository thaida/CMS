<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\FilmRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\FilmRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubCategoryRepository;
use Input;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilmController extends Controller {
	
	/**
	 * The BlogRepository instance.
	 *
	 * @var App\Repositories\SubCategoryRepository
	 */
	protected $sub_cat_gestion;
	
	/**
	 * The CategoryRepository instance.
	 *
	 * @var App\Repositories\FilmRepository
	 */
	protected $film_gestion;
	
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
	public function __construct(FilmRepository $film_gestion, SubCategoryRepository $sub_cat_gestion) {
		$this->sub_cat_gestion = $sub_cat_gestion;
		$this->film_gestion = $film_gestion;
		
		$this->nbrPages = 2;
		
		
		 $this->middleware('redac', ['except' => ['indexFront', 'show', 'tag', 'search']]);
		 $this->middleware('admin', ['only' => ['updateSeen', 'updateActive']]);
		 $this->middleware('ajax', ['only' => ['updateSeen', 'updateActive']]);
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Redirection
	 */
	public function index() {
		return redirect ( route ( 'film.order', [ 
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
		$posts = $this->film_gestion->index ( config("constants.LIMIT"), null, $request->name, $request->sort );
		
		$links = $posts->appends ( [ 
				'name' => $request->name,
				'sort' => $request->sort 
		] );
		
		if ($request->ajax ()) {
			return response ()->json ( [ 
					'view' => view ( 'back.film.table', compact ( 'posts' ) )->render (),
					'links' => $links->setPath ( 'order' )->render () 
			] );
		}
		
		$links->setPath ( '' )->render ();
		
		$order = ( object ) [ 
				'name' => $request->name,
				'sort' => 'sort-' . $request->sort 
		];
		
		return view ( 'back.film.index', compact ( 'posts', 'links', 'order' ) );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function edit($id) {
		$film = $this->film_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$url = config ( 'medias.url' );
		$img_host_url = config ( 'medias.image-host' );
		return view ( 'back.film.edit', array_merge ( $this->film_gestion->edit ( $film ), compact ( 'url' ), compact('img_host_url') ), $select = $this->sub_cat_gestion->getAllByFilmSelect () );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param App\Http\Requests\CategoryRequest $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function update(FilmRequest $request, $id) {
		$film = $this->film_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->film_gestion->update ( $request->all (), $film );
		
		return redirect ( 'film' )->with ( 'ok', trans ( 'back/film.updated' ) );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function destroy($id) {
		$film = $this->film_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->film_gestion->destroy ( $film );
		
		return redirect ( 'film' )->with ( 'ok', trans ( 'back/film.destroyed' ) );
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		$url = config ( 'medias.url' );
		return view ( 'back.film.create', array_merge ( compact ( 'url' ), $this->sub_cat_gestion->getAllByFilmSelect () ) );
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param App\Http\Requests\PostRequest $request        	
	 * @return Response
	 */
	public function store(FilmRequest $request) {

		$this->film_gestion->store ( $request->all (), $request->user ()->id );
		
		return redirect ( 'film' )->with ( 'ok', trans ( 'back/film.stored' ) );
	}
	
	/**
	 * Update "active" for the specified resource in storage.
	 *
	 * @param Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function updatePublish(Request $request, $id) {
		$this->film_gestion->updatePublish ( $request->all (), $id );
		
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
		$this->film_gestion->updateFront ( $request->all (), $id );
	
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
		$film_url = config('medias.film-host');
		$post = $this->film_gestion->show ( $slug );
		
		//tim nhung film lien quan toi film nay de dua vao muc de xuat
		$films = $this->film_gestion->filmRelated($slug);
		
		return view ( 'front.film.show', array_merge ( $this->film_gestion->show($slug), $this->film_gestion->filmRelated($slug), compact ( 'user' ), compact('img_url'), compact('film_url') ) );
	}
}

