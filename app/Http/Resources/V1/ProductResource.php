<?php

namespace App\Http\Resources\V1;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'weight' => $this->weight,
            'price' => number_format($this->price, 2),
            'description' => $this->description,
            'author' => new AuthorResource($this->author),
            'categories' => CategoryResource::collection($this->categories),
            'tags' => TagResource::collection($this->tags),
            'images' => ProductImageResource::collection($this->images),
            'reviews' => ReviewResource::collection($this->reviews),
            'preview' => asset('storage/' . $this->preview),
        ];
    }
}
