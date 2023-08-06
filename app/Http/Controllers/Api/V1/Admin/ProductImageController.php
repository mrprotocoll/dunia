<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Client\Request;

class ProductImageController extends Controller
{

    public function store(Request $request, Product $product) {
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'max:2048'],
        ]);

        if ($request->hasFile('images')) {
            $product->addImages($request);
        }
    }
}
