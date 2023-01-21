<?php

namespace App\Exceptions\ProblemDetails;

class ProblemDetailsException extends \Exception
{
    // エラー内容が判別可能な文字列のコードです。
    public readonly string $errorCode;

    // 人間が読むことのできる短いサマリーです。
    public readonly string $title;

    // 人間が読むことのできる説明文です。
    public readonly string|null $detail;

    // エラーの詳細ドキュメントへのURLです。
    public readonly string $type;

    // 問題の発生箇所の参照URIです。
    public readonly string|null $instance;

    // サーバによって生成されたHTTPステータスコードです。
    public readonly int $status;

    public function __construct(
        string $errorCode = null,
        string $title = null,
        string $detail = null,
        string $type = null,
        int    $status = null,
        string $instance = null,
    )
    {
        parent::__construct($title);

        $this->errorCode = $errorCode ?? $this->defaultErrorCode();
        $this->title = $title ?? $this->defaultTitle();
        $this->detail = $detail ?? $this->defaultDetail();
        $this->type = $type ?? $this->defaultType();
        $this->status = $status ?? $this->defaultStatus();
        $this->instance = $instance ?? $this->defaultInstance();
    }

    public function defaultErrorCode(): string
    {
        return 'ProblemDetails';
    }

    public function defaultTitle(): string
    {
        return __('error.title');
    }

    public function defaultDetail(): string|null
    {
        return null;
    }

    public function defaultType(): string
    {
        return 'about:blank';
    }

    public function defaultStatus(): int
    {
        return 500;
    }

    public function defaultInstance(): string|null
    {
        return \Request::url();
    }

    public function toArray(): array
    {
        return [
            'code' => $this->errorCode,
            'title' => $this->title,
            'type' => $this->type,
            'detail' => $this->detail,
            'status' => $this->status,
            'instance' => $this->instance,
        ];
    }

    public function report(): bool
    {
        return false;
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->toArray(), $this->status, $this->headers());
    }

    protected function headers(): array
    {
        return [];
    }

}
