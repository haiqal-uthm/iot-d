<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report - {{ now()->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .filter-info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Report</h1>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if($filters['start_date'] || $filters['end_date'])
    <div class="filter-info">
        <h3>Filters Applied:</h3>
        <p>Date Range: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</p>
        <p>Durian ID: {{ $filters['durian_id'] ?? 'All Types' }}</p>
        <p>Storage Location: {{ $filters['storage_location'] ?? 'All Locations' }}</p>
        <p>Transaction Type: {{ $filters['type'] ?? 'All Types' }}</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date Added</th>
                <th>Durian Type</th>
                <th>Weight/Quantity</th>
                <th>Type</th>
                <th>Storage Location</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $transaction->durian->name ?? 'N/A' }}</td>
                <td>{{ $transaction->quantity }}</td>
                <td>{{ ucfirst($transaction->type) }}</td>
                <td>{{ $transaction->storage->name ?? 'N/A' }}</td>
                <td>{{ $transaction->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>