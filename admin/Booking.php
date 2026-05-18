<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

include __DIR__ . '/../config/nav.php';
include __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/notifications.php';

$user = get_currnt_user();
$user_id = $user['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['booking_id']) && !empty($_POST['action'])) {
    $booking_id = (int) $_POST['booking_id'];
    $action = $_POST['action'] === 'approve' ? 'confirmed' : 'rejected';

    if (in_array($action, ['confirmed', 'rejected'], true)) {
        $update_stmt = $conn->prepare("UPDATE bookings SET Booking_status = ? WHERE Booking_id = ?");
        $update_stmt->execute([$action, $booking_id]);

        // Create notification using helper function
        create_booking_status_notification($conn, $booking_id, $action);

        header('Location: Booking.php');
        exit;
    }
}

$pending_count = 0;
$approved_count = 0;
$flagged_count = 0;
$estimated_revenue = 0;

try {
    $pending_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE Booking_status = 'pending'");
    $pending_stmt->execute();
    $pending_count = (int) $pending_stmt->fetchColumn();

    $approved_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE Booking_status IN ('confirmed', 'approved')");
    $approved_stmt->execute();
    $approved_count = (int) $approved_stmt->fetchColumn();

    $flagged_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE Booking_status = 'pending' AND Payment_deadline < NOW()");
    $flagged_stmt->execute();
    $flagged_count = (int) $flagged_stmt->fetchColumn();

    $revenue_stmt = $conn->prepare("SELECT SUM(Total_price) FROM bookings WHERE Booking_status IN ('confirmed', 'approved')");
    $revenue_stmt->execute();
    $estimated_revenue = (float) $revenue_stmt->fetchColumn();
    
    // Check for alerts and notify admins (once per session)
    if (!isset($_SESSION['admin_alerts_checked'])) {
        if ($pending_count > 0) {
            notify_admins_pending_approvals($conn);
        }
        if ($flagged_count > 0) {
            notify_admins_overdue_payments($conn);
        }
        $_SESSION['admin_alerts_checked'] = true;
    }
} catch (PDOException $e) {
    $pending_count = $approved_count = $flagged_count = 0;
    $estimated_revenue = 0;
}

$bookings_stmt = $conn->prepare(
    "SELECT 
        b.Booking_id,
        b.Booking_status,
        b.Event_date,
        b.Payment_deadline,
        b.Total_price,
        b.Guest_count,
        b.Package_id,
        u.First_name,
        u.Last_name,
        v.Name AS Venue_name
    FROM bookings b
    LEFT JOIN users u ON b.User_id = u.User_id
    LEFT JOIN venue v ON b.Venue_id = v.Venue_id
    ORDER BY b.Event_date DESC
    LIMIT 5"
);
$bookings_stmt->execute();
$bookings = $bookings_stmt->fetchAll(PDO::FETCH_ASSOC);

