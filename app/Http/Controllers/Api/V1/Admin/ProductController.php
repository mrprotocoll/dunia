<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        //
        $product = new Product();
        $product->name = $request->name;
        $product->author_id = $request->author;
        $product->price = $request->price;
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
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . Str::slug($image->getClientOriginalName(), '_');

                    // Store the image in the 'public' disk (storage/app/public)
                    Storage::disk('public')->put($imageName, file_get_contents($image));

                    // Save the image file name in the database
                    $product->images()->create(['name' => $imageName]);
                }
            }
        });

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
