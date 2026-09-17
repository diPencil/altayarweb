<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['name', 'profession', 'city', 'comment'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'profession' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'comment' => 'required|string|min:10|max:3000',
            'rating' => 'required|integer|between:1,5',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'الاسم', 'profession' => 'المهنة', 'city' => 'المدينة', 'comment' => 'التعليق', 'rating' => 'التقييم'];
    }
}
