<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\ContinueRegistrationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Catalog\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Social\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
});

Route::get('/register', [RegisterController::class, 'show'])
    ->name('register');

Route::get('/catalog', [CatalogController::class, 'index'])
    ->name('catalog');

Route::get('/catalog/plants/{plant}', [CatalogController::class, 'show'])
    ->name('catalog.plants.show');

Route::get('/register/continue', [ContinueRegistrationController::class, 'show'])
    ->name('register.continue');

$registerContinueStore = Route::post('/register/continue', [ContinueRegistrationController::class, 'store'])
    ->name('register.continue.store');

if (! config('auth_throttle.disabled')) {
    $registerContinueStore->middleware('throttle:registration');
}

Route::middleware(['auth'])->group(function (): void {
    Route::get('/account', [AccountController::class, 'show'])
        ->name('account');

    Route::get('/account/favorites', [FavoriteController::class, 'index'])
        ->name('account.favorites');

    Route::post('/plants/{plant}/favorite', [FavoriteController::class, 'store'])
        ->name('plants.favorite.store');

    Route::delete('/plants/{plant}/favorite', [FavoriteController::class, 'destroy'])
        ->name('plants.favorite.destroy');
});
