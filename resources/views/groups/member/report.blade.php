<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 5px 10px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .data-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #ecf0f1;
            color: #2c3e50;
            padding: 8px;
            text-align: left;
            border: 1px solid #bdc3c7;
        }
        td {
            padding: 8px;
            border: 1px solid #bdc3c7;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report</h1>
        <p>Generated on: {{ now()->format('d F, Y') }}</p>
    </div>

    <!-- Group Overview Section -->
    <div class="section">
        <div class="section-title">Group Overview</div>
        <div class="data-row">
            <span class="label">Group Name:</span> {{ $groupData['GroupName'] }}
        </div>
        <div class="data-row">
            <span class="label">Admin Name:</span> {{ $groupData['AdminName'] }}
        </div>
        <div class="data-row">
            <span class="label">Total Members:</span> {{ $groupData['TotalMembers'] }}
        </div>
        <div class="data-row">
            <span class="label">DPS Type:</span> {{ $groupData['DPSType'] }}
        </div>
        <div class="data-row">
            <span class="label">Time Period:</span> {{ $groupData['TimePeriod'] }}
        </div>
        <div class="data-row">
            <span class="label">Installment Amount:</span> {{ number_format($groupData['InstallmentAmount'], 2) }}
        </div>
        <div class="data-row">
            <span class="label">Start Date:</span> {{ $groupData['StartDate'] }}
        </div>
    </div>

    <!-- Financial Summary Section -->
    <div class="section">
        <div class="section-title">Financial Summary</div>
        <div class="data-row">
            <span class="label">Total Savings:</span> {{ number_format($groupData['TotalSavings'], 2) }}
        </div>
        <div class="data-row">
            <span class="label">Total Investments:</span> {{ number_format($groupData['TotalInvestments'], 2) }}
        </div>
        <div class="data-row">
            <span class="label">Total Returns:</span> {{ number_format($groupData['TotalReturns'], 2) }}
        </div>
        <div class="data-row">
            <span class="label">Net Profit:</span> {{ number_format($groupData['Profit'], 2) }}
        </div>
        <div class="data-row">
            <span class="label">Emergency Fund:</span> {{ number_format($groupData['EmergencyFund'], 2) }}
        </div>
    </div>

    <!-- Member Information Section -->
    <div class="section">
        <div class="section-title">Member Information</div>
        <div class="data-row">
            <span class="label">Member Name:</span> {{ $memberData['MemberName'] }}
        </div>
        <div class="data-row">
            <span class="label">Role:</span> {{ $memberData['Role'] }}
        </div>
        <div class="data-row">
            <span class="label">Join Date:</span> {{ $memberData['JoinDate'] }}
        </div>
        <div class="data-row">
            <span class="label">Total Savings:</span> {{ number_format($memberData['TotalSavings'], 2) }}
        </div>
        <div class="data-row">
            <span class="label">Total Loans:</span> {{ number_format($memberData['TotalLoans'], 2) }}
        </div>
        <div class="data-row">
            <span class="label">Total Withdrawals:</span> {{ number_format($memberData['TotalWithdrawals'], 2) }}
        </div>
    </div>

    <!-- Recent Transactions Section -->
    <div class="section">
        <div class="section-title">Recent Transactions</div>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction['transaction_id'] }}</td>
                    <td>{{ number_format($transaction['amount'], 2) }}</td>
                    <td>{{ $transaction['payment_method'] }}</td>
                    <td>{{ $transaction['PaymentTime'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recent Loans Section -->
    <div class="section">
        <div class="section-title">Recent Loans</div>
        <table>
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Approve Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $loan)
                <tr>
                    <td>{{ number_format($loan['amount'], 2) }}</td>
                    <td>{{ $loan['ApproveDate'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Recent Withdrawals Section -->
    <div class="section">
        <div class="section-title">Recent Withdrawals</div>
        <table>
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Approve Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdrawals as $withdrawal)
                <tr>
                    <td>{{ number_format($withdrawal['amount'], 2) }}</td>
                    <td>{{ $withdrawal['ApproveDate'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated report. No signature is required.</p>
    </div>
</body>
</html> 