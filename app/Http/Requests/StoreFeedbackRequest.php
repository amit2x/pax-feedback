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
            // Idempotency key — REQUIRED, generated client-side per wizard instance
            'submission_uuid' => ['required', 'uuid'],

            // Core answers
            'overall_rating' => ['required', 'integer', 'between:1,5'],
            'feedback_type' => ['required', Rule::in(['compliment', 'suggestion', 'complaint', 'query'])],

            // Optional categorization — validated against real DB rows
            'category_id' => ['nullable', 'integer', 'exists:feedback_categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:feedback_subcategories,id'],

            // Comment — max 500 characters, no HTML
            'comment' => ['nullable', 'string', 'max:500'],

            // Anonymous flag
            'is_anonymous' => ['required', 'boolean'],

            // Contact details (required only if NOT anonymous)
            'name' => ['nullable', 'string', 'max:100'],
            'mobile' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'preferred_contact_method' => ['nullable', Rule::in(['email', 'mobile', 'none'])],

            // Flight info (optional)
            'flight_number' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z0-9]{2,3}\d{1,4}$/i'],
            'travel_date' => ['nullable', 'date', 'before_or_equal:today'],

            // Uploads
            'voice' => [
                'nullable',
                'file',
                'max:10240',
                'mimetypes:audio/webm,audio/ogg,audio/mpeg,audio/wav,audio/mp4,video/webm,application/octet-stream',
            ],
            'photos' => ['nullable', 'array', 'max:2'],
            'photos.*' => [
                'file',
                'max:5120',
                // Use mimetypes (finfo-verified) not mimes (extension-based)
                'mimetypes:image/jpeg,image/png,image/webp',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalize email
        if ($this->filled('email')) {
            $this->merge(['email' => strtolower(trim((string) $this->input('email')))]);
        }

        // Strip control characters from name
        if ($this->filled('name')) {
            $this->merge([
                'name' => preg_replace('/[\x00-\x1F\x7F]/u', '', (string) $this->input('name')),
            ]);
        }

        // Normalize mobile — strip non-digits
        if ($this->filled('mobile')) {
            $this->merge([
                'mobile' => preg_replace('/\D+/', '', (string) $this->input('mobile')),
            ]);
        }

        // Cast is_anonymous to bool
        if ($this->has('is_anonymous')) {
            $this->merge([
                'is_anonymous' => filter_var($this->input('is_anonymous'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        // If anonymous, strip contact fields
        if ($this->boolean('is_anonymous')) {
            $this->merge([
                'name' => null,
                'mobile' => null,
                'email' => null,
                'preferred_contact_method' => 'none',
            ]);
        }
    }

    /**
     * Extra rule: if not anonymous, at least one contact method must be present.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($this->boolean('is_anonymous')) {
                return;
            }

            $hasContact = $this->filled('mobile') || $this->filled('email');
            if (! $hasContact) {
                $v->errors()->add(
                    'mobile',
                    'Please provide a mobile number or email so we can contact you.'
                );
            }
        });
    }
}
