<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AuthorRequest;
use App\Http\Requests\V1\CategoryRequest;
use App\Http\Resources\V1\AuthorResource;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Author;
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
     *
     * @apiResourceCollection App\Http\Resources\V1\CategoryResource
     * @apiResourceModel App\Models\Category
     *
     * @return CategoryResource
     */
    public function index(): CategoryResource
    {
        //
        return CategoryResource::collection(Category::paginate());
    }

    /**
     * Create a new category.
     *
     * @bodyParam name string required Name of category
     *
     * @apiResource App\Http\Resources\V1\CategoryResource
     * @apiResourceModel App\Models\Category
     * @param CategoryRequest $request
     * @return CategoryResource
     */
    public function store(CategoryRequest $request): CategoryResource
    {
        //
        return new CategoryResource(Category::create($request->validated()));
    }

    /**
     * Display the specific category by ID.
     *
     * @apiResource App\Http\Resources\V1\CategoryResource
     * @apiResourceModel App\Models\Category
     * @urlParam id string required Category ID
     *
     * @param Category $category
     * @return CategoryResource
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specific category.
     *
     * @bodyParam name string required Name of category
     *
     * @apiResource App\Http\Resources\V1\CategoryResource
     * @apiResourceModel App\Models\Category
     *
     * @param CategoryRequest $request
     * @param Category $category
     * @return CategoryResource
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return new CategoryResource(Category::find($category->id));
    }

    /**
     * Delete a category.
     *
     * @apiResource App\Http\Resources\V1\CategoryResource
     * @apiResourceModel App\Models\Category
     * @urlParam id string required Category ID
     *
     * @param Category $category
     * @return CategoryResource Deleted category
     */
    public function destroy(Category $category)
    {
        $deletedCategory = Category::find($category->id);
        $category->delete();
        return new CategoryResource($deletedCategory);
    }
}
