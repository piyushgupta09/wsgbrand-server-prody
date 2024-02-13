<?php

namespace Fpaipl\Prody\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fpaipl\Prody\Models\Supplier;

class SupplierRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return Supplier::validationRules();
    }    
}
