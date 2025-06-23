<x-mail::message>
# Welcome to {{ config('app.name') }}! 🎉

Hi **{{ $user->username }}**,

Welcome aboard! We're thrilled to have you join our community. Your account has been successfully created and you're now ready to get started.

## What's Next?

- **Complete your profile** - Add your details to personalize your experience
- **Explore our features** - Discover everything we have to offer
- **Connect with others** - Start building your network

<x-mail::button :url="config('app.url')">
Get Started
</x-mail::button>

## Need Help?

If you have any questions or need assistance, don't hesitate to reach out to our support team. We're here to help you succeed!

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
