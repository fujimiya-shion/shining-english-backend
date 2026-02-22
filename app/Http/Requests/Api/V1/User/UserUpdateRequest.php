<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birthday' => ['nullable', 'date'],
            'avatar' => ['nullable', 'string'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'birthday.date' => 'Birthday must be a valid date.',
            'city_id.integer' => 'City id must be an integer.',
            'city_id.exists' => 'City not found.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    }
}
