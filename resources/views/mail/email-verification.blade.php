<x-mail::message>
# Welcome to {{ config('app.name') }}! 🎉

Hi there,

Thank you for joining us! To complete your registration and start using your account, please verify your email address using the code below.

## Your Email Verification Code

<div style="text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 4px; color: #333;">
{{ $emailVerification->token }}
</div>

## How to Verify

1. **Copy the code** above
2. **Go to** {{ config('app.url') }}
3. **Enter the code** in the verification field
4. **Click verify** to activate your account

## What Happens Next?

Once you verify your email address, you'll be able to:
- ✅ **Access your account** - Log in and start using our services
- 🔐 **Secure your account** - Set up additional security features
- 🚀 **Explore features** - Discover everything we have to offer

<x-mail::button :url="config('app.url')">
Go to {{ config('app.name') }}
</x-mail::button>

## Important Security Notes

- ⏰ **Expires in**: 10 minutes
- 🔒 **Never share** this code with anyone
- 🚫 **Don't reply** to this email with the code
- ✅ **Use immediately** for best security

## Didn't Create an Account?

If you didn't create an account with us, you can safely ignore this email.

## Need Help?

If you're having trouble with the verification process, please contact our support team.

Thanks,<br>
The {{ config('app.name') }} Team

---

**This is an automated message. Please do not reply to this email.**
</x-mail::message> 