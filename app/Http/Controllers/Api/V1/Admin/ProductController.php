<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @group Product Management
 *
 * Endpoints to manage products
 */
class ProductController extends Controller
{
    /**
     * Store a new product with related information and files.
     *
     * @param ProductRequest $request The product creation request.
     *
     * @bodyParam name string required The name of the product.
     * @bodyParam author int required The ID of the product's author.
     * @bodyParam price float required The price of the product.
     * @bodyParam description string required The description of the product.
     * @bodyParam weight float required The weight of the product.
     * @bodyParam print_price float required The printing price of the product.
     * @bodyParam preview file The preview file of the product.
     * @bodyParam product_file file The main product file.
     * @bodyParam categories array required The array of category IDs associated with the product.
     * @bodyParam tags array required The array of tag IDs associated with the product.
     * @bodyParam images[] file An array of additional images for the product (if applicable).
     *
     * @response {
     *     "data": {
     *         "id": 1,
     *         "name": "Product A",
     *         "author_id": 2,
     *         "price": 19.99,
     *         "description": "Description of Product A",
     *         "weight": 0.5,
     *         "print_price": 5.99,
     *         "preview": "book_previews/product_a_preview.jpg",
     *         "product_file": "books/product_a_file.pdf",
     *         "categories": [...],
     *         "tags": [...],
     *         "images": [...]
     *     }
     * }
     * @response 500 {
     *     "error": "Error occurred. Try again"
     * }
     *
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
        $product->weight = $request->weight;
        $product->print_price = $request->print_price;
        $product->preview = $request->file('preview');
        $product->product_file = $request->file('product_file');
        $categories = Category::whereIn('id', $request->categories)->get();
        $tags = Tag::whereIn('id', $request->tags)->get();


        DB::transaction(function () use ($product, $categories, $tags, $request){
            // Handle BOOK preview upload
            if ($request->hasFile('preview')) {
                $previewName = "book_previews/".FileHelper::formatName($request->file('preview')->getClientOriginalName());
                $product->preview = $previewName;
            }

            // Handle product file upload
            if ($request->hasFile('product_file')) {
                $productFileName = "books/".FileHelper::formatName($request->file('product_file')->getClientOriginalName());
                $product->product_file = $productFileName;
            }

            // Save product
            if ($product->save()) {
                Storage::disk('public')->put($previewName, file_get_contents($request->file('preview')));
                Storage::disk('public')->put($productFileName, file_get_contents($request->file('product_file')));
            }else{
                // Handle failed save
                return response()->json(['Error occurred. Try again'], 500);
            }

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
