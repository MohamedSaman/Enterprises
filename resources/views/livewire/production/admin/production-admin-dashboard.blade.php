<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
            min-height: 100vh;
            padding: 2rem 0.5rem;
        }

        /* Stat Cards Row */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .sample-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.75rem 2rem;
            border: 1px solid rgba(30, 41, 59, 0.08);
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            height: 140px;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .sample-card:hover {
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .card-accent {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            border-radius: 14px 0 0 14px;
            background: linear-gradient(180deg, #0284c7 0%, #0369a1 100%);
        }

        .card-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 1.85rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.15rem;
            letter-spacing: -0.02em;
        }

        .card-sub {
            font-size: 0.8rem;
            color: #10b981;
            font-weight: 700;
        }

        .card-icon-box {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0284c7;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
        }

        /* Chart & List Row */
        .middle-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid rgba(30, 41, 59, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.35rem;
        }

        .section-subtitle {
            font-size: 0.85rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .btn-link-custom {
            font-size: 0.7rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f8fafc;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        /* Recent Production List */
        .production-list {
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
        }

        .production-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .item-info-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.15rem;
        }

        .item-info-sub {
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 600;
        }

        .item-time {
            font-size: 0.85rem;
            font-weight: 700;
            color: #94a3b8;
        }

        /* Monthly Analysis */
        .monthly-card {
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 4rem;
        }

        .stacked-bar-container {
            height: 16px;
            background: #f1f5f9;
            border-radius: 100px;
            display: flex;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .legend {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 700;
        }

        .legend-color {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .bar-rows {
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        .bar-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .month-name {
            color: #1e293b;
            font-weight: 700;
        }

        .unit-count {
            color: #64748b;
            font-size: 0.8rem;
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .middle-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section-card,
            .monthly-card {
                padding: 1.25rem;
            }

            .fab {
                right: 1rem;
                bottom: 1rem;
            }
        }

        /* FAB */
        .fab {
            position: fixed;
            bottom: 2.5rem;
            right: 2.5rem;
            background: #00a3e0;
            color: white;
            padding: 0.85rem 1.75rem;
            border-radius: 100px;
            font-weight: 800;
            box-shadow: 0 10px 20px rgba(0, 163, 224, 0.3);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            z-index: 1000;
            text-decoration: none;
        }

        .chart-container-inner {
            height: 280px;
            width: 100%;
        }
    </style>
    @endpush

    <!-- Stats Grid -->
    <div class="stats-grid">
        @foreach($stats as $index => $stat)
        <div class="sample-card">
            <div class="card-accent" style="background-color: {{ $stat['color'] }};"></div>
            <div class="card-label">{{ $stat['label'] }}</div>
            <div class="card-value">{{ $stat['value'] }}</div>
            <div class="card-sub">{{ $stat['sub'] }}</div>
            <div class="card-icon-box">
                <i class="bi {{ $stat['icon'] }}"></i>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Middle Grid: Chart & List -->
    <div class="middle-grid">
        <!-- Daily Production Trend -->
        <div class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Daily Production Trend</h2>
                    <p class="section-subtitle">Real-time output analysis (Last 7 Days)</p>
                </div>
                <a href="{{ route('production.admin.batches') }}" class="btn-link-custom">
                    View Full Report <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="chart-container-inner">
                <canvas id="productionTrendChart"></canvas>
            </div>
        </div>

        <!-- Recent Production -->
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">Recent Production</h2>
            </div>
            <div class="production-list">
                @forelse($recentProductions as $item)
                <div class="production-item">
                    <div class="item-left">
                        <div class="status-dot" style="background-color: {{ $item['status_color'] }};"></div>
                        <div>
                            <div class="item-info-title">{{ $item['unit'] }}</div>
                            <div class="item-info-sub">{{ $item['activity'] }}</div>
                        </div>
                    </div>
                    <div class="item-time">
                        {{ $item['time'] }}
                    </div>
                </div>
                @empty
                <div class="text-muted small">No production logs found.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Production Analysis -->
    <div class="monthly-card">
        <div class="section-header">
            <div>
                <h2 class="section-title">Monthly Production Analysis</h2>
                <p class="section-subtitle">Aggregated output across all manufacturing sectors</p>
            </div>
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-color" style="background-color: #005a8b;"></div>
                Produced Units
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #8a6114;"></div>
                Expense Intensity
            </div>
        </div>

        <div class="bar-rows">
            @foreach($monthlyStats as $month)
            <div class="bar-group">
                <div class="bar-label-row">
                    <div class="month-name">
                        {{ $month['month'] }}
                        @if($month['is_peak'])
                        <span class="text-warning fw-bold small ms-2">(PEAK)</span>
                        @endif
                    </div>
                    <div class="unit-count"><b>{{ number_format($month['produced']) }}</b> UNITS</div>
                </div>
                <div class="stacked-bar-container">
                    <div class="bar-segment" style="width: {{ $month['produced_width'] }}%; background-color: #005a8b;"></div>
                    <div class="bar-segment" style="width: {{ $month['expense_width'] }}%; background-color: #8a6114;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Floating Action Button -->
    <a href="{{ route('production.admin.batches') }}" class="fab">
        <i class="bi bi-plus-lg fs-5"></i>
        Log Production
    </a>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('productionTrendChart').getContext('2d');

        const labels = @json($dailyLabels);
        const data = @json($dailyValues);
        const capacityTarget = @json($dailyCapacity);

        if (window.productionChart) {
            window.productionChart.destroy();
        }

        window.productionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Units Produced',
                        data: data,
                        backgroundColor: function(context) {
                            return context.dataIndex === 2 ? '#8a6114' : '#00a3e0';
                        },
                        borderRadius: 4,
                        borderSkipped: false,
                        barThickness: 60,
                        grouped: false, // Ensure they overlap
                        categoryPercentage: 1.0,
                        barPercentage: 1.0
                    },
                    {
                        label: 'Capacity',
                        data: Array(labels.length).fill(capacityTarget),
                        backgroundColor: 'rgba(238, 242, 255, 0.8)',
                        borderRadius: 4,
                        borderSkipped: false,
                        barThickness: 60,
                        grouped: false, // Ensure they overlap
                        order: 2,
                        categoryPercentage: 1.0,
                        barPercentage: 1.0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#1e293b',
                        padding: 10,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            padding: 20,
                            font: {
                                size: 10,
                                weight: '700',
                                family: 'Inter'
                            },
                        },
                    },
                    y: {
                        display: false,
                        beginAtZero: true,
                        max: capacityTarget,
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush