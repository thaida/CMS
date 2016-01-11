<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateFilmsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create ( 'films', function (Blueprint $table) {
			$table->increments ( 'id' );
			$table->string ( 'title', 100 );
			$table->string ( 'film_path', 1000 );
			$table->string ( 'poster_path', 1000 );
			$table->string ( 'subtitle_path', 1000 );
			$table->string ( 'slug', 255 )->unique ();
			$table->string ( 'short_summary', 500 );
			$table->string ( 'summary', 1000 );
			$table->string ( 'director', 500 );
			$table->string ( 'actor', 500 );
			$table->dateTime ( 'release_date' );
			$table->integer( 'running_time')->unsigned();
			//so tap, mac dinh la phim le co 1 tap
			$table->integer ( 'num')->unsigned()->default(1);
			//tap so may
			$table->integer ( 'episode')->unsigned();
			//ngon ngu
			$table->string ( 'language', 300);		
			//link toi tap 1 cua film
			$table->integer ( 'first_episode_id')->unsigned();
			
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
			$table->timestamps ();
		} );
		
		Schema::table ( 'films', function (Blueprint $table) {
			$table->foreign ( 'user_id' )->references ( 'id' )->on ( 'users' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
		} );
		
		Schema::table ( 'films', function (Blueprint $table) {
			$table->foreign ( 'nation_id' )->references ( 'id' )->on ( 'nationals' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
		} );
		Schema::table ( 'films', function (Blueprint $table) {
			$table->foreign ( 'producer_id' )->references ( 'id' )->on ( 'producers' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
		} );
		Schema::table ( 'films', function (Blueprint $table) {
			$table->foreign ( 'sub_cat_id' )->references ( 'id' )->on ( 'sub_categories' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table ( 'films', function (Blueprint $table) {
			$table->dropForeign ( 'films_user_id_foreign' );
		} );
		Schema::table ( 'films', function (Blueprint $table) {
			$table->dropForeign ( 'films_sub_cat_id_foreign' );
		} );
		
		Schema::table ( 'films', function (Blueprint $table) {
			$table->dropForeign ( 'films_nation_id_foreign' );
		} );
		Schema::table ( 'films', function (Blueprint $table) {
			$table->dropForeign ( 'films_producer_id_foreign' );
		} );
		
		Schema::drop ( 'films' );
	}
}
