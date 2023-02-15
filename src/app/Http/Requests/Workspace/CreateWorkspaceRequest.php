<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;

class CreateWorkspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:100', 'regex:/\A(?!-)([0-9a-z-]){1,100}(?<!-)\z/'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_public' => ['required', 'boolean'],
        ];
    }
}
