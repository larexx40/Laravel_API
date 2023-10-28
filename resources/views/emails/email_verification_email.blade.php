<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
</head>
<body style="text-align: center; background-color: #30BE82; color: #fff; font-family: Arial, sans-serif; padding: 20px;">
    <!-- App Logo -->
    <div style="margin: 20px 0;">
        <img src="{{ asset('images/logo.png') }}" alt="Your App Logo">
    </div>
    
    <p>Hello, welcome to {{ config('app.name') }}</p>
    <p>Use the OTP below to verify your email </p>
    <p>OTP: <strong>{{ $otp }}</strong></p>
    <p>If you didn't create an account with us, please ignore this email. The OTP is valid for a limited time.</p>

    <!-- Contact Support -->
    <div style="margin: 20px 0;">
        <p>If you need assistance, please contact our support team:</p>
        <p>Email: support@example.com</p>
        <p>Phone: +1 (123) 456-7890</p>
    </div>
</body>
</html>