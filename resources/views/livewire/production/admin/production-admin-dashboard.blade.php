<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            background-color: #f8faff;
            min-height: 100vh;
            padding: 1rem 0;
        }

        /* Stat Cards Row */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .sample-card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem 1.75rem;
            border: 1px solid #eef2f6;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            height: 140px;
            justify-content: center;
        }

        .card-accent {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            border-radius: 8px 0 0 8px;
        }

        .card-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
        }

        .card-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0.15rem;
            letter-spacing: -0.02em;
        }

        .card-sub {
            font-size: 0.8rem;
            color: #fbbf24;
            font-weight: 700;
        }

        .card-icon-box {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 42px;
            height: 42px;
            background: #f1f6fc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0088cc;
            font-size: 1.15rem;
        }

        /* Chart & List Row */
        .middle-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-card {
            background: #fff;
            border-radius: 12px;
            padding: 2.25rem;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            display: flex;
            flex-direction: column;
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
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
                <a href="#" class="btn-link-custom">
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
                @foreach($recentProductions as $item)
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
                @endforeach
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
                Primary Sector
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #8a6114;"></div>
                Auxiliary Output
            </div>
        </div>

        <div class="bar-rows">
            <!-- January -->
            <div class="bar-group">
                <div class="bar-label-row">
                    <div class="month-name">January</div>
                    <div class="unit-count"><b>1,240</b> UNITS</div>
                </div>
                <div class="stacked-bar-container">
                    <div class="bar-segment" style="width: 70%; background-color: #005a8b;"></div>
                    <div class="bar-segment" style="width: 15%; background-color: #8a6114;"></div>
                </div>
            </div>

            <!-- February -->
            <div class="bar-group">
                <div class="bar-label-row">
                    <div class="month-name">February</div>
                    <div class="unit-count"><b>1,480</b> UNITS</div>
                </div>
                <div class="stacked-bar-container">
                    <div class="bar-segment" style="width: 75%; background-color: #005a8b;"></div>
                    <div class="bar-segment" style="width: 12%; background-color: #8a6114;"></div>
                </div>
            </div>

            <!-- March (Peak) -->
            <div class="bar-group">
                <div class="bar-label-row">
                    <div class="month-name">March <span class="text-warning fw-bold small ms-2">(PEAK)</span></div>
                    <div class="unit-count"><b>1,824</b> UNITS</div>
                </div>
                <div class="stacked-bar-container">
                    <div class="bar-segment" style="width: 82%; background-color: #005a8b;"></div>
                    <div class="bar-segment" style="width: 10%; background-color: #8a6114;"></div>
                </div>
            </div>

            <!-- April -->
            <div class="bar-group">
                <div class="bar-label-row">
                    <div class="month-name">April</div>
                    <div class="unit-count"><b>1,150</b> UNITS</div>
                </div>
                <div class="stacked-bar-container">
                    <div class="bar-segment" style="width: 55%; background-color: #005a8b;"></div>
                    <div class="bar-segment" style="width: 25%; background-color: #8a6114;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <a href="#" class="fab">
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

        window.productionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
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
                        data: Array(labels.length).fill(250),
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
                        max: 300,
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