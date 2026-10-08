<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class SanitizedFormRequest extends FormRequest
{
    protected function sanitizeTextFields(array $fields): void
    {
        foreach ($fields as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $this->merge([$field => trim(strip_tags($value))]);
            }
        }
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            parent::failedValidation($validator);
        }

        throw new HttpResponseException(response()->view(
            'errors.validation',
            ['messages' => $validator->errors()->all()],
            422,
        ));
    }
}