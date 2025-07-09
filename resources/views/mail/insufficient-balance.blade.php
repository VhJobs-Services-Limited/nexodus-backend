<x-mail::message>
# Insufficient Balance Alert ⚠️

Hi Admin,

This is an automated alert to notify you that the **{{ $providerName }}** service has insufficient balance to process transactions.

## Balance Details

- **Current Balance**: ₦{{ $currentBalance }}
- **Required Amount**: ₦{{ $requiredAmount }}
- **Shortfall**: ₦{{ $shortfall }}

## Action Required

Please top up the **{{ $providerName }}** account immediately to ensure uninterrupted service for your users.

## Service Impact

- ❌ **Bill payments** may be temporarily unavailable
- ❌ **Airtime purchases** may be affected
- ❌ **Data bundle purchases** may be suspended

## Recommended Actions

1. **Immediate**: Add funds to the {{ $providerName }} account
2. **Monitor**: Check balance regularly to prevent future issues
3. **Verify**: Confirm the top-up was successful before resuming operations

## Account Information

- **Provider**: {{ $providerName }}
- **Alert Time**: {{ now()->format('Y-m-d H:i:s T') }}

## Need Help?

If you need assistance with the top-up process or have any questions, please contact the technical team immediately.

Thanks,<br>
The {{ config('app.name') }} System

---

**This is an automated alert. Please do not reply to this email.**
</x-mail::message> 