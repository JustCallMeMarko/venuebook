<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["admin"] ?? [];
$active_nav = 'Dashboard';  
$page_title = 'Dashboard';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --navy: #1A1A2E;
        --accent-gold: #A67C52;
        --bg-cream: #F9F7F2;
    }
    .font-cinzel { font-family: 'Cinzel', serif; }
    
    /* Admin Stat Cards */
    .stat-card {
        background: #ffffff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 20px;
        transition: transform 0.2s;
    }
    .stat-label { font-size: 11px; font-weight: 700; color: #777; text-transform: uppercase; letter-spacing: 1px; }
    .stat-value { font-size: 28px; font-weight: 600; color: var(--navy); }
    
    /* Section Cards */
    .section-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 24px;
    }
    .chart-container { height: 250px; position: relative; }

    /* Custom Table Styling */
    .bookings-table th { font-size: 11px; text-transform: uppercase; color: #888; border-bottom: 2px solid #f8f9fa; }
    .venue-tag { background: #f9f1e8; color: var(--accent-gold); padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; }
    
    /* Alert Dots */
    .alert-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .dot-warn { background: var(--accent-gold); }
    .dot-ok { background: #2e7d52; }
</style>

<div class="container-fluid">
    <!-- Header Section -->
    <header class="mb-4">
        <h1 class="font-cinzel display-6 fw-bold text-navy">Portfolio Overview</h1>
        <p class="text-muted">Comprehensive analytics on performance of bookings, payments, and venue utilization</p>
    </header>

    <!-- Top Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Total Bookings</div>
                <div class="stat-value font-cinzel">1,284</div>
                <div class="small mt-2">
                    <span class="text-success fw-bold">▲ 12%</span> <span class="text-muted">vs last month</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Total Payments</div>
                <div class="stat-value font-cinzel">$412,850</div>
                <div class="small mt-2">
                    <span class="text-success fw-bold">▲ 8%</span> <span class="text-muted">vs last month</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Active Venues</div>
                <div class="stat-value font-cinzel">24</div>
                <div class="small mt-2">
                    <span class="text-danger fw-bold">▼ 2</span> <span class="text-muted">vs last month</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Revenue Chart -->
        <div class="col-12 col-lg-8">
            <div class="section-card shadow-sm h-100">
                <h3 class="h6 fw-bold mb-1">Revenue Distribution</h3>
                <p class="small text-muted mb-4">Monthly booking revenue across all venues</p>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- System Alerts -->
        <div class="col-12 col-lg-4">
            <div class="section-card shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h6 fw-bold mb-0">System Alerts</h3>
                    <span class="badge bg-danger rounded-pill">4</span>
                </div>
                
                <div class="d-flex gap-3 mb-3 pb-3 border-bottom border-light">
                    <span class="alert-dot dot-warn mt-2"></span>
                    <div>
                        <p class="small fw-bold mb-0">Payment overdue — Grand Ballroom #4821</p>
                        <small class="text-muted">2 hours ago</small>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3 pb-3 border-bottom border-light">
                    <span class="alert-dot dot-warn mt-2"></span>
                    <div>
                        <p class="small fw-bold mb-0">Contract unsigned — Victoria Hall, Oct 13</p>
                        <small class="text-muted">5 hours ago</small>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-4">
                    <span class="alert-dot dot-ok mt-2"></span>
                    <div>
                        <p class="small fw-bold mb-0">New booking confirmed — Rooftop Garden</p>
                        <small class="text-muted">Yesterday</small>
                    </div>
                </div>

                <button class="btn btn-light btn-sm w-100 fw-bold border mt-auto">View All Alerts →</button>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="section-card shadow-sm mb-5">
        <div class="d-md-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="h6 fw-bold mb-1">Recent Bookings</h3>
                <p class="small text-muted mb-0">Latest confirmed, pending, and cancelled event bookings</p>
            </div>
            <button class="btn btn-outline-dark btn-sm fw-bold px-3 mt-3 mt-md-0 font-cinzel">Manage Events →</button>
        </div>

        <div class="table-responsive">
            <table class="table bookings-table align-middle">
                <thead>
                    <tr>
                        <th>Client / Event</th>
                        <th>Venue</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-bold text-navy small">Ana Reyes</div>
                            <div class="text-muted" style="font-size: 11px;">Wedding Reception</div>
                        </td>
                        <td><span class="venue-tag">Grand Ballroom</span></td>
                        <td class="small">Nov 15, 2026</td>
                        <td class="small fw-bold">$18,500</td>
                        <td><span class="badge bg-success-subtle text-success px-3">Confirmed</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-bold text-navy small">Marco Silva</div>
                            <div class="text-muted" style="font-size: 11px;">Corporate Gala</div>
                        </td>
                        <td><span class="venue-tag">Victoria Hall</span></td>
                        <td class="small">Oct 22, 2026</td>
                        <td class="small fw-bold">$12,400</td>
                        <td><span class="badge bg-warning-subtle text-warning px-3">Pending</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#1A1A2E',
                backgroundColor: 'rgba(26, 26, 46, 0.05)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>