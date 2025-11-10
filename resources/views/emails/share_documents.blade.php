<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Documents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .user-name {
            color: #0053FF;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
        }
        .sub-heading {
            text-align: center;
            color: #000000;
            font-size: 20px;
            margin-top: 10px;
            font-weight: 500;
        }
        .description {
            font-size: 16px;
            color: #555555;
            margin-top: 20px;
            padding-left: 2px;
        }
        .documents-list {
            margin-top: 20px;
            padding-left: 20px;
            font-size: 14px;
        }
        .personal-note {
            margin-top: 22px;
            font-weight: 600;
        }
        .note-message {
            margin-top: 7px;
            font-style: normal;
            color: #333333;
            font-weight: 500;
            font-size: 16px;
        }
        hr {
            margin-top: 22px;
            border: none;
            border-top: 1px solid #dddddd;
        }
        /* Hidden preheader to remove email summary */
        .preheader {
            display: none !important;
            visibility: hidden;
            mso-hide: all;
            font-size: 1px;
            line-height: 1px;
            max-height: 0px;
            max-width: 0px;
            opacity: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="email-container">

        <span class="preheader"></span>

        <div style="text-align:center; margin-bottom:30px;">
            <img src="https://console-ywc.nativeappdev.com/images/yacht-worker-council-app-icon.jpg" alt="YWC Logo" 
                style="max-width:100px; display:block; margin:0 auto;">
        </div>

        <div class="user-name">{{ $senderName }}</div>

        <div class="sub-heading">shared documents with you</div>

        <div class="description">
            The following documents have been shared with you:
        </div>

        
        <ul class="documents-list">
            @foreach($documents as $doc)
                <li>
                    @if($doc->type === 'idvisa')
                        {{ $doc->extra_name ?? 'ID/Visa Document' }}
                    @elseif($doc->type === 'certificates')
                        {{ $doc->extra_name ?? 'Certificate Document' }}
                    @else
                        {{ $doc->extra_name ?? 'Other Document' }}
                    @endif
                </li>
            @endforeach
        </ul>

        @if(!empty($messageText))
            <div class="personal-note">Personal note:</div>
            <div class="note-message">
                <p>{!! nl2br(e($messageText)) !!}</p>
            </div>
        @endif
        <hr>
    </div>
</body>
</html>
