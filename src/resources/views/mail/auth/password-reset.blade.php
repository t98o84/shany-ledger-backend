@php
    /**
    * @var \App\Models\User $user
    * @var string $resetUrl
    */
@endphp

<x-mail::message>
# @lang('mail/auth/password-reset.subject')

@lang('mail/auth/password-reset.line1', ['name' => config('app.name')])

@lang('mail/auth/password-reset.line2')

<x-mail::button :url="$resetUrl">
@lang('mail/auth/password-reset.action')
</x-mail::button>

@lang('mail/auth/password-reset.line3')

@lang('mail/auth/password-reset.line4')
{{ config('app.name') }}<br><br>

---

@lang('mail/auth/password-reset.line5', ['resetUrl' => $resetUrl])
</x-mail::message>
