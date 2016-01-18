<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->increments('id');
            $table->string ( 'title', 1000 );
            $table->string ( 'music_path', 1000 );
            $table->string ( 'poster_path', 1000 );
            $table->string ( 'subtitle_path', 1000 );
            $table->string ( 'slug', 255 )->unique ();
            $table->string ( 'short_summary', 500 );
            $table->string ( 'summary', 1000 );
            $table->string ( 'director', 500 );
            $table->string ( 'actor', 500 );
            $table->dateTime ( 'release_date' );
            $table->integer( 'running_time')->unsigned();
            	
            //ngon ngu
            $table->string ( 'language', 300);
            	
            $table->integer ( 'star')->unsigned();
            //quoc gia
            $table->integer ( 'nation_id')->unsigned();
            //nha san xuat
            $table->integer ( 'producer_id')->unsigned();
            $table->boolean ( 'publish' )->default ( false );
            $table->boolean ( 'isHot' )->default ( false );
            $table->boolean ( 'isFree' )->default ( true );
            $table->integer ( 'user_id' )->unsigned ();
            $table->bigInteger ( 'counter' );
            $table->integer ( 'sub_cat_id' )->unsigned ();
            $table->timestamps();
        });
        
        	Schema::table ( 'musics', function (Blueprint $table) {
        		$table->foreign ( 'user_id' )->references ( 'id' )->on ( 'users' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
        	} );
        	
        		/* Schema::table ( 'musics', function (Blueprint $table) {
        			$table->foreign ( 'nation_id' )->references ( 'id' )->on ( 'nations' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
        		} );
        			Schema::table ( 'musics', function (Blueprint $table) {
        				$table->foreign ( 'producer_id' )->references ( 'id' )->on ( 'producers' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
        			} ); */
        				Schema::table ( 'musics', function (Blueprint $table) {
        					$table->foreign ( 'sub_cat_id' )->references ( 'id' )->on ( 'sub_categories' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
        				} );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table ( 'musics', function (Blueprint $table) {
    		$table->dropForeign ( 'musics_user_id_foreign' );
    	} );
    		Schema::table ( 'musics', function (Blueprint $table) {
    			$table->dropForeign ( 'musics_sub_cat_id_foreign' );
    		} );
    	
    			/* Schema::table ( 'musics', function (Blueprint $table) {
    				$table->dropForeign ( 'musics_nation_id_foreign' );
    			} );
    				Schema::table ( 'musics', function (Blueprint $table) {
    					$table->dropForeign ( 'musics_producer_id_foreign' );
    				} ); */
        Schema::drop('musics');
    }
}
