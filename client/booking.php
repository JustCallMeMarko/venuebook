<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');
include __DIR__ . '/../config/nav.php';
include __DIR__ . '/../config/db.php';


$active_nav = 'Booking';  
$page_title = 'Booking';

include __DIR__ . '/../includes/top_sidebar.php';

$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$user_id = $_SESSION['user_id'];

$count_stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE User_id = ?");
$count_stmt->execute([$user_id]);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}
$offset = ($current_page - 1) * $items_per_page;

$stmt = $conn->prepare("
    SELECT b.*, v.Name as Venue_name 
    FROM bookings b
    LEFT JOIN venue v ON b.Venue_id = v.Venue_id
    WHERE b.User_id = ?
    ORDER BY 
        (b.Booking_status = 'pending') DESC,
        b.Event_date ASC
    LIMIT ? OFFSET ?
");

$stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt->bindValue(2, $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Key Deadlines - upcoming payment deadlines
$deadlines_stmt = $conn->prepare("
    SELECT b.Payment_deadline, b.Booking_id, v.Name as Venue_name
    FROM bookings b
    LEFT JOIN venue v ON b.Venue_id = v.Venue_id
    WHERE b.User_id = ?
    AND b.Payment_deadline >= NOW()
    ORDER BY b.Payment_deadline ASC
    LIMIT 3
");
$deadlines_stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$deadlines_stmt->execute();
$deadlines = $deadlines_stmt->fetchAll(PDO::FETCH_ASSOC);

// Payment Overview
$payment_stmt = $conn->prepare("
    SELECT 
        SUM(b.Total_price) as total_committed,
        SUM(CASE WHEN p.Status = 'completed' THEN p.Amount ELSE 0 END) as amount_paid
    FROM bookings b
    LEFT JOIN payments p ON b.Booking_id = p.Booking_id
    WHERE b.User_id = ?
");
$payment_stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$payment_stmt->execute();
$payment_overview = $payment_stmt->fetch(PDO::FETCH_ASSOC);

$total_committed = $payment_overview['total_committed'] ?? 0;
$amount_paid = $payment_overview['amount_paid'] ?? 0;
$balance_due = $total_committed - $amount_paid;



?>

<style>
    :root {
        --bg-cream: #f8f6f1;
        --navy: #0e1b2d;
        --accent-gold: #A67C52;
    }
    .text-tag { font-size: 10px; font-weight: 800; color: var(--accent-gold); letter-spacing: 1px; }
    .font-cinzel { font-family: 'Cinzel', serif; }

    .card.border-0.shadow-sm {
        border-radius: 8px !important;
        overflow: hidden;
    }
    
    /* Booking Card Styles */
    .booking-card {
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .booking-card.overdue {
        border-left: 5px solid #dc3545;
    }
    .venue-thumb {
        width: 140px; height: 110px;
        object-fit: cover;
        border-radius: 8px;
    }
    .info-label {
        font-size: 10px;
        font-weight: 800;
        color: #b0b0b0;
        display: block;
        margin-bottom: 2px;
    }

    /* Widget Styles */
    .widget-card {
        border-radius: 12px;
        border: none;
    }
    .bg-navy { background-color: var(--navy); }
    .bg-soft-gold { background-color: #f1f0e9; }
    
    /* Timeline Dots */
    .timeline-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
    }

    .table th:first-child,
    .table td:first-child {
        padding-left: 16px !important;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <h1 class="font-cinzel display-5 fw-bold mt-1">My Bookings</h1>
        <p class="text-secondary mb-0">Manage your corporate events, track payment deadlines, and finalize contracts.</p>
    </div>

    <div class="row g-4">

        <!-- Left: Booking List -->
        <div class="col-12 col-xl-8">

        <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="p-2">Venue</th>
                        <th class="p-2">Event Date</th>
                        <th class="p-2">Guests</th>
                        <th class="p-2">Total Price</th>
                        <th class="p-2">Payment Deadline</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center p-4 text-muted">No bookings found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <?php
                                $is_overdue = strtotime($b['Payment_deadline']) < time() && $b['Booking_status'] === 'pending';
                            ?>
                            <tr class="<?= $is_overdue ? 'table-danger' : '' ?>">
                                <td class="p-2 fw-semibold"><?= htmlspecialchars($b['Venue_name'] ?? 'N/A') ?></td>
                                <td class="p-2"><?= date('M d, Y', strtotime($b['Event_date'])) ?></td>
                                <td class="p-2"><?= htmlspecialchars($b['Guest_count']) ?></td>
                                <td class="p-2">$<?= number_format($b['Total_price'], 2) ?></td>
                                <td class="p-2 <?= $is_overdue ? 'text-danger fw-bold' : '' ?>">
                                    <?= $is_overdue ? '⚠ ' : '' ?><?= date('M d, Y', strtotime($b['Payment_deadline'])) ?>
                                </td>
                                <td class="p-2">
                                    <?php if ($b['Booking_status'] === 'confirmed'): ?>
                                        <span class="badge bg-light text-secondary border px-2 py-1">CONFIRMED</span>
                                    <?php elseif ($is_overdue): ?>
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">PAYMENT OVERDUE</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">PENDING</span>
                                    <?php endif; ?>
                                </td>

                                <td class="p-2">
                                    <button class="btn btn-outline-secondary btn-sm px-3 text-nowrap" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bookingModal"
                                        data-venue="<?= htmlspecialchars($b['Venue_name'] ?? 'N/A') ?>"
                                        data-date="<?= date('M d, Y', strtotime($b['Event_date'])) ?>"
                                        data-guests="<?= htmlspecialchars($b['Guest_count']) ?>"
                                        data-price="$<?= number_format($b['Total_price'], 2) ?>"
                                        data-deadline="<?= date('M d, Y', strtotime($b['Payment_deadline'])) ?>"
                                        data-status="<?= htmlspecialchars($b['Booking_status']) ?>"
                                        data-overdue="<?= $is_overdue ? '1' : '0' ?>">
                                        View
                                    </button>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $current_page - 1 ?>">« Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $current_page + 1 ?>">Next »</a>
            </li>
        </ul>
    </nav>
   <?php endif; ?>

        </div><!-- end col-xl-8 -->

        <!-- Right: Widgets -->
        <div class="col-12 col-xl-4">
            
            <!-- Payment Widget -->
            <div class="card widget-card bg-navy text-white p-4 mb-4">
                <h3 class="h6 fw-bold mb-4">Payment Overview</h3>
                <div class="d-flex justify-content-between mb-2 small opacity-75">
                    <span>Total Committed</span>
                    <strong>$<?= number_format($total_committed, 2) ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-3 small opacity-75">
                    <span>Amount Paid</span>
                    <strong>$<?= number_format($amount_paid, 2) ?></strong>
                </div>
                <hr class="border-secondary mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="small fw-bold">Balance Due</span>
                    <span class="h4 fw-bold mb-0" style="color: #d4af37;">$<?= number_format($balance_due, 2) ?></span>
                </div>
                <button class="btn btn-light btn-sm w-100 fw-bold py-2">Download Tax Receipt</button>
            </div>

            <!-- Deadlines Widget -->
            <div class="card widget-card bg-soft-gold p-4">
                <h3 class="h6 fw-bold mb-4">Key Deadlines</h3>
                <?php if (empty($deadlines)): ?>
                    <p class="small text-muted">No upcoming deadlines.</p>
                <?php else: ?>
                    <?php foreach ($deadlines as $d): ?>
                    <div class="d-flex align-items-start mb-3">
                        <span class="timeline-dot bg-danger mt-1"></span>
                        <div>
                            <span class="text-muted fw-bold" style="font-size: 10px;">
                                <?= date('M d', strtotime($d['Payment_deadline'])) ?>
                            </span>
                            <p class="small fw-bold mb-0">Payment Due</p>
                            <small class="text-muted"><?= htmlspecialchars($d['Venue_name'] ?? 'N/A') ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <p class="text-tag text-uppercase mb-0" style="font-size:10px;font-weight:800;color:#8e734b;letter-spacing:1px;">Booking details</p>
                    <h5 class="modal-title font-cinzel fw-bold" id="modalVenueName"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3 border-top pt-3">
                    <div class="col-6">
                        <p class="info-label text-uppercase">Event date</p>
                        <p class="small fw-bold mb-0" id="modalDate"></p>
                    </div>

                    <div class="col-6">
                        <p class="info-label text-uppercase">Guests</p>
                        <p class="small fw-bold mb-0" id="modalGuests"></p>
                    </div>

                    <div class="col-6">
                        <p class="info-label text-uppercase">Total price</p>
                        <p class="small fw-bold mb-0" id="modalPrice"></p>
                    </div>

                    <div class="col-6">
                        <p class="info-label text-uppercase">Payment deadline</p>
                        <p class="small fw-bold mb-0" id="modalDeadline"></p>
                    </div>

                    <div class="col-6">
                        <p class="info-label text-uppercase">Status</p>
                        <p class="small fw-bold mb-0" id="modalStatus"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0" id="modalActions">
            </div>
        </div>
    </div>
</div>

<script>
    const bookingModal = document.getElementById('bookingModal');

    bookingModal.addEventListener('show.bs.modal', function(event) {

        const btn = event.relatedTarget;

        const venue = btn.getAttribute('data-venue');
        const date = btn.getAttribute('data-date');
        const guests = btn.getAttribute('data-guests');
        const price = btn.getAttribute('data-price');
        const deadline = btn.getAttribute('data-deadline');
        const status = btn.getAttribute('data-status');
        const overdue = btn.getAttribute('data-overdue');

        document.getElementById('modalVenueName').textContent = venue;
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalGuests').textContent = guests;
        document.getElementById('modalPrice').textContent = price;
        document.getElementById('modalDeadline').textContent = deadline;

        // Status badge
        let statusBadge = '';

        if (status === 'confirmed') {
            statusBadge = '<span class="badge bg-light text-secondary border px-2 py-1">CONFIRMED</span>';
        } 
        else if (overdue === '1') {
            statusBadge = '<span class="badge bg-danger-subtle text-danger px-2 py-1">PAYMENT OVERDUE</span>';
        } 
        else {
            statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">PENDING</span>';
        }

        document.getElementById('modalStatus').innerHTML = statusBadge;

        // Action buttons
        let actions = '';

        if (status === 'confirmed') {
            actions = `
                <button class="btn btn-navy px-4">Make Payment</button>
                <button class="btn btn-outline-secondary px-4">View Contract</button>
            `;
        } 
        else if (overdue === '1') {
            actions = `
                <button class="btn btn-danger px-4">! Settle Balance</button>
                <button class="btn btn-outline-secondary px-4">View Contract</button>
            `;
        } 
        else {
            actions = `
                <button class="btn btn-warning px-4">Make Payment</button>
                <button class="btn btn-outline-secondary px-4">View Contract</button>
            `;
        }

        document.getElementById('modalActions').innerHTML = actions;

    });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>