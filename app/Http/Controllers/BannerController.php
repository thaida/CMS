<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\BannerRequest;
use App\Repositories\BannerRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubCategoryRepository;

class BannerController extends Controller {
	
	/**
	 * The BlogRepository instance.
	 *
	 * @var App\Repositories\SubCategoryRepository
	 */
	protected $banner_gestion;
	
	/**
	 * The CategoryRepository instance.
	 *
	 * @var App\Repositories\SubCatRepository
	 */
	protected $sub_cat_gestion;
	
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
	public function __construct(BannerRepository $banner_gestion, SubCategoryRepository $sub_cat_gestion) {
		
		$this->banner_gestion = $banner_gestion;
		$this->sub_cat_gestion = $sub_cat_gestion;
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
		return redirect ( route ( 'banner.order', [ 
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
		$posts = $this->banner_gestion->index ( config("constants.LIMIT"), null, $request->name, $request->sort );
		
		$links = $posts->appends ( [ 
				'name' => $request->name,
				'sort' => $request->sort 
		] );
		
		if ($request->ajax ()) {
			return response ()->json ( [ 
					'view' => view ( 'back.banner.table', compact ( 'posts' ) )->render (),
					'links' => $links->setPath ( 'order' )->render () 
			] );
		}
		
		$links->setPath ( '' )->render ();
		
		$order = ( object ) [ 
				'name' => $request->name,
				'sort' => 'sort-' . $request->sort 
		];
		
		return view ( 'back.banner.index', compact ( 'posts', 'links', 'order' ) );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function edit($id) {
		$film = $this->banner_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$url = config ( 'medias.url' );
		$img_host_url = config ( 'medias.image-host' );
		return view ( 'back.banner.edit', array_merge ( $this->banner_gestion->edit ( $film ), compact ( 'url' ), compact('img_host_url') ), $select = $this->sub_cat_gestion->getAllByFilmSelect () );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param App\Http\Requests\CategoryRequest $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function update(BannerRequest $request, $id) {
		$banner = $this->banner_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->banner_gestion->update ( $request->all (), $banner );
		
		return redirect ( 'banner' )->with ( 'ok', trans ( 'back/banner.updated' ) );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function destroy($id) {
		$film = $this->banner_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->banner_gestion->destroy ( $film );
		
		return redirect ( 'banner' )->with ( 'ok', trans ( 'back/banner.destroyed' ) );
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		$url = config ( 'medias.url' );
		return view ( 'back.banner.create', array_merge ( compact ( 'url' ), $this->sub_cat_gestion->getAllByFilmSelect () ) );
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param App\Http\Requests\PostRequest $request        	
	 * @return Response
	 */
	public function store(BannerRequest $request) {

		$this->banner_gestion->store ( $request->all (), $request->user ()->id );
		
		return redirect ( 'banner' )->with ( 'ok', trans ( 'back/banner.stored' ) );
	}
	
	/**
	 * Update "active" for the specified resource in storage.
	 *
	 * @param Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function updatePublish(Request $request, $id) {
		$this->banner_gestion->updatePublish ( $request->all (), $id );
		
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
		
		return view ( 'front.banner.show', array_merge ( $this->banner_gestion->show ( $slug ), compact ( 'user' ) ) );
	}
}

