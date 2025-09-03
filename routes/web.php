<?php

use App\Http\Controllers\LinkController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route for the home page (displays the form)
Route::get('/', [LinkController::class, 'index'])->name('home');

// Route for submitting the form and creating a short link
Route::post('/shorten', [LinkController::class, 'store'])->name('shorten.store');

// Route for redirecting from the short code to the original URL
Route::get('/{shortCode}', [LinkController::class, 'show'])->name('shorten.show');