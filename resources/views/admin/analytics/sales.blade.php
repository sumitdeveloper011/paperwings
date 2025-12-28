@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-dollar-sign"></i>
                    Sales Report
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="modern-card">
                <div class="modern-card__body text-center">
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold; color: #28a745;">
                        ${{ number_format($totalRevenue, 2) }}
                    </div>
                    <div class="stat-label text-muted">Total Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="modern-card">
                <div class="modern-card__body text-center">
                    <div class="stat-value" style="font-size: 2rem; font-weight: bold; color: #007bff;">
                        {{ number_format($totalOrders) }}
                    </div>
                    <div class="stat-label text-muted">Total Orders</div>
                </div>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="modern-card__header">
            <h3 class="modern-card__title">Daily Sales</h3>
        </div>
        <div class="modern-card__body">
            @if($salesData->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesData as $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                                    <td><span class="badge bg-primary">{{ $data->orders }}</span></td>
                                    <td><strong>${{ number_format($data->revenue, 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No sales data available.</p>
            @endif
        </div>
    </div>
</div>
@endsection

