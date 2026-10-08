<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class StoreProviderReplyRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeTextFields(['reply_text']);
    }

    public function rules(): array
    {
        return ['reply_text' => ['required', 'string', 'min:2', 'max:500']];
    }
}