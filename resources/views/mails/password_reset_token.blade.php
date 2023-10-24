<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Email</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>We have received a request to reset your password. Please use the following OTP to reset your password:</p>
    <p>OTP: <strong>{{ $otp }}</strong></p>
    <p>If you didn't request a password reset, please ignore this email. The OTP is valid for a limited time.</p>
</body>
</html>