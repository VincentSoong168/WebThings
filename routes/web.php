<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register_verify', 'Auth\RegisterController@showVerifyForm')->name('register.verify.form');
Route::post('register_verify', 'Auth\RegisterController@sendVerifyForm')->name('send.verify');
Route::get('register/{token}/{email}', 'Auth\RegisterController@showRegistrationForm')->name('register.form');
Route::post('register', 'Auth\RegisterController@register')->name('register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
//------------------------------------------------------------------------------------------------------------

Route::get('/facebook', 'Auth\LoginController@facebook')->name('facebook.login');
Route::get('/facebook/callback', 'Auth\LoginController@facebook_callback')->name('facebook.callback');
//------------------------------------------------------------------------------------------------------------

Route::get('/home', 'UsersController@index')->name('home');
Route::get('/user/{id}/edit', 'UsersController@edit')->name('user.edit');
Route::put('/user/{id}', 'UsersController@update')->name('user.update');
Route::post('/user/logout', 'Auth\LoginController@user_logout')->name('user.logout');
//------------------------------------------------------------------------------------------------------------

Route::prefix('panel')->group(function () {
	Route::get('/home', 'AdminsController@index')->name('admin.home');
	Route::get('/intent', 'AdminsController@intent')->name('admin.intent');

	Route::get('/login', 'Auth\AdminLoginController@login')->name('admin.login');
	Route::post('/login_check', 'Auth\AdminLoginController@login_check')->name('admin.login.check');

	Route::get('/password/reset', 'Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
	Route::post('/password/email', 'Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
	Route::get('/password/reset/{token}', 'Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');
	Route::post('/password/reset', 'Auth\AdminResetPasswordController@reset');

	Route::post('/logout', 'Auth\AdminLoginController@admin_logout')->name('admin.logout');

	Route::resource('/article', 'ArticleController');

	Route::resource('/category', 'CategoryController', ['except' => ['create', 'show']]);

	Route::resource('/tag', 'TagController', ['except' => ['create', 'edit']]);

	Route::resource('/product', 'ProductController', ['except' => ['show']]);
});
//------------------------------------------------------------------------------------------------------------

Route::get('/article', 'PageController@show_article_list')->name('page.article.list');
Route::get('/article/{id}', 'PageController@show_single_article')->name('page.single.article');
Route::get('/product', 'PageController@show_product_list')->name('page.product.list');

Route::get('/cart', 'CartController@index')->name('cart.index');
Route::get('/cart/destroy/all', 'CartController@destroy_all')->name('cart.destroy.all');
Route::get('/cart/add/{id}', 'CartController@add_to_cart')->name('cart.add');
Route::get('/cart/remove/{id}', 'CartController@remove_from_cart')->name('cart.remove');
Route::get('/cart/pay', 'CartController@send_to_ecpay')->name('cart.pay');
Route::post('/cart/receive/{order_no}', 'CartController@ecpay_receive')->name('cart.receive');
Route::get('/cart_list', 'CartController@showList')->name('cart.list');

//------------------------------------------------------------------------------------------------------------
Route::get('/cart/fake', 'CartController@fake');
Route::get('/email', function(){
	return view('email.register_verify');
});