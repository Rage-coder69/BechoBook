<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\UserController;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/addBook', [BookController::class, 'store'])->name('addBook');
Route::post('/getBooks', [BookController::class, 'getBooks'])->name('getBooks');
//Route::get('/getBook/{id}', [ BookController::class, 'edit' ])->name('getBook');
Route::delete('/deleteBook/{id}', [ BookController::class, 'destroy' ])->name('deleteBook');
Route::post('/getFilteredBooks', [ BookController::class, 'filteredBooks' ])->name('getBook');

Route::get('/getCategories', [CategoryController::class, 'index'])->name('getCategories');

/*Route::post('/addPdf', [ PdfController::class, 'store' ])->name('addPdf');
Route::delete('/deletePdf/{id}', [ PdfController::class, 'destroy' ])->name('deletePdf');
Route::get('/getPdfs', [PdfController::class, 'index'])->name('getPdfs');*/

Route::post('/registerUser', [RegisterController::class, 'register'])->name('registerUser');
Route::post('/loginUser', [LoginController::class, 'login'])->name('loginUser');
Route::get('/getUser/{id}', [UserController::class, 'edit'])->name('getUser');
Route::post('/updateUser', [UserController::class, 'update'])->name('updateUser');
Route::post('/userBooks', [UserController::class, 'userBooks'])->name('userBooks');
Route::get('/logoutUser', [LogoutController::class, 'logout'])->middleware('auth:sanctum')->name('logoutUser');


Route::post('/getUserByPhone', [UserController::class, 'getUserByPhone'])->name('getUserByPhone');
Route::post('/changeValues', [CustomController::class, 'changeValues'])->name('changeValues');

//Route::get('/test', [BookController::class, 'changeURL']);

Route::post('/addReward', [RewardsController::class, 'store'])->name('addReward')->name('addReward');
Route::get('/getRewards', [RewardsController::class, 'index'])->name('getRewards');
Route::post('/getUserReward', [RewardsController::class, 'getUserReward'])->name('getUserReward');

Route::post('/addProduct', [ProductsController::class, 'store'])->name('addProduct');
Route::get('/getProducts', [ProductsController::class, 'index'])->name('getProducts');
Route::post('/deleteProduct', [ProductsController::class, 'destroy'])->name('deleteProduct');

Route::post('/addOrder', [OrdersController::class, 'store'])->name('addOrder');
Route::get('/getOrders', [OrdersController::class, 'index'])->name('getOrders');
Route::post('/getUserOrders', [OrdersController::class, 'getUserOrders'])->name('getUserOrders');
Route::post('/changeOrderStatus', [OrdersController::class, 'changeStatus'])->name('changeOrderStatus');
