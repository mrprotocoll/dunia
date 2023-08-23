<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'code' => $this->order_number,
            'shippingPrice' => $this->shipping_price > 1 ? number_format($this->shipping_price,2) : null,
            'billingAddress' => $this->billing_address_id ? new BillingAddressResource($this->billing_address_id) : "",
            'totalPrice' => number_format($this->total_price,2),
            'status' => strtolower($this->status),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'cart' => $this->cartDetails(),
            'customer' => new UserResource($this->whenLoaded('user'))
        ];
    }

    // Create a method to extract cart details
    private function cartDetails()
    {
        return $this->products->map(function ($product) {
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $product->pivot->quantity,
                'total_price' => $product->pivot->total_price,
            ];
        });
    }
}
