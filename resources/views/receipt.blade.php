<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contribution Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #22223b;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            max-width: 480px;
            margin: 30px auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px #e0e7ef;
            padding: 32px 24px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header img {
            height: 48px;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 22px;
            color: #1e40af;
            margin: 0 0 8px 0;
        }
        .section {
            margin-bottom: 18px;
        }
        .label {
            font-weight: bold;
            color: #22223b;
            width: 140px;
            display: inline-block;
        }
        .value {
            color: #374151;
        }
        .thankyou {
            text-align: center;
            margin-top: 32px;
            color: #10b981;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 11px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" alt="CholoSave Logo" onerror="this.style.display='none'">
            <h1>Contribution Receipt</h1>
        </div>
        <div class="section">
            <span class="label">Name:</span> <span class="value">{{ $user->name }}</span>
        </div>
        <div class="section">
            <span class="label">Campaign:</span> <span class="value">{{ $campaign->title }}</span>
        </div>
        <div class="section">
            <span class="label">Amount:</span> <span class="value">৳{{ number_format($contribution->amount, 2) }}</span>
        </div>
        <div class="section">
            <span class="label">Payment Method:</span> <span class="value">{{ ucfirst($payment_method) }}</span>
        </div>
        <div class="section">
            <span class="label">Date:</span> <span class="value">{{ $contribution->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="thankyou">
            Thank you for your generous contribution!<br>
            <span style="font-size:0.95em;color:#374151;">This receipt is computer generated and does not require a signature.</span>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CholoSave. All rights reserved.
        </div>
    </div>
</body>
</html> 