<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductFilterRequest extends FormRequest
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
        // ensure at least one of the request param is present
        return [
            'release_date' => ['nullable', Rule::in(['ASC', 'DESC'])],
            'age' => ['nullable', 'exists:age_ranges,id'],
            'price' => ['nullable', Rule::in(['ASC', 'DESC'])],
        ];
    }
}
