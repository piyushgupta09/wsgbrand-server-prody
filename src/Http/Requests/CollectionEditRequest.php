<?php

namespace Fpaipl\Prody\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Fpaipl\Prody\Models\Collection;

class CollectionEditRequest extends FormRequest
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
        $defaultValidation = Collection::validationRules();
        $specificValidation = [
            'name' => ['required', Rule::unique('collections')->ignore($this->model)], //, 'unique:collections'
        ];
        return array_merge($specificValidation, $defaultValidation);
    }
}
