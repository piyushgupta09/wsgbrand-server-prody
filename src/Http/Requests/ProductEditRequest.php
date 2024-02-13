<?php

namespace Fpaipl\Prody\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Fpaipl\Prody\Models\Product;

class ProductEditRequest extends FormRequest
{    
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $defaultValidation = Product::validationRules();
        $specificValidation = [
            'code' => ['required', Rule::unique('products')->ignore($this->model)],
        ];
        return array_merge($specificValidation, $defaultValidation);
    }
}
