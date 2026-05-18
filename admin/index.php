<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

include __DIR__ . '/../config/nav.php';

$active_nav = 'Dashboard';
$page_title = 'Dashboard';

include __DIR__ . '/../includes/top_sidebar.php';
require_once __DIR__ . '/../config/db.php';

// Fetch summary stats
$totBookingsStmt = $conn->prepare("SELECT COUNT(*) FROM bookings");
$totBookingsStmt->execute();
$total_bookings = (int)$totBookingsStmt->fetchColumn();

$totalPaymentsStmt = $conn->prepare("SELECT COALESCE(SUM(Amount),0) FROM payments WHERE Status = 'completed'");
$totalPaymentsStmt->execute();
$total_payments = (float)$totalPaymentsStmt->fetchColumn();

$venuesStmt = $conn->prepare("SELECT COUNT(*) FROM venue");
$venuesStmt->execute();
$total_venues = (int)$venuesStmt->fetchColumn();

// Recent bookings
$recentStmt = $conn->prepare("SELECT b.Booking_id, b.Event_date, b.Total_price, b.Booking_status, b.Guest_count, u.First_name, u.Last_name, v.Name AS VenueName
    FROM bookings b
    LEFT JOIN users u ON b.User_id = u.User_id
    LEFT JOIN venue v ON b.Venue_id = v.Venue_id
    ORDER BY b.Booking_id DESC
    LIMIT 6");
$recentStmt->execute();
$recent_bookings = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

// Build chart data for last 12 months
$months = [];
$monthKeys = [];
for ($i = 11; $i >= 0; $i--) {
    $ts = strtotime("-{$i} months");
    $months[] = date('M', $ts);
    $monthKeys[] = date('Y-m', $ts);
}

$revenueData = array_fill(0, 12, 0);
$bookingsData = array_fill(0, 12, 0);

// fetch payments grouped by year-month
$payStmt = $conn->prepare("SELECT DATE_FORMAT(Paid_at, '%Y-%m') AS ym, SUM(Amount) as total FROM payments WHERE Status = 'completed' AND Paid_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY ym ORDER BY ym ASC");
$payStmt->execute();
$payRows = $payStmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($payRows as $row) {
    $idx = array_search($row['ym'], $monthKeys);
    if ($idx !== false) {
        $revenueData[$idx] = (float)$row['total'];
    }
}

// fetch bookings count by event_date year-month
$bookStmt = $conn->prepare("SELECT DATE_FORMAT(Event_date, '%Y-%m') AS ym, COUNT(*) as cnt FROM bookings WHERE Event_date >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) GROUP BY ym ORDER BY ym ASC");
$bookStmt->execute();
$bookRows = $bookStmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($bookRows as $row) {
    $idx = array_search($row['ym'], $monthKeys);
    if ($idx !== false) {
        $bookingsData[$idx] = (int)$row['cnt'];
    }
}

// JSON for JS
$chart_labels = json_encode($months);
$chart_revenue = json_encode($revenueData);
$chart_bookings = json_encode($bookingsData);
?>

<style>
    :root {
        --navy: #1A1A2E;
        --accent-gold: #A67C52;
        --bg-cream: #F9F7F2;
    }
    .member-portal-tag { font-size: 10px; font-weight: 800; color: var(--accent-gold); letter-spacing: 1px; }

    .font-cinzel {
        font-family: 'Cinzel', serif;
    }

    /* Admin Stat Cards */
    .stat-card {
        background: #ffffff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 20px;
        transition: transform 0.2s;
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        color: #777;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 600;
        color: var(--navy);
    }

    /* Section Cards */
    .section-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 24px;
    }

    .chart-container {
        height: 250px;
        position: relative;
    }

    /* Custom Table Styling */
    .bookings-table th {
        font-size: 11px;
        text-transform: uppercase;
        color: #888;
        border-bottom: 2px solid #f8f9fa;
    }

    .venue-tag {
        background: #f9f1e8;
        color: var(--accent-gold);
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
    }

    /* Alert Dots */
    .alert-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .dot-warn {
        background: var(--accent-gold);
    }

    .dot-ok {
        background: #2e7d52;
    }
