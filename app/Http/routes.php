<?php

// Home
Route::get('/', [
	'uses' => 'HomeController@index', 
	'as' => 'home'
]);
Route::get('language/{lang}', 'HomeController@language')->where('lang', '[A-Za-z_-]+');


// Admin
Route::get('admin', [
	'uses' => 'AdminController@admin',
	'as' => 'admin',
	'middleware' => 'admin'
]);

Route::get('medias', [
	'uses' => 'AdminController@filemanager',
	'as' => 'medias',
	'middleware' => 'redac'
]);


// Blog
Route::get('blog/order', ['uses' => 'BlogController@indexOrder', 'as' => 'blog.order']);
Route::get('articles', 'BlogController@indexFront');
Route::get('blog/tag', 'BlogController@tag');
Route::get('blog/search', 'BlogController@search');

Route::put('postseen/{id}', 'BlogController@updateSeen');
Route::put('postactive/{id}', 'BlogController@updateActive');

Route::resource('blog', 'BlogController');
//category
Route::resource('category', 'CategoryController');
Route::get('category/order', ['uses' => 'CategoryController@indexOrderTest', 'as' => 'category.order']);
Route::get('category/search', 'CategoryController@search');

// CAT
Route::get('cat/order', ['uses' => 'CatController@indexOrder', 'as' => 'cat.order']);
Route::put('catactive/{id}', 'CatController@updateActive');

Route::resource('cat', 'CatController');

// SUB CATEGORY
Route::get('subcategory/order', ['uses' => 'SubCatController@indexOrder', 'as' => 'subcat.order']);
Route::put('subcategoryactive/{id}', 'SubCatController@updateActive');

Route::resource('subcat', 'SubCatController');


// Film
Route::get('film/order', ['uses' => 'FilmController@indexOrder', 'as' => 'film.order']);
Route::get('film/phim-bo', ['uses' => 'FilmController@series', 'as' => 'film.series']);
Route::get('film/phim-le', ['uses' => 'FilmController@single', 'as' => 'film.single']);
Route::get('film/tim-kiem/{keyword?}', ['uses' => 'FilmController@search', 'as' => 'film.search']);

Route::put('filmpublish/{id}', 'FilmController@updatePublish');
Route::put('filmhot/{id}', 'FilmController@updateFront');
Route::get('phim', ['uses' => 'FilmController@allfilm', 'as' => 'film.allphim']);
Route::get('phim/{cat}', ['uses' => 'FilmController@filmbycat', 'as' => 'film.filmbycat']);
Route::resource('film', 'FilmController');
// MUSIC
Route::get('music/order', ['uses' => 'MusicController@indexOrder', 'as' => 'music.order']);
Route::get('music/phim-bo', ['uses' => 'MusicController@series', 'as' => 'music.series']);
Route::get('music/phim-le', ['uses' => 'MusicController@single', 'as' => 'music.single']);

Route::put('musicpublish/{id}', 'MusicController@updatePublish');
Route::put('musichot/{id}', 'MusicController@updateFront');
Route::get('nhac', ['uses' => 'MusicController@allmusic', 'as' => 'music.allmusic']);
Route::get('nhac/{cat}', ['uses' => 'MusicController@filmbycat', 'as' => 'music.musicbycat']);
Route::resource('music', 'MusicController');

// Banner
Route::get('banner/order', ['uses' => 'BannerController@indexOrder', 'as' => 'banner.order']);
Route::put('bannerpublish/{id}', 'BannerController@updatePublish');

Route::resource('banner', 'BannerController');

// Comment
Route::resource('comment', 'CommentController', [
	'except' => ['create', 'show']
]);

//AJAX 
Route::get('ajax/{action}', ['uses' => 'AjaxController@helpers', 'as' => 'ajax.helpers'])
    ->where('action', '.*');

Route::put('commentseen/{id}', 'CommentController@updateSeen');
Route::put('uservalid/{id}', 'CommentController@valid');


// Contact
Route::resource('contact', 'ContactController', [
	'except' => ['show', 'edit']
]);


// User
Route::get('user/sort/{role}', 'UserController@indexSort');

Route::get('user/roles', 'UserController@getRoles');
Route::post('user/roles', 'UserController@postRoles');

Route::put('userseen/{user}', 'UserController@updateSeen');

Route::resource('user', 'UserController');

// Auth
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
