<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;

/**
 * @group Product Management
 *
 * Endpoints to manage products
 */
class ProductController extends Controller
{
    /**
     * Display a listing of products with ID.
     */
    public function index()
    {
        //
        $products = Product::with('author')->paginate();
        return ProductResource::collection($products);
    }

    /**
     * Display a specific product by ID.
     */
    public function show(Product $product)
    {
        //
        $product->with('author')->get();
        return new ProductResource($product);
    }
}
