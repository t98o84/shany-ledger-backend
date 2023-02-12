<?php

namespace App\Actions;
use Lang;
use LogicException;

trait HasErrorCode
{
    public function code(): string
    {
        return $this->value;
    }

    public function title(): string
    {
        $key = "{$this->messageBaseKey()}.$this->value";

        if (!Lang::has($key)) {
            throw new LogicException('エラーコードのタイトルは必ず実装する必要があります。');
        }

        $title = Lang::get($key);

        if (is_string($title)) {
            return $title;
        }

        if (is_array($title) && isset($title['title']) && is_string($title['title'])) {
            return $title['title'];
        }

        throw new LogicException('エラーコードのタイトルは文字列もしくは、キーがtitleで値が文字列の項目を持つ配列である必要があります。');
    }

    public function detail(): string|null
    {
        $detail = Lang::get("{$this->messageBaseKey()}.$this->value.detail");

        if (is_array($detail) && isset($detail['detail']) && is_string($detail['detail'])) {
            return $detail['detail'];
        }

        return null;
    }

    abstract protected function messageBaseKey(): string;
}
