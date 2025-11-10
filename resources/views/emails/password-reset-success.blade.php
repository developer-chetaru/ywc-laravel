<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset Successful</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f9f9f9; padding:20px;">
    <div style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; padding:30px; border:1px solid #ddd;">
        <h2 style="color:#333;">Hello {{ $user->name }},</h2>
        <p style="color:#555;">Your password has been successfully reset.</p>
        <p style="color:#555;">If you did not make this change, please contact our support team immediately.</p>
        <br>
        <p style="font-size:12px; color:#888; text-align:center;">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>