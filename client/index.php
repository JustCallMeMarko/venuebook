<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');

include __DIR__ . '/../config/nav.php';
include __DIR__ . '/../config/db.php';
$user = get_currnt_user();
$user_id = $user['user_id'] ?? null;

$active_nav = 'Dashboard';  
$page_title = 'Dashboard';

$featured_booking = null;
$other_upcoming_bookings = [];

if ($user_id) {
    $upcoming_stmt = $conn->prepare(
        "SELECT b.*, v.Name as Venue_name, v.image as Venue_image\n" .
        "FROM bookings b\n" .
        "LEFT JOIN venue v ON b.Venue_id = v.Venue_id\n" .
        "WHERE b.User_id = ? AND b.Event_date >= CURDATE() AND b.Booking_status != 'cancelled'\n" .
        "ORDER BY b.Event_date ASC\n" .
        "LIMIT 3"
    );
    $upcoming_stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $upcoming_stmt->execute();
    $upcoming_bookings = $upcoming_stmt->fetchAll(PDO::FETCH_ASSOC);
    $featured_booking = $upcoming_bookings[0] ?? null;
    $other_upcoming_bookings = array_slice($upcoming_bookings, 1);

    // Fetch upcoming payments
    $payments_stmt = $conn->prepare("
        SELECT b.Booking_id, v.Name as Venue_name, b.Payment_deadline, b.Total_price,
               (SELECT COALESCE(SUM(Amount), 0) FROM payments WHERE Booking_id = b.Booking_id AND Status = 'completed') as Total_paid
        FROM bookings b
        LEFT JOIN venue v ON b.Venue_id = v.Venue_id
        WHERE b.User_id = ? AND b.Booking_status NOT IN ('cancelled', 'rejected')
        HAVING (b.Total_price - Total_paid) > 0
        ORDER BY b.Payment_deadline ASC
        LIMIT 4
    ");
    $payments_stmt->execute([$user_id]);
    $upcoming_payments = $payments_stmt->fetchAll(PDO::FETCH_ASSOC);
}

function booking_status_badge(array $booking): array {
    $status = strtolower(trim($booking['Booking_status'] ?? 'upcoming'));
    switch ($status) {
        case 'confirmed':
            return ['bg-success-subtle text-success', 'CONFIRMED'];
        case 'pending':
            return ['bg-warning-subtle text-warning', 'PENDING'];
        case 'cancelled':
        case 'rejected':
            return ['bg-danger-subtle text-danger', strtoupper($status)];
        default:
            return ['bg-secondary-subtle text-secondary', strtoupper($status)];
    }
}

include __DIR__ . '/../includes/top_sidebar.php';
?>

<!-- <style>
    /* Internal CSS for custom aesthetic touches not covered by Bootstrap */
    :root {
        --gold: #A67C52;
        --navy: #0e1b2d;
    }
    .member-portal-tag { font-size: 10px; font-weight: 800; color: var(--gold); letter-spacing: 1px; }
    .font-cinzel { font-family: 'Cinzel', serif; }
    
    /* Featured Card Styling */
    .featured-card { 
        height: 350px; 
        background: url('https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&q=80&w=1000') center/cover;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }
    .featured-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(transparent, rgba(14, 27, 45, 0.95));
        display: flex; flex-direction: column; justify-content: flex-end;
    }

    /* Timeline styling */
    .activity-timeline { list-style: none; padding-left: 0; }
    .activity-item { position: relative; padding-left: 45px; margin-bottom: 25px; }
    .activity-item::before {
        content: ""; position: absolute; left: 16px; top: 32px; bottom: -25px;
        width: 1px; background: #dee2e6;
    }
    .activity-item:last-child::before { display: none; }
    .act-icon {
        position: absolute; left: 0; top: 0;
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 12px;
    }
</style> -->

