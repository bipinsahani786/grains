<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill of Supply - Sale #{{ $sale->sale_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #000; margin: 0; padding: 0; background-color: #fff; }
        .bill-container { width: 100%; max-width: 800px; margin: 0 auto; border: 2px solid #000; box-sizing: border-box; }
        
        /* Top Header */
        .top-row { display: flex; justify-content: space-between; padding: 4px 10px; font-weight: bold; font-size: 13px; }
        .top-row-left { line-height: 1.4; }
        .top-row-center { background: #000; color: #fff; padding: 2px 10px; height: fit-content; margin-top: 5px; font-size: 14px; }
        .top-row-right { line-height: 1.4; text-align: right; }
        
        .main-title { text-align: center; margin: 3px 0; }
        .main-title h1 { font-size: 34px; margin: 0; text-transform: uppercase; letter-spacing: 1px; font-family: 'Times New Roman', Times, serif; }
        .main-title p { margin: 2px 0; font-size: 15px; font-weight: bold; }
        
        .bill-info { display: flex; justify-content: space-between; padding: 4px 10px; border-top: 2px solid #000; border-bottom: 2px solid #000; font-weight: bold; font-size: 15px; }
        
        /* Sections */
        .section-box { padding: 3px 10px; border-bottom: 2px solid #000; font-size: 13px; font-weight: bold; line-height: 1.6; }
        .section-box .title { font-size: 13px; margin-bottom: 2px; text-decoration: underline; }
        
        /* Items Table */
        table.items { width: 100%; border-collapse: collapse; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 13px; font-weight: bold; }
        table.items th { background: #fff; }
        table.items td.left { text-align: left; }
        table.items td.right { text-align: right; }
        
        /* Lower Section */
        .lower-section { display: flex; border-top: 2px solid #000; }
        .lower-left { width: 55%; border-right: 2px solid #000; display: flex; flex-direction: column; }
        .lower-right { width: 45%; }
        
        /* Summary Table */
        .summary-table { width: 100%; border-collapse: collapse; font-size: 13px; font-weight: bold; }
        .summary-table td { border-bottom: 1px solid #000; padding: 4px 5px; }
        .summary-table td:first-child { border-right: 1px solid #000; }
        
        /* Dispatch */
        .dispatch-info { padding: 4px 10px; font-size: 11px; font-weight: bold; line-height: 1.9; border-bottom: 2px solid #000; }
        .dispatch-val { border-bottom: 1px solid #000; display: inline-block; font-weight: normal; padding: 0 3px; min-width: 40px; }
        .signatory-box { padding: 5px; text-align: center; font-weight: bold; font-size: 14px; min-height: 70px; position: relative; }
        .signatory-box .auth { position: absolute; bottom: 5px; right: 10px; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .bill-container { border: 2px solid #000; }
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
        @if(request('with_header') == 'true' && $company->sale_header_path)
            <div style="text-align: center; border-bottom: 2px solid #000;">
                <img src="{{ asset('storage/' . $company->sale_header_path) }}" alt="Header" style="max-width:100%; max-height:140px; display:block; margin:0 auto;">
            </div>
        @elseif(request('with_header') != 'false')
            <div class="top-row">
                <div class="top-row-left">
                    GSTIN : {{ $company->gstin ?? 'N/A' }}<br>
                    PAN No : {{ $company->pan_display ?? ($company->pan_no ?? 'N/A') }}
                </div>
                <div class="top-row-center">BILL OF SUPPLY</div>
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
            <div>Bill No :- <span style="color:red; font-size:18px;">{{ $sale->sale_no }}</span></div>
            <div>Date : {{ \Carbon\Carbon::parse($sale->date)->format('d-m-Y') }}</div>
        </div>
        
        {{-- Consignee --}}
        <div class="section-box">
            <div class="title">Details of Consignee (Shipped to)</div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">Name:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->party->name ?? '' }}</span>
                <span style="white-space: nowrap;">Broker Name:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->broker->name ?? '' }}</span>
            </div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">Address:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->party->address ?? '' }}</span>
            </div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">GST No.:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->party->gst_no ?? 'N/A' }}</span>
                <span style="white-space: nowrap;">Po No:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->po_no ?? '' }}</span>
                <span style="white-space: nowrap;">STATE Code:</span>
                <span style="border-bottom: 1px dotted #000; width: 50px; text-align: center; font-weight: normal;">{{ $sale->party->gst_no && strlen($sale->party->gst_no) >= 2 ? substr($sale->party->gst_no, 0, 2) : '' }}</span>
            </div>
        </div>
        
        {{-- Receiver --}}
        <div class="section-box">
            <div class="title">Details of Delivery and Receivers</div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">Name:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->party->name ?? '' }}</span>
            </div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">Address:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->party->address ?? '' }}</span>
            </div>
            <div style="display: flex; gap: 5px;">
                <span style="white-space: nowrap;">GST No.:</span>
                <span style="border-bottom: 1px dotted #000; flex-grow: 1; font-weight: normal;">{{ $sale->party->gst_no ?? 'N/A' }}</span>
            </div>
        </div>
        
        {{-- Items Table --}}
        <?php
            // quantity and rate are ALREADY stored in Quintals / per-Quintal by the controller
            // so we use them directly — NO re-conversion
            $qtyInQtl = (float) $sale->quantity;
            $ratePerQtl = (float) $sale->rate;
            
            $qtyQun = floor($qtyInQtl);
            $qtyKg = round(($qtyInQtl - $qtyQun) * 100, 2);
            $formattedKg = fmod($qtyKg, 1) !== 0.0 ? number_format($qtyKg, 2) : str_pad((int)$qtyKg, 2, '0', STR_PAD_LEFT);
            
            // Gross item amount
            $itemGrossTotal = round($qtyInQtl * $ratePerQtl, 2);
            $itemGrossRs = floor($itemGrossTotal);
            $itemGrossP = round(($itemGrossTotal - $itemGrossRs) * 100);
            
            // Net Amount
            $amtTotal = $sale->net_amount ?? $sale->total_amount;
            $amtRs = floor($amtTotal);
            $amtP = round(($amtTotal - $amtRs) * 100);
        ?>
        <table class="items">
            <thead>
                <tr>
                    <th rowspan="2" style="width:35px;">Sl.<br>No.</th>
                    <th rowspan="2">PARTICULARS</th>
                    <th rowspan="2" style="width:50px;">HSN</th>
                    <th rowspan="2" style="width:45px;">Bags</th>
                    <th colspan="2">Weight</th>
                    <th rowspan="2" style="width:80px;">RATE<br>(QUN.)</th>
                    <th colspan="2">Amount</th>
                </tr>
                <tr>
                    <th style="width:50px; border-top: 1px solid #000;">QUN.</th>
                    <th style="width:40px; border-top: 1px solid #000;">KG</th>
                    <th style="width:80px; border-top: 1px solid #000;">Rs.</th>
                    <th style="width:30px; border-top: 1px solid #000;">P.</th>
                </tr>
            </thead>
            <tbody>
                <tr style="height: 75px; vertical-align: top;">
                    <td>1</td>
                    <td class="left">
                        {{ $sale->grain->name ?? 'N/A' }}
                        @if($sale->notes)
                            <br><span style="font-weight: normal; font-size: 12px; color: #444;">{{ $sale->notes }}</span>
                        @endif
                    </td>
                    <td>{{ $sale->grain->hsn_code ?? '' }}</td>
                    <td>{{ $sale->bags_count ?? ($sale->unit == 'Bags' ? (int)$sale->quantity : '') }}</td>
                    <td>{{ $qtyQun }}</td>
                    <td>{{ $formattedKg }}</td>
                    <td class="right">{{ number_format($ratePerQtl, 2) }}</td>
                    <td class="right">{{ number_format($itemGrossRs) }}</td>
                    <td>{{ str_pad($itemGrossP, 2, '0', STR_PAD_LEFT) }}</td>
                </tr>
                @if(isset($sale->payments) && $sale->payments->count() > 0)
                <tr style="vertical-align: top;">
                    <td colspan="7" class="left" style="font-size:11px;">
                        <em>Payments: @foreach($sale->payments as $payment) {{ str_replace('_', ' ', ucfirst($payment->mode)) }} (₹{{ number_format($payment->amount, 2) }}) @endforeach</em>
                    </td>
                    <td class="right"></td>
                    <td></td>
                </tr>
                @endif
            </tbody>
        </table>
        
        {{-- Lower Section --}}
        <div class="lower-section">
            <div class="lower-left">
                {{-- Amount in Words --}}
                <div style="padding: 6px 10px; font-size:13px; font-weight:bold; border-bottom: 2px solid #000;">
                    Rs. in words <span style="font-weight:normal; font-style:italic;">{{ \App\Helpers\UnitHelper::amountToWords($amtTotal) }}</span>
                </div>
                
                {{-- Bank Details --}}
                <div style="padding: 6px 10px; font-size: 12px; border-bottom: 1px solid #000; flex-grow: 1;">
                    <div style="font-weight: bold; margin-bottom: 3px; text-decoration: underline;">Bank Details :</div>
                    <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 1px 0; width: 100px;">Bank Name:</td>
                            <td style="padding: 1px 0; font-weight: normal;">{{ $company->bank_name ?? '' }} {{ $company->branch_name ? '('.$company->branch_name.')' : '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">A/C Holder Name:</td>
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
                        @if($company->upi_id)
                        <tr>
                            <td style="padding: 1px 0;">UPI ID:</td>
                            <td style="padding: 1px 0; font-weight: normal;">{{ $company->upi_id }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                {{-- Terms & Conditions --}}
                @if($company->billing_terms_conditions)
                <div style="padding: 5px 10px; font-size: 11px;">
                    <div style="font-weight: bold; margin-bottom: 2px; text-decoration: underline;">Terms & Conditions :</div>
                    <div style="font-weight: normal; white-space: pre-wrap; line-height: 1.4;">{{ $company->billing_terms_conditions }}</div>
                </div>
                @endif
            </div>
            
            <div class="lower-right">
                {{-- Summary Table --}}
                <table class="summary-table">
                    <tr>
                        <td class="left">Items Total :</td>
                        <td class="right" style="width: 90px;">{{ number_format($itemGrossTotal, 2) }}</td>
                    </tr>
                    @if(isset($sale->charges) && $sale->charges->count() > 0)
                        @foreach($sale->charges as $charge)
                            <tr>
                                <td class="left">{{ $charge->name ?? 'Charge' }} :</td>
                                <td class="right">{{ $charge->type == 'deduct' ? '-' : '+' }}₹{{ number_format($charge->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                    @if($sale->discount_amount > 0)
                        <tr>
                            <td class="left">Discount ({{ $sale->discount_percent }}%) :</td>
                            <td class="right" style="color:red;">-₹{{ number_format($sale->discount_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="left">Total Amount Before Tax :</td>
                        <td class="right">{{ number_format($amtRs) }}.{{ str_pad($amtP, 2, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="left">Tax Amount&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GST :</td>
                        <td class="right">0.00</td>
                    </tr>
                    <tr>
                        <td class="left">Total Amount After Tax :</td>
                        <td class="right">{{ number_format($amtRs) }}.{{ str_pad($amtP, 2, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    @if($sale->amount_paid > 0)
                        <tr>
                            <td class="left" style="color:green;">Amount Paid :</td>
                            <td class="right" style="color:green;">₹{{ number_format($sale->amount_paid, 2) }}</td>
                        </tr>
                        @if($sale->outstanding_amount > 0)
                            <tr>
                                <td class="left" style="color:red;">Balance Due :</td>
                                <td class="right" style="color:red;">₹{{ number_format($sale->outstanding_amount, 2) }}</td>
                            </tr>
                        @endif
                    @endif
                </table>
                
                {{-- Dispatch Info --}}
                <div class="dispatch-info">
                    The material being Despatched truck No <span class="dispatch-val" style="min-width:20%;">{{ $sale->truck_no ?? '' }}</span><br>
                    Driver Name <span class="dispatch-val" style="min-width:60%;">{{ $sale->driver_name ?? '' }} {{ $sale->driver_phone ? '('.$sale->driver_phone.')' : '' }}</span><br>
                    kindly Pay Total Rs <span class="dispatch-val">{{ $sale->truck_fare ? number_format($sale->truck_fare, 2) : '' }}</span> Adv. Rs <span class="dispatch-val">{{ $sale->freight_advance > 0 ? number_format($sale->freight_advance, 2) : '' }}</span><br>
                    Balance Rs <span class="dispatch-val" style="min-width:25%;">{{ $sale->freight_balance > 0 ? number_format($sale->freight_balance, 2) : '' }}</span> As Truck fare<br>
                    &amp; Verify the materials
                </div>
                
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
