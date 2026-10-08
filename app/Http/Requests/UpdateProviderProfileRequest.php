<?php

namespace App\Http\Requests;

use App\Http\Requests\SanitizedFormRequest;
use Illuminate\Validation\Rule;

class UpdateProviderProfileRequest extends SanitizedFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->sanitizeTextFields([
            'title', 'bio', 'whatsapp_number', 'instagram_url', 'website_url', 'address',
        ]);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'province_code' => ['required', 'string', Rule::exists('provinces', 'code')],
            'regency_code' => ['required', 'string', Rule::exists('regencies', 'code')->where('province_code', $this->input('province_code'))],
            'district_code' => ['required', 'string', Rule::exists('districts', 'code')->where('regency_code', $this->input('regency_code'))],
            'title' => ['required', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1500'],
            'starting_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'whatsapp_number' => ['required', 'string', 'max:20', 'regex:/^[0-9+().\s-]{7,20}$/'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}