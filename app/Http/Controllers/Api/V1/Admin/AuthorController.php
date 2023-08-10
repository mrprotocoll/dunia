<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AuthorRequest;
use App\Http\Resources\V1\AuthorResource;
use App\Models\Author;

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
     * @apiResourceCollection App\Http\Resources\V1\AuthorResource
     * @apiResourceModel App\Models\Author
     */
    public function index(): AuthorResource
    {
        //
        $authors = Author::paginate();
        return AuthorResource::collection($authors);
    }

    /**
     * Create a new author.
     */
    public function store(AuthorRequest $request) : AuthorResource
    {
        //
        $author = Author::create($request->validated());
        return new AuthorResource($author);
    }

    /**
     * Display an author by ID.
     *
     * return
     */
    public function show(Author $author) : AuthorResource
    {
        //
        return new AuthorResource($author);
    }

    /**
     * Update the specified Author by ID.
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
