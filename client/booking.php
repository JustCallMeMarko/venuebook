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
            SELECT b.*, v.Name as Venue_name,
                (SELECT COALESCE(SUM(Amount), 0) FROM payments WHERE Booking_id = b.Booking_id AND Status = 'completed') as Total_paid
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



?>

<style>
    :root {
        --bg-cream: #f8f6f1;
        --navy: #0e1b2d;
        --accent-gold: #A67C52;
    }

    .text-tag {
        font-size: 10px;
        font-weight: 800;
        color: var(--accent-gold);
        letter-spacing: 1px;
    }

    .font-cinzel {
        font-family: 'Cinzel', serif;
    }

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
        width: 140px;
        height: 110px;
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

    .bg-navy {
        background-color: var(--navy);
    }

    .bg-soft-gold {
        background-color: #f1f0e9;
    }

    /* Timeline Dots */
    .timeline-dot {
        width: 8px;
        height: 8px;
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
        <div class="col-12">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="p-2">Venue</th>
                                <th class="p-2">Event Date</th>
                                <th class="p-2">Guests</th>
                                <th class="p-2">Total Price</th>
                                <th class="p-2">Amount Paid</th>
                                <th class="p-2">Balance</th>
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
                                    $status = strtolower(trim($b['Booking_status'] ?? ''));
                                    $is_overdue = strtotime($b['Payment_deadline']) < time() && $status === 'pending';
                                    ?>
                                    <tr class="<?= $is_overdue ? 'table-danger' : '' ?>">
                                        <td class="p-2 fw-semibold"><?= htmlspecialchars($b['Venue_name'] ?? 'N/A') ?></td>
                                        <td class="p-2"><?= date('M d, Y', strtotime($b['Event_date'])) ?></td>
                                        <td class="p-2"><?= htmlspecialchars($b['Guest_count']) ?></td>
                                        <td class="p-2">₱<?= number_format($b['Total_price'], 2) ?></td>
                                        <td class="p-2 text-success">₱<?= number_format($b['Total_paid'], 2) ?></td>
                                        <td class="p-2 fw-semibold">₱<?= number_format($b['Total_price'] - $b['Total_paid'], 2) ?></td>
                                        <td class="p-2 <?= $is_overdue ? 'text-danger fw-bold' : '' ?>">
                                            <?= $is_overdue ? '⚠ ' : '' ?><?= date('M d, Y', strtotime($b['Payment_deadline'])) ?>
                                        </td>
                                        <td class="p-2">
                                            <?php if ($status === 'confirmed'): ?>
                                                <span class="badge bg-light text-secondary border px-2 py-1">CONFIRMED</span>
                                            <?php elseif ($status === 'cancelled' || $status === 'rejected'): ?>
                                                <span class="badge bg-secondary text-white px-2 py-1"><?= strtoupper($status) ?></span>
                                            <?php elseif ($is_overdue): ?>
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1">PAYMENT OVERDUE</span>
                                            <?php elseif ($status === 'approved'): ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">APPROVED</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">PENDING</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="p-2 d-flex align-items-center">
                                            <button class="btn btn-outline-secondary btn-sm px-3 text-nowrap"
                                                data-bs-toggle="modal"
                                                data-bs-target="#bookingModal"
                                                data-venue="<?= htmlspecialchars($b['Venue_name'] ?? 'N/A') ?>"
                                                data-date="<?= date('M d, Y', strtotime($b['Event_date'])) ?>"
                                                data-guests="<?= htmlspecialchars($b['Guest_count']) ?>"
                                                data-price="<?= (float)$b['Total_price'] ?>"
                                                data-paid="<?= (float)$b['Total_paid'] ?>"
                                                data-deadline="<?= date('M d, Y', strtotime($b['Payment_deadline'])) ?>"
                                                data-status="<?= htmlspecialchars($b['Booking_status']) ?>"
                                                data-overdue="<?= $is_overdue ? '1' : '0' ?>"
                                                data-id="<?= $b['Booking_id'] ?>">
                                                View
                                            </button>

                                            <?php if (!in_array($status, ['confirmed', 'cancelled', 'rejected']) && ($b['Total_price'] - $b['Total_paid']) > 0): ?>
                                                <a href="PayBalance.php?booking_id=<?= htmlspecialchars($b['Booking_id']) ?>" class="btn btn-primary btn-sm ms-2">Pay Balance</a>
                                            <?php endif; ?>
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

        </div><!-- end col-12 -->


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
                        <p class="info-label text-uppercase">Amount Paid</p>
                        <p class="small fw-bold mb-0 text-success" id="modalPaid"></p>
                    </div>

                    <div class="col-6">
                        <p class="info-label text-uppercase">Remaining Balance</p>
                        <p class="small fw-bold mb-0" id="modalBalance"></p>
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
        const price = parseFloat(btn.getAttribute('data-price')) || 0;
        const paid = parseFloat(btn.getAttribute('data-paid')) || 0;
        const balance = price - paid;
        const deadline = btn.getAttribute('data-deadline');
        const status = btn.getAttribute('data-status');
        const overdue = btn.getAttribute('data-overdue');
        const bookingId = btn.getAttribute('data-id');
        
        document.getElementById('modalVenueName').textContent = venue;
        document.getElementById('modalDate').textContent = date;
        document.getElementById('modalGuests').textContent = guests;
        document.getElementById('modalPrice').textContent = '₱' + price.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('modalPaid').textContent = '₱' + paid.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('modalBalance').textContent = '₱' + balance.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('modalDeadline').textContent = deadline;

        // Status badge
        let statusBadge = '';

        if (status === 'confirmed') {
            statusBadge = '<span class="badge bg-light text-secondary border px-2 py-1">CONFIRMED</span>';
        } else if (status === 'cancelled' || status === 'rejected') {
            statusBadge = `<span class="badge bg-secondary text-white px-2 py-1">${status.toUpperCase()}</span>`;
        } else if (overdue === '1') {
            statusBadge = '<span class="badge bg-danger-subtle text-danger px-2 py-1">PAYMENT OVERDUE</span>';
        } else if (status === 'approved') {
            statusBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">APPROVED</span>';
        } else {
            statusBadge = '<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">PENDING</span>';
        }

        document.getElementById('modalStatus').innerHTML = statusBadge;

        // Action buttons
        let actions = '';
        const contractLink = `<a href="Contract.php?booking_id=${bookingId}" class="btn btn-outline-secondary px-4">View Contract</a>`;
        
        const cancelForm = `
            <form action="../actions/cancel_booking.php" method="POST" class="d-inline ms-2" onsubmit="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');">
                <input type="hidden" name="booking_id" value="${bookingId}">
                <button type="submit" class="btn btn-outline-danger px-4">Cancel Booking</button>
            </form>
        `;

        if (status === 'cancelled' || status === 'rejected') {
            actions = ``;
        } else if (balance <= 0) {
            actions = `
                <span class="badge bg-success py-2 px-3 me-2">Fully Paid / Confirmed</span>
                ${contractLink}
                ${cancelForm}
            `;
            } else if (paid > 0 && balance > 0) {
            actions = `
                ${contractLink}
                ${cancelForm}
            `;
        } else if (status === 'approved') {
            actions = `
                ${contractLink}
                ${cancelForm}
            `;
        } else if (status === 'pending') {
            actions = `
                <button class="btn btn-secondary px-4" disabled>Awaiting Approval</button>
                ${contractLink}
                ${cancelForm}
            `;
        } else {
            actions = `
                ${contractLink}
                ${cancelForm}
            `;
        }

        document.getElementById('modalActions').innerHTML = actions;

    });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>