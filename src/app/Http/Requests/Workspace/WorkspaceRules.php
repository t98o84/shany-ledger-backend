<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;

class WorkspaceRules
{
    public static function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:100', 'regex:/\A(?!-)([0-9a-z-]){1,100}(?<!-)\z/'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_public' => ['required', 'boolean'],
        ];
    }

    public static function except(array $keys): array
    {
        return \Arr::except(static::rules(), $keys);
    }

    public static function only(array $keys): array
    {
        return \Arr::only(static::rules(), $keys);
    }
}
