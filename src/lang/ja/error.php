<?php

return [
    'title' => '何らかの問題が発生しました。',

    'bad_requests' => [
        'title' => 'リクエストに問題があります。',
    ],

    'conflict' => [
        'title' => '現在のサーバーの状態とリクエスト内容の間で衝突が発生しました。データを更新して再度お試しください。',
    ],

    'forbidden' => [
        'title' => '権限がないため、リクエストが拒否されました。',
    ],

    'gone' => [
        'title' => 'リソースはもう永久に存在しません。',
    ],

    'internal_server_error' => [
        'title' => 'サーバーに問題が発生しました。問題が解決しない場合は、時間を置いて再度お試しください。',
    ],

    'not_found' => [
        'title' => 'リソースが存在しないか、または権限がありません。',
    ],

    'too_many_requests' => [
        'title' => '試行回数が多すぎます。',
        'title_with_seconds' => '試行回数が多すぎます。 :seconds 秒後に再度お試しください。',
        'title_with_datetime' => '試行回数が多すぎます。 :datetime 以降に再度お試しください。',
    ],

    'unauthorized' => [
        'title' => '有効な認証情報がありません。サインインして再度お試しください。',
    ],

    'validation' => [
        'title' => 'リクエストに問題があります。',
    ],
];
