<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateBannersTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create ( 'banners', function (Blueprint $table) {
			$table->increments ( 'id' );
			$table->string ( 'title', 100 );
			$table->string ( 'link', 1000 );
			$table->string ( 'poster_path', 1000 );
			$table->string ( 'summary', 1000 );
			$table->integer ( 'sub_cat_id' )->unsigned ();
			$table->boolean ( 'publish' )->default ( false );
			$table->timestamps ();
		} );
		
		Schema::table ( 'banners', function (Blueprint $table) {
			$table->foreign ( 'sub_cat_id' )->references ( 'id' )->on ( 'sub_categories' )->onDelete ( 'restrict' )->onUpdate ( 'restrict' );
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table ( 'banners', function (Blueprint $table) {
			$table->dropForeign ( 'banners_sub_cat_id_foreign' );
		} );
		
		Schema::drop ( 'banners' );
	}
}
