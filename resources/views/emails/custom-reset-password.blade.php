<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $orgName }} Invitation</title>
    <style type="text/css">
        /* This CSS is for general styles. For spacing, we'll use inline styles. */
        .join-btn {
            background: #0053FF; 
            color: #fff !important; /* !important ensures button text color isn't overridden */
            padding: 12px 20px; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            display: inline-block;
        }
        .join-btn:hover { opacity: 0.9; }
    </style>
</head>
<body style="background:#f9f9f9; margin:0; padding:20px; font-family: Arial, Helvetica, sans-serif;">
    <div style="max-width:600px; margin:0 auto; background:#fff; padding:40px 20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <div style="text-align:center; margin-bottom:20px;">
            <div style="width:67px; height:67px; padding:4px; border:1px solid #0053ff; border-radius:50%; display:inline-block;">
                <img src="https://console-ywc.nativeappdev.com/images/ywc-logo.svg" alt="YWC Logo" style="max-width:100%; display:block;" />
            </div>
        </div>
        
        <h2 style="color:#000000; margin-top:0; margin-bottom:18px; font-family: Arial, Helvetica, sans-serif;">Hello {{ ucfirst($userFullName) }},</h2>
      
        <p style="color:#000000; margin-bottom:20px; font-family: Arial, Helvetica, sans-serif;">
            We received a request to reset the password for your YWC account. If you made this request, please click the button below to set a new password.
        </p>

        <p style="margin-bottom: 25px; text-align: center;">
            <a href="{{ $resetUrl }}" class="join-btn" style="background:#0053FF; color:#fff !important; padding:12px 20px; text-decoration:none; border-radius:5px; font-weight:bold; display:inline-block;">
                Reset Password
            </a>
        </p>

        <p style="color:#000000; margin-bottom:18px; font-family: Arial, Helvetica, sans-serif;">
            This link will expire in <strong>24 hours</strong> for your security.
        </p>
        
        <p style="color:#000000; margin-bottom:20px; font-family: Arial, Helvetica, sans-serif;">
            If you didn’t request a password reset, you can safely ignore this email.
          your account will remain secure.
        </p>
        
        <p style="color:#000000; margin-bottom:5px; font-family: Arial, Helvetica, sans-serif;">
            Thanks,
        </p>
        <p style="color:#000000; margin-top:0; font-family: Arial, Helvetica, sans-serif;">
            The YWC Team
        </p>
    </div>
</body>
</html>