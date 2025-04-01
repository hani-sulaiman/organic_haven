<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request</title>
    <style>
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #e3342f;
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
            background-color: #e3342f;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Hello,</h1>
        <p>You have requested to reset your password for your account at our Online Store. Please use the OTP code below to proceed.</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>This OTP is valid for the next 10 minutes.</p>
        <a href="{{ config('app.url') }}" class="button">Visit Our Store</a>
        <p>If you did not request a password reset, please ignore this email.</p>
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
