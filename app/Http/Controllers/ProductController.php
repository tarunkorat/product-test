<?php

namespace App\Http\Controllers;

use App\Services\ProductStorage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(ProductStorage $storage)
    {
        return view('products.index', [
            'products' => $storage->all()
        ]);
    }
}
