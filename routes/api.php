<?php


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
Route::post('register', 'Api\AuthController@register');
Route::post('auth/login', 'Api\AuthController@login');
Route::get('auth/refresh', 'Api\AuthController@refresh');

//Route::get('/vendaapi/getPdv', 'Api\VendaController@getPdv')->name('auth.getPdv');
//Route::get('/vendaapi/getProducts', 'Api\VendaController@getProducts')->name('auth.getProducts');
//Route::get('/vendaapi', 'Api\VendaController@index')->name('index');
//Route::post('/vendaapi/saveProductsSale', 'Api\VendaController@saveProductsSale')->name('auth.saveProductsSale');

Route::group(['middleware' => ['apiJwt'], 'prefix' => 'auth'], function() {
    Route::get('users', 'Api\UserController@index');
    Route::post('logout', 'Api\AuthController@logout');

    Route::post('vendaapi/carts','Api\VendaController@carts')->name('auth.carts');
    Route::post('vendaapi/saveProductSale','Api\VendaController@saveProductSale')->name('auth.saveProductSale');
    Route::Resource('vendaapi','Api\VendaController');

    Route::apiResource('loginapi','Api\LoginApiUserController');
    Route::apiResource('formaapi','Api\FormaPagamentoController');
    Route::apiResource('empresaapi','Api\LojasController');

    Route::get('/produtoapi/search','Api\ProdutoController@search')->name('auth.search');
    Route::apiResource('produtoapi','Api\ProdutoController');

    Route::apiResource('fluxoapi','Api\FluxoController');
    Route::apiResource('trocaapi','Api\TrocaController');
    Route::Resource('clienteapi','Api\ClienteController');
    Route::apiResource('cashbackapi','Api\CashBackController');

});


