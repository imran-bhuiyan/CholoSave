<!DOCTYPE html>
<html>
<head>
    <title>Payment OTP - CholoSave</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #1E40AF;
            text-align: center;
            padding: 15px;
            background-color: #fff;
            border: 2px solid #1E40AF;
            border-radius: 5px;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        .amount {
            font-size: 18px;
            color: #1E40AF;
            font-weight: bold;
        }
        .group-name {
            font-weight: bold;
        }
        .expiry {
            color: #dc2626;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Payment OTP Verification</h2>
        </div>
        
        <p>Hello,</p>
        
        <p>Your OTP for the payment of <span class="amount">৳{{ number_format($amount, 2) }}</span> to group "<span class="group-name">{{ $groupName }}</span>" is:</p>
        
        <div class="otp-code">
            {{ $otp }}
        </div>
        
        <p>This OTP will <span class="expiry">expire in 2 minutes</span>. Please do not share this OTP with anyone.</p>
        
        <div class="footer">
            <p>This is an automated message, please do not reply to this email.</p>
            <p>If you didn't request this payment, please contact our support team immediately.</p>
            <p>&copy; {{ date('Y') }} CholoSave. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 