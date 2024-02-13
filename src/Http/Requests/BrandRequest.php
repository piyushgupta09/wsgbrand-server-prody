<?php

namespace Fpaipl\Prody\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fpaipl\Prody\Models\Brand;

class BrandRequest extends FormRequest
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
        $rules = Brand::validationRules();
    
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $modelId = $this->route('brand')->name;
            $rules['name'] = ['required', 'unique:brands,name,' . $modelId];
        }
    
        return $rules;
    }   
}
