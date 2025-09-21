<x-mail::message>
Hello {{ $username }},

We are happy to inform you that your sale of ₦**{{ $amount }}** has been completed successfully
@if($paymentMethod == 'wallet')
and the funds has been credited to your wallet.
@else
and the funds has been credited to your bank account.
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
