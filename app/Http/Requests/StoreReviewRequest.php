<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;
use App\Services\ReviewTextSanitizer;
use Illuminate\Contracts\Validation\Validator;

class StoreReviewRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'comment' => ReviewTextSanitizer::sanitize((string) $this->input('comment', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:8', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (ReviewTextSanitizer::containsForbiddenTerm((string) $this->input('comment'))) {
                $validator->errors()->add('comment', 'Ulasan mengandung kata yang tidak diizinkan.');
            }
        });
    }
}