<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');
require_once __DIR__ . '/../config/db.php';

$user = get_currnt_user();

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
if ($booking_id <= 0) {
    header('Location: booking.php');
    exit;
}

// Fetch booking data
$stmt = $conn->prepare('
    SELECT 
        b.*, 
        c.contractid,
        v.Name AS VenueName, v.Location AS VenueLocation,
        p.Name AS PackageName,
        u.First_name AS AdminFirst, u.Last_name AS AdminLast
    FROM bookings b
    JOIN contracts c ON b.Booking_id = c.bookingid
    JOIN venue v ON b.Venue_id = v.Venue_id
    LEFT JOIN packages p ON b.Package_id = p.Package_id
    JOIN users u ON v.User_id = u.User_id
    WHERE b.Booking_id = ? AND b.User_id = ?
');
$stmt->execute([$booking_id, $user['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: booking.php');
    exit;
}

include __DIR__ . '/../config/nav.php';

$active_nav = 'Booking';
$page_title = 'Contract Agreement';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --vb-bg-color: #fbf8f3;
        --accent-gold: #A67C52;
        --vb-border-light: #e0e0e0;
    }

    .member-portal-tag { font-size: 10px; font-weight: 800; color: var(--accent-gold); letter-spacing: 1px; }
    
    /* Contract Box Styling */
    .contract-paper {
        background: white;
        border: 1px solid var(--vb-border-light);
        border-radius: 8px;
        padding: 4rem;
        max-width: 900px;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .vb-serif {
        font-family: 'Bodoni Moda', serif;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--accent-gold);
        margin-bottom: 0.5rem;
    }

    .section-header {
        font-size: 1.2rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 5px;
    }

    .contract-text {
        line-height: 1.8;
        color: #444;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .sig-line {
        border-top: 1px solid #333;
        margin-top: 5rem;
        text-align: center;
        padding-top: 0.8rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .btn-print {
        background-color: #12192a;
        color: white;
        font-size: 0.85rem;
        padding: 0.5rem 1.5rem;
        border-radius: 4px;
        border: none;
        transition: opacity 0.2s;
    }

    .btn-print:hover {
        opacity: 0.9;
        color: white;
    }

    /* Print logic */
    @media print {
        .sidebar, .vb-page-header, .notif-bell, .btn-print, header, nav, .bottom-sidebar {
            display: none !important;
        }

        .vb-main-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .contract-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            max-width: 100% !important;
        }
    }
</style>

<div class="container-fluid">
    <div class="d-md-flex justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="text-tag text-uppercase">Contract #<?= str_pad($booking['contractid'], 5, '0', STR_PAD_LEFT) ?></span>
            <h1 class="font-cinzel display-5 fw-bold mt-1">Legal Agreement</h1>
            <p class="text-secondary mb-0">Contract for <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
        </div>
        <div>
            <a href="booking.php" class="btn btn-outline-secondary btn-sm me-2 d-print-none">Back</a>
            <button class="btn-print shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Print Contract
            </button>
        </div>
    </div>

    <!-- The Paper Document -->
    <div class="contract-paper mb-5">
        <div class="d-flex align-items-center mb-4">
            <img src="/venuebook/assets/images/Logo.svg" style="width: 28px; margin-right: 12px;">
            <span class="vb-serif fw-bold" style="font-size: 1.4rem; letter-spacing: 1px;">VENUEBOOK</span>
        </div>

        <hr class="mb-5 opacity-10">

        <!-- Parties Involved -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="info-label">Client / Licensee</div>
                <div class="fw-bold fs-5"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
            </div>
            <div class="col-6">
                <div class="info-label">Administrator</div>
                <div class="fw-bold fs-5"><?= htmlspecialchars($booking['AdminFirst'] . ' ' . $booking['AdminLast']) ?></div>
                <div class="small text-muted">Estate Operations Manager</div>
            </div>
        </div>

        <!-- Agreement Text -->
        <h2 class="section-header vb-serif">I. General Agreement</h2>
        <p class="contract-text">
            The Client hereby agrees to pay the Administrator the total amount of <strong>₱<?= number_format((float)$booking['Total_price'], 2) ?></strong>. Payments are considered refundable up to <strong>three (3) days prior</strong> to the specified event date (<strong><?= date('F j, Y', strtotime($booking['Event_date'])) ?></strong>) or payment deadline. It is understood that if the Client cancels within 3 days of the specified date, the payment or downpayment shall be deemed strictly non-refundable as per company policy.
        </p>

        <h2 class="section-header vb-serif">II. Venue Assignment</h2>
        <p class="contract-text">
            <strong><?= htmlspecialchars($booking['VenueName'], ENT_QUOTES) ?></strong><br>
            <?= htmlspecialchars($booking['VenueLocation'], ENT_QUOTES) ?>.
        </p>

        <h2 class="section-header vb-serif">III. Service Package</h2>
        <p class="contract-text">
            <?php if ($booking['PackageName']): ?>
                <strong><?= htmlspecialchars($booking['PackageName'], ENT_QUOTES) ?>:</strong> Standard venue access, including chosen inclusions and dedicated event concierge.
            <?php else: ?>
                <strong>No Package Selected:</strong> Standard venue access only. No specialized inclusions or catering provided.
            <?php endif; ?>
        </p>

        <h2 class="section-header vb-serif">IV. Event Schedule</h2>
        <p class="contract-text">
            Scheduled Date: <strong><?= date('l, F j, Y', strtotime($booking['Event_date'])) ?></strong><br>
            Guest Count: <?= htmlspecialchars($booking['Guest_count'], ENT_QUOTES) ?> Guests
        </p>

        <!-- Signatures -->
        <div class="row mt-5 pt-4">
            <div class="col-5">
                <div class="sig-line text-muted">Client's Signature</div>
            </div>
            <div class="col-2"></div>
            <div class="col-5">
                <div class="sig-line text-muted">Administrator's Signature</div>
            </div>
        </div>

        <div class="text-center mt-5">
            <small class="text-muted" style="font-size: 10px;">Generated electronically via VenueBook Estate Management System</small>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>