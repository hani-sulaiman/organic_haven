<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email Address</title>
    <style>
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #3490dc;
            letter-spacing: 4px;
            text-align: center;
            margin: 20px 0;
        }
        .content {
            font-family: Arial, sans-serif;
            color: #555555;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Hello {{ $name }},</h1>
        <p>Thank you for registering at our Online Store. Please use the verification code below to verify your email address.</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>This code is valid for the next 10 minutes.</p>
        <a href="{{ config('app.url') }}" class="button">Visit Our Store</a>
        <p>If you did not create an account, no further action is required.</p>
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
