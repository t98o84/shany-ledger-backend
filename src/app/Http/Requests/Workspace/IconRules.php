<?php

namespace App\Http\Requests\Workspace;

use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\ImageFile;

class IconRules
{
    public static function rules(): array
    {
        return [
            'icon' => ['required', ImageFile::image()->max(2048)->dimensions(new Dimensions(['width' => 128, 'height' => 128]))],
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
