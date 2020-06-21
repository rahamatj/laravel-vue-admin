@component('mail::message')
# Hello!

Your OTP is {{ $generatedOtp }}

Thank you for using our application!

Thanks,<br>
{{ config('app.name') }}
@endcomponent
