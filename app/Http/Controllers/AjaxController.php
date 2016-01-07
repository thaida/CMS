<?php

namespace App\Http\Controllers;

use Input;
use App\Repositories\NationRepository;

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
		$keyword = "";
		if (Input::get ( 'term' ) != null)
			$keyword = Input::get ( 'term' );
		
		switch ($action) {
			case 'nation' :
				$data = $this->getAllNations ( $keyword );
				break;
			default :
				return [ ];
		}
		$returnData = array ();
		foreach ( $data as $e ) {
			$returnData [] = $e->name;
		}
		return json_encode ( $returnData );
	}
	
	private function getAllNations($keyword) {
		$nations = new NationRepository ();
		$data = $nations->queryNationWithKeyword ( $keyword );
		return $data;
	}
}