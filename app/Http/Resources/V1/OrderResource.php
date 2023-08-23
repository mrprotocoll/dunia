<?php

namespace App\Http\Resources\V1;

use App\Models\BillingAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->order_number,
            'shippingPrice' => $this->shipping_price > 1 ? number_format($this->shipping_price,2) : null,
            'billingAddress' => $this->billing_address_id ? BillingAddress::find($this->billing_address_id) : "",
            'totalPrice' => number_format($this->total_price,2),
            'status' => strtolower($this->status),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'cart' => ResourceCollection::collection($this->products),
            'customer' => new UserResource($this->user_id)
        ];
    }
}
