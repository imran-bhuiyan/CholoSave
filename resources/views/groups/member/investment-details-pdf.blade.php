<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Investment Details - {{ $group->group_name }}</title>
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
        .stats {
            margin-bottom: 20px;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .stat-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .amount {
            text-align: right;
        }
        .status {
            font-weight: bold;
        }
        .status-pending { color: #e67e22; }
        .status-active { color: #27ae60; }
        .status-completed { color: #2980b9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Investment Details</h1>
        <p>Group: {{ $group->group_name }}</p>
        <p>Generated on: {{ now()->format('d F, Y') }}</p>
    </div>

    <!-- Summary Section -->
    <div class="section">
        <div class="section-title">Investment Summary</div>
        <div class="stats">
            <div class="stat-row">
                <span class="stat-label">Total Investments:</span>
                <span class="amount">BDT {{ number_format($totalInvestment, 2) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Expected Returns:</span>
                <span class="amount">BDT {{ number_format($totalExpected, 2) }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">Actual Returns:</span>
                <span class="amount">BDT {{ number_format($totalActual, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Investment Details Section -->
    <div class="section">
        <div class="section-title">Investment History</div>
        <table>
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Expected</th>
                    <th>Actual</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($investments as $investment)
                    <tr>
                        <td class="amount">BDT {{ number_format($investment->amount, 2) }}</td>
                        <td>{{ $investment->investment_type }}</td>
                        <td class="status status-{{ $investment->status }}">
                            {{ ucfirst($investment->status) }}
                        </td>
                        <td class="amount">BDT {{ number_format($investment->ex_profit, 2) }}</td>
                        <td class="amount">
                            @if($investment->returns->isNotEmpty())
                                BDT {{ number_format($investment->returns->sum('amount'), 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($investment->returns->isNotEmpty())
                                {{ $investment->returns->first()->created_at->format('d M, Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated report. No signature is required.</p>
        <p>Thank you for using CholoSave Investment Services</p>
    </div>
</body>
</html> 