<?php

namespace App\Repositories;

use DB;

class NationRepository extends BaseRepository {

    
    /**
     * Create a new NationRepository instance.
     *
     * @return void
     */
    public function __construct()
    {        
    	//$this->model = $nation;
    }

    

    /**
     * Create a query for Post.
     *
     * @return array of nation
     */
    public function queryNationWithKeyword($keyword)
    {
        $nations = DB::table("nations");
        
        if(isset($keyword))
        	$nations =$nations->where('name', 'LIKE', '%'.$keyword.'%');

         return $nations->get();
    }

    

}