</style>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="mb-4">
        <span class="member-portal-tag text-uppercase">Dashboard</span>
        <h1 class="font-cinzel display-5 fw-bold text-navy mt-1">Portfolio Overview</h1>
        <p class="text-muted mb-0">Comprehensive analytics on performance of bookings, payments, and venue utilization.</p>
    </div>
    <!-- Top Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Total Bookings</div>
                <div class="stat-value font-cinzel"><?= number_format($total_bookings) ?></div>
                <div class="small mt-2">
                    <span class="text-muted">All-time bookings</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Total Payments</div>
                <div class="stat-value font-cinzel">₱<?= number_format($total_payments, 2) ?></div>
                <div class="small mt-2">
                    <span class="text-muted">Completed payments</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Registered Venues</div>
                <div class="stat-value font-cinzel"><?= number_format($total_venues) ?></div>
                <div class="small mt-2">
                    <span class="text-muted">Total venues</span>
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
                </div>

                <?php if (!empty($recent_bookings)): ?>
                    <?php foreach ($recent_bookings as $rb): ?>
                        <?php
                            $status = strtolower(trim($rb['Booking_status'] ?? ''));
                            $iconClass = ($status === 'pending') ? 'dot-warn' : 'dot-ok';
                        ?>
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom border-light">
                            <span class="alert-dot <?= $iconClass ?> mt-2"></span>
                            <div>
                                <p class="small fw-bold mb-0"><?= htmlspecialchars($rb['First_name'] . ' ' . $rb['Last_name']) ?> — <?= htmlspecialchars($rb['VenueName'] ?? 'Venue') ?></p>
                                <small class="text-muted"><?= date('M d, Y', strtotime($rb['Event_date'])) ?> · <?= htmlspecialchars(ucfirst($rb['Booking_status'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-muted small">No recent alerts</div>
                <?php endif; ?>

                <a href="../admin/Booking.php" class="btn btn-light btn-sm w-100 fw-bold border mt-auto text-center">Manage Events →</a>
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
            <a href="Booking.php" class="btn btn-outline-dark btn-sm fw-bold px-3 mt-3 mt-md-0 font-cinzel">Manage Events →</a>
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
                    <?php if (!empty($recent_bookings)): ?>
                        <?php foreach ($recent_bookings as $r): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-navy small"><?= htmlspecialchars($r['First_name'] . ' ' . $r['Last_name']) ?></div>
                                    <div class="text-muted" style="font-size: 11px;">Guests: <?= htmlspecialchars($r['Guest_count'] ?? 'N/A') ?></div>
                                </td>
                                <td><span class="venue-tag"><?= htmlspecialchars($r['VenueName'] ?? 'N/A') ?></span></td>
                                <td class="small"><?= date('M d, Y', strtotime($r['Event_date'])) ?></td>
                                <td class="small fw-bold">₱<?= number_format($r['Total_price'], 2) ?></td>
                                <td>
                                    <?php $st = strtolower($r['Booking_status']); ?>
                                    <?php if ($st === 'confirmed'): ?>
                                        <span class="badge bg-success-subtle text-success px-3">Confirmed</span>
                                    <?php elseif ($st === 'pending'): ?>
                                        <span class="badge bg-warning-subtle text-warning px-3">Pending</span>
                                    <?php elseif ($st === 'cancelled' || $st === 'rejected'): ?>
                                        <span class="badge bg-secondary text-white px-3"><?= htmlspecialchars(ucfirst($st)) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark px-3"><?= htmlspecialchars(ucfirst($st)) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">No recent bookings</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    const labels = <?= $chart_labels ?>;
    const revenue = <?= $chart_revenue ?>;
    const bookings = <?= $chart_bookings ?>;

    new Chart(ctx, {
        data: {
            labels: labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Revenue (₱)',
                    data: revenue,
                    backgroundColor: 'rgba(26, 26, 46, 0.8)',
                    yAxisID: 'y',
                },
                {
                    type: 'line',
                    label: 'Bookings',
                    data: bookings,
                    borderColor: '#A67C52',
                    backgroundColor: 'rgba(166,124,82,0.1)',
                    tension: 0.35,
                    fill: false,
                    yAxisID: 'y2',
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(val) { return '₱' + Number(val).toLocaleString(); }
                    }
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>