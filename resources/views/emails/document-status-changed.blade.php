<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Status Update</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 20px 0; text-align: center; background-color: #0053FF;">
                <h1 style="color: #ffffff; margin: 0;">Yacht Workers Council</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 40px 20px; background-color: #f5f5f5;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #333333; margin-top: 0;">
                                @if($status === 'approved')
                                    ✅ Document Approved
                                @elseif($status === 'rejected')
                                    ❌ Document Rejected
                                @else
                                    📄 Document Status Updated
                                @endif
                            </h2>
                            
                            <p style="color: #666666; line-height: 1.6;">
                                Hello {{ $document->user->first_name }},
                            </p>
                            
                            <p style="color: #666666; line-height: 1.6;">
                                @if($status === 'approved')
                                    Your document has been <strong style="color: #0C7B24;">approved</strong> and is now active in your profile.
                                @elseif($status === 'rejected')
                                    Your document has been <strong style="color: #EB1C24;">rejected</strong>. Please review the feedback below and resubmit.
                                @else
                                    The status of your document has been updated to <strong>{{ ucfirst($status) }}</strong>.
                                @endif
                            </p>
                            
                            <div style="background-color: #f9f9f9; border-left: 4px solid #0053FF; padding: 15px; margin: 20px 0;">
                                <p style="margin: 0; color: #333333; font-weight: bold;">Document Details:</p>
                                <p style="margin: 5px 0; color: #666666;">
                                    <strong>Name:</strong> {{ $document->document_name ?? 'Document #' . $document->id }}
                                </p>
                                @if($document->documentType)
                                <p style="margin: 5px 0; color: #666666;">
                                    <strong>Type:</strong> {{ $document->documentType->name }}
                                </p>
                                @endif
                                @if($document->document_number)
                                <p style="margin: 5px 0; color: #666666;">
                                    <strong>Number:</strong> {{ $document->document_number }}
                                </p>
                                @endif
                            </div>
                            
                            @if($notes)
                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
                                <p style="margin: 0; color: #333333; font-weight: bold;">
                                    @if($status === 'approved')
                                        Approval Notes:
                                    @else
                                        Rejection Reason:
                                    @endif
                                </p>
                                <p style="margin: 10px 0 0 0; color: #666666; white-space: pre-wrap;">{{ $notes }}</p>
                            </div>
                            @endif
                            
                            @if($status === 'rejected')
                            <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0;">
                                <p style="margin: 0; color: #721c24; font-weight: bold;">Next Steps:</p>
                                <p style="margin: 10px 0 0 0; color: #721c24;">
                                    Please review the rejection reason above, make necessary corrections, and resubmit your document.
                                </p>
                            </div>
                            @endif
                            
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ route('documents') }}" 
                                   style="display: inline-block; padding: 12px 30px; background-color: #0053FF; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                    View My Documents
                                </a>
                            </div>
                            
                            <p style="color: #999999; font-size: 12px; margin-top: 30px; border-top: 1px solid #eeeeee; padding-top: 20px;">
                                This is an automated notification from Yacht Workers Council. 
                                If you have any questions, please contact our support team.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
