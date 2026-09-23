<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'overall_rating' => ['required', 'integer', 'between:1,5'],
            'feedback_type' => ['required', Rule::in(['compliment', 'suggestion', 'complaint', 'query'])],
            'category_id' => ['nullable', 'integer', 'exists:feedback_categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:feedback_subcategories,id'],
            'comment' => ['nullable', 'string', 'max:500'],

            'is_anonymous' => ['required', 'boolean'],

            'name' => ['nullable', 'string', 'max:100'],
            'mobile' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'preferred_contact_method' => ['nullable', Rule::in(['email', 'mobile', 'none'])],

            'flight_number' => ['nullable', 'string', 'max:10'],
            'travel_date' => ['nullable', 'date', 'before_or_equal:today'],

            'voice' => ['nullable', 'file', 'max:10240', 'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/wav,application/octet-stream'],
            'photos' => ['nullable', 'array', 'max:2'],
            'photos.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('email')) {
            $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
        }

        if ($this->filled('name')) {
            $this->merge([
                'name' => preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $this->input('name')),
            ]);
        }

        if ($this->filled('mobile')) {
            $this->merge([
                'mobile' => preg_replace('/\D+/', '', (string) $this->input('mobile')),
            ]);
        }

        if ($this->has('is_anonymous')) {
            $this->merge(['is_anonymous' => (bool) $this->input('is_anonymous')]);
        }
    }
}
