<?php

use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\EventController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/self', [EventController::class, 'self'])->name('event.self');

    Route::group(['prefix' => 'event'], function () {
        Route::get('/', [EventController::class, 'index'])->name('event.index');
        Route::get('/{id}', [EventController::class, 'show'])->name('event.show');
        Route::post('/{id}', [EventController::class, 'store'])->name('event.store');
        Route::get('/create/{id}', [EventController::class, 'create'])->name('event.create');
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/{id}', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';
