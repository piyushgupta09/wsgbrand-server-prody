<?php

namespace Fpaipl\Prody\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Fpaipl\Prody\Models\Measurement;

class MeasurementCreateRequest extends FormRequest
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
        $defaultValidation = Measurement::validationRules();
        $specificValidation = [
            'name' => ['required',],
        ];
        return array_merge($specificValidation, $defaultValidation);
    }
}