$active_nav = 'Booking';  
$page_title = 'Booking';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --accent-red: #B23B3B;
        --accent-gold: #A67C52;
        --navy-dark: #0A1128;
    }
    .member-portal-tag { font-size: 10px; font-weight: 800; color: var(--accent-gold); letter-spacing: 1px; }

    .font-playfair { font-family: 'Playfair Display', serif; }

    /* Admin Stat Cards */
    .stat-card { 
        background: white; 
        border-radius: 4px; 
        padding: 1.5rem; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.02); 
    }
    .stat-label { font-size: 0.7rem; font-weight: 700; color: #8E8E8E; text-transform: uppercase; letter-spacing: 1px; }
    .stat-value { font-size: 2.2rem; font-weight: 400; display: block; margin-top: 5px; }
    .text-flagged { color: var(--accent-red); }

    /* Table & Status Styling */
    .table-container { background: white; border-radius: 4px; border: 1px solid #EAE5DF; overflow: hidden; }
    .table thead th { background-color: #F9F9F9; font-size: 0.65rem; text-transform: uppercase; padding: 1rem; font-weight: 800; }
    .table tbody td { padding: 1.2rem 1rem; vertical-align: middle; font-size: 0.85rem; }
    
    .client-avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold; margin-right: 10px; }
    
    .badge-pending { background-color: #F3C65F; color: #000; border-radius: 4px; padding: 6px 12px; font-weight: 700; font-size: 0.7rem; }
    .badge-approved { background-color: #E8F5E9; color: #2E7D32; border-radius: 4px; padding: 6px 12px; font-weight: 700; font-size: 0.7rem; }
    .badge-rejected { background-color: #F8D7DA; color: #842029; border-radius: 4px; padding: 6px 12px; font-weight: 700; font-size: 0.7rem; }

    /* Workflow Banner */
    .workflow-section { 
        background-color: var(--navy-dark); 
        color: white; padding: 2.5rem; border-radius: 4px; 
    }
    .btn-white { background: white; color: var(--navy-dark); font-weight: 600; border-radius: 2px; padding: 0.8rem 1.5rem; border: none; font-size: 0.8rem; }
    .btn-outline-custom { border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.8rem 1.5rem; font-size: 0.8rem; border-radius: 2px; }
</style>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-md-flex justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="member-portal-tag text-uppercase">Booking</span>
            <h1 class="font-cinzel display-5 fw-bold text-navy mt-1">Booking Management</h1>
            <p class="text-muted mb-0">Manage all booking requests and approvals.</p>
        </div>
         <div class="d-flex gap-2 mt-3 mt-md-0">
            <input type="text" class="form-control" placeholder="Search Ref or Client..." style="max-width: 250px;">
            <button class="btn btn-outline-secondary btn-sm px-3">Date Range</button>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Pending Requests</span>
                <span class="stat-value"><?= number_format($pending_count) ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Approved Today</span>
                <span class="stat-value"><?= number_format($approved_count) ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Flagged Items</span>
                <span class="stat-value text-flagged"><?= number_format($flagged_count) ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Est. Revenue</span>
                <span class="stat-value">$<?= number_format($estimated_revenue, 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Approvals Table -->
    <div class="table-container shadow-sm mb-5">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Client</th>
                        <th>Venue</th>
                        <th>Package</th>
                        <th>Event Date</th>
                        <th>Total</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No bookings found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <?php
                                $client_name = trim(($booking['First_name'] ?? '') . ' ' . ($booking['Last_name'] ?? ''));
                                $initials = '';
                                if (!empty($booking['First_name'])) {
                                    $initials .= strtoupper($booking['First_name'][0]);
                                }
                                if (!empty($booking['Last_name'])) {
                                    $initials .= strtoupper($booking['Last_name'][0]);
                                }
                                if ($initials === '') {
                                    $initials = 'CL';
                                }
                                $status = strtolower(trim($booking['Booking_status'] ?? 'pending'));
                                $badgeClass = $status === 'confirmed' || $status === 'approved'
                                    ? 'badge-approved'
                                    : ($status === 'rejected' ? 'badge-rejected' : 'badge-pending');
                                $statusLabel = $status === 'confirmed' ? 'APPROVED' : strtoupper($status);
                            ?>
                            <tr>
                                <td class="text-muted">#<?= htmlspecialchars($booking['Booking_id'] ?? '') ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="client-avatar" style="background-color: #D1E3F8; color: #0A3D62;">
                                            <?= htmlspecialchars($initials) ?>
                                        </div>
                                        <strong class="small"><?= htmlspecialchars($client_name ?: 'Guest') ?></strong>
                                    </div>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($booking['Venue_name'] ?? 'Unknown Venue') ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($booking['Package_id'] ?? 'N/A') ?></td>
                                <td class="text-muted small"><?= !empty($booking['Event_date']) ? date('M d, Y', strtotime($booking['Event_date'])) : 'TBD' ?></td>
                                <td><strong class="small">$<?= number_format((float) ($booking['Total_price'] ?? 0), 2) ?></strong></td>
                                <td class="text-muted small"><?= !empty($booking['Payment_deadline']) ? date('M d, Y', strtotime($booking['Payment_deadline'])) : 'N/A' ?></td>
                                <td><span class="<?= $badgeClass ?>"><?= $statusLabel ?></span></td>
                                <td>
                                    <?php if ($status === 'pending'): ?>
                                        <div class="d-flex gap-2">
                                            <form method="post" class="m-0">
                                                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['Booking_id']) ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-success px-2">Approve</button>
                                            </form>
                                            <form method="post" class="m-0">
                                                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['Booking_id']) ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-danger px-2">Reject</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic" style="font-size: 0.65rem;">
                                            <?= $status === 'rejected' ? 'Rejected' : 'Validated' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Workflow Section -->
    <div class="workflow-section d-md-flex justify-content-between align-items-center mb-5">
        <div class="mb-4 mb-md-0">
            <h4 class="h3 fw-normal mb-3 font-playfair">Elite Approval Workflow</h4>
            <p class="mb-0 opacity-75 small" style="max-width: 600px;">
                Ensure all booking conditions are met before final verification. Status changes will trigger immediate client notifications through the Estate Reserve portal.
            </p>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-white px-4">POLICY</button>
            <button class="btn btn-outline-custom px-4">RESOLUTION</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>