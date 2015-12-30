<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeLocale;
use App\Repositories\BannerRepository;
use Log;
use App\Models\Film;
use App\Models\Tag;

class HomeController extends Controller
{
	/**
	 * The Tag instance.
	 *
	 * @var App\Models\Tag
	 */
	protected $film;
	public function __construct(
			Film $film)
	{
		$this->film = $film;
	//	$this->comment = $comment;
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
		$filmCondition = ['publish' => 1, 'isHot' => 1];
		$films = $this->film->where($filmCondition)->get();
		
		return view('front.index', array_merge(compact('films'), compact('url'), compact('film_url')));
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
