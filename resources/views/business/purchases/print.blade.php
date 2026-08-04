<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill of Supply - Purchase #{{ $purchase->purchase_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .bill-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            box-sizing: border-box;
            background-color: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            position: relative;
        }
        .header img {
            max-width: 100%;
            max-height: 150px;
            display: block;
            margin: 0 auto;
        }
        .text-header {
            padding: 10px;
        }
        .text-header h1 {
            margin: 5px 0;
            font-size: 28px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .text-header p {
            margin: 2px 0;
            font-size: 14px;
        }
        .top-info {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 14px;
        }
        .bill-info {
            display: flex;
            justify-content: space-between;
            padding: 5px 10px;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 16px;
        }
        .consignee-box {
            padding: 5px 10px;
            border-bottom: 2px solid #000;
        }
        .consignee-box h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            text-decoration: underline;
        }
        .details-grid {
            display: table;
            width: 100%;
        }
        .details-row {
            display: table-row;
        }
        .details-cell {
            display: table-cell;
            padding: 2px 0;
            font-size: 14px;
        }
        .details-cell.label {
            width: 80px;
        }
        .details-cell.value {
            border-bottom: 1px dotted #000;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 14px;
        }
        .items-table th {
            font-weight: bold;
        }
        .items-table td.left {
            text-align: left;
        }
        .items-table td.right {
            text-align: right;
        }
        .totals-row td {
            font-weight: bold;
        }
        .footer-section {
            display: flex;
            border-top: 2px solid #000;
        }
        .bank-details {
            width: 50%;
            border-right: 2px solid #000;
            padding: 5px 10px;
            font-size: 12px;
        }
        .bank-details h4 {
            margin: 0 0 5px 0;
            text-decoration: underline;
        }
        .signatory-box {
            width: 50%;
            position: relative;
            padding: 5px 10px;
            min-height: 100px;
        }
        .signatory-box .for-company {
            text-align: center;
            font-weight: bold;
            margin-top: 5px;
        }
        .signatory-box .auth-sign {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-weight: bold;
        }
        .terms {
            padding: 5px 10px;
            font-size: 11px;
            border-top: 1px solid #000;
        }
        
        .footer-img {
            width: 100%;
            border-top: 2px solid #000;
        }
        .footer-img img {
            max-width: 100%;
            display: block;
        }
        
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .bill-container { border: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align:center; padding: 10px; background:#f0f0f0; margin-bottom:10px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size:16px; cursor:pointer;">Print Bill</button>
        <a href="?with_header={{ request('with_header') == 'true' ? 'false' : 'true' }}" style="margin-left:20px;">
            Toggle Header/Footer (Current: {{ request('with_header') == 'true' ? 'ON' : 'OFF' }})
        </a>
    </div>

    <div class="bill-container">
        
        <!-- Header -->
        @if(request('with_header') == 'true' && $company->purchase_header_path)
            <div class="header" style="border-bottom:none; padding:0;">
                <img src="{{ Storage::url($company->purchase_header_path) }}" alt="Header">
            </div>
            <div style="border-bottom: 2px solid #000;"></div>
        @elseif(request('with_header') != 'false')
            <div class="header">
                <div class="top-info">
                    <div>GSTIN: {{ $company->gstin ?? 'N/A' }}</div>
                    <div><span style="background:#000; color:#fff; padding:2px 5px;">BILL OF SUPPLY</span></div>
                    <div>Ph: {{ $company->phone ?? 'N/A' }}</div>
                </div>
                <div class="text-header">
                    <h1>{{ $company->name }}</h1>
                    <p>{{ $company->address }}</p>
                </div>
            </div>
        @else
            <!-- Blank space for pre-printed letterhead -->
            <div style="height: 150px;"></div>
            <div style="border-bottom: 2px solid #000;"></div>
        @endif

        <div class="bill-info">
            <div>Purchase No :- <span style="color:red;">{{ $purchase->purchase_no }}</span></div>
            <div>Date : {{ \Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}</div>
        </div>

        <div class="consignee-box">
            <h3>Details of Seller</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 80px; vertical-align: top; font-size: 14px; padding: 2px 0;">Name :</td>
                    <td style="border-bottom: 1px dotted #000; vertical-align: top; font-size: 14px; padding: 2px 0;">{{ $purchase->party->name ?? 'Cash' }}</td>
                </tr>
                <tr>
                    <td style="width: 80px; vertical-align: top; font-size: 14px; padding: 2px 0;">Address :</td>
                    <td style="border-bottom: 1px dotted #000; vertical-align: top; font-size: 14px; padding: 2px 0;">{{ $purchase->party->address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="width: 80px; vertical-align: top; font-size: 14px; padding: 2px 0;">GST No. :</td>
                    <td style="border-bottom: 1px dotted #000; vertical-align: top; font-size: 14px; padding: 2px 0;">{{ $purchase->party->gst_no ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:50px;">Sl. No.</th>
                    <th>PARTICULARS</th>
                    <th style="width:120px;">Quantity</th>
                    <th style="width:100px;">RATE</th>
                    <th style="width:120px;">Amount<br>Rs.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="left">
                            <strong>{{ $item->grain->name ?? 'N/A' }}</strong>
                            @if($item->unit == 'Bags' && $item->quantity)
                            <br><small>({{ $item->quantity }} Bags)</small>
                            @endif
                        </td>
                        
                        <td>@qty($item->quantity)</td>
                        
                        <td class="right">@rateRaw($item->rate)</td>
                        <td class="right">{{ number_format($item->quantity * $item->rate, 2) }}</td>
                    </tr>
                @endforeach
                
                @if($purchase->items->count() < 4)
                    <!-- Empty rows to fill space if there are few items -->
                    @for($i = $purchase->items->count() + 1; $i <= 4; $i++)
                    <tr style="height: 30px;">
                        <td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    @endfor
                @endif
                
                <tr class="totals-row">
                    <td colspan="4" class="right" style="font-size:16px;"><strong>Net Amount :</strong></td>
                    <td class="right" style="font-size:16px;"><strong>{{ number_format($purchase->total_amount, 2) }}</strong></td>
                </tr>
                
                @if(isset($purchase->payments) && $purchase->payments->count() > 0)
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="4" class="right" style="font-size:12px; padding: 2px 5px;">
                            <strong>Payments Received:</strong>
                            @foreach($purchase->payments as $payment)
                                <br>{{ ucfirst($payment->mode) }}: 
                            @endforeach
                        </td>
                        <td class="right" style="font-size:12px; padding: 2px 5px;">
                            <br>
                            @foreach($purchase->payments as $payment)
                                {{ number_format($payment->amount, 2) }}<br>
                            @endforeach
                        </td>
                    </tr>
                    <tr class="totals-row">
                        <td colspan="4" class="right" style="font-size:14px; color: #d9534f;"><strong>Balance Due :</strong></td>
                        <td class="right" style="font-size:14px; color: #d9534f;"><strong>{{ number_format($purchase->total_amount - $purchase->payments->sum('amount'), 2) }}</strong></td>
                    </tr>
                @else
                    <tr class="totals-row">
                        <td colspan="4" class="right" style="font-size:14px; color: #d9534f;"><strong>Balance Due :</strong></td>
                        <td class="right" style="font-size:14px; color: #d9534f;"><strong>{{ number_format($purchase->total_amount, 2) }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div style="padding: 10px; border-bottom: 2px solid #000;">
            <strong>Rs. in words: </strong> 
            <span style="font-style: italic;">
                Rupees {{ number_format($purchase->total_amount, 2) }} Only.
            </span>
        </div>

        <div class="footer-section">
            <div class="bank-details" style="border-right: none;">
                @if($company->billing_terms_conditions)
                <div class="terms" style="margin-top: 10px; border-top: none;">
                    <strong>Term & Conditions</strong><br>
                    <div style="white-space: pre-wrap;">{{ $company->billing_terms_conditions }}</div>
                </div>
                @endif
            </div>
            <div class="signatory-box">
                <div class="for-company" style="text-align: right;">For, {{ $company->name }}</div>
                <div class="auth-sign">
                    @if($company->signature_stamp_path)
                        <img src="{{ asset('storage/' . $company->signature_stamp_path) }}" alt="Signature" style="max-height: 50px; display: block; margin: 5px 0 5px auto;">
                    @endif
                    {{ $company->billing_authorised_signatory_text ?? 'Authorised Signatory' }}
                </div>
            </div>
        </div>

        <!-- Footer Image -->
        @if(request('with_header') == 'true' && $company->purchase_footer_path)
            <div class="footer-img">
                <img src="{{ Storage::url($company->purchase_footer_path) }}" alt="Footer">
            </div>
        @endif

    </div>

</body>
</html>
