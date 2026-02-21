<?php

namespace App\Http\Requests\Api\V1\QuizAttempt;

use Illuminate\Foundation\Http\FormRequest;

class QuizAttemptStoreRequest extends FormRequest
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
            'score_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'passed' => ['required', 'boolean'],
            'submitted_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'score_percent.required' => 'Score is required.',
            'score_percent.numeric' => 'Score must be a number.',
            'score_percent.min' => 'Score must be at least 0.',
            'score_percent.max' => 'Score must be at most 100.',
            'passed.required' => 'Passed is required.',
            'passed.boolean' => 'Passed must be true or false.',
            'submitted_at.date' => 'Submitted at must be a valid datetime.',
        ];
    }
}
