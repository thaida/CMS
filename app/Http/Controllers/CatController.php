<?php namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\SearchRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;

class CatController extends Controller {

	/**
	 * The BlogRepository instance.
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
	 * @param  App\Repositories\CategoryRepository $blog_gestion
	 * @return void
	*/
	public function __construct(
		CategoryRepository $cat_gestion
		)
	{
		$this->cat_gestion = $cat_gestion;
		$this->nbrPages = 2;

/*		$this->middleware('redac', ['except' => ['indexFront', 'show', 'tag', 'search']]);
		$this->middleware('admin', ['only' => ['updateSeen', 'updateActive']]);
		$this->middleware('ajax', ['only' => ['updateSeen', 'updateActive']]);
		*/
	}	


	/**
	 * Display a listing of the resource.
	 *
	 * @return Redirection
	 */
	public function index()
	{
		return redirect(route('cat.order', [
			'name' => 'created_at',
			'sort' => 'asc'
		]));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @return Response
	 */
	public function indexOrder(Request $request)
	{
		//$statut = $this->user_gestion->getStatut();
		$posts = $this->cat_gestion->index(10, null, $request->name, $request->sort );
		
		$links = $posts->appends([
				'name' => $request->name,
				'sort' => $request->sort
			]);

		if($request->ajax()) {
			return response()->json([
				'view' => view('back.cat.table', compact( 'posts'))->render(), 
				'links' => $links->setPath('order')->render()
			]);		
		}

		$order = (object)[ 
				'name' => $request->name,
			'sort' => 'sort-' . $request->sort			
		];

		return view('back.cat.index', compact('posts', 'links', 'order'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(		
			$id)
	{
		$post = $this->cat_gestion->getById($id);
	
		//$this->authorize('change', $post);
	
		$url = config('medias.url');
	
		return view('back.cat.edit',  array_merge($this->cat_gestion->edit($post), compact('url')));
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  App\Http\Requests\CategoryRequest $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(
			CategoryRequest $request,
			$id)
	{
		$post = $this->cat_gestion->getById($id);
	
		//$this->authorize('change', $post);
	
		$this->cat_gestion->update($request->all(), $post);
	
		return redirect('cat')->with('ok', trans('back/blog.updated'));
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$post = $this->cat_gestion->getById($id);
	
		//$this->authorize('change', $post);
	
		$this->cat_gestion->destroy($post);
	
		return redirect('cat')->with('ok', trans('back/cat.destroyed'));
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$url = config('medias.url');
	
		return view('back.cat.create')->with(compact('url'));
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  App\Http\Requests\PostRequest $request
	 * @return Response
	 */
	public function store(CategoryRequest $request)
	{
		$this->cat_gestion->store($request->all(), $request->user()->id);
	
		return redirect('cat')->with('ok', trans('back/cat.stored'));
	}
	
	
	/**
	 * Update "active" for the specified resource in storage.
	 *
	 * @param  Illuminate\Http\Request $request
	 * @param  int  $id
	 * @return Response
	 */
	public function updateActive(
			Request $request,
			$id)
	{
		$this->cat_gestion->updateActive($request->all(), $id);
	
		return response()->json();
	}
}

