<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ledger Entries</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; }
        .text-end { text-align: right; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .header { margin-bottom: 20px; }
        .summary { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; width: 25%; }
        .summary-value { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Ledger Entries</h2>
        <p>Generated on: {{ \Carbon\Carbon::now()->format('d M, Y h:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div>Total Debit</div>
            <div class="summary-value text-success">₹{{ number_format($totalDebit, 2) }}</div>
        </div>
        <div class="summary-item">
            <div>Total Credit</div>
            <div class="summary-value text-danger">₹{{ number_format($totalCredit, 2) }}</div>
        </div>
        <div class="summary-item">
            <div>Net Balance</div>
            <div class="summary-value">₹{{ number_format($netBalance, 2) }}</div>
        </div>
        <div class="summary-item">
            <div>Transactions</div>
            <div class="summary-value">{{ $totalTransactions }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Party</th>
                <th>Type</th>
                <th class="text-end">Debit (₹)</th>
                <th class="text-end">Credit (₹)</th>
                <th class="text-end">Balance (₹)</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d M, Y') }}</td>
                    <td>{{ $entry->party->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($entry->entry_type) }}</td>
                    <td class="text-end {{ $entry->debit > 0 ? 'text-success' : '' }}">
                        {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '' }}
                    </td>
                    <td class="text-end {{ $entry->credit > 0 ? 'text-danger' : '' }}">
                        {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '' }}
                    </td>
                    <td class="text-end fw-bold">
                        {{ number_format($entry->balance_after, 2) }}
                    </td>
                    <td>
                        @if($entry->entry_type == 'purchase' && $entry->reference)
                            @foreach($entry->reference->items ?? [] as $item)
                                {{ $item->grain->name ?? 'N/A' }} ({{ $item->quantity }} {{ $item->unit }} @ ₹{{ $item->rate }})<br>
                            @endforeach
                        @elseif($entry->entry_type == 'sale' && $entry->reference)
                            {{ $entry->reference->grain->name ?? 'N/A' }} ({{ $entry->reference->quantity }} {{ $entry->reference->unit }} @ ₹{{ $entry->reference->rate }})
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
