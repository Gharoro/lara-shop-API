<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 *  Auth Routes
 */
Route::prefix('v1/auth')->group(function () {

    Route::post('register', 'AuthController@signup');

    Route::post('login', 'AuthController@login');

    Route::get('logout', ['middleware' => 'auth:api', 'uses' => 'AuthController@logout']);
});

/**
 *  User Routes
 */
Route::prefix('v1/user')->group(function () {

    Route::get('profile', ['middleware' => 'auth:api', 'uses' => 'CustomerController@getUser']);

    Route::put('/{userId}/edit',  ['middleware' => 'auth:api', 'uses' => 'CustomerController@editProfile']);
});

/**
 *  Products Routes
 */
Route::prefix('v1/products')->group(function () {

    Route::post('/', ['middleware' => 'auth:api', 'uses' => 'ProductsController@addProduct']);

    Route::get('/', 'ProductsController@getProducts');

    Route::get('/{prodId}', 'ProductsController@getOneProduct');

    Route::delete('/{prodId}/delete', ['middleware' => 'auth:api', 'uses' => 'ProductsController@deleteProduct']);
});

/**
 *  Cart Routes
 */
Route::prefix('v1/cart')->group(function () {

    Route::post('/{prodId}', ['middleware' => 'auth:api', 'uses' => 'CartController@addToCart']);

    Route::get('/user_cart', ['middleware' => 'auth:api', 'uses' => 'CartController@getCart']);

    Route::delete('/{userId}/{prodId}/remove', function ($userId, $prodId) {
        return 'Delete single product from cart';
    });
});


/**
 *  Category Routes
 */
Route::prefix('v1/categories')->group(function () {

    Route::post('/', function () {
        return 'Adds a category';
    });

    Route::get('/', function () {
        return 'View all categories';
    });

    Route::get('/{catId}', function ($catId) {
        return 'View single category';
    });

    Route::put('/{catId}/edit', function ($catId) {
        return 'Edit single category';
    });

    Route::delete('/{catId}/delete', function ($catId) {
        return 'Delete single category';
    });
});


/**
 *  Order Routes
 */
Route::prefix('v1/orders')->group(function () {

    Route::post('/{userId}/{cartId}', function () {
        return 'Place an order';
    });

    Route::get('/{userId}', function () {
        return 'View orders';
    });
});

/**
 *  Checkout Route
 */
Route::post('/checkout/{userId}/{orderId}', function () {
    return 'Checkout payment';
});
