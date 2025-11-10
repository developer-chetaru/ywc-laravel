<!DOCTYPE html>
<html>
<head>
    <title>Account Activation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 80px;
            background-color: #f4f4f4;
        }
        .message {
            background: #fff;
            display: inline-block;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .success {
            color: green;
        }
        .already {
            color: orange;
        }
    </style>
</head>
<body>
    <div class="message">
     @if ($user->is_active)
    <h2 class="success">✅ Account activated successfully!</h2>
@else
    <h2 class="already">⚠️ Account is not activated yet.</h2>
@endif

    </div>
</body>
</html>
