<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');

require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../config/nav.php';

$venue_id = isset($_GET['venue_id']) ? (int)$_GET['venue_id'] : 0;
if ($venue_id <= 0) {
    header('Location: Venue.php');
    exit;
}

// Fetch venue details
$stmt = $conn->prepare('SELECT * FROM venue WHERE Venue_id = ?');
$stmt->execute([$venue_id]);
$venue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venue) {
    header('Location: Venue.php');
    exit;
}

$booked_dates_stmt = $conn->prepare("SELECT Event_date FROM bookings WHERE Venue_id = ? AND Booking_status IN ('pending','confirmed','approved')");
$booked_dates_stmt->execute([$venue_id]);
$booked_dates = $booked_dates_stmt->fetchAll(PDO::FETCH_COLUMN);
$booked_dates_json = json_encode($booked_dates);

$active_nav = 'Venue';  
$page_title = 'Step 1 - Venue Selection';

include __DIR__ . '/../includes/top_sidebar.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    :root {
        --picker-primary: #0c182a;
        --picker-secondary: #bf9b74;
        --secondary-light: #f7f3ec;
        --bg-main: var(--primary-color);
    }
    .font-crimson { font-family: 'Crimson Pro', serif; }
    .font-playfair { font-family: 'Playfair Display SC', serif; }

    /* Timeline Styling */
    .steps-container { max-width: 650px; margin: 0 auto 4rem; position: relative; display: flex; justify-content: space-between; align-items: center; }
    .step-line { position: absolute; height: 2px; background: #e2e2e1; width: 100%; top: 20px; z-index: 1; }
    .step-unit { position: relative; z-index: 2; text-align: center; background: var(--bg-main); padding: 0 15px; }
    .step-circle { 
        width: 40px; height: 40px; background: #e2e2e1; border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 700; color: #888; margin: 0 auto 8px; 
    }
    .step-unit.active .step-circle { background: var(--picker-primary); color: #fff; }
    .step-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; }
    .step-unit.active .step-label { color: var(--picker-primary); }

    /* Main Card & Form */
    .venue-main-card { background: #fff; padding: 2.5rem; border-radius: 12px; }
    .venue-img-large { width: 100%; max-width: 220px; height: 220px; object-fit: cover; border-radius: 10px; }
    .form-section { background: var(--secondary-light); border: 1px solid #eaddca; padding: 2rem; border-radius: 12px; }

    /* Summary Panel */
    .summary-panel { background: #fdfcf9; border: 1px solid #e8e8e8; padding: 2.5rem; border-radius: 12px; position: sticky; top: 20px; }
    .btn-action { background: var(--picker-primary); color: #fff; width: 100%; padding: 1rem; border-radius: 8px; font-weight: 600; border: none; transition: 0.3s; }
    .btn-action:hover { background: #152945; color: #fff; }
</style>

<div class="container-fluid">
    <!-- Timeline Steps -->
    <div class="steps-container">
        <div class="step-line"></div>
        <div class="step-unit active">
            <div class="step-circle shadow-sm">1</div>
            <div class="step-label">Venue</div>
        </div>
        <div class="step-unit">
            <div class="step-circle shadow-sm">2</div>
            <div class="step-label">Package</div>
        </div>
        <div class="step-unit">
            <div class="step-circle shadow-sm">3</div>
            <div class="step-label">Payment</div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Left: Details & Form -->
        <div class="col-12 col-xl-8">
            <div class="venue-main-card shadow-sm border-0 mb-4">
                <div class="d-md-flex align-items-start mb-4">
                    <?php if (!empty($venue['image'])): ?>
                        <img src="<?= htmlspecialchars($venue['image'], ENT_QUOTES) ?>" class="venue-img-large me-md-4 mb-3 mb-md-0 shadow-sm" alt="Venue">
                    <?php else: ?>
                        <div class="venue-img-large me-md-4 mb-3 mb-md-0 shadow-sm d-flex align-items-center justify-content-center bg-light text-muted">
                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="d-flex align-items-baseline flex-wrap">
                            <h2 class="font-crimson display-6 mb-0 me-3"><?= htmlspecialchars($venue['Name'], ENT_QUOTES) ?></h2>
                            <span class="text-secondary fw-bold fs-4">₱<?= number_format((float)$venue['Price_per_day'], 2) ?></span>
                        </div>
                        <p class="text-muted small mt-2"><?= htmlspecialchars($venue['Description'] ?? 'No description available.', ENT_QUOTES) ?></p>
                        <div class="row mt-3 g-2">
                            <div class="col-12 small"><i class="fas fa-map-marker-alt text-warning me-2"></i> <?= htmlspecialchars($venue['Location'], ENT_QUOTES) ?></div>
                            <div class="col-12 small"><i class="fas fa-users text-warning me-2"></i> Up to <?= htmlspecialchars($venue['Capacity'], ENT_QUOTES) ?> Guests</div>
                        </div>
                    </div>
                </div>

                <!-- Form Inputs -->
                <div class="form-section shadow-sm">
                    <form action="packages.php" method="GET" id="step1Form">
                        <input type="hidden" name="venue_id" value="<?= $venue_id ?>">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Guest Count</label>
                                <div class="input-group shadow-sm">
                                    <input type="number" name="guest_count" class="form-control py-2 border-0" placeholder="e.g. 150" min="1" max="<?= htmlspecialchars($venue['Capacity'], ENT_QUOTES) ?>" required>
                                    <span class="input-group-text bg-white border-0"><i class="fas fa-users text-muted"></i></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Preferred Date</label>
                                <input type="text" id="eventDateInput" name="event_date" class="form-control py-2 border-0 shadow-sm bg-white" placeholder="Select an available date..." required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Summary Sidebar -->
        <div class="col-12 col-xl-4">
            <aside class="summary-panel shadow-sm">
                <h4 class="font-crimson mb-4 pb-2 border-bottom">Booking Summary</h4>
                
                <div class="mb-4">
                    <label class="text-muted fw-bold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px; text-transform:uppercase;">Selected Venue</label>
                    <div class="fw-bold"><?= htmlspecialchars($venue['Name'], ENT_QUOTES) ?></div>
                    <small class="text-muted">Capacity: <?= htmlspecialchars($venue['Capacity'], ENT_QUOTES) ?> Guests • ₱<?= number_format((float)$venue['Price_per_day'], 2) ?> Base</small>
                </div>

                <div class="py-3 border-top border-bottom" style="border-style: dashed !important; border-color: #dee2e6 !important;">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Venue Hire</span>
                        <span class="fw-bold">₱<?= number_format((float)$venue['Price_per_day'], 2) ?></span>
                    </div>
                    <?php 
                    $service_fee = 250.00;
                    $total = (float)$venue['Price_per_day'] + $service_fee;
                    ?>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Service Fee</span>
                        <span class="fw-bold">₱<?= number_format($service_fee, 2) ?></span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <span class="fw-bold">Estimated Total</span>
                    <h3 class="fw-bold mb-0 text-navy">₱<?= number_format($total, 2) ?></h3>
                </div>

                <button class="btn-action shadow-sm" type="submit" form="step1Form">
                    Proceed to Package <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </aside>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    const bookedDates = <?= $booked_dates_json ?>;
    
    flatpickr("#eventDateInput", {
        minDate: "today",
        disable: bookedDates,
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        placeholder: "Select an available date..."
    });

    document.getElementById('step1Form').addEventListener('submit', function(e) {
        const dateInput = document.getElementById('eventDateInput').value;
        if (!dateInput) {
            e.preventDefault();
            alert("Please select a Preferred Date before proceeding.");
        }
    });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>