<?php

namespace Fpaipl\Prody\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fpaipl\Prody\Models\Material;

class MaterialRequest extends FormRequest
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
        $rules = Material::validationRules();
    
        // If the request is updating an existing material, modify the unique rule for 'sid'
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $materialId = $this->route('material')->id; // Retrieve the ID of the material being updated
            $rules['sid'] = ['required', 'unique:materials,sid,' . $materialId];
        }
    
        return $rules;
    }    
}
