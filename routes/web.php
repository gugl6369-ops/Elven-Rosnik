<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\PlantController as AdminPlantController;
use App\Http\Controllers\Admin\RecipeController as AdminRecipeController;
use App\Http\Controllers\Admin\StoryController as AdminStoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ContinueRegistrationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Catalog\CatalogController;
use App\Http\Controllers\Catalog\RecipeController as CatalogRecipeController;
use App\Http\Controllers\Social\CommentController;
use App\Http\Controllers\Social\CommentReactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Social\FavoriteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/home/feed', [HomeController::class, 'feed'])->middleware('throttle:feed')->name('home.feed');
});

Route::get('/register', [RegisterController::class, 'show'])
    ->name('register');

Route::get('/catalog', [CatalogController::class, 'index'])
    ->middleware('throttle:catalog')
    ->name('catalog');

Route::get('/catalog/plants/{plant}', [CatalogController::class, 'show'])
    ->middleware('throttle:catalog')
    ->name('catalog.plants.show');

Route::get('/catalog/recipes/{recipe}', [CatalogRecipeController::class, 'show'])
    ->middleware('throttle:catalog')
    ->name('catalog.recipes.show');

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

    Route::post('/account/avatar', [AccountController::class, 'updateAvatar'])
        ->middleware('throttle:uploads')
        ->name('account.avatar.update');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])
        ->middleware('throttle:sensitive')
        ->name('account.profile.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])
        ->middleware('throttle:sensitive')
        ->name('account.password.update');

    Route::get('/account/favorites', [FavoriteController::class, 'index'])
        ->name('account.favorites');

    Route::post('/plants/{plant}/favorite', [FavoriteController::class, 'store'])
        ->middleware('throttle:mutations')
        ->name('plants.favorite.store');

    Route::delete('/plants/{plant}/favorite', [FavoriteController::class, 'destroy'])
        ->middleware('throttle:mutations')
        ->name('plants.favorite.destroy');

    Route::post('/recipes/{recipe}/favorite', [FavoriteController::class, 'storeRecipe'])
        ->middleware('throttle:mutations')
        ->name('recipes.favorite.store');
    Route::delete('/recipes/{recipe}/favorite', [FavoriteController::class, 'destroyRecipe'])
        ->middleware('throttle:mutations')
        ->name('recipes.favorite.destroy');

    Route::post('/plants/{plant}/comments', [CommentController::class, 'storePlant'])
        ->middleware('throttle:mutations')
        ->name('plants.comments.store');
    Route::patch('/plants/{plant}/comments/{comment}', [CommentController::class, 'updatePlant'])
        ->middleware('throttle:mutations')
        ->name('plants.comments.update');
    Route::delete('/plants/{plant}/comments/{comment}', [CommentController::class, 'destroyPlant'])
        ->middleware('throttle:mutations')
        ->name('plants.comments.destroy');

    Route::post('/recipes/{recipe}/comments', [CommentController::class, 'storeRecipe'])
        ->middleware('throttle:mutations')
        ->name('recipes.comments.store');
    Route::patch('/recipes/{recipe}/comments/{comment}', [CommentController::class, 'updateRecipe'])
        ->middleware('throttle:mutations')
        ->name('recipes.comments.update');
    Route::delete('/recipes/{recipe}/comments/{comment}', [CommentController::class, 'destroyRecipe'])
        ->middleware('throttle:mutations')
        ->name('recipes.comments.destroy');

    Route::post('/plants/{plant}/comments/{comment}/reaction', [CommentReactionController::class, 'storePlant'])
        ->middleware('throttle:mutations')
        ->name('plants.comments.reaction.store');
    Route::delete('/plants/{plant}/comments/{comment}/reaction', [CommentReactionController::class, 'destroyPlant'])
        ->middleware('throttle:mutations')
        ->name('plants.comments.reaction.destroy');

    Route::post('/recipes/{recipe}/comments/{comment}/reaction', [CommentReactionController::class, 'storeRecipe'])
        ->middleware('throttle:mutations')
        ->name('recipes.comments.reaction.store');
    Route::delete('/recipes/{recipe}/comments/{comment}/reaction', [CommentReactionController::class, 'destroyRecipe'])
        ->middleware('throttle:mutations')
        ->name('recipes.comments.reaction.destroy');
});

Route::middleware(['auth', 'admin', 'throttle:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::redirect('/', '/admin/stories')->name('home');

    Route::get('/stories', [AdminStoryController::class, 'index'])->name('stories.index');
    Route::post('/stories', [AdminStoryController::class, 'store'])
        ->middleware('throttle:uploads')
        ->name('stories.store');
    Route::patch('/stories/{story}/status', [AdminStoryController::class, 'updateStatus'])
        ->name('stories.update-status');
    Route::delete('/stories/{story}', [AdminStoryController::class, 'destroy'])->name('stories.destroy');

    Route::get('/plants', [AdminPlantController::class, 'index'])->name('plants.index');
    Route::post('/plants', [AdminPlantController::class, 'store'])
        ->middleware('throttle:uploads')
        ->name('plants.store');
    Route::put('/plants/{plant}', [AdminPlantController::class, 'update'])
        ->middleware('throttle:uploads')
        ->name('plants.update');
    Route::delete('/plants/{plant}', [AdminPlantController::class, 'destroy'])->name('plants.destroy');

    Route::get('/recipes', [AdminRecipeController::class, 'index'])->name('recipes.index');
    Route::post('/recipes', [AdminRecipeController::class, 'store'])->name('recipes.store');
    Route::put('/recipes/{recipe}', [AdminRecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [AdminRecipeController::class, 'destroy'])->name('recipes.destroy');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/admin', [AdminUserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::patch('/users/{user}/block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');
});
