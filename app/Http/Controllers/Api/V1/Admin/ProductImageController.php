<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductImageRequest;
use App\Http\Resources\V1\ProductImageResource;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;

class ProductImageController extends Controller
{

    public function store(ProductImageRequest $request, Product $product): ProductResource {
        if ($request->hasFile('images')) {
            $product->addImages($request);
        }

        return new ProductResource($product);
    }

    public function destroy( Product $product, ProductImage $productImage) {
        $productImage->delete();
        return new ProductImageResource($productImage);
    }
}
