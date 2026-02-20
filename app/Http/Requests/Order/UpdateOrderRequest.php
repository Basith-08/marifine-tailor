<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
            'tanggal_masuk' => ['required', 'date'],
            'deadline' => ['required', 'date', 'after:tanggal_masuk'],
            'jenis_pakaian' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(array_column(OrderStatus::cases(), 'value'))],
        ];
    }
}
