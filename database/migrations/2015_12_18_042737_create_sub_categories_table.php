<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateSubCategoriesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create ( 'sub_categories', function (Blueprint $table) {
			$table->increments ( 'id' );
			$table->string ( 'title', 100 );
			$table->string ( 'slug', 255 )->unique ();
			$table->string ( 'summary', 1000 );
			$table->boolean ( 'active' )->default ( false );
			$table->integer ( 'user_id' )->unsigned ();
			$table->integer ( 'cat_id' )->unsigned ();
			$table->timestamps ();
		} );
		
		Schema::table ( 'sub_categories', function (Blueprint $table) {
			$table->foreign ( 'user_id' )->references ( 'id' )->on ( 'users' )
				->onDelete ( 'restrict' )
				->onUpdate ( 'restrict' );
		} );
		
		Schema::table ( 'sub_categories', function (Blueprint $table) {
			$table->foreign ( 'cat_id' )->references ( 'id' )->on ( 'categories' )
				->onDelete ( 'restrict' )
				->onUpdate ( 'restrict' );
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table ( 'sub_categories', function (Blueprint $table) {
			$table->dropForeign ( 'sub_categories_user_id_foreign' );
		} );
		
		Schema::table ( 'sub_categories', function (Blueprint $table) {
			$table->dropForeign ( 'sub_categories_categories_foreign' );
		} );
		
		Schema::drop ( 'sub_categories' );
	}
}
