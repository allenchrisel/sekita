<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends SanitizedFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->sanitizeTextFields(['name', 'email']);
        $this->merge(['email' => mb_strtolower((string) $this->input('email'))]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
