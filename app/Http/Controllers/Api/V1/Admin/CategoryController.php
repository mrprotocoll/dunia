<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;

/**
 * @group Categories Management
 *
 * Endpoint to manage categories
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        //
        return CategoryResource::collection(Category::paginate());
    }

    /**
     * Create a new catgeory.
     */
    public function store(CategoryRequest $request)
    {
        //
        return new CategoryResource(Category::create($request->validated()));
    }

    /**
     * Display the specific catgeory by ID.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specific category.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return new CategoryResource(Category::find($category->id));
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        $deletedCategory = Category::find($category->id);
        $category->delete();
        return new CategoryResource($deletedCategory);
    }
}
