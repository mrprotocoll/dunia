<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductFilterRequest;
use App\Http\Requests\V1\ProductListRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Author;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;

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
     *
     * @return ResourceCollection
     */
    public function index(ProductListRequest $request): ResourceCollection
    {
        $products = Product::with(['categories', 'author'])
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

    /**
     * Get product by category
     *
     * Retrieve paginated list of products within a specific category.
     *
     * @param Category $category The category to retrieve products from.
     *
     * @response {
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Product Name",
     *             "author": {...},
     *             "price": 19.99,
     *             "description": "Product description",
     *         },
     *         ...
     *     ],
     *     "links": {
     *         "first": "...",
     *         "last": "...",
     *         "prev": null,
     *         "next": "..."
     *     },
     *     "meta": {
     *         "current_page": 1,
     *         "from": 1,
     *         "last_page": 3,
     *         "path": "...",
     *         "per_page": 10,
     *         "to": 10,
     *         "total": 30
     *     }
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse | ResourceCollection
     */
    public function categories(Category $category) {
        try {
            // Retrieve paginated products within the specified category
            $products = $category->products()->paginate();
            return ProductResource::collection($products);
        }
        catch (\Exception $exception) {
            return response()->json(['message' =>'Oops something went wrong'], 500);
        }
    }

    /**
     * Filter Products
     * Retrieve a paginated list of products based on filtering criteria.
     *
     * @queryParam release_date string|null The release date sorting order (ASC or DESC).
     * @queryParam age int|null The ID of the age range to filter products by.
     * @queryParam price string|null The price sorting order (ASC or DESC).
     *
     * @response {
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Product Name",
     *             "author": {...},
     *             "price": 19.99,
     *             "description": "Product description",
     *             "categories": [...],
     *             "age_range": {...}
     *         },
     *     ],
     *     "links": {
     *         "first": "...",
     *         "last": "...",
     *         "prev": null,
     *         "next": "..."
     *     },
     *     "meta": {
     *         "current_page": 1,
     *         "from": 1,
     *         "last_page": 3,
     *         "path": "...",
     *         "per_page": 10,
     *         "to": 10,
     *         "total": 30
     *     }
     * }
     * @response 422 {
     *     "errors": {
     *         "release_date": ["The selected release date is invalid."],
     *         "age": ["The selected age is invalid."],
     *         "price": ["The selected price is invalid."]
     *     }
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @param ProductFilterRequest $request The request object containing filter parameters.
     * @return \Illuminate\Http\JsonResponse | ResourceCollection
     */
    public function filter(ProductFilterRequest $request) {
        try {
            if (!filled($request->release_date) && !filled($request->age) && !filled($request->price)) {
                return response()->json(['message' => "At least One of the fields must be filled"], 400);
            }
            $products = Product::with(['categories', 'author', 'age_range'])
                ->when($request->age, function ($query) use ($request) {
                    $query->where('age_range_id', $request->age);
                })
                ->when($request->release_date, function ($query) use ($request) {
                    $query->orderBy('created_at', $request->release_date );
                })
                ->when($request->price, function ($query) use ($request) {
                    $query->orderBy('price', $request->price );
                })->paginate();
            return ProductResource::collection($products);
        }
        catch (ValidationException $validationException) {
            return response()->json(['errors' => $validationException->errors()], 422);
        }
        catch (\Exception $exception) {
            return response()->json(['message' =>'Oops something went wrong'], 500);
        }
    }
}
