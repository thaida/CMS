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
			$table->string ( 'slug', 255 )->unique ();
			$table->string ( 'summary', 1000 );
			$table->boolean ( 'publish' )->default ( false );
			$table->boolean ( 'isHot' )->default ( false );
			$table->integer ( 'user_id' )->unsigned ();
			$table->integer ( 'sub_cat_id' )->unsigned ();
			$table->timestamps ();
		} );
		
		Schema::table ( 'films', function (Blueprint $table) {
			$table->foreign ( 'user_id' )->references ( 'id' )->on ( 'users' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
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
		
		Schema::drop ( 'films' );
	}
}
