<x-mail::message>
    # Verification Code 🔐

    Hi there,

    Here's your one-time password (OTP):

    ## Your Verification Code

    <div
        style="text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 4px; color: #333;">
        {{ $otp }}
    </div>

    ## Important Security Notes

    - ⏰ **Expires in**: {{ ($expiry ?? '10') . 'minutes' }}
    - 🔒 **Never share** this code with anyone
    - 🚫 **Don't reply** to this email with the code
    - ✅ **Use immediately** for best security

    ## Didn't Request This Code?

    If you didn't request this verification code, please:
    - **Ignore this email** if you didn't make the request
    - **Contact support** immediately if you're concerned about your account security
    - **Change your password** if you suspect unauthorized access

    <x-mail::button :url="config('app.url')">
        Go to {{ config('app.name') }}
    </x-mail::button>

    ## Need Help?

    If you're having trouble with the verification process, please contact our support team.

    Thanks,<br>
    The {{ config('app.name') }} Security Team

    ---

    **This is an automated message. Please do not reply to this email.**
</x-mail::message>
