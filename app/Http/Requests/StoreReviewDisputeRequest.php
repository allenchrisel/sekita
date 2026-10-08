<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class StoreReviewDisputeRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeTextFields(['reason', 'evidence_details']);
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:160'],
            'evidence_details' => ['nullable', 'string', 'max:1500'],
        ];
    }
}