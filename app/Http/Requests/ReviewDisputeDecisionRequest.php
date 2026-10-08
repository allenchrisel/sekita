<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;
use Illuminate\Validation\Rule;

class ReviewDisputeDecisionRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(['APPROVED', 'REJECTED'])]];
    }
}