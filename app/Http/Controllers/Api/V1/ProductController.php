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
     * Display a listing of products
     *
     * @apiResourceCollection App\Http\Resources\V1\ProductResource
     * @apiResourceModel App\Models\Product
     *
     * @return ProductResource
     */
    public function index(): ProductResource
    {
        //
        $products = Product::with('author')->paginate();
        return ProductResource::collection($products);
    }

    /**
     * Display a specific product by ID.
     *
     * @apiResource App\Http\Resources\V1\ProductResource
     * @apiResourceModel App\Models\Product
     *
     * @urlParam id string required Product ID
     * @param Product $product
     * @return ProductResource
     */
    public function show(Product $product)
    {
        //
        $product->with('author')->get();
        return new ProductResource($product);
    }
}
