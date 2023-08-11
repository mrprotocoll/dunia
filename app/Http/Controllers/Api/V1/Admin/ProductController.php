<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

/**
 * @group Product Management
 *
 * Endpoints to manage products
 */
class ProductController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @authenticated
     * @bodyParam name string required The name of the product.
     * @bodyParam author integer required The ID of the author of the product.
     * @bodyParam price float required The price of the product.
     * @bodyParam description string required The description of the product.
     * @bodyParam categories array required An array of category IDs associated with the product.
     * @bodyParam tags array required An array of tag IDs associated with the product.
     * @bodyParam images[] file An array of image files for the product.
     *
     * @response {
     *      "data": {
     *          "id": 1,
     *          "name": "Sample Product",
     *          "author_id": 1,
     *          "price": 19.99,
     *          "description": "A description of the sample product.",
     *          "categories": [
     *              {
     *                  "id": 1,
     *                  "name": "Category A"
     *              },
     *              {
     *                  "id": 2,
     *                  "name": "Category B"
     *              }
     *          ],
     *          "tags": [
     *              {
     *                  "id": 1,
     *                  "name": "Tag X"
     *              },
     *              {
     *                  "id": 2,
     *                  "name": "Tag Y"
     *              }
     *          ]
     *      }
     *  }
     * @apiResource App\Http\Resources\V1\ProductResource
     * @apiResourceModel App\Models\Product
     * @param ProductRequest $request
     * @return ProductResource
     */
    public function store(ProductRequest $request): ProductResource
    {
        // initialise columns
        $product = new Product();
        $product->name = $request->name;
        $product->author_id = $request->author;
        $product->price = $request->price;
        $product->description = $request->description;
        $categories = Category::whereIn('id', $request->categories)->get();
        $tags = Tag::whereIn('id', $request->tags)->get();

        DB::transaction(function () use ($product, $categories, $tags, $request){
            $product->save();
            // save categories
            $product->categories()->attach($categories);

            // save tags
            $product->tags()->attach($tags);

            // Store multiple images for the product
            if ($request->hasFile('images')) {
                $product->addImages($request);
            }
        });

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @authenticated
     *
     * @apiResource App\Http\Resources\V1\ProductResource
     * @apiResourceModel App\Models\Product
     * @param ProductRequest $request
     * @return ProductResource
     */
    public function update(Product $product, ProductRequest $request): ProductResource
    {
        //
        $product->name = $request->name;
        $product->author_id = $request->author;
        $product->price = $request->price;
        $product->description = $request->description;
        $categories = Category::whereIn('id', $request->categories)->get();
        $tags = Tag::whereIn('id', $request->tags)->get();

        DB::transaction(function () use ($product, $categories, $tags, $request){
            $product->save();
            // save categories
            $product->categories()->sync($categories);

            // save tags
            $product->tags()->sync($tags);
        });

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
