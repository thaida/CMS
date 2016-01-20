<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeLocale;
use App\Models\Film;
use App\Models\Banner;
use App\Models\Tag;
use DB;

class HomeController extends Controller
{
	/**
	 * The Tag instance.
	 *
	 * @var App\Models\Tag
	 */
	protected $film;
	public function __construct(Film $film)
	{
		$this->film = $film;
	}
	
	
	/**
	 * Display the home page.
	 *
	 * @return Response
	 */
	public function index()
	{
		$url = config('medias.image-host');
		$film_url = config('medias.film-host');
		//hien thi film hot, sap xep moi nhat len tren
		$filmCondition = ['publish' => 1, 'isHot' => 1];
		$films = $this->film->where($filmCondition)
						->orderBy('created_at', 'desc')
						->get();
		//$banners = $this->getBanner();
		$cond = ['publish' => 1, 'sub_cat_id' => 5];
		$banners = Banner::where($cond)->get();
		
		return view('front.index', compact('films','url','film_url', 'banners'));
	}
		

	/**
	 * Change language.
	 *
	 * @param  App\Jobs\ChangeLocaleCommand $changeLocale
	 * @param  String $lang
	 * @return Response
	 */
	public function language( $lang,
		ChangeLocale $changeLocale)
	{		
		$lang = in_array($lang, config('app.languages')) ? $lang : config('app.fallback_locale');
		$changeLocale->lang = $lang;
		$this->dispatch($changeLocale);

		return redirect()->back();
	}
	

}
