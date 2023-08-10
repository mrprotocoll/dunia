<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\TagRequest;
use App\Http\Resources\V1\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

/**
 * @group Tags Management
 *
 * Endpoint to manage tags
 */
class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index()
    {
        //
        return TagResource::collection(Tag::paginate());
    }

    /**
     * Create a new Tag.
     */
    public function store(TagRequest $request)
    {
        //
        return new TagResource(Tag::create($request->validated()));
    }

    /**
     * Display the specific tag by ID.
     */
    public function show(Tag $tag)
    {
        //
        return new TagResource($tag);
    }

    /**
     * Update a specific tag by ID.
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());
        return new TagResource(Tag::find($tag->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        //
    }

}
