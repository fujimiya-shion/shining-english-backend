<?php

namespace App\Http\Requests\Api\V1\Course;

use Illuminate\Foundation\Http\FormRequest;

class CourseFilterRequest extends FormRequest
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
            'category_id' => ['nullable', 'integer'],
            'status' => ['nullable', 'boolean'],
            'price_min' => ['nullable', 'integer', 'min:0'],
            'price_max' => ['nullable', 'integer', 'min:0'],
            'rating_min' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rating_max' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'learned_min' => ['nullable', 'integer', 'min:0'],
            'learned_max' => ['nullable', 'integer', 'min:0'],
            'q' => ['nullable', 'string'],
            'name' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.integer' => 'Category id must be an integer.',
            'status.boolean' => 'Status must be a boolean.',
            'price_min.integer' => 'Minimum price must be an integer.',
            'price_max.integer' => 'Maximum price must be an integer.',
            'rating_min.numeric' => 'Minimum rating must be a number.',
            'rating_max.numeric' => 'Maximum rating must be a number.',
            'learned_min.integer' => 'Minimum learned must be an integer.',
            'learned_max.integer' => 'Maximum learned must be an integer.',
            'page.integer' => 'Page must be an integer.',
            'perPage.integer' => 'Per page must be an integer.',
        ];
    }
}
