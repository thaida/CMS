<?php


namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubCategoryRepository;

class SubCatController extends Controller {
	
	/**
	 * The BlogRepository instance.
	 *
	 * @var App\Repositories\SubCategoryRepository
	 */
	protected $sub_cat_gestion;
	
	/**
	 * The CategoryRepository instance.
	 *
	 * @var App\Repositories\CategoryRepository
	 */
	protected $cat_gestion;
	
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
	public function __construct(SubCategoryRepository $sub_cat_gestion, CategoryRepository $cat_gestion) {
		$this->sub_cat_gestion = $sub_cat_gestion;
		$this->cat_gestion = $cat_gestion;
		
		$this->nbrPages = 2;
		
		/*
		 * $this->middleware('redac', ['except' => ['indexFront', 'show', 'tag', 'search']]);
		 * $this->middleware('admin', ['only' => ['updateSeen', 'updateActive']]);
		 * $this->middleware('ajax', ['only' => ['updateSeen', 'updateActive']]);
		 */
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Redirection
	 */
	public function index() {
		return redirect ( route ( 'subcat.order', [ 
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
		
		//$statut = $this->user_gestion->getStatut();
		$posts = $this->sub_cat_gestion->index (  config("constants.LIMIT"), null, $request->name, $request->sort );
		
		$links = $posts->appends ( [ 
				'name' => $request->name,
				'sort' => $request->sort 
		] );
		
		if($request->ajax()) {
			return response()->json([
					'view' => view('back.subcat.table', compact( 'posts'))->render(),
					'links' => $links->setPath('order')->render()
			]);
		}
		
		$links->setPath('')->render();
		
		$order = ( object ) [ 
				'name' => $request->name,
				'sort' => 'sort-' . $request->sort 
		];
		
		return view ( 'back.subcat.index', compact ( 'posts', 'links', 'order' ) );
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function edit($id) {
		$post = $this->sub_cat_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$url = config ( 'medias.url' );
		
		return view ( 'back.subcat.edit', array_merge ( $this->sub_cat_gestion->edit ( $post ), compact ( 'url' ) ), $select = $this->cat_gestion->getAllSelect () );
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param App\Http\Requests\CategoryRequest $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function update(SubCategoryRequest $request, $id) {
		$post = $this->sub_cat_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->sub_cat_gestion->update ( $request->all (), $post );
		
		return redirect ( 'subcat' )->with ( 'ok', trans ( 'back/blog.updated' ) );
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function destroy($id) {
		$post = $this->sub_cat_gestion->getById ( $id );
		
		// $this->authorize('change', $post);
		
		$this->sub_cat_gestion->destroy ( $post );
		
		return redirect ( 'subcat' )->with ( 'ok', trans ( 'back/cat.destroyed' ) );
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
		$url = config ( 'medias.url' );
		return view ( 'back.subcat.create', array_merge ( compact ( 'url' ), $this->cat_gestion->getAllSelect () ) );
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param App\Http\Requests\PostRequest $request        	
	 * @return Response
	 */
	public function store(SubCategoryRequest $request) {
		$this->sub_cat_gestion->store ( $request->all (), $request->user ()->id );
		
		return redirect ( 'subcat' )->with ( 'ok', trans ( 'back/subcat.stored' ) );
	}
	
	/**
	 * Update "active" for the specified resource in storage.
	 *
	 * @param Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return Response
	 */
	public function updateActive(Request $request, $id) {
		$this->sub_cat_gestion->updateActive ( $request->all (), $id );
		
		return response ()->json ();
	}
}

