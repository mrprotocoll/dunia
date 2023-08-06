<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            //
            'name' => ['required'],
            'images' => ['required', 'array'],
            'images.*' => ['image'],
            'price' => ['required'],
            'categories' => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
            'author' => ['required', 'exists:authors,id']
        ];
    }
}
