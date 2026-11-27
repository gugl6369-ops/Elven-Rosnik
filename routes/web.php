<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\PlantController as AdminPlantController;
use App\Http\Controllers\Admin\RecipeController as AdminRecipeController;
use App\Http\Controllers\Admin\StoryController as AdminStoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::redirect('/', '/admin/stories')->name('home');

    Route::get('/stories', [AdminStoryController::class, 'index'])->name('stories.index');
    Route::post('/stories', [AdminStoryController::class, 'store'])->name('stories.store');
    Route::delete('/stories/{story}', [AdminStoryController::class, 'destroy'])->name('stories.destroy');

    Route::get('/plants', [AdminPlantController::class, 'index'])->name('plants.index');
    Route::post('/plants', [AdminPlantController::class, 'store'])->name('plants.store');
    Route::put('/plants/{plant}', [AdminPlantController::class, 'update'])->name('plants.update');
    Route::delete('/plants/{plant}', [AdminPlantController::class, 'destroy'])->name('plants.destroy');

    Route::get('/recipes', [AdminRecipeController::class, 'index'])->name('recipes.index');
    Route::post('/recipes', [AdminRecipeController::class, 'store'])->name('recipes.store');
    Route::put('/recipes/{recipe}', [AdminRecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [AdminRecipeController::class, 'destroy'])->name('recipes.destroy');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::patch('/users/{user}/block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');
});
