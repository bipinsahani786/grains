<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Voucher - #{{ $purchase->purchase_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; margin: 0; padding: 0; background-color: #fff; }
        .bill-container { width: 100%; max-width: 800px; margin: 0 auto; border: 2px solid #000; box-sizing: border-box; }
        
        /* Top Header */
        .top-row { display: flex; justify-content: space-between; padding: 4px 10px; font-weight: bold; font-size: 13px; }
        .top-row-left { line-height: 1.4; }
        .top-row-center { background: #000; color: #fff; padding: 3px 12px; height: fit-content; margin-top: 5px; font-size: 14px; letter-spacing: 1px; }
        .top-row-right { line-height: 1.4; text-align: right; }
        
        .main-title { text-align: center; margin: 3px 0; }
        .main-title h1 { font-size: 32px; margin: 0; text-transform: uppercase; letter-spacing: 1px; font-family: 'Times New Roman', Times, serif; }
        .main-title p { margin: 2px 0; font-size: 14px; font-weight: bold; }
        
        .bill-info { display: flex; justify-content: space-between; padding: 4px 10px; border-top: 2px solid #000; border-bottom: 2px solid #000; font-weight: bold; font-size: 14px; }
        
        /* Sections */
        .section-box { padding: 3px 10px; border-bottom: 2px solid #000; font-size: 13px; font-weight: bold; line-height: 1.6; }
        .section-box .title { font-size: 13px; margin-bottom: 2px; text-decoration: underline; }
        
        /* Items Table */
        table.items { width: 100%; border-collapse: collapse; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 12px; font-weight: bold; }
        table.items th { background: #fff; }
        table.items td.left { text-align: left; }
        table.items td.right { text-align: right; }
        
        /* Lower Section */
        .lower-section { display: flex; border-top: 2px solid #000; }
        .lower-left { width: 55%; border-right: 2px solid #000; display: flex; flex-direction: column; }
        .lower-right { width: 45%; }
        
        /* Summary Table */
        .summary-table { width: 100%; border-collapse: collapse; font-size: 12px; font-weight: bold; }
        .summary-table td { border-bottom: 1px solid #000; padding: 4px 6px; }
        .summary-table td:first-child { border-right: 1px solid #000; }
        
        .signatory-box { padding: 5px; text-align: center; font-weight: bold; font-size: 13px; min-height: 80px; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .bill-container { border: 2px solid #000; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding: 10px; background:#f0f0f0; margin-bottom:10px;">
        <button onclick="window.print()" style="padding: 8px 24px; font-size:16px; font-weight:bold; cursor:pointer; background:#28a745; color:#fff; border:none; border-radius:4px;">Print Purchase Voucher</button>
        <a href="?with_header={{ request('with_header') == 'true' ? 'false' : 'true' }}" style="margin-left:20px; text-decoration:none; font-weight:bold; color:#0066cc;">
            Toggle Header Image (Current: {{ request('with_header') == 'true' ? 'ON' : 'OFF' }})
        </a>
    </div>
    
    <div class="bill-container">
        @if(request('with_header') == 'true' && $company->purchase_header_path)
            <div style="text-align: center; border-bottom: 2px solid #000;">
                <img src="{{ Storage::url($company->purchase_header_path) }}" alt="Header" style="max-width:100%; max-height:140px; display:block; margin:0 auto;">
            </div>
        @elseif(request('with_header') != 'false')
            <div class="top-row">
                <div class="top-row-left">
                    GSTIN : {{ $company->gstin ?? 'N/A' }}<br>
                    PAN No : {{ $company->pan_display ?? ($company->pan_no ?? 'N/A') }}
                </div>
                <div class="top-row-center">PURCHASE VOUCHER</div>
                <div class="top-row-right">
                    📞 {{ $company->phone ?? 'N/A' }}<br>
                    💬 {{ $company->whatsapp_no ?? ($company->phone ?? 'N/A') }}
                </div>
            </div>
            <div class="main-title">
                <h1>{{ $company->brand_name ?? $company->name }}</h1>
                <p>{{ $company->address }}</p>
            </div>
        @else
            <div style="height: 120px;"></div>
        @endif
        
        <div class="bill-info">
            <div>Purchase No :- <span style="color:red; font-size:16px;">{{ $purchase->purchase_no }}</span></div>
            <div>Date : {{ \Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }} {{ $purchase->purchase_time ? \Carbon\Carbon::parse($purchase->purchase_time)->format('h:i A') : '' }}</div>
        </div>
        
        {{-- Supplier / Seller Details --}}
        <div class="section-box">
            <div class="title">Details of Supplier / Seller</div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">Seller Name:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $purchase->party->name ?? 'Cash Seller' }}</span>
                @if($purchase->broker)
                    <span style="white-space: nowrap;">Broker:</span>
                    <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $purchase->broker->name }}</span>
                @endif
            </div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">Address:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $purchase->party->address ?? 'N/A' }}</span>
                <span style="white-space: nowrap;">Phone:</span>
                <span style="border-bottom: 1px dotted #000; width: 140px; font-weight: normal;">{{ $purchase->party->phone ?? 'N/A' }}</span>
            </div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">GST No.:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $purchase->party->gst_no ?? 'Unregistered' }}</span>
                <span style="white-space: nowrap;">Aadhar / PAN:</span>
                <span style="border-bottom: 1px dotted #000; width: 150px; font-weight: normal;">{{ $purchase->party->aadhar_no ?? ($purchase->party->pan_no ?? 'N/A') }}</span>
                <span style="white-space: nowrap;">State Code:</span>
                <span style="border-bottom: 1px dotted #000; width: 45px; text-align: center; font-weight: normal;">{{ $purchase->party->gst_no && strlen($purchase->party->gst_no) >= 2 ? substr($purchase->party->gst_no, 0, 2) : '' }}</span>
            </div>
        </div>
        
        {{-- Items Table --}}
        <table class="items">
            <thead>
                <tr>
                    <th rowspan="2" style="width:35px;">Sl.<br>No.</th>
                    <th rowspan="2">PARTICULARS / GRAIN</th>
                    <th rowspan="2" style="width:60px;">HSN</th>
                    <th rowspan="2" style="width:80px;">Godown</th>
                    <th colspan="2" style="border-bottom: 1px solid #000;">Weight</th>
                    <th rowspan="2" style="width:85px;">RATE<br>(QUN.)</th>
                    <th colspan="2" style="border-bottom: 1px solid #000;">Amount</th>
                </tr>
                <tr>
                    <th style="width:50px; border-top: 1px solid #000;">QUN.</th>
                    <th style="width:40px; border-top: 1px solid #000;">KG</th>
                    <th style="width:75px; border-top: 1px solid #000;">Rs.</th>
                    <th style="width:28px; border-top: 1px solid #000;">P.</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $itemsGrossTotal = 0;
                @endphp
                @foreach($purchase->items as $index => $item)
                    @php
                        $qtyInQtl = (float) $item->quantity;
                        $ratePerQtl = (float) $item->rate;
                        $lineTotal = round($qtyInQtl * $ratePerQtl, 2);
                        $itemsGrossTotal += $lineTotal;
                        
                        $qtyQun = floor($qtyInQtl);
                        $qtyKg = round(($qtyInQtl - $qtyQun) * 100, 2);
                        $formattedKg = fmod($qtyKg, 1) !== 0.0 ? number_format($qtyKg, 2) : str_pad((int)$qtyKg, 2, '0', STR_PAD_LEFT);
                        
                        $lineRs = floor($lineTotal);
                        $lineP = round(($lineTotal - $lineRs) * 100);
                    @endphp
                    <tr style="height: 38px; vertical-align: middle;">
                        <td>{{ $index + 1 }}</td>
                        <td class="left">
                            <strong>{{ $item->grain->name ?? 'N/A' }}</strong>
                            @if($item->moisture)
                                <small class="text-muted">(Moisture: {{ $item->moisture }}%)</small>
                            @endif
                        </td>
                        <td>{{ $item->grain->hsn_code ?? '' }}</td>
                        <td><small>{{ $item->godown->name ?? 'Main' }}</small></td>
                        <td>{{ $qtyQun }}</td>
                        <td>{{ $formattedKg }}</td>
                        <td class="right">{{ number_format($ratePerQtl, 2) }}</td>
                        <td class="right">{{ number_format($lineRs) }}</td>
                        <td>{{ str_pad($lineP, 2, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                @endforeach
                
                @if($purchase->items->count() < 3)
                    @for($i = $purchase->items->count() + 1; $i <= 3; $i++)
                    <tr style="height: 28px;">
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    @endfor
                @endif
            </tbody>
        </table>
        
        {{-- Lower Section --}}
        @php
            $grandTotal = (float) $purchase->total_amount;
            $grandRs = floor($grandTotal);
            $grandP = round(($grandTotal - $grandRs) * 100);
            $paidTotal = $purchase->total_paid;
            $dueTotal = $purchase->remaining_outstanding;
        @endphp

        <div class="lower-section">
            <div class="lower-left">
                {{-- Amount in Words --}}
                <div style="padding: 5px 10px; font-size:12px; font-weight:bold; border-bottom: 2px solid #000;">
                    Rs. in words <span style="font-weight:normal; font-style:italic;">{{ \App\Helpers\UnitHelper::amountToWords($grandTotal) }}</span>
                </div>
                
                {{-- Bank Details --}}
                <div style="padding: 5px 10px; font-size: 11px; border-bottom: 1px solid #000; flex-grow: 1;">
                    <div style="font-weight: bold; margin-bottom: 2px; text-decoration: underline;">Buyer Bank Details :</div>
                    <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 1px 0; width: 90px;">Bank Name:</td>
                            <td style="padding: 1px 0; font-weight: normal;">{{ $company->bank_name ?? '' }} {{ $company->branch_name ? '('.$company->branch_name.')' : '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">A/C Holder:</td>
                            <td style="padding: 1px 0; font-weight: normal;">{{ $company->account_holder ?? $company->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">A/C No:</td>
                            <td style="padding: 1px 0; font-weight: normal;">{{ $company->account_no ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">IFSC Code:</td>
                            <td style="padding: 1px 0; font-weight: normal;">{{ $company->ifsc_code ?? '' }}</td>
                        </tr>
                    </table>
                </div>
                
                {{-- Terms & Conditions --}}
                @if($company->billing_terms_conditions)
                <div style="padding: 4px 10px; font-size: 10px;">
                    <div style="font-weight: bold; margin-bottom: 1px; text-decoration: underline;">Terms & Conditions :</div>
                    <div style="font-weight: normal; white-space: pre-wrap; line-height: 1.3;">{{ $company->billing_terms_conditions }}</div>
                </div>
                @endif
            </div>
            
            <div class="lower-right">
                {{-- Summary Table --}}
                <table class="summary-table">
                    <tr>
                        <td class="left">Items Total :</td>
                        <td class="right" style="width: 95px;">₹{{ number_format($itemsGrossTotal, 2) }}</td>
                    </tr>
                    @if(isset($purchase->charges) && $purchase->charges->count() > 0)
                        @foreach($purchase->charges as $charge)
                            @php
                                $isDeduct = str_starts_with(strtolower($charge->type), 'deduct');
                            @endphp
                            <tr>
                                <td class="left">{{ $charge->type ?? 'Charge' }} :</td>
                                <td class="right">{{ $isDeduct ? '-' : '+' }}₹{{ number_format($charge->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                    <tr>
                        <td class="left">Total Purchase Amount :</td>
                        <td class="right">₹{{ number_format($grandRs) }}.{{ str_pad($grandP, 2, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    @if($paidTotal > 0)
                        <tr>
                            <td class="left" style="color:green;">Amount Paid :</td>
                            <td class="right" style="color:green;">₹{{ number_format($paidTotal, 2) }}</td>
                        </tr>
                        @if($dueTotal > 0)
                            <tr>
                                <td class="left" style="color:red;">Balance Payable Due :</td>
                                <td class="right" style="color:red;">₹{{ number_format($dueTotal, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td class="left" style="color:green;">Payment Status :</td>
                                <td class="right" style="color:green;">Fully Paid ✓</td>
                            </tr>
                        @endif
                    @else
                        <tr>
                            <td class="left" style="color:red;">Balance Payable Due :</td>
                            <td class="right" style="color:red;">₹{{ number_format($dueTotal, 2) }}</td>
                        </tr>
                    @endif
                </table>
                
                {{-- Signatory --}}
                <div class="signatory-box">
                    <div style="font-weight: bold; font-size: 13px; margin-bottom: 2px; text-align: right; padding-right: 15px;">For, {{ $company->brand_name ?? $company->name }}</div>
                    <div style="min-height: 95px; display: flex; align-items: center; justify-content: flex-end; padding-right: 15px; margin: 2px 0;">
                        @if($company->signature_stamp_path)
                            <img src="{{ Storage::url($company->signature_stamp_path) }}" alt="Signature" style="max-height: 95px; max-width: 250px; width: auto; object-fit: contain; display: block;">
                        @endif
                    </div>
                    <div class="auth" style="font-size: 13px; font-weight: bold; text-align: right; padding-right: 15px;">{{ $company->billing_authorised_signatory_text ?? 'Authorised Signatory' }}</div>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>
