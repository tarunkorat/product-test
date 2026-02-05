<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Product Routes
Route::get('/', [ProductController::class, 'index']);
Route::get('/products/list', [ProductController::class, 'list']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/products/render', [ProductController::class, 'render']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);

