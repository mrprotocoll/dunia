<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AuthorRequest;
use App\Http\Resources\V1\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @group Author Management
 *
 * Endpoint to manage authors
 */
class AuthorController extends Controller
{
    /**
     * Display a listing of the authors.
     *
     * @authenticated
     *
     * @apiResourceCollection App\Http\Resources\V1\AuthorResource
     * @apiResourceModel App\Models\Author
     *
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        //
        $authors = Author::paginate();
        return AuthorResource::collection($authors);
    }

    /**
     * Create a new author.
     *
     * @authenticated
     *
     * @bodyParam name string required Name of author. Example: Isaac Johnson
     * @bodyParam email string required Email of author.
     *
     * @apiResource App\Http\Resources\V1\AuthorResource
     * @apiResourceModel \App\Models\Author
     * @param AuthorRequest $request
     * @return AuthorResource
     */
    public function store(AuthorRequest $request) : AuthorResource
    {
        //
        $author = Author::create($request->validated());
        return new AuthorResource($author);
    }

    /**
     * Display an author by ID.
     * @authenticated
     * @apiResource App\Http\Resources\V1\AuthorResource
     * @apiResourceModel \App\Models\Author
     * @urlParam id string required Author ID
     *
     * @param Author $author
     * @return AuthorResource
     */
    public function show(Author $author) : AuthorResource
    {
        //
        return new AuthorResource($author);
    }

    /**
     * Update the specified Author by ID.
     *
     * @bodyParam name string required Name of author. Example: Isaac Johnson
     * @bodyParam email string required Email of author.
     *
     * @authenticated
     *
     * @apiResource App\Http\Resources\V1\AuthorResource
     * @apiResourceModel App\Models\Author
     * @param AuthorRequest $request
     * @param Author $author
     * @return AuthorResource
     */
    public function update(AuthorRequest $request, Author $author): AuthorResource
    {
        //
        $author->update($request->validated());
        return new AuthorResource(Author::find($author->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        //
    }
}
