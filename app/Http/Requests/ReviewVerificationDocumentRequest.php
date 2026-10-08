<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;
use Illuminate\Validation\Rule;

class ReviewVerificationDocumentRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeTextFields(['rejection_reason']);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['VERIFIED', 'REJECTED'])],
            'rejection_reason' => ['nullable', 'required_if:status,REJECTED', 'string', 'max:1000'],
        ];
    }
}