<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Post Was {{ ucfirst($reactionLabel) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #0043EF; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0;">Yacht Workers Council</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
        <h2 style="color: #0043EF; margin-top: 0;">Your Post Was {{ ucfirst($reactionLabel) }}</h2>
        
        <p>Hello,</p>
        
        <p><strong>{{ $reactor->first_name }} {{ $reactor->last_name }}</strong> {{ $reactionLabel }} your post in:</p>
        
        <div style="background: white; padding: 15px; border-left: 4px solid #0043EF; margin: 20px 0;">
            <h3 style="margin: 0;">{{ $post->thread->title }}</h3>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $threadUrl }}" style="background: #0043EF; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                View Post
            </a>
        </div>
    </div>
</body>
</html>
