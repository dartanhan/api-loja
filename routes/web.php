<?php

use App\Http\Controllers\FluxoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PdvController;
use App\Http\Controllers\ProductBestSellersController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProdutoInativoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\SwaggerController;

use Illuminate\Support\Facades\Route;

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


    Route::get('/', 'AuthController@dashboard')->name('admin');
    Route::get('/admin/login', 'AuthController@showLoginForm')->name('admin.login');
    Route::post('/admin/login/do', 'AuthController@login')->name('admin.login.do');

Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function(){
    Route::get('/home', [HomeController::class,'index'])->name('admin.home');

    Route::get('/dashboard', 'AuthController@dashboard')->name('admin.dashboard');
    Route::get('/logout', 'AuthController@logout')->name('admin.logout');

    Route::get('/produto/pictures/{id}',[ProdutoController::class,'pictures'])->name('pictures');
    Route::get('/produto/getProducts/{id}',[ProdutoController::class,'getProducts'])->name('getProducts');
   Route::resource('produto','ProdutoController');

    Route::get('/produto/getProdutoInativos/{id}',[ProdutoInativoController::class,'getProdutoInativos'])->name('getProdutoInativos');
    Route::resource('produtoInativo','ProdutoInativoController');

    Route::get('/product/code','ProductController@code')->name('product.code');
    Route::post('/importProduct', 'ProductController@importProduct')->name('import-product');
    Route::resource('product','ProductController');
    Route::resource('productBlock','ProductBlockController');
    Route::resource('productMin','ProductMinController');

    Route::get('productbestsellers/cards/{data}',[ProductBestSellersController::class,'cards']);
    Route::get('productbestsellers/details/{id}/{data}',[ProductBestSellersController::class,'details']);
    Route::get('productbestsellers/detailsCost/{id}/{data}',[ProductBestSellersController::class,'detailsCost']);
    Route::get('productbestsellers/getListProductsSales/{id}/{data}',[ProductBestSellersController::class,'getListProductsSales'])->name('getListProductsSales');

    Route::resource('productbestsellers','ProductBestSellersController');

    Route::resource('productSaleDay','ProductSaleDayController');

    Route::resource('/estoque','EstoqueController');

    Route::resource('/image', 'ProdutoImagemController');

    Route::resource('/fornecedor','FornecedorController');

    Route::resource('/cor','CorController');

    Route::resource('/categoria','CategoriaController');

    Route::resource('/usuario','UserSystemController');

    Route::resource('/payment','PaymentController');

    Route::resource('/origem','OrigemNfceController');

    Route::get('/pdv', [PdvController::class,'index'])->name('admin.pdv');

    Route::get('/relatorio/chartDay/{dataini}/{datafim}/{store_id}',[RelatorioController::class,'chartDay'])->name('chartDay');
    Route::get('/relatorio/editSales/{store_id}',[RelatorioController::class,'editSales'])->name('editSales');
    Route::post('/relatorio/dailySalesList',[RelatorioController::class,'dailySalesList']);
    Route::get('/relatorio/detailSales/{id}',[RelatorioController::class,'detailSales']);
    Route::get('/relatorio/detailCart/{id}',[RelatorioController::class,'detailCart']);
    Route::get('/relatorio/buscaTaxa/{id}',[RelatorioController::class,'buscaTaxa']);
    Route::get('/relatorio/mes/{ano}',[RelatorioController::class ,'mes']);
    Route::get('/relatorio/card/{ano}',[RelatorioController::class,'card']);
    Route::get('/relatorio/chartLineGroupYear/{year}',[RelatorioController::class,'chartLineGroupYear']);
    Route::get('/relatorio/chartLineMultiGroupYear',[RelatorioController::class,'chartLineMultiGroupYear']);

    Route::resource('/relatorio','RelatorioController');

    Route::resource('/tarifa','TarifaController');

    Route::resource('/cashback','CashbackController');

    Route::resource('/conferenciames','ConferenciaController');

    Route::post('/gastosfixofiltro', 'GastosFixoController@getFormGasto')->name('gastosfixo.filtro');
    Route::resource('/gastosfixo','GastosFixoController');

    //Route::get('/fluxo/card/{ano}/edit',[FluxoController::class,'card']);
    Route::get('/fluxo/chart/{id}',[FluxoController::class,'chart']);
    Route::resource('/fluxo','FluxoController');

    Route::resource('/flux',FluxController::class);

    Route::resource('/calendario','CalendarioController');

    Route::resource('/cliente','ClienteController');

    /*Route::get('/dashbord', function(){
        return view('admin.dashbord');
    })->name('admin.dashbord');*/

    Route::get('/graficos', function(){
        return view('admin.graficos');
    })->name('admin.chart');


    /*Route::get('/usuarios', function(){
        return view('admin.usuarios');
    })->name('admin.users');*/

});

/*Route::resource('/usuario','UserSystemController');
Route::resource('/relatorio','RelatorioController');
Route::get('/produto/getProducts/{id}',[ProdutoController::class,'getProducts'])->name('getProducts');
Route::resource('produto','ProdutoController');
Route::get('/fluxo/chart/{id}','FluxoController@chartFluxo');
Route::resource('/fluxo','FluxoController');*/
//Route::post('/importProduct', 'ProductController@importProduct')->name('importProduct');
/*
Route::resource('/conferenciames','ConferenciaController');
Route::get('/relatorio/dailySalesList/{id}','RelatorioController@dailySalesList');
Route::get('/relatorio/detailCart/{id}','RelatorioController@detailCart');
Route::get('/relatorio/detailSales/{id}','RelatorioController@detailSales');
Route::get('/relatorio/chartDay','RelatorioController@chartDay');
Route::get('/relatorio/buscaTaxa/{id}','RelatorioController@buscaTaxa');
Route::get('/relatorio/totalMes','RelatorioController@totalMes');
Route::get('/relatorio/fechamentoMes','RelatorioController@fechamentoMes');
Route::resource('/categoria','CategoriaController');
//Route::resource('/fluxo','FluxoController');
//Route::resource('/calendario','CalendarioController');
Route::get('/gastosfixofiltro', 'GastosFixoController@getFormGasto')->name('gastosfixo.filtro');
//Route::resource('/gastosfixo','GastosFixoController');
Route::resource('productBestSellers','ProductBestSellersController');*/
//Route::get('/relatorio/chartDay/{dataini}/{datafim}','RelatorioController@chartDay')->name('chartDay');
//Route::get('/relatorio/chartLineGroupYear/{year}',[RelatorioController::class,'chartLineGroupYear']);
//Route::get('/relatorio/chartLineMultiGroupYear',[RelatorioController::class,'chartLineMultiGroupYear']);
//Route::resource('/relatorio','RelatorioController');

//Route::post('/relatorio/dailySalesList',[RelatorioController::class,'dailySalesList']);
