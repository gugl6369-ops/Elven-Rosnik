<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Catalog\CatalogController;
use App\Http\Controllers\Auth\ContinueRegistrationController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (Request $request) {
    if (! $request->user()) {
        return redirect()->route('login');
    }

    return Inertia::render('Home', [
        'appName' => config('app.name'),
    ]);
})->name('home');

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
});
