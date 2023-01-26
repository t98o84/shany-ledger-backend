@php
    /**
    * @var \App\Models\User $user
    */
@endphp

<x-mail::message>
# @lang('mail/auth/password-reset-notification.subject')

@lang('mail/auth/password-reset-notification.line1')

@lang('mail/auth/password-reset-notification.line2')

@lang('mail/auth/password-reset-notification.line3', ['mail' => config('mail.to.customer_support')])

@lang('mail/auth/password-reset-notification.line4')
{{ config('app.name') }}<br><br>
</x-mail::message>
