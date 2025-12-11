<!DOCTYPE html>
<html>
<head>
    <title>Financial Report - {{ $data['period']['type'] }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
        .card { border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Report - {{ ucfirst($data['period']['type']) }}</h1>
        <p>{{ \Carbon\Carbon::parse($data['period']['start'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($data['period']['end'])->format('M d, Y') }}</p>
        <p>Generated for: {{ $user->first_name }} {{ $user->last_name }}</p>
    </div>

    <div class="summary">
        <div class="card">
            <h3>Total Income</h3>
            <p style="font-size: 24px; color: green;">€{{ number_format($data['summary']['total_income'], 2) }}</p>
        </div>
        <div class="card">
            <h3>Total Expenses</h3>
            <p style="font-size: 24px; color: red;">€{{ number_format($data['summary']['total_expenses'], 2) }}</p>
        </div>
        <div class="card">
            <h3>Net Cash Flow</h3>
            <p style="font-size: 24px;">€{{ number_format($data['summary']['net_cash_flow'], 2) }}</p>
        </div>
        <div class="card">
            <h3>Savings Rate</h3>
            <p style="font-size: 24px;">{{ number_format($data['summary']['savings_rate'], 1) }}%</p>
        </div>
    </div>

    <h2>Net Worth Summary</h2>
    <table>
        <tr>
            <th>Total Assets</th>
            <th>Total Debts</th>
            <th>Net Worth</th>
        </tr>
        <tr>
            <td>€{{ number_format($data['summary']['total_assets'], 2) }}</td>
            <td>€{{ number_format($data['summary']['total_debts'], 2) }}</td>
            <td>€{{ number_format($data['summary']['net_worth'], 2) }}</td>
        </tr>
    </table>
</body>
</html>

