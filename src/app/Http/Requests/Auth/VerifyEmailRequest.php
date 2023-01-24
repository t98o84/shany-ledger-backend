<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hash' => ['required', 'string', 'max:100'],
            'expiration' => ['required', 'integer', 'min:0', 'regex:/^[0-9]+$/'],
            'signature' => ['required', 'string', 'max:100'],
        ];
    }
}
