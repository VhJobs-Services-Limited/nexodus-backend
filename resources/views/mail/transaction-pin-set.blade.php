<x-mail::message>
Hi {{ $user->username }},

We're letting you know that your transaction PIN was just set for your account.

If you made this change, no further action is needed.

## Security Tips

- If you did **not** set this PIN, please open the app and reset your PIN immediately.
- Never share your transaction PIN with anyone.
- Use a unique PIN that you don't use elsewhere.

## Need Help?

If you have any questions or need assistance, please contact our support team through the app.

Thanks,<br>
The {{ config('app.name') }} Team

---

**This is an automated message. Please do not reply to this email.**
</x-mail::message> 