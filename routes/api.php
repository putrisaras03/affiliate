<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/produk/fetch/{accountId}', [ShopeeController::class, 'fetchProductsFromShopee'])
    ->name('produk.fetch');

// Route::apiResource('/products', ProductController::class);
