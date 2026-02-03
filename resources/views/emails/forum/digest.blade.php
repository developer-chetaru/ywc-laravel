<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mode === 'daily' ? 'Daily' : 'Weekly' }} Forum Digest</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #0066FF; padding: 20px; border-radius: 8px; margin-bottom: 20px; color: #fff;">
        <h1 style="color: #fff; margin-top: 0;">{{ $mode === 'daily' ? 'Daily' : 'Weekly' }} Forum Digest</h1>
        <p style="margin: 0; color: #fff;">
            You have <strong>{{ $totalCount }}</strong> new notification{{ $totalCount !== 1 ? 's' : '' }}.
        </p>
    </div>

    @foreach($groupedNotifications as $type => $notifications)
        <div style="background-color: #fff; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #333; margin-top: 0; font-size: 18px;">
                {{ ucfirst(str_replace('_', ' ', $type)) }} ({{ $notifications->count() }})
            </h2>
            
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($notifications->take(10) as $notification)
                    <li style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                        <a href="{{ $notification->link }}" style="color: #0066FF; text-decoration: none;">
                            {{ $notification->title }}
                        </a>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                            {{ $notification->message }}
                        </p>
                        <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </li>
                @endforeach
            </ul>

            @if($notifications->count() > 10)
                <p style="margin-top: 15px; color: #666; font-size: 14px;">
                    <a href="{{ route('forum.notifications.index') }}" style="color: #0066FF;">View all {{ $notifications->count() }} notifications →</a>
                </p>
            @endif
        </div>
    @endforeach

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ route('forum.notifications.index') }}" style="display: inline-block; background-color: #0066FF; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            View All Notifications
        </a>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; font-size: 12px; color: #999; text-align: center;">
        <p>You're receiving this {{ $mode }} digest because you have digest mode enabled for some notification types.</p>
        <p><a href="{{ route('forum.notifications.preferences') }}" style="color: #0066FF;">Manage notification preferences</a></p>
    </div>
</body>
</html>
