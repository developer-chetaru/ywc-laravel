<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Welcome to Yacht Workers Council Waitlist</title>
  <style>
    @media only screen and (max-width: 600px) {
      .red-bar {
        width: 100% !important;
      }
    }
  </style>
</head>
<body style="font-family: sans-serif; padding: 0; margin: 0; background: #F9F9F9;">
  <!-- Top Blue Bar -->
   <div class="red-bar" style="height: 6px; width: 62%; background-color: blue; margin: 0 auto; border-radius: 4px;"></div>
  <div style="max-width: 500px; margin: auto; background: #FFFFFF; padding: 20px; border-radius: 0 0 8px 8px;">
    <!-- Logo -->
    <img src="{{ asset('images/yacht-worker-council-app-icon.jpg') }}" alt="Yacht Workers Council Logo" style="width:52px; max-width:160px; margin: 0 auto 15px auto; display:block;" />
    
    <!-- Content Section -->
    <h2 style="margin-bottom: 20px;">Hello {{ $waitlist->first_name ?? 'there' }},</h2>
    <p style="margin: 0 0 15px 0;">Thank you for joining the Yacht Workers Council waitlist!</p>

    <p style="margin: 0 0 15px 0;">We're excited to have you on board. You've taken the first step toward accessing the premier platform for yacht crew networking, career management, and industry insights.</p>

    <p style="margin: 0 0 15px 0;">Here's what happens next:</p>
    <ul style="margin: 0 0 20px 0; padding-left: 20px;">
        <li style="margin-bottom: 10px;">We'll review your request and keep you updated on our launch progress</li>
        <li style="margin-bottom: 10px;">You'll be among the first to know when the platform is ready</li>
        <li style="margin-bottom: 10px;">We'll send you exclusive updates and early access opportunities</li>
    </ul>

    <p style="margin: 0 0 15px 0;">Your waitlist details:</p>
    <div style="background: #F5F6FA; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <p style="margin: 5px 0;"><strong>Email:</strong> {{ $waitlist->email }}</p>
        @if($waitlist->role)
        <p style="margin: 5px 0;"><strong>Role:</strong> {{ ucfirst($waitlist->role) }}</p>
        @endif
        <p style="margin: 5px 0;"><strong>Status:</strong> {{ ucfirst($waitlist->status) }}</p>
    </div>

    <p style="margin: 0 0 15px 0;">We're building something special for the yachting community, and we can't wait to share it with you!</p>
    
    <p style="margin: 0 0 15px 0;">If you have any questions, feel free to reach out to us.</p>
    
    <p>Thank you,<br>The Yacht Workers Council Team</p>
    
    <!-- Footer -->
    <br>
    <hr style="border: none; border-top: 1px solid #ccc;">
    <p style="font-size: 12px; color: #888; text-align: center;">
      © {{ date('Y') }}
      <span style="color: blue; font-weight: bold;">YWC<sup>®</sup></span> - ALL RIGHTS RESERVED
      <br>
      <span>Email:
        <a href="mailto:{{ config('app.support_email', 'support@yachtworkerscouncil.com') }}" 
           style="color: #888; text-decoration: none;">
           {{ config('app.support_email', 'support@yachtworkerscouncil.com') }}
        </a></span>
    </p>
  </div>
</body>
</html>

