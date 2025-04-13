<?php
/**
 * @author  Alexander Vakhovski (AlexWaha)
 * @link    https://alexwaha.com
 * @email   support@alexwaha.com
 * @license MIT
 */

use Illuminate\Support\Facades\Route;


Route::post('admin', [AdminController::class, 'index'])->name('admin');

Route::localizedRoutes(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('about', [AboutController::class, 'index'])->name('about');

    Route::name('blog.')->prefix('/blog')->group(function () {
        Route::get('', [BlogController::class, 'index'])->name('index');
        Route::get('/page/{page?}', [BlogController::class, 'index'])->name('paginated');
        Route::get('/{post}', [BlogController::class, 'show'])->name('show');
    });
}, ['web', 'localize.setLocale', 'localize.paginated']);

