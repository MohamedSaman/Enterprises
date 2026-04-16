<div class="dashboard-wrapper">
    @push('styles')
    <style>
        .dashboard-wrapper {
            min-height: 100vh;
            padding: 1.2rem 0 2rem;
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.12), transparent 30%),
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.10), transparent 24%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 16px;
            padding: 1rem;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.05);
        }

        .chart-card,
        .list-card {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.05);
        }

        .chart-wrap {
            position: relative;
            height: 280px;
        }

        .recent-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid #eef2f6;
        }

        .recent-item:last-child {
            border-bottom: 0;
        }

        .recent-title {
            font-weight: 700;
            color: #0f172a;
        }

        .recent-sub {
            font-size: 0.82rem;
            color: #64748b;
        }

        .metric-sub {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.35rem;
            font-weight: 600;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        .panel {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.05);
        }

        .dashboard-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .dashboard-title {
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .metric-sub strong {
            color: #0f172a;
        }

        .chart-card h5,
        .list-card h5 {
            color: #0f172a;
        }

        .recent-badge {
            display: inline-flex;
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 800;
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @endpush

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="dashboard-badge mb-2">Production Control</span>
            <div class="dashboard-title">Supervisor Dashboard</div>
            <p class="text-muted mb-0">Track your assigned production batches and today's performance.</p>
        </div>
        <a href="{{ route('production.staff.batches') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-list-ul me-1"></i> My Batches
        </a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Last Month Production</div>
            <div class="stat-value">{{ number_format($lastMonthProduced) }}</div>
            <div class="metric-sub">Total production items produced last month</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">This Month Target</div>
            <div class="stat-value">{{ number_format($currentMonthTarget) }}</div>
            <div class="metric-sub">Current target for active batches</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completion Rate</div>
            <div class="stat-value">{{ number_format($completionPercent) }}%</div>
            <div class="metric-sub">Produced {{ number_format($currentMonthProduced) }} of {{ number_format($currentMonthTarget) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending Items</div>
            <div class="stat-value">{{ number_format($pendingItems) }}</div>
            <div class="metric-sub">Remaining to complete this month's target</div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="chart-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Day by Day Production</h5>
                        <p class="text-muted mb-0 small">Last 7 days output trend</p>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="staffProductionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="list-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Recent Production Items</h5>
                        <p class="text-muted mb-0 small">Latest daily entries from your batches</p>
                    </div>
                    <a href="{{ route('production.staff.batches') }}" class="small text-decoration-none">View All</a>
                </div>

                @forelse($recentProductionItems as $item)
                <div class="recent-item">
                    <div>
                        <div class="recent-title">{{ $item['batch_code'] }} · Day {{ $item['day_no'] }}</div>
                        <div class="recent-sub">{{ $item['work_date'] }} | Size {{ $item['size'] }} | {{ $item['note'] }}</div>
                    </div>
                    <div class="text-end">
                        <div class="recent-badge mb-1">Recent entry</div>
                        <div class="fw-bold">{{ number_format($item['produced_qty']) }} items</div>
                        <div class="recent-sub">{{ number_format($item['expense_amount'], 2) }} expense</div>
                    </div>
                </div>
                @empty
                <div class="text-muted small">No production logs added yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="application/json" id="staff-production-chart-labels">
    {
        !!json_encode($chartLabels) !!
    }
</script>
<script type="application/json" id="staff-production-chart-values">
    {
        !!json_encode($chartValues) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('staffProductionChart');
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        const labels = JSON.parse(document.getElementById('staff-production-chart-labels')?.textContent || '[]');
        const values = JSON.parse(document.getElementById('staff-production-chart-values')?.textContent || '[]');

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Production Items',
                    data: values,
                    backgroundColor: '#00a3e0',
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 32,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                                weight: '700',
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#eef2f6'
                        },
                        ticks: {
                            color: '#64748b',
                        },
                    },
                },
            },
        });
    });
</script>
@endpush