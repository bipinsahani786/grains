@extends('layouts.app')

@section('content')
<div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard</h5>
                    </div>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <form method="GET" class="d-flex align-items-center gap-2" id="dashboardFilterForm">
                                <select name="period" class="form-select form-select-sm" onchange="toggleCustomDates(this.value)">
                                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Date</option>
                                </select>
                                
                                <div id="customDateFields" class="d-flex align-items-center gap-2" style="display: {{ $period == 'custom' ? 'flex' : 'none !important' }};">
                                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                </div>
                                
                                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->

            <script>
            function toggleCustomDates(val) {
                const fields = document.getElementById('customDateFields');
                if (val === 'custom') {
                    fields.style.display = 'flex';
                    fields.style.setProperty('display', 'flex', 'important');
                } else {
                    fields.style.display = 'none';
                    fields.style.setProperty('display', 'none', 'important');
                    document.getElementById('dashboardFilterForm').submit();
                }
            }
            </script>
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                    <!-- [Today's Purchases] start -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card stretch stretch-full" onclick="window.location.href='{{ route('business.purchases.index') }}'" style="cursor: pointer;">
                            <div class="card-body p-3 hover-effect">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                        <div class="avatar-text bg-soft-primary text-primary">
                                            <i class="feather-shopping-cart"></i>
                                        </div>
                                        <div class="overflow-hidden w-100">
                                            <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($periodPurchases, 0) }}</div>
                                            <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Purchases</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Today's Purchases] end -->
                    <!-- [Today's Sales] start -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card stretch stretch-full" onclick="window.location.href='{{ route('business.sales.index') }}'" style="cursor: pointer;">
                            <div class="card-body p-3 hover-effect">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                        <div class="avatar-text bg-soft-success text-success">
                                            <i class="feather-trending-up"></i>
                                        </div>
                                        <div class="overflow-hidden w-100">
                                            <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($periodSales, 0) }}</div>
                                            <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Sales</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Today's Sales] end -->
                    <!-- [Today's Profit] start -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card stretch stretch-full" onclick="window.location.href='{{ route('business.reports.profit') }}'" style="cursor: pointer;">
                            <div class="card-body p-3 hover-effect">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                        <div class="avatar-text bg-soft-warning text-warning">
                                            <i class="feather-pie-chart"></i>
                                        </div>
                                        <div class="overflow-hidden w-100">
                                            <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($periodProfit, 0) }}</div>
                                            <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Net Profit</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Today's Profit] end -->
                    <!-- [Today's Expenses] start -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card stretch stretch-full" onclick="window.location.href='{{ route('business.expenses.index') }}'" style="cursor: pointer;">
                            <div class="card-body p-3 hover-effect">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                        <div class="avatar-text bg-soft-danger text-danger">
                                            <i class="feather-trending-down"></i>
                                        </div>
                                        <div class="overflow-hidden w-100">
                                            <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($periodExpenses, 0) }}</div>
                                            <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Expenses</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Today's Expenses] end -->
                    <!-- [Current Stock] start -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card stretch stretch-full" onclick="window.location.href='{{ route('business.reports.current-stock') }}'" style="cursor: pointer;">
                            <div class="card-body p-3 hover-effect">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                        <div class="avatar-text bg-soft-info text-info">
                                            <i class="feather-package"></i>
                                        </div>
                                        <div class="overflow-hidden w-100">
                                            <div class="fs-5 fw-bold text-dark text-truncate">{{ $currentStock }} Units</div>
                                            <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Current Stock</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Current Stock] end -->
                    <!-- [Total Receivable] start -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card stretch stretch-full" onclick="window.location.href='{{ route('business.financials.ledger.index') }}'" style="cursor: pointer;">
                            <div class="card-body p-3 hover-effect">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                        <div class="avatar-text bg-soft-success text-success">
                                            <i class="feather-arrow-down-circle"></i>
                                        </div>
                                        <div class="overflow-hidden w-100">
                                            <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($totalReceivable, 0) }}</div>
                                            <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Receivable</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Total Receivable] end -->
                    <!-- [Total Payable] start -->
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="card stretch stretch-full" onclick="window.location.href='{{ route('business.financials.ledger.index') }}'" style="cursor: pointer;">
                            <div class="card-body p-3 hover-effect">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex gap-3 align-items-center w-100 overflow-hidden">
                                        <div class="avatar-text bg-soft-danger text-danger">
                                            <i class="feather-arrow-up-circle"></i>
                                        </div>
                                        <div class="overflow-hidden w-100">
                                            <div class="fs-5 fw-bold text-dark text-truncate">₹{{ number_format($totalPayable, 0) }}</div>
                                            <h3 class="fs-12 fw-semibold text-truncate-1-line mb-0">Total Payable</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Total Payable] end -->
                </div> <!-- End of stats row -->
                
                <div class="row">
                    <!-- [Charts Section] -->
                    <div class="col-xl-8 col-lg-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Sales & Purchases (Last 7 Days)</h5>
                            </div>
                            <div class="card-body">
                                <div id="salesPurchaseChart"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-lg-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Stock Overview by Grain</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                @if(empty($grainSeries))
                                    <div class="text-muted text-center my-5">No stock data available</div>
                                @else
                                    <div id="stockChart"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- [Charts Section] end -->
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
        
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Sales and Purchase Chart
        var salesData = @json($salesData);
        var purchasesData = @json($purchasesData);
        var datesData = @json($datesData);

        var optionsArea = {
            series: [{
                name: 'Sales (₹)',
                data: salesData
            }, {
                name: 'Purchases (₹)',
                data: purchasesData
            }],
            chart: {
                height: 350,
                type: 'area',
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#2e7d32', '#f59e0b'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: datesData,
                labels: {
                    style: { colors: '#64748b' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b' },
                    formatter: function (value) {
                        return "₹" + value.toLocaleString();
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "₹" + val.toLocaleString()
                    }
                }
            }
        };

        var chartArea = new ApexCharts(document.querySelector("#salesPurchaseChart"), optionsArea);
        chartArea.render();

        // Stock Donut Chart
        var grainLabels = @json($grainLabels);
        var grainSeries = @json($grainSeries);
        
        if (grainSeries.length > 0) {
            var optionsDonut = {
                series: grainSeries,
                labels: grainLabels,
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'Inter, sans-serif',
                    background: 'transparent'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: { show: true },
                                value: { show: true, formatter: function (val) { return val + " Units" } },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Total Stock',
                                    color: '#64748b'
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " Units"
                        }
                    }
                }
            };

            var chartDonut = new ApexCharts(document.querySelector("#stockChart"), optionsDonut);
            chartDonut.render();
        }
    });
</script>
@endpush