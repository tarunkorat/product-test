<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Product Routes
Route::get('/', [ProductController::class, 'index']);
