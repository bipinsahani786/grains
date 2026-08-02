@extends('layouts.app')

@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">SuperAdmin Dashboard</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Dashboard</li>
            </ul>
        </div>
    </div>
    
    <div class="main-content">
        <!-- Top Metrics Row -->
        <div class="row">
            <!-- Total Companies -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary border-primary border-opacity-10 rounded-3">
                                    <i class="feather-briefcase fs-16"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $totalCompanies }}</span></div>
                                    <div class="fs-13 fw-semibold text-muted text-truncate-1-line">Total Tenants</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total MRR -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-success text-success border-success border-opacity-10 rounded-3">
                                    <i class="feather-dollar-sign fs-16"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark">₹<span class="counter">{{ number_format($mrr, 0) }}</span></div>
                                    <div class="fs-13 fw-semibold text-muted text-truncate-1-line">Monthly Recurring Rev.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-warning text-warning border-warning border-opacity-10 rounded-3">
                                    <i class="feather-users fs-16"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $totalUsers }}</span></div>
                                    <div class="fs-13 fw-semibold text-muted text-truncate-1-line">Total Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Plans -->
            <div class="col-xxl-3 col-md-6">
                <div class="card stretch stretch-full">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div class="d-flex gap-4 align-items-center">
                                <div class="avatar-text avatar-lg bg-soft-info text-info border-info border-opacity-10 rounded-3">
                                    <i class="feather-credit-card fs-16"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold text-dark"><span class="counter">{{ $activePlansCount }}</span></div>
                                    <div class="fs-13 fw-semibold text-muted text-truncate-1-line">Active Plans</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Revenue Area Chart -->
            <div class="col-xxl-8">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Revenue Analytics (Last 6 Months)</h5>
                    </div>
                    <div class="card-body">
                        <div id="revenue-chart" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Plan Distribution Donut Chart -->
            <div class="col-xxl-4">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Active Subscriptions by Plan</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="plan-distribution-chart" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Companies Table -->
        <div class="row">
            <div class="col-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Recently Onboarded Companies</h5>
                    </div>
                    <div class="card-body custom-card-action p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Company</th>
                                        <th>Type</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Joined On</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentCompanies as $company)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-text avatar-sm bg-soft-primary text-primary rounded">
                                                    {{ strtoupper(substr($company->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $company->name }}</h6>
                                                    <span class="fs-12 text-muted">{{ $company->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-soft-secondary text-secondary text-capitalize">{{ str_replace('_', ' ', $company->type ?? 'N/A') }}</span></td>
                                        <td>
                                            @php
                                                $activeSub = $company->subscriptions->where('status', 'active')->first();
                                            @endphp
                                            @if($activeSub && $activeSub->plan)
                                                <span class="badge bg-soft-primary text-primary">{{ $activeSub->plan->name }}</span>
                                            @else
                                                <span class="badge bg-soft-warning text-warning">No Plan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($company->is_active)
                                                <span class="badge bg-soft-success text-success">Active</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $company->created_at->format('d M, Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('superadmin.companies.edit', $company->id) }}" class="btn btn-sm btn-light">Manage</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No companies onboarded yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. Revenue Area Chart
        var revenueOptions = {
            series: [{
                name: 'Revenue (₹)',
                data: @json($revenueSeries)
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                fontFamily: 'inherit'
            },
            colors: ['#3454D1'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: @json($revenueLabels),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return "₹" + value.toLocaleString();
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "₹" + val.toLocaleString();
                    }
                }
            }
        };

        var revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), revenueOptions);
        revenueChart.render();


        // 2. Plan Distribution Donut Chart
        var planLabels = @json($planLabels);
        var planSeries = @json($planSeries);
        
        // Convert series array values from string to integers explicitly if needed, but json handles it
        // If empty, show dummy data so chart doesn't break
        if(planSeries.length === 0) {
            planLabels = ['No Subscriptions'];
            planSeries = [1];
        }

        var planOptions = {
            series: planSeries.map(Number),
            labels: planLabels,
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'inherit'
            },
            colors: ['#3454D1', '#17C666', '#EA4D4D', '#F6A810', '#15B2D6'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                            },
                            value: {
                                show: true,
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom'
            },
            stroke: {
                show: false
            }
        };

        var planChart = new ApexCharts(document.querySelector("#plan-distribution-chart"), planOptions);
        planChart.render();
    });
</script>
@endpush
