<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You Were Mentioned</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h1 style="color: #0066FF; margin-top: 0;">You Were Mentioned</h1>
        <p style="margin: 0;">
            <strong>{{ $mentioner->first_name }} {{ $mentioner->last_name }}</strong> mentioned you in a forum thread.
        </p>
    </div>

    <div style="background-color: #fff; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #333; margin-top: 0;">{{ $thread->title }}</h2>
        <p style="color: #666; font-size: 14px;">
            <a href="{{ $url }}" style="color: #0066FF; text-decoration: none;">View the post →</a>
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ $url }}" style="display: inline-block; background-color: #0066FF; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            View Post
        </a>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; text-align: center;">
        <p>You're receiving this email because you were mentioned in a forum thread.</p>
        <p><a href="{{ route('forum.notifications.preferences') }}" style="color: #0066FF;">Manage notification preferences</a></p>
    </div>
</body>
</html>
