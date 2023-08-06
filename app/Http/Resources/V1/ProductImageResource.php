<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Return the formatted product image data in the API response
        return [
            'id' => $this->id,
            'url' => asset('storage/' . $this->image), // Return the full URL to the image
        ];
    }
}
