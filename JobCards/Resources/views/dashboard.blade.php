@extends('layouts.default')

@section('content')
<style>
    .jc-dash { font-family: 'Poppins', sans-serif; }
    .jc-dash .stat-card {
        background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: transform 0.2s, box-shadow 0.2s; cursor: default; border: 1px solid #eef2f7;
    }
    .jc-dash .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
    .jc-dash .stat-icon {
        width: 56px; height: 56px; border-radius: 14px; display: flex;
        align-items: center; justify-content: center; font-size: 22px;
    }
    .jc-dash .stat-value { font-size: 32px; font-weight: 700; color: #1a1a2e; margin: 0; line-height: 1; }
    .jc-dash .stat-label { font-size: 13px; color: #7f8c8d; margin: 4px 0 0 0; font-weight: 500; }
    .jc-dash .section-title {
        font-size: 16px; font-weight: 600; color: #1a1a2e; margin-bottom: 16px;
        padding-bottom: 8px; border-bottom: 2px solid #E91E8C;
    }
    .jc-dash .chart-card {
        background: #fff; border-radius: 12px; padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #eef2f7; height: 100%;
    }
    .jc-dash .activity-item {
        padding: 12px 0; border-bottom: 1px solid #f0f0f0;
        display: flex; align-items: center; gap: 12px;
    }
    .jc-dash .activity-item:last-child { border-bottom: none; }
    .jc-dash .status-badge {
        display: inline-block; padding: 4px 12px; border-radius: 20px;
        font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .jc-dash .progress-mini {
        height: 6px; border-radius: 3px; background: #eef2f7; overflow: hidden; width: 80px;
    }
    .jc-dash .progress-mini-bar { height: 100%; border-radius: 3px; transition: width 0.3s; }
    .jc-dash .quick-actions .btn {
        border-radius: 10px; padding: 10px 20px; font-weight: 600; font-size: 13px;
        border: none; transition: all 0.2s;
    }
    .jc-dash .assignee-bar {
        display: flex; align-items: center; gap: 10px; padding: 8px 0;
    }
    .jc-dash .assignee-bar .bar {
        flex: 1; height: 24px; border-radius: 6px; background: #eef2f7; overflow: hidden;
    }
    .jc-dash .assignee-bar .bar-fill {
        height: 100%; border-radius: 6px; display: flex; align-items: center;
        padding-left: 8px; font-size: 11px; font-weight: 600; color: #fff;
    }
    .jc-dash .overdue-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
</style>

<div class="jc-dash">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="font-weight:700;color:#1a1a2e;margin:0;">Job Card Dashboard</h3>
            <p style="color:#7f8c8d;margin:4px 0 0 0;font-size:13px;">Overview of all job card activity</p>
        </div>
        <div class="quick-actions d-flex gap-2">
            <a href="{{ route('jobcards.create') }}" class="btn" style="background:#E91E8C;color:#fff;">
                <i class="fa fa-plus mr-1"></i> New Job Card
            </a>
            <a href="{{ route('jobcards.index') }}" class="btn" style="background:#1a1a2e;color:#fff;">
                <i class="fa fa-list mr-1"></i> All Job Cards
            </a>
            <a href="{{ route('jobcards.admin.types') }}" class="btn" style="background:#6c757d;color:#fff;">
                <i class="fa fa-cog mr-1"></i> Setup
            </a>
        </div>
    </div>

    <!-- Stat Cards Row -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(26,26,46,0.08);color:#1a1a2e;"><i class="fa fa-briefcase"></i></div>
                    <div>
                        <p class="stat-value">{{ $stats['total'] }}</p>
                        <p class="stat-label">Total Jobs</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(0,123,255,0.1);color:#007bff;"><i class="fa fa-spinner"></i></div>
                    <div>
                        <p class="stat-value">{{ $stats['inProgress'] }}</p>
                        <p class="stat-label">In Progress</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(40,167,69,0.1);color:#28a745;"><i class="fa fa-check-circle"></i></div>
                    <div>
                        <p class="stat-value">{{ $stats['completed'] }}</p>
                        <p class="stat-label">Completed</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon {{ $stats['overdue'] > 0 ? 'overdue-pulse' : '' }}" style="background:rgba(220,53,69,0.1);color:#dc3545;"><i class="fa fa-exclamation-triangle"></i></div>
                    <div>
                        <p class="stat-value" style="{{ $stats['overdue'] > 0 ? 'color:#dc3545;' : '' }}">{{ $stats['overdue'] }}</p>
                        <p class="stat-label">Overdue</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(23,162,184,0.1);color:#17a2b8;"><i class="fa fa-paper-plane"></i></div>
                    <div>
                        <p class="stat-value">{{ $stats['submitted'] }}</p>
                        <p class="stat-label">Submitted</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:rgba(233,30,140,0.1);color:#E91E8C;"><i class="fa fa-clock-o"></i></div>
                    <div>
                        <p class="stat-value">{{ $stats['avgCompletionDays'] }}</p>
                        <p class="stat-label">Avg Days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Trend -->
        <div class="col-lg-8 mb-3">
            <div class="chart-card">
                <h5 class="section-title">Monthly Trend</h5>
                <canvas id="monthlyTrendChart" height="100"></canvas>
            </div>
        </div>
        <!-- Jobs by Type -->
        <div class="col-lg-4 mb-3">
            <div class="chart-card">
                <h5 class="section-title">Jobs by Type</h5>
                <canvas id="byTypeChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Activity + Workload -->
    <div class="row mb-4">
        <!-- Recent Activity -->
        <div class="col-lg-7 mb-3">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="section-title mb-0" style="border:none;padding:0;">Recent Activity</h5>
                    <a href="{{ route('jobcards.index') }}" style="font-size:12px;color:#E91E8C;text-decoration:none;font-weight:600;">View All →</a>
                </div>
                <div id="recentActivity">
                    @forelse($stats['recentActivity'] as $item)
                    <div class="activity-item">
                        <div style="flex:0 0 auto;">
                            @php
                                $sc = $statuses[$item->status] ?? ['color' => '#6c757d', 'icon' => 'fa-file'];
                            @endphp
                            <span class="status-badge" style="background:{{ $sc['color'] }}22;color:{{ $sc['color'] }};">
                                <i class="fa {{ $sc['icon'] }} mr-1"></i>{{ $sc['label'] ?? $item->status }}
                            </span>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <a href="{{ route('jobcards.show', $item->id) }}" style="font-weight:600;color:#1a1a2e;text-decoration:none;font-size:13px;">
                                {{ $item->job_code }}
                            </a>
                            <span style="color:#7f8c8d;font-size:12px;"> — {{ $item->company_name }} ({{ $item->client_code }})</span>
                            <br><span style="color:#aaa;font-size:11px;">{{ $item->job_type_name }}</span>
                        </div>
                        <div style="flex:0 0 auto;text-align:right;">
                            <div class="progress-mini mb-1">
                                <div class="progress-mini-bar" style="width:{{ $item->completion_percentage }}%;background:{{ $item->completion_percentage >= 100 ? '#28a745' : '#E91E8C' }};"></div>
                            </div>
                            <span style="font-size:11px;color:#7f8c8d;">{{ number_format($item->completion_percentage, 0) }}%</span>
                        </div>
                    </div>
                    @empty
                    <p style="color:#aaa;text-align:center;padding:30px 0;">No job cards yet. Create your first one!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Workload by Assignee -->
        <div class="col-lg-5 mb-3">
            <div class="chart-card">
                <h5 class="section-title">Workload by Assignee</h5>
                @php
                    $maxAssignee = $stats['byAssignee']->max('count') ?: 1;
                    $barColors = ['#E91E8C', '#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1'];
                @endphp
                @forelse($stats['byAssignee'] as $idx => $a)
                <div class="assignee-bar">
                    <span style="width:120px;font-size:13px;font-weight:500;color:#1a1a2e;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $a->assignee }}</span>
                    <div class="bar">
                        <div class="bar-fill" style="width:{{ ($a->count / $maxAssignee) * 100 }}%;background:{{ $barColors[$idx % count($barColors)] }};">
                            {{ $a->count }}
                        </div>
                    </div>
                </div>
                @empty
                <p style="color:#aaa;text-align:center;padding:30px 0;">No active assignments</p>
                @endforelse

                <!-- Status Breakdown -->
                <h5 class="section-title mt-4">Status Breakdown</h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($statuses as $key => $s)
                    <div style="background:{{ $s['color'] }}11;border:1px solid {{ $s['color'] }}33;border-radius:8px;padding:8px 14px;text-align:center;min-width:90px;">
                        <div style="font-size:20px;font-weight:700;color:{{ $s['color'] }};">{{ $stats['statusCounts'][$key] ?? 0 }}</div>
                        <div style="font-size:11px;color:#7f8c8d;font-weight:500;">{{ $s['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trend Chart
    var monthlyData = @json($stats['monthlyTrend']);
    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'bar',
        data: {
            labels: monthlyData.map(m => m.label),
            datasets: [
                {
                    label: 'Created',
                    data: monthlyData.map(m => m.created),
                    backgroundColor: 'rgba(233,30,140,0.7)',
                    borderRadius: 6,
                    barPercentage: 0.4,
                },
                {
                    label: 'Completed',
                    data: monthlyData.map(m => m.completed),
                    backgroundColor: 'rgba(40,167,69,0.7)',
                    borderRadius: 6,
                    barPercentage: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top', labels: { font: { family: 'Poppins', size: 12 } } } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Poppins' } }, grid: { color: '#f0f0f0' } },
                x: { ticks: { font: { family: 'Poppins' } }, grid: { display: false } }
            }
        }
    });

    // Jobs by Type Chart
    var typeData = @json($stats['byType']);
    if (typeData.length > 0) {
        var typeColors = ['#E91E8C', '#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#dc3545', '#20c997', '#6c757d'];
        new Chart(document.getElementById('byTypeChart'), {
            type: 'doughnut',
            data: {
                labels: typeData.map(t => t.name),
                datasets: [{
                    data: typeData.map(t => t.count),
                    backgroundColor: typeData.map((_, i) => typeColors[i % typeColors.length]),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 }, padding: 12, usePointStyle: true } }
                }
            }
        });
    } else {
        document.getElementById('byTypeChart').parentElement.innerHTML += '<p style="color:#aaa;text-align:center;">No data yet</p>';
    }
});
</script>
@endsection
