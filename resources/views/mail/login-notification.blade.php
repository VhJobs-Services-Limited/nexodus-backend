<x-mail::message>
# New Login Detected

Hello {{ $fullname }},

We detected a new login to your Nexodus account.

**Login Details:**
- **Time:** {{ $timestamp }}
- **IP Address:** {{ $ipAddress }}
- **Device:** {{ $userAgent }}

If this was you, you can safely ignore this email. If you don't recognize this login, please contact our support team immediately and consider changing your password.

<x-mail::button :url="config('app.url')">
Visit Nexodus
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}

<small>This is an automated security notification. Please do not reply to this email.</small>
</x-mail::message>
