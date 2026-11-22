<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home', [
    'appName' => config('app.name'),
])->name('home');
