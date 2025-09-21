<x-mail::message>
# Hello {{ $username }},

We have an update on your recent coin sale:

@switch($cryptoTransaction->status)
@case('success')
    We are happy to inform you that your sale of **{{ $cryptoTransaction->amount }} {{ strtoupper($cryptoTransaction->currency) }}** was successful.
    The funds is currently being processed and will be credited to your account shortly.
@break

@case('failed')
    **Unfortunately, your sale could not be completed.**
    The attempted sale of **{{ $cryptoTransaction->amount }} {{ strtoupper($cryptoTransaction->currency) }}** has failed.
    Please contact support if you believe this is an error.
@break

@default
    Your sale of **{{ $cryptoTransaction->amount }} {{ strtoupper($cryptoTransaction->currency) }}** is currently being processed.
    We’ll notify you once it’s confirmed.
@endswitch

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
