<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Shared with You</title>
    <style>
        body {
            font-family: 'DM Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0053FF;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .button {
            display: inline-block;
            background-color: #0053FF;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 8px 8px;
        }
        .sections-list {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .section-item {
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Profile Shared with You</h1>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        
        <p><strong>{{ $sender->first_name }} {{ $sender->last_name }}</strong> has shared their professional profile with you via Yacht Workers Council.</p>
        
        @if($share->personal_message)
        <div style="background-color: #f0f7ff; padding: 15px; border-left: 4px solid #0053FF; margin: 20px 0;">
            <p style="margin: 0;"><strong>Message from {{ $sender->first_name }}:</strong></p>
            <p style="margin: 5px 0 0 0;">{{ $share->personal_message }}</p>
        </div>
        @endif
        
        <div class="sections-list">
            <h3 style="margin-top: 0;">Shared Sections:</h3>
            @foreach($share->sections_to_share ?? [] as $section)
            <div class="section-item">
                • {{ ucfirst(str_replace('_', ' ', $section)) }}
            </div>
            @endforeach
        </div>
        
        <p>Click the button below to view the shared profile:</p>
        
        <div style="text-align: center;">
            <a href="{{ $shareUrl }}" class="button">View Shared Profile</a>
        </div>
        
        @if($share->expires_at)
        <p style="color: #666; font-size: 14px; margin-top: 20px;">
            <strong>Note:</strong> This share link will expire on {{ \Carbon\Carbon::parse($share->expires_at)->format('M d, Y') }}.
        </p>
        @endif
        
        <p style="margin-top: 30px;">Best regards,<br>Yacht Workers Council</p>
    </div>
    
    <div class="footer">
        <p>This email was sent by Yacht Workers Council. If you have any questions, please contact support.</p>
        <p>&copy; {{ date('Y') }} Yacht Workers Council. All rights reserved.</p>
    </div>
</body>
</html>
