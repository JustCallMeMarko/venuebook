<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');

require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../config/nav.php';

$venue_id = isset($_GET['venue_id']) ? (int)$_GET['venue_id'] : 0;
$guest_count = isset($_GET['guest_count']) ? (int)$_GET['guest_count'] : 0;
$event_date = isset($_GET['event_date']) ? $_GET['event_date'] : '';

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

// Fetch packages and inclusions
$stmt = $conn->prepare("
    SELECT p.*, GROUP_CONCAT(i.inclusion SEPARATOR '|') as inclusions
    FROM packages p
    LEFT JOIN inclusions i ON p.Package_id = i.package_id
    WHERE p.Venue_id = ? AND p.Status = 'active'
    GROUP BY p.Package_id
");
$stmt->execute([$venue_id]);
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$active_nav = 'Venue';
$page_title = 'Step 2 - Package Selection';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --picker-primary: #0c182a;
        --picker-secondary: #bf9b74;
        --secondary-light: #f7f3ec;
        --bg-main: var(--primary-color);
        --vb-dark-blue: #0c182a;
        --vb-light-brown: #bf9b74;
        --vb-muted-brown: #8D765E;
        --vb-active-bg: #FAF6ED;
    }

    .font-crimson {
        font-family: 'Crimson Pro', serif;
    }

    .font-playfair {
        font-family: 'Playfair Display SC', serif;
    }

    /* Timeline Styling */
    .steps-container {
        max-width: 650px;
        margin: 0 auto 1rem;
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .step-line {
        position: absolute;
        height: 2px;
        background: #e2e2e1;
        width: 100%;
        top: 20px;
        z-index: 1;
    }

    .step-unit {
        position: relative;
        z-index: 2;
        text-align: center;
        background: var(--bg-main);
        padding: 0 15px;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        background: #e2e2e1;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #888;
        margin: 0 auto 8px;
    }

    .step-unit.active .step-circle {
        background: var(--picker-primary);
        color: #fff;
    }

    .step-unit.completed .step-circle {
        background: #A2BB92;
        color: #fff;
    }

    .step-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #888;
    }

    .step-unit.active .step-label {
        color: var(--picker-primary);
    }

    /* Package Cards */
    .package-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (min-width: 768px) {
        .package-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .vb-card {
        background: white;
        border-radius: 16px;
        border: 2px solid #eaeaea;
        padding: 1.8rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .vb-card:hover {
        border-color: var(--vb-light-brown);
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
    }

    .vb-card.selected {
        border-color: var(--vb-dark-blue);
        background-color: #fcfbf9;
        box-shadow: 0 8px 20px rgba(12, 24, 42, 0.08);
    }

    .vb-card .selection-indicator {
        position: absolute;
        top: 15px;
        right: 15px;
        color: var(--vb-dark-blue);
        font-size: 1.5rem;
        line-height: 1;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }

    .vb-card.selected .selection-indicator {
        opacity: 1;
    }

    .pkg-name {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--vb-dark-blue);
        margin-bottom: 0.5rem;
    }

    .pkg-price {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--vb-light-brown);
        margin-bottom: 1.5rem;
    }

    .inclusion-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #888;
        letter-spacing: 1px;
        margin-bottom: 0.8rem;
    }

    .inclusion-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-grow: 1;
        align-items: flex-start;
    }

    .inclusion-badge {
        background: #f8f6f1;
        color: var(--vb-dark-blue);
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        border: 1px solid #eaeaea;
        height: fit-content;
    }

    .inclusion-badge i {
        color: #A2BB92;
        margin-right: 0.4rem;
        font-size: 0.9rem;
    }

    /* Summary Sidebar */
    .summary-card {
        background: white;
        border: 1px solid #E2DFD9;
        border-radius: 12px;
        padding: 1.8rem;
        position: sticky;
        top: 20px;
    }

    .summary-label {
        font-size: 10px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    .summary-val {
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .dotted-divider {
        border-top: 1px dotted #ccc;
        margin: 1.5rem 0;
    }

    .btn-next {
        background: var(--vb-dark-blue);
        color: white;
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-next:hover:not(:disabled) {
        background: #1e2d44;
    }

    .btn-next:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid px-4">
    <!-- Timeline Steps -->
    <div class="steps-container">
        <div class="step-line"></div>
        <div class="step-unit completed">
            <div class="step-circle shadow-sm"><i class="bi bi-check2"></i></div>
            <div class="step-label">Venue</div>
        </div>
        <div class="step-unit active">
            <div class="step-circle shadow-sm">2</div>
            <div class="step-label">Package</div>
        </div>
        <div class="step-unit">
            <div class="step-circle shadow-sm">3</div>
            <div class="step-label">Payment</div>
        </div>
    </div>

    <div class="row gx-lg-5">
        <!-- Left Column: Package Options -->
        <div class="col-12 col-lg-8">
            <form action="Payment.php" method="GET" id="step2Form">
                <input type="hidden" name="venue_id" value="<?= $venue_id ?>">
                <input type="hidden" name="guest_count" value="<?= htmlspecialchars($guest_count, ENT_QUOTES) ?>">
                <input type="hidden" name="event_date" value="<?= htmlspecialchars($event_date, ENT_QUOTES) ?>">
                <input type="hidden" name="package_id" id="selected_package_id" value="">

                <?php if (empty($packages)): ?>
                    <div class="alert alert-warning">No active packages are available for this venue right now.</div>
                <?php else: ?>
                    <div class="package-grid">
                        <!-- None Option -->
                        <div class="vb-card shadow-sm selected" onclick="selectPackage('', 0, 'None (Venue Only)', this)">
                            <i class="bi bi-check-circle-fill selection-indicator"></i>
                            <div class="d-flex flex-column h-100">
                                <h3 class="pkg-name text-muted">None</h3>
                                <div class="pkg-price">₱0.00</div>
                                <div class="inclusion-label">Details</div>
                                <div class="inclusion-badges">
                                    <span class="inclusion-badge"><i class="bi bi-info-circle text-muted"></i> Venue rental only</span>
                                    <span class="inclusion-badge"><i class="bi bi-info-circle text-muted"></i> Self-catered</span>
                                </div>
                            </div>
                        </div>

                        <?php foreach ($packages as $pkg): ?>
                            <div class="vb-card shadow-sm" onclick="selectPackage(<?= $pkg['Package_id'] ?>, <?= (float)$pkg['Price'] ?>, '<?= htmlspecialchars($pkg['Name'], ENT_QUOTES) ?>', this)">
                                <i class="bi bi-check-circle-fill selection-indicator"></i>
                                <div class="d-flex flex-column h-100">
                                    <h3 class="pkg-name"><?= htmlspecialchars($pkg['Name'], ENT_QUOTES) ?></h3>
                                    <div class="pkg-price">₱<?= number_format((float)$pkg['Price'], 2) ?></div>
                                    
                                    <?php
                                    $incl_array = !empty($pkg['inclusions']) ? explode('|', $pkg['inclusions']) : [];
                                    ?>
                                    <?php if (!empty($incl_array)): ?>
                                        <div class="inclusion-label">Inclusions</div>
                                        <div class="inclusion-badges">
                                            <?php foreach ($incl_array as $inc): ?>
                                                <span class="inclusion-badge"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($inc, ENT_QUOTES) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Right Column: Summary Sidebar -->
        <div class="col-12 col-lg-4">
            <aside class="summary-card shadow-sm mt-4 mt-lg-0">
                <h2 class="h5 fw-bold font-playfair border-bottom pb-3 mb-4">Booking Summary</h2>

                <div class="summary-label">Selected Venue</div>
                <div class="summary-val"><?= htmlspecialchars($venue['Name'], ENT_QUOTES) ?></div>

                <div class="summary-label">Selected Package</div>
                <div class="summary-val text-primary" id="summary-pkg-name">None (Venue Only)</div>

                <div class="summary-label">Date & Guests</div>
                <div class="summary-val text-dark"><?= htmlspecialchars($event_date, ENT_QUOTES) ?> (<?= htmlspecialchars($guest_count, ENT_QUOTES) ?> Guests)</div>

                <div class="dotted-divider"></div>

                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Venue Hire</span>
                    <span>₱<?= number_format((float)$venue['Price_per_day'], 2) ?></span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Package Service</span>
                    <span id="summary-pkg-price">₱0.00</span>
                </div>
                <?php $service_fee = 250.00; ?>
                <div class="d-flex justify-content-between small text-muted mb-3">
                    <span>Service Fee</span>
                    <span>₱<?= number_format($service_fee, 2) ?></span>
                </div>

                <div class="d-flex justify-content-between align-items-center fw-bold fs-5 mb-4 py-3 border-top">
                    <span>Total Est.</span>
                    <span style="color: var(--vb-dark-blue); font-size: 1.4rem;" id="summary-total">₱<?= number_format((float)$venue['Price_per_day'] + $service_fee, 2) ?></span>
                </div>

                <button class="btn-next mb-3" type="submit" form="step2Form" id="btn-proceed">
                    Next: Payment <i class="bi bi-arrow-right ms-2"></i>
                </button>

                <div class="text-center">
                    <a href="Picker.php?venue_id=<?= $venue_id ?>" class="text-dark text-decoration-none small fw-bold opacity-75">Back to Details</a>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    const venueBasePrice = <?= (float)$venue['Price_per_day'] ?>;
    const serviceFee = <?= $service_fee ?>;

    function selectPackage(id, price, name, element) {
        document.getElementById('selected_package_id').value = id;

        document.querySelectorAll('.vb-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');

        document.getElementById('summary-pkg-name').innerText = name;
        document.getElementById('summary-pkg-price').innerText = '₱' + price.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const total = venueBasePrice + serviceFee + parseFloat(price);
        document.getElementById('summary-total').innerText = '₱' + total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        document.getElementById('btn-proceed').disabled = false;
    }
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>