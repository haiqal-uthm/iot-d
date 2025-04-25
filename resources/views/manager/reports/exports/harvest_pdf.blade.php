<!DOCTYPE html>
<html>
<head>
    <title>Harvest Report - {{ now()->format('Y-m-d') }}</title>
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
        <h1>Harvest Report</h1>
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if($filters['start_date'] || $filters['end_date'])
    <div class="filter-info">
        <h3>Filters Applied:</h3>
        <p>Date Range: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</p>
        <p>Farmer ID: {{ $filters['farmer_id'] ?? 'N/A' }}</p>
        <p>Durian ID: {{ $filters['durian_id'] ?? 'N/A' }}</p>
        <p>Orchard ID: {{ $filters['orchard_id'] ?? 'N/A' }}</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Farmer</th>
                <th>Durian Type</th>
                <th>Quantity</th>
                <th>Storage</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($harvests as $harvest)
            <tr>
                <td>{{ $harvest->harvest_date->format('Y-m-d') }}</td>
                <td>{{ $harvest->farmer->user->name }}</td>
                <td>{{ $harvest->durian->name }}</td>
                <td>{{ $harvest->total_harvested }}</td>
                <td>{{ $harvest->storage->name ?? 'Not Assigned' }}</td>
                <td>{{ $harvest->storage->status ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>