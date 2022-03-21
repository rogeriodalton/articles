<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

$router->get('/', 'ApiController@index');
$router->post('/', 'ApiController@index');
$router->post('/login', 'JWTAuthController@login');

$router->group([
    'middleware' => 'apiJwt'

], function ($router) {
    $router->post('logout', 'JWTAuthController@logout');
    $router->post('refresh', 'JWTAuthController@refresh');
    $router->post('allPermission', 'JWTAuthController@allPermission');
    $router->post('permission', 'JWTAuthController@permission');
    $router->post('me', 'JWTAuthController@me');

    $router->group(['prefix' => 'client'], function () use ($router) {
        $router->get('/', 'ClientController@index');
        $router->get('/{id}', 'ClientController@show');
        $router->post('/', 'ClientController@store');
        $router->put('/{id}', 'ClientController@update');
        $router->delete('/{id}', 'ClientController@destroy');
    });

    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->get('/', 'UsersController@index');
        $router->get('/{id}', 'UsersController@show');
        $router->post('/', 'UsersController@store');
        $router->put('/{id}', 'UsersController@update');
        $router->delete('/{id}', 'UsersController@destroy');
    });

    $router->group(['prefix' => 'article'], function () use ($router) {
        $router->get('/', 'ArticleController@index');
        $router->get('/{id}', 'ArticleController@show');
        $router->post('/', 'ArticleController@store');
        $router->put('/{id}', 'ArticleController@update');
        $router->delete('/{id}', 'ArticleController@destroy');
    });

    $router->group(['prefix' => 'discountOrder'], function () use ($router) {
        $router->get('/', 'DiscountOrdersController@index');
        $router->get('/{id}', 'DiscountOrdersController@show');
        $router->post('/', 'DiscountOrdersController@store');
        $router->put('/{id}', 'DiscountOrdersController@update');
        $router->delete('/{id}', 'DiscountOrdersController@destroy');
    });


    $router->group(['prefix' => 'discountRules'], function () use ($router) {
        $router->get('/', 'DiscountRuleController@index');
        $router->get('/{id}', 'DiscountRuleController@show');
        $router->post('/', 'DiscountRuleController@store');
        $router->put('/{id}', 'DiscountRuleController@update');
        $router->delete('/{id}', 'DiscountRuleController@destroy');
    });

    $router->group(['prefix' => 'orders'], function () use ($router) {
        $router->get('/', 'OrderController@index');
        $router->get('/{id}', 'OrderController@show');
        $router->post('/', 'OrderController@store');
        $router->put('/{id}', 'OrderController@update');
        $router->delete('/{id}', 'OrderController@destroy');
    });

    $router->group(['prefix' => 'orderItems'], function () use ($router) {
        $router->get('/', 'OrderItemsController@index');
        $router->get('/{id}', 'OrderItemsController@show');
        $router->post('/', 'OrderItemsController@store');
        $router->put('/{id}', 'OrderItemsController@update');
        $router->delete('/{id}', 'OrderItemsController@destroy');
    });

    $router->group(['prefix' => 'finalizeOrder'], function () use ($router) {
        $router->get('/', 'FinalizeOrderController@index');
        $router->get('/{id}', 'FinalizeOrderController@show');
        $router->post('/', 'FinalizeOrderController@execute');
        $router->put('/{id}', 'FinalizeOrderController@update');
        $router->delete('/{id}', 'FinalizeOrderController@destroy');
    });

});