<!-- Main Dashboard Content -->
<div class="container-fluid">
    
    <!-- Header Row -->
    <div class="    mb-4 gap-3">
            <span class="text-tag text-uppercase">Dashboard</span>
            <h1 class="font-cinzel display-5 fw-bold mt-1">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?></h1>
            <p class="text-secondary mb-0">Your curated dashboard for bespoke event planning and exclusive venue access.</p>
    </div>

    <div class="row g-4">
        <!-- Left Column: Featured & Upcoming -->
        <div class="col-12 col-xl-8">
            
            <!-- Featured Card -->
            <?php if ($featured_booking): ?>
                <?php 
                $bg_image = !empty($featured_booking['Venue_image']) ? htmlspecialchars($featured_booking['Venue_image'], ENT_QUOTES) : 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&q=80&w=1000'; 
                ?>
                <div class="featured-card mb-4 shadow-sm" style="background: linear-gradient(rgba(14, 27, 45, 0.45), rgba(14, 27, 45, 0.85)), url('<?= $bg_image ?>') center/cover;">
                    <div class="featured-overlay p-4">
                        <h2 class="text-white font-cinzel"><?php echo htmlspecialchars($featured_booking['Venue_name'] ?? 'Upcoming Event'); ?></h2>
                        <p class="text-light opacity-75 mb-4"><?php echo date('l, M j, Y', strtotime($featured_booking['Event_date'])); ?> • <?php echo htmlspecialchars($featured_booking['Event_time'] ?? 'TBD'); ?></p>
                        <div class="d-flex flex-wrap align-items-center gap-4 border-top border-secondary pt-3">
                            <div>
                                <label class="d-block text-light small text-uppercase">Guest Count</label>
                                <span class="text-white fw-bold"><?php echo htmlspecialchars($featured_booking['Guest_count'] ?? 'N/A'); ?> Guests</span>
                            </div>
                            <div>
                                <?php list($badgeClass, $statusLabel) = booking_status_badge($featured_booking); ?>
                                <label class="d-block text-light small text-uppercase">Status</label>
                                <span class="text-white fw-bold"><?php echo htmlspecialchars($statusLabel); ?></span>
                            </div>
                            <button class="btn btn-light btn-sm ms-auto fw-bold px-3">View Contract</button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-4 border-0 shadow-sm p-4 text-center">
                    <p class="text-muted m-0">No confirmed bookings yet.
                    <a href="/venuebook/client/Venue.php" class="fw-semibold" style="color: var(--accent-gold);">Browse Venues &rarr;</a></p>
                </div>
            <?php endif; ?>

            <!-- Upcoming Events Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 fw-bold mb-0">Upcoming Events</h3>
                <a href="/venuebook/client/booking.php" class="text-decoration-none small fw-bold" style="color: var(--accent-gold);">View All</a>
            </div>

            <div class="row g-3">
                <?php if (empty($other_upcoming_bookings)): ?>
                    <div class="col-12">
                        <div class="card h-100 border-0 shadow-sm p-4 text-center">
                            <p class="text-muted mb-0">No other upcoming bookings found.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($other_upcoming_bookings as $booking): ?>
                        <?php list($badgeClass, $statusLabel) = booking_status_badge($booking); ?>
                        <div class="col-12 col-md-6">
                            <div class="card h-100 border-0 shadow-sm p-3">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="bg-light text-dark p-2 rounded">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </div>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($statusLabel); ?></span>
                                </div>
                                <h4 class="h6 fw-bold mb-1"><?php echo htmlspecialchars($booking['Venue_name'] ?? 'Event Venue'); ?></h4>
                                <p class="small text-muted mb-2"><?php echo date('M d, Y', strtotime($booking['Event_date'])); ?> • <?php echo htmlspecialchars($booking['Event_time'] ?? 'TBD'); ?></p>
                                <div class="mt-auto small fw-bold"><i class="fa-solid fa-users me-1"></i> <?php echo htmlspecialchars($booking['Guest_count'] ?? 'N/A'); ?> Guests</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Activity & Support -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm p-4" style="background-color: #f8f7f2;">
                <h3 class="h6 fw-bold mb-4">Upcoming Payments</h3>
                
                <ul class="activity-timeline">
                    <?php if (empty($upcoming_payments)): ?>
                        <li class="activity-item">
                            <div class="act-icon bg-success-subtle text-success"><i class="fa-solid fa-check"></i></div>
                            <strong class="d-block small">All caught up!</strong>
                            <p class="small text-muted mb-1">You have no pending payments.</p>
                        </li>
                    <?php else: ?>
                        <?php foreach ($upcoming_payments as $payment): ?>
                            <?php 
                                $balance = $payment['Total_price'] - $payment['Total_paid'];
                                $is_overdue = strtotime($payment['Payment_deadline']) < time();
                                $icon_class = $is_overdue ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning';
                                $icon = $is_overdue ? 'fa-exclamation-triangle' : 'fa-file-invoice-dollar';
                            ?>
                            <li class="activity-item">
                                <div class="act-icon <?= $icon_class ?>"><i class="fa-solid <?= $icon ?>"></i></div>
                                <strong class="d-block small">Balance: ₱<?= number_format($balance, 2) ?></strong>
                                <p class="small text-muted mb-1"><?= htmlspecialchars($payment['Venue_name']) ?></p>
                                <span class="text-uppercase fw-bold <?= $is_overdue ? 'text-danger' : 'text-secondary' ?>" style="font-size: 9px;">
                                    <?= $is_overdue ? 'Overdue: ' : 'Due: ' ?><?= date('M j, Y', strtotime($payment['Payment_deadline'])) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <div class="card border-dark-subtle bg-white p-3 mt-4">
                    <label class="member-portal-tag d-block mb-2">Customer Support</label>
                    <p class="small text-muted mb-3">Have questions or need assistance? Reach out to our support team.</p>
                    <a class="btn btn-dark btn-sm w-100 fw-bold" href="mailto:support@venuebook.com">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>