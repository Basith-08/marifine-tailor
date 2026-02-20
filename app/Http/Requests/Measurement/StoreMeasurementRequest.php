<?php

namespace App\Http\Requests\Measurement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMeasurementRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')],
            'shoulder' => ['nullable', 'numeric'],
            'chest' => ['nullable', 'numeric'],
            'waist' => ['nullable', 'numeric'],
            'sleeve' => ['nullable', 'numeric'],
            'other_measurements' => ['nullable', 'array'],
        ];
    }
}
