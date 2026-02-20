<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        // Get the customer ID from the route. Assumes route model binding is used.
        // If not, you might need to get it from $this->route('customer') or similar.
        $customerId = $this->route('customer')->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'address' => ['nullable', 'string'],
        ];
    }
}
