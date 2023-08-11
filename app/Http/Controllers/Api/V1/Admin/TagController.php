<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\TagRequest;
use App\Http\Resources\V1\TagResource;
use App\Models\Tag;

/**
 * @group Tags Management
 *
 * Endpoint to manage tags
 */
class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     *
     * @apiResourceCollection App\Http\Resources\V1\TagResource
     * @apiResourceModel App\Models\Tag
     *
     * @return TagResource
     */
    public function index(): TagResource
    {
        //
        return TagResource::collection(Tag::paginate());
    }

    /**
     * Create a new Tag.
     *
     * @apiResource App\Http\Resources\V1\TagResource
     * @apiResourceModel App\Models\Tag
     *
     * @param TagRequest $request
     * @return TagResource
     */
    public function store(TagRequest $request)
    {
        //
        return new TagResource(Tag::create($request->validated()));
    }

    /**
     * Display the specific tag by ID.
     *
     * @apiResource App\Http\Resources\V1\TagResource
     * @apiResourceModel App\Models\Tag
     *
     * @param Tag $tag
     * @return TagResource
     */
    public function show(Tag $tag): TagResource
    {
        //
        return new TagResource($tag);
    }

    /**
     * Update a specific tag by ID.
     *
     * @apiResource App\Http\Resources\V1\TagResource
     * @apiResourceModel App\Models\Tag
     *
     * @param TagRequest $request
     * @param Tag $tag
     * @return TagResource
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
