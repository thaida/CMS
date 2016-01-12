<?php

namespace App\Http\Controllers;

use Input;
use DB;
use App\Repositories\NationRepository;
use App\Repositories\FilmRepository;

class AjaxController extends Controller {
	
	/**
	 * Create a new AjaxController instance.
	 *
	 * @param App\Repositories\UserRepository $user_gestion        	
	 * @return void
	 */
	public function __construct() {
	}
	
	public function helpers($action) {
		$data = [ ];
		$returnData = array ();
		$keyword = "";
		if (Input::get ( 'term' ) != null)
			$keyword = Input::get ( 'term' );
		
		switch ($action) {
			case 'nation' :
				$data = $this->getAllNations ( $keyword );
				foreach ( $data as $e ) {
					$returnData [] = $e->name;
				}
				break;
			case 'films' :
				$data = $this->getAllFirstFilms ( $keyword );
				foreach ( $data as $e ) {
					$tmp = (object)["id" => $e->id,  "label" => $e->title];
					$returnData [] = $tmp;
				}
				break;
			default :
				return [ ];
		}
		
		
		return json_encode ( $returnData, JSON_UNESCAPED_UNICODE );
	}
	
	private function getAllNations($keyword) {
		$nations = new NationRepository ();
		$data = $nations->queryNationWithKeyword ( $keyword );
		return $data;
	}
	
	private function getAllFirstFilms($keyword) {
		$films = DB::table("films");
		$films = $films->where('publish', 1);
		
		if(isset($keyword))
			$films = $films->where('title', 'LIKE', '%'.$keyword.'%');
		
		$data = $films->get();
		return $data;
	}
}
