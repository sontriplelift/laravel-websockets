<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::apiResource('users', UserController::class);

// Route::group([
//     'middleware' => ['auth'],
//     'prefix' => 'yee',
//     'name' => 'users.'
// ], function() {
//     Route::get('/users', [UserController::class, 'index'])->name('index');
//     Route::get('/users/{user}', [UserController::class, 'show'])->name('show');
//     Route::post('/users', [UserController::class, 'store'])->name('store');
//     Route::patch('/users/{user}', [UserController::class, 'update'])->name('update');
//     Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('destroy');
// });

// Route::middleware(['auth'])
Route::name('users.')
    ->group(function() {
        // Route::get('/users', [UserController::class, 'index'])->name('index')->withoutMiddleware('auth');
        Route::get('/users', [UserController::class, 'index'])->name('index');
        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('show')
            // ->where('user', '[0-9]+');
            ->whereNumber('user');
        Route::post('/users', [UserController::class, 'store'])->name('store');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
