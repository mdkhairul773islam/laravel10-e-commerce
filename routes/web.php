<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/remove', [ProductController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/get', [ProductController::class, 'getCart'])->name('cart.get');
Route::post('/checkout', [ProductController::class, 'checkout'])->name('checkout');


Route::get('/thankyou', function () {
    return "thankyou for your order";
})->name('thankyou');


Route::get('/c-clean', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    session()->flush();
    return "All cache cleared from " . env('APP_NAME');
});