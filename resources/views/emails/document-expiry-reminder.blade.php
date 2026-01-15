<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Expiry Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h1 style="color: #0053FF; margin-top: 0;">YWC Document Expiry Reminder</h1>
    </div>

    <div style="background-color: {{ $isExpired ? '#fee' : '#fff3cd' }}; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid {{ $isExpired ? '#dc3545' : '#ffc107' }};">
        <h2 style="margin-top: 0; color: {{ $isExpired ? '#dc3545' : '#856404' }};">
            @if($isExpired)
                ⚠️ Document Expired
            @else
                ⏰ Document Expiring Soon
            @endif
        </h2>
        <p style="margin-bottom: 0; font-size: 16px;">
            <strong>{{ $document->document_name ?? 'Your document' }}</strong>
            @if($document->documentType)
                <br><small style="color: #666;">Type: {{ $document->documentType->name }}</small>
            @endif
        </p>
    </div>

    <div style="background-color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3 style="color: #333; margin-top: 0;">Document Details</h3>
        <table style="width: 100%; border-collapse: collapse;">
            @if($document->document_number)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Document Number:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $document->document_number }}</td>
            </tr>
            @endif
            @if($document->expiry_date)
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Expiry Date:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $document->expiry_date->format('F d, Y') }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 8px 0;"><strong>Time Remaining:</strong></td>
                <td style="padding: 8px 0; color: {{ $isExpired ? '#dc3545' : '#856404' }}; font-weight: bold;">{{ $timeRemaining }}</td>
            </tr>
        </table>
    </div>

    <div style="background-color: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <p style="margin: 0;">
            @if($isExpired)
                Your document has expired. Please renew it as soon as possible to avoid any disruptions.
            @else
                Please ensure you renew this document before it expires to maintain compliance.
            @endif
        </p>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('documents.show', $document->id) }}" 
           style="display: inline-block; background-color: #0053FF; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            View Document Details
        </a>
    </div>

    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 12px; color: #666;">
        <p style="margin: 0;">
            This is an automated reminder from YWC. You can manage your document expiry reminders in your account settings.
        </p>
        <p style="margin: 10px 0 0 0;">
            <a href="#" style="color: #0053FF;">Unsubscribe from expiry reminders</a>
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #999;">
        <p>© {{ date('Y') }} YWC. All rights reserved.</p>
    </div>
</body>
</html>
