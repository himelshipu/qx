<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
            text-align: center;
            margin-bottom: 30px;
        }
        .message {
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .code-box {
            background-color: #f9f9f9;
            border: 2px solid #007bff;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 4px;
            font-family: 'Courier New', monospace;
        }
        .code-note {
            color: #999999;
            font-size: 14px;
            margin-top: 15px;
        }
        .footer {
            border-top: 1px solid #eeeeee;
            padding-top: 20px;
            margin-top: 30px;
            color: #999999;
            font-size: 14px;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>
        
        <div class="message">
            <p>Hello {{ $user->name }},</p>
            <p>Thank you for registering with us. To complete your email verification, please use the verification code below:</p>
        </div>

        <div class="code-box">
            <div class="code">{{ $verificationCode }}</div>
            <div class="code-note">Valid for 15 minutes</div>
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong> Never share this code with anyone. We will never ask you for this code via email or phone.
        </div>

        <div class="message">
            <p>If you did not register for this account, please ignore this email.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
