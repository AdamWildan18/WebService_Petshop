<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'auth'], function () use ($router){
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});

$router->group(['middleware' => ['auth']], function ($router)
{
    $router->get('/product', 'ProductController@index');
    $router->get('/product/{id}', 'ProductController@show');
    $router->put('/product/{id}', 'ProductController@update');
    $router->post('/product', 'ProductController@store');
    $router->get('/product/image/{imageName}', 'ProductController@image');
    $router->get('/product/video/{videoName}', 'ProductController@video');
    $router->delete('/product/{id}', 'ProductController@destroy');
});

$router->group(['middleware' => ['auth']], function ($router)
{
    $router->get('/order', 'OrderController@index');
    $router->get('/order/{id}', 'OrderController@show');
    $router->put('/order/{id}', 'OrderController@update');
    $router->post('/order', 'OrderController@store');
});

$router->group(['middleware' => ['auth']], function ($router)
{
    $router->get('/comment', 'CommentController@index');
    $router->get('/comment/{id}', 'CommentController@show');
    $router->put('/comment/{id}', 'CommentController@update');
    $router->post('/comment', 'CommentController@store');
});

