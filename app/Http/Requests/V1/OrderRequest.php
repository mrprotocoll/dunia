<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
            'cart' => ['required', 'array'],
            'cart.*.product_id' => ['required', 'exists:products,id'],
            'cart.*.quantity' => ['required', 'numeric', 'gt:0'],
            'shipping' => ['array'],
            'shipping.price' => ['required_if:shipping,*'],
            'shipping.destination' => ['required_if:shipping,*','exists:billing_addresses,id']
        ];
    }
}
