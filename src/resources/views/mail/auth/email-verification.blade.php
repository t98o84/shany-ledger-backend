@php
    /**
    * @var \App\Models\User $user
    * @var string $verificationUrl
    */
@endphp

<x-mail::message>
# @lang('mail/auth/email-verification.subject')

@lang('mail/auth/email-verification.thanks', ['name' => config('app.name')])

@lang('mail/auth/email-verification.please_click')

<x-mail::button :url="$verificationUrl">
@lang('mail/auth/email-verification.verify_button')
</x-mail::button>

@lang('mail/auth/email-verification.having_no_clue')

@lang('mail/auth/email-verification.regards')
{{ config('app.name') }}<br><br>

---

@lang('mail/auth/email-verification.cannot_click_button', ['verificationUrl' => $verificationUrl])
</x-mail::message>
