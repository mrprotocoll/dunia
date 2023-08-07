<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    /**
     * Store a newly created resource in storage.
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
