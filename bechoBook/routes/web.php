<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
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
/*
Route::get('/', function () {

    return view('index',[
        'categories' => SubCategory::all()
    ]);
})->middleware('customAuth')->name('pdfForm');

Route::get('/login', function (){
    return view('login');
})->name('loginView');

Route::get('/logout', function (){
    auth()->logout();
    return redirect()->route('loginView');
})->name('logout');

Route::post('/addPdf', [ PdfController::class, 'store' ])->name('addPdf');*/
Route::get('/', function () {
    return view('welcome');
});
