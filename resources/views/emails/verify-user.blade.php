<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Password Reset OTP</title>
  <style>
    @media only screen and (max-width: 600px) {
      .red-bar {
        width: 100% !important;
      }
    }
  </style>
</head>
<body style="font-family: sans-serif; padding: 0; margin: 0; background: #F9F9F9;">
  <!-- :red_circle: Top Red Bar -->
   <div class="red-bar" style="height: 6px; width: 62%; background-color: blue; margin: 0 auto; border-radius: 4px;"></div>
  <div style="max-width: 500px; margin: auto; background: #FFFFFF; padding: 20px; border-radius: 0 0 8px 8px;">
    <!-- :large_red_square: Logo (optional local image) -->
    <img src="{{ asset('images/yacht-worker-council-app-icon.jpg') }}" alt="Tribe365 Logo" style="width:52px; max-width:160px; margin: 0 auto 15px auto; display:block;" />
    
    <!-- :closed_lock_with_key: OTP Section -->
    <h2 style="margin-bottom: 20px;">Hello {{ $user->first_name }},</h2>
    <p style="margin: 0 0 15px 0;">Welcome to Yacht Workers Council.</p>

<p style="margin: 0 0 15px 0;">Your account has been successfully created.</p>

<p style="margin: 0 0 15px 0;">To get started, please activate your account.</p>

<p style="margin: 0 0 20px 0;">Click the button below to complete your activation:</p>
    <p><a href="{{ $verificationUrl }}"
       style="display:inline-block; padding:10px 20px; background:blue; color:white; border-radius:5px; text-decoration:none;">
        Activate My Account
    </a></p>
    
    <p style="margin: 0 0 15px 0;">If you did not sign up for this account, please ignore this email.</p>
    
    <p>Thank you,<br>The YWC Team</p>
    <!-- :small_red_triangle_down: Footer -->
    <br>
    <hr style="border: none; border-top: 1px solid #ccc;">
    <p style="font-size: 12px; color: #888; text-align: center;">
      © {{ date('Y') }}
      <span style="color: blue; font-weight: bold;">YWC<sup>®</sup></span> - ALL RIGHTS RESERVED
      <br>
      <span>Email:
        <a href="mailto:{{ config('app.support_email') }}" 
           style="color: #888; text-decoration: none;">
           {{ config('app.support_email') }}
        </a></span>
    </p>
  </div>
</body>
</html>
