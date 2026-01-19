<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Certificate</title>
    <style>
        @page {
            margin: 40px;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .certificate-container {
            max-width: 800px;
            margin: 0 auto;
            border: 3px solid #0053FF;
            padding: 40px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0053FF;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0053FF;
            margin-bottom: 10px;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #0053FF;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .certificate-number {
            font-size: 10px;
            color: #999;
            margin-top: 10px;
        }
        .content {
            margin: 30px 0;
            text-align: center;
        }
        .declaration {
            font-size: 14px;
            line-height: 1.8;
            margin: 20px 0;
            text-align: center;
        }
        .highlight {
            color: #0053FF;
            font-weight: bold;
            font-size: 16px;
        }
        .details-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
            width: 40%;
        }
        .detail-value {
            color: #333;
            width: 60%;
            text-align: right;
        }
        .verification-badge {
            display: inline-block;
            padding: 8px 15px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
        }
        .signature-block {
            text-align: center;
            width: 40%;
        }
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 50px;
            padding-top: 10px;
            font-weight: bold;
        }
        .signature-title {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #0053FF;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .seal {
            width: 100px;
            height: 100px;
            border: 3px solid #0053FF;
            border-radius: 50%;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #0053FF;
            font-size: 10px;
            text-align: center;
            padding: 10px;
        }
        .qr-code {
            margin: 20px auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">YACHT WORKERS COUNCIL</div>
            <div class="subtitle">Document Verification Services</div>
            <div class="title">Certificate of Verification</div>
            <div class="certificate-number">Certificate No: {{ $certificateNumber }}</div>
        </div>

        <!-- Verification Badge -->
        <div class="content">
            <div class="verification-badge">
                ✓ VERIFIED - LEVEL {{ $verification->verificationLevel->level ?? 'N/A' }}
            </div>
        </div>

        <!-- Declaration -->
        <div class="declaration">
            This is to certify that the document titled<br>
            <span class="highlight">{{ $document->document_name ?? 'Document' }}</span><br>
            belonging to<br>
            <span class="highlight">{{ $document->user->first_name }} {{ $document->user->last_name }}</span><br>
            has been verified and authenticated.
        </div>

        <!-- Document Details -->
        <div class="details-box">
            <div class="detail-row">
                <span class="detail-label">Document Type:</span>
                <span class="detail-value">{{ ucfirst($document->type) }}</span>
            </div>
            @if($document->document_number)
            <div class="detail-row">
                <span class="detail-label">Document Number:</span>
                <span class="detail-value">{{ $document->document_number }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Document Owner:</span>
                <span class="detail-value">{{ $document->user->first_name }} {{ $document->user->last_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Verification Level:</span>
                <span class="detail-value">Level {{ $verification->verificationLevel->level ?? 'N/A' }} - {{ $verification->verificationLevel->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Verified By:</span>
                <span class="detail-value">{{ $verification->verifier->first_name ?? 'YWC' }} {{ $verification->verifier->last_name ?? 'Staff' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Verification Date:</span>
                <span class="detail-value">{{ $verification->verified_at ? \Carbon\Carbon::parse($verification->verified_at)->format('d M Y, H:i') : 'N/A' }}</span>
            </div>
            @if($verification->notes)
            <div class="detail-row">
                <span class="detail-label">Verification Notes:</span>
                <span class="detail-value">{{ $verification->notes }}</span>
            </div>
            @endif
        </div>

        <!-- Official Seal -->
        <div class="seal">
            YACHT WORKERS<br>COUNCIL<br>OFFICIAL SEAL
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-line">
                    Verification Officer
                </div>
                <div class="signature-title">
                    {{ $verification->verifier->first_name ?? 'YWC' }} {{ $verification->verifier->last_name ?? 'Staff' }}
                </div>
            </div>
            <div class="signature-block">
                <div class="signature-line">
                    Authorized Signatory
                </div>
                <div class="signature-title">
                    Yacht Workers Council
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>This is a digitally generated certificate</strong></p>
            <p>Issued on: {{ now()->format('d F Y, H:i:s') }} UTC</p>
            <p>Certificate ID: {{ $certificateNumber }}</p>
            <p>Verify authenticity at: {{ config('app.url') }}/verify/{{ $certificateNumber }}</p>
            <p>&copy; {{ now()->year }} Yacht Workers Council. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
