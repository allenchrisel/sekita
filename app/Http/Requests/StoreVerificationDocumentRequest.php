<?php

namespace App\Http\Requests;

use App\Enums\DocumentType;
use App\Http\Requests\SanitizedFormRequest;
use Illuminate\Validation\Rule;

class StoreVerificationDocumentRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', Rule::enum(DocumentType::class)],
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}