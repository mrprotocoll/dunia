<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductListRequest;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Author;
use App\Models\Category;
use App\Models\Product;

/**
 * @group Product Management
 *
 * Endpoints to manage products
 */
class ProductController extends Controller
{

    /**
     * Retrieve a paginated list of products with optional filters.
     *
     * @param ProductListRequest $request The product index request.
     *
     * @queryParam name string Search for products by name (partial match).
     * @queryParam author string Search for products by author's name.
     * @queryParam category string Search for products by category name.
     *
     * @response {
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Product A",
     *             "author_id": 1,
     *             "price": 19.99,
     *             "description": "Description of Product A",
     *             "category": {
     *                 "id": 2,
     *                 "name": "Category X"
     *             },
     *             "author": {
     *                 "id": 1,
     *                 "name": "Author Y"
     *             }
     *         },
     *         {
     *             "id": 2,
     *             "name": "Product B",
     *             "author_id": 2,
     *             "price": 29.99,
     *             "description": "Description of Product B",
     *             "category": {
     *                 "id": 3,
     *                 "name": "Category Z"
     *             },
     *             "author": {
     *                 "id": 2,
     *                 "name": "Author Z"
     *             }
     *         }
     *     ],
     *     "links": {
     *         "first": "https://api.example.com/products?page=1",
     *         "last": "https://api.example.com/products?page=3",
     *         "prev": null,
     *         "next": "https://api.example.com/products?page=2"
     *     },
     *     "meta": {
     *         "current_page": 1,
     *         "from": 1,
     *         "last_page": 3,
     *         "path": "https://api.example.com/products",
     *         "per_page": 10,
     *         "to": 10,
     *         "total": 25
     *     }
     * }
     * @apiResourceCollection App\Http\Resources\V1\ProductResource
     * @apiResourceModel App\Models\Product
     * @return ProductResource
     */
    public function index(ProductRequest $request): ProductResource
    {
        //
        $products = Product::with(['category', 'author'])
            ->when($request->name, function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            })
            ->when($request->author, function ($query) use ($request) {
                $author = Author::where('name', $request->input('author'))->first();
                if($author) {
                    $query->where('author_id', $author->id);
                }
            })
            ->when($request->category, function ($query) use ($request) {
                $category = Category::where('name', $request->category)->first();
                if($category) {
                    $query->whereHas('categories', function ($q) use ($category) {
                        $q->where('category_id', $category->id);
                    });
                }
            })
            ->paginate();
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
