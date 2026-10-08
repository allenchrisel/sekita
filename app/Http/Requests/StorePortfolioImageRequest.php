<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;

class StorePortfolioImageRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeTextFields(['caption']);
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'caption' => ['nullable', 'string', 'max:160'],
        ];
    }
}