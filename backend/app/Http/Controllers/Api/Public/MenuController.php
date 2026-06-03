<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;

class MenuController extends Controller
{
    public function categories()
    {
        // Hanya tampilkan kategori yang aktif
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    public function products()
    {
        // Hanya tampilkan produk yang aktif beserta kategorinya
        $products = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->get();

        return ProductResource::collection($products);
    }
}
