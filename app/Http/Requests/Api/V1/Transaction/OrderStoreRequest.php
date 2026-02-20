<?php

namespace App\Http\Requests\Api\V1\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:cart,buy_now'],
            'payment_method' => ['nullable', 'string', 'in:cod,payos'],
            'course_id' => ['required_if:type,buy_now', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Order type is required.',
            'type.in' => 'Order type must be cart or buy_now.',
            'payment_method.in' => 'Payment method must be cod or payos.',
            'course_id.required_if' => 'Course id is required for buy now.',
            'course_id.integer' => 'Course id must be an integer.',
            'quantity.integer' => 'Quantity must be an integer.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
