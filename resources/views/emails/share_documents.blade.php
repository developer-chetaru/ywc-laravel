<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Documents - Yacht Workers Council</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #0053FF;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header img {
            max-width: 120px;
            height: auto;
        }
        .email-content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 16px;
            color: #333333;
            margin-bottom: 20px;
        }
        .main-message {
            font-size: 16px;
            color: #333333;
            margin-bottom: 30px;
        }
        .sender-name {
            color: #0053FF;
            font-weight: 600;
        }
        .documents-section {
            background-color: #f9f9f9;
            border-left: 4px solid #0053FF;
            padding: 20px;
            margin: 25px 0;
        }
        .documents-section h3 {
            margin: 0 0 15px 0;
            color: #0053FF;
            font-size: 18px;
            font-weight: 600;
        }
        .documents-list {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
        }
        .documents-list li {
            margin: 8px 0;
            color: #333333;
            font-size: 15px;
        }
        .personal-message {
            background-color: #f0f7ff;
            border-left: 4px solid #0053FF;
            padding: 15px 20px;
            margin: 25px 0;
        }
        .personal-message strong {
            color: #0053FF;
        }
        .personal-message p {
            margin: 8px 0 0 0;
            color: #333333;
            font-style: italic;
        }
        .cta-section {
            text-align: center;
            margin: 35px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #0053FF;
            color: #ffffff !important;
            padding: 14px 35px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 5px;
        }
        .cta-button:hover {
            background-color: #0044DD;
        }
        .info-section {
            background-color: #f9f9f9;
            padding: 20px;
            margin: 30px 0;
            border-radius: 5px;
        }
        .info-section h3 {
            margin: 0 0 12px 0;
            color: #0053FF;
            font-size: 16px;
            font-weight: 600;
        }
        .info-section p {
            margin: 8px 0;
            color: #555555;
            font-size: 14px;
        }
        .help-section {
            margin: 30px 0;
        }
        .help-section h3 {
            color: #0053FF;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .help-section p {
            color: #555555;
            font-size: 14px;
            margin: 8px 0;
        }
        .security-reminder {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 25px 0;
        }
        .security-reminder strong {
            color: #856404;
        }
        .security-reminder p {
            margin: 5px 0;
            color: #856404;
            font-size: 14px;
        }
        .email-footer {
            background-color: #f9f9f9;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer p {
            margin: 8px 0;
            color: #666666;
            font-size: 14px;
        }
        .email-footer .tagline {
            color: #0053FF;
            font-weight: 600;
            margin: 15px 0;
        }
        .email-footer .links {
            margin: 20px 0;
        }
        .email-footer .links a {
            color: #0053FF;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }
        .email-footer .links a:hover {
            text-decoration: underline;
        }
        .email-footer .copyright {
            color: #999999;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header with Logo -->
        <div class="email-header">
            <img src="{{ asset('images/yacht-worker-council-app-icon.jpg') }}" alt="Yacht Workers Council Logo" />
        </div>

        <!-- Main Content -->
        <div class="email-content">
            <div class="greeting">
                Hi there,
            </div>

            <div class="main-message">
                <span class="sender-name">{{ $senderName }}</span> has shared important documents with you through the Yacht Workers Council platform.
            </div>

            <!-- Documents Section -->
            <div class="documents-section">
                <h3>Documents Shared:</h3>
                <ul class="documents-list">
                    @foreach($documents as $doc)
                        <li>
                            @if($doc->type === 'idvisa')
                                {{ $doc->extra_name ?? 'ID/Visa Document' }}
                            @elseif($doc->type === 'certificate')
                                {{ $doc->extra_name ?? 'Certificate Document' }}
                            @else
                                {{ $doc->extra_name ?? ucfirst($doc->type) }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Personal Message -->
            @if(!empty($messageText))
            <div class="personal-message">
                <strong>Personal Message:</strong>
                <p>"{{ $messageText }}"</p>
            </div>
            @endif

            <!-- What's Next Section -->
            <div class="info-section">
                <h3>What's Next?</h3>
                <p>View and download these documents securely in your YWC account. All shared documents are encrypted and stored safely in accordance with maritime data protection standards.</p>
            </div>

            <!-- Primary CTA Button -->
            <div class="cta-section">
                <a href="{{ route('documents') }}" class="cta-button">View Documents</a>
            </div>

            <!-- Help Section -->
            <div class="help-section">
                <h3>Need Help?</h3>
                <p>If you have questions about these documents or need assistance accessing them, contact <span class="sender-name">{{ $senderName }}</span> directly through the YWC messaging system or reach out to our support team.</p>
            </div>

            <!-- Security Reminder -->
            <div class="security-reminder">
                <p><strong>Security Reminder:</strong> Only open documents from people you know and trust. If you didn't expect this sharing request, please report it to our support team immediately.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="tagline">Stay connected,</p>
            <p><strong>The Yacht Workers Council Team</strong></p>
            <p style="color: #666666; font-size: 13px; margin-top: 10px;">The crew-owned cooperative serving yachting professionals worldwide</p>
            
            <div class="links">
                <a href="{{ route('documents') }}">View Documents</a> |
                <a href="{{ route('dashboard') }}">Your Dashboard</a> |
                <a href="mailto:{{ config('app.support_email', 'support@yachtworkerscouncil.com') }}">Support</a>
            </div>
            
            <p class="copyright">
                &copy; {{ date('Y') }} Yacht Workers Council. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
