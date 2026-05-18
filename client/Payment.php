<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');
require_once __DIR__ . '/../config/db.php';

$venue_id = isset($_GET['venue_id']) ? (int)$_GET['venue_id'] : 0;
$package_id = !empty($_GET['package_id']) ? (int)$_GET['package_id'] : null;
$guest_count = isset($_GET['guest_count']) ? (int)$_GET['guest_count'] : 0;
$event_date = isset($_GET['event_date']) ? $_GET['event_date'] : '';

if ($venue_id <= 0) {
    header('Location: Venue.php');
    exit;
}

// Fetch Venue
$stmt = $conn->prepare('SELECT * FROM venue WHERE Venue_id = ?');
$stmt->execute([$venue_id]);
$venue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venue) {
    header('Location: Venue.php');
    exit;
}

// Fetch Package
$package_price = 0;
if ($package_id) {
    $stmt = $conn->prepare('SELECT Price FROM packages WHERE Package_id = ?');
    $stmt->execute([$package_id]);
    $pkg = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($pkg) {
        $package_price = (float)$pkg['Price'];
    } else {
        $package_id = null;
    }
}

$venue_price = (float)$venue['Price_per_day'];
$subtotal = $venue_price + $package_price;
$service_fee = 250.00;
$total = $subtotal + $service_fee;

include __DIR__ . '/../config/nav.php';

$active_nav = 'Venue';  
$page_title = 'Step 3 - Payment';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --vb-accent-brown: #C19A6B;
        --vb-dark-panel: #17181C;
    }

    /* Timeline Styling */
    .steps-container { max-width: 650px; margin: 0 auto 4rem; position: relative; display: flex; justify-content: space-between; align-items: center; }
    .step-line { position: absolute; height: 2px; background: #e2e2e1; width: 100%; top: 20px; z-index: 1; }
    .step-unit { position: relative; z-index: 2; text-align: center; background: var(--primary-color); padding: 0 15px; }
    .step-circle { 
        width: 40px; height: 40px; background: #e2e2e1; border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 700; color: #888; margin: 0 auto 8px; 
    }
    .step-unit.active .step-circle { background: #0c182a; color: #fff; }
    .step-unit.completed .step-circle { background: #A2BB92; color: #fff; }
    .step-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; }
    .step-unit.active .step-label { color: #0c182a; }

    /* Credit Card Mockup */
    .credit-card {
        max-width: 380px; background: #3C4245; border-radius: 15px; padding: 25px; color: white;
        aspect-ratio: 1.58 / 1; display: flex; flex-direction: column; justify-content: space-between;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .card-numbers { font-family: 'Space Mono', monospace; font-size: 1.25rem; letter-spacing: 3px; display: flex; justify-content: space-between; }

    /* Custom Inputs */
    .custom-form-group { max-width: 380px; border: 1px solid #DEE2E6; border-radius: 8px; background: white; overflow: hidden; }
    .custom-input-row { display: flex; align-items: center; border-bottom: 1px solid #F0F0F0; }
    .custom-input-row:last-child { border-bottom: none; }
    .input-icon { width: 50px; text-align: center; color: #CCC; }
    .custom-form-group input { border: none; padding: 15px 0; font-size: 14px; width: 100%; outline: none; }

    /* Summary Panel */
    .summary-panel { 
        background-color: var(--vb-dark-panel); color: #A0A0A0; padding: 30px 25px; 
        display: flex; flex-direction: column; border-radius: 12px; position: sticky; top: 20px;
    }
    .total-amount { font-size: 2.5rem; color: white; font-weight: 500; margin-bottom: 25px; }
    .btn-pay { background: #FAF9F7; border: none; padding: 15px; border-radius: 6px; font-weight: 600; color: #333; width: 100%; transition: 0.3s; }
    .btn-pay:hover { background: #fff; transform: translateY(-2px); }
</style>

<div class="container-fluid">
    <!-- Timeline Steps -->
    <div class="steps-container">
        <div class="step-line"></div>
        <div class="step-unit completed">
            <div class="step-circle shadow-sm"><i class="bi bi-check2"></i></div>
            <div class="step-label">Venue</div>
        </div>
        <div class="step-unit completed">
            <div class="step-circle shadow-sm"><i class="bi bi-check2"></i></div>
            <div class="step-label">Package</div>
        </div>
        <div class="step-unit active">
            <div class="step-circle shadow-sm">3</div>
            <div class="step-label">Payment</div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Left: Payment Form -->
        <div class="col-12 col-xl-7">
            <!-- Payment Type -->
            <div class="mb-5">
                <h5 class="mb-3 small fw-bold text-uppercase opacity-75">Payment Plan</h5>
                <div class="d-flex flex-column gap-2">
                    <label class="d-flex align-items-center p-3 border rounded shadow-sm" style="cursor: pointer; background: white;">
                        <input type="radio" name="payment_type_ui" value="full" checked class="me-3 form-check-input" onchange="updateTotal(1, 'full')">
                        <div class="flex-grow-1">
                            <span class="fw-bold d-block">Full Payment</span>
                            <span class="small text-muted">Pay the entire 100% upfront today.</span>
                        </div>
                    </label>
                    <label class="d-flex align-items-center p-3 border rounded shadow-sm" style="cursor: pointer; background: white;">
                        <input type="radio" name="payment_type_ui" value="downpayment" class="me-3 form-check-input" onchange="updateTotal(0.5, 'downpayment')">
                        <div class="flex-grow-1">
                            <span class="fw-bold d-block">Downpayment</span>
                            <span class="small text-muted">Pay 50% now to reserve, and the rest later.</span>
                        </div>
                    </label>
                </div>
            </div>

            <h2 class="fw-semibold mb-4">Choose a payment method</h2>
            <div class="d-flex gap-2 mb-5">
                <button class="btn btn-outline-dark px-4 active">Card</button>
                <button class="btn btn-outline-secondary px-4" disabled>Cash</button>
                <button class="btn btn-outline-secondary px-4" disabled>PayPal</button>
            </div>

            <h5 class="mb-3 small fw-bold text-uppercase opacity-75">Card details</h5>
            
            <!-- Credit Card Graphic -->
            <div class="credit-card mb-4">
                <div class="d-flex justify-content-between">
                    <div style="width: 45px; height: 32px; background: #E6AE6A; border-radius: 4px;"></div>
                    <i class="bi bi-wifi text-white fs-4"></i>
                </div>
                <div class="card-numbers my-3" id="mockNumber">
                    0000 0000 0000 0000
                </div>
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <div class="small text-uppercase" id="mockName">JOHN WICK</div>
                        <div class="smaller opacity-75" id="mockExp">00/00</div>
                    </div>
                    <div class="d-flex">
                        <div style="width: 24px; height: 24px; background: #EB001B; border-radius: 50%; margin-right: -10px;"></div>
                        <div style="width: 24px; height: 24px; background: #F79E1B; border-radius: 50%; opacity: 0.8;"></div>
                    </div>
                </div>
            </div>

            <!-- Card Inputs -->
            <div class="mb-2 small fw-bold">Cardholder Name <i class="bi bi-pencil-square ms-1"></i></div>
            <div class="custom-form-group shadow-sm">
                <div class="custom-input-row">
                    <div class="input-icon"><i class="bi bi-person"></i></div>
                    <input type="text" id="cardName" placeholder="John Wick">
                </div>
                <div class="custom-input-row">
                    <div class="input-icon"><i class="bi bi-credit-card"></i></div>
                    <input type="text" id="cardNumber" placeholder="1234 1234 1234 1234" maxlength="19">
                </div>
                <div class="d-flex">
                    <div class="flex-fill border-end custom-input-row">
                        <input type="text" id="cardExp" placeholder="MM/YY" class="ps-4" maxlength="5">
                    </div>
                    <div class="flex-fill custom-input-row">
                        <input type="text" id="cardCvc" placeholder="CVC" class="ps-4" maxlength="4">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Payment Summary -->
        <div class="col-12 col-xl-5">
            <aside class="summary-panel shadow-lg">
                <h3 class="mb-5">Payment Summary</h3>
                
                <div class="d-flex justify-content-between mb-3">
                    <span>Venue Rental</span>
                    <span class="text-white fw-medium">₱<?= number_format($venue_price, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Package Service</span>
                    <?php if ($package_id): ?>
                        <span class="text-white fw-medium">₱<?= number_format($package_price, 2) ?></span>
                    <?php else: ?>
                        <span class="text-white fw-medium opacity-50">None</span>
                    <?php endif; ?>
                </div>
                
                <hr class="border-secondary my-4 opacity-25">
                
                <div class="d-flex justify-content-between mb-3">
                    <span>Processing Fee</span>
                    <span class="text-white fw-medium">₱<?= number_format($service_fee, 2) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Taxes</span>
                    <span class="text-white fw-medium">₱0.00</span>
                </div>
                
                <div class="mt-auto">
                    <div class="small fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">Amount to Pay</div>
                    <div class="total-amount font-mono" id="displayTotal">₱<?= number_format($total, 2) ?></div>
                    <form action="../actions/process_payment.php" method="POST" id="paymentForm">
                        <input type="hidden" name="venue_id" value="<?= $venue_id ?>">
                        <input type="hidden" name="package_id" value="<?= $package_id ? $package_id : '' ?>">
                        <input type="hidden" name="guest_count" value="<?= htmlspecialchars($guest_count, ENT_QUOTES) ?>">
                        <input type="hidden" name="event_date" value="<?= htmlspecialchars($event_date, ENT_QUOTES) ?>">
                        <input type="hidden" name="payment_type" id="paymentTypeInput" value="full">
                        <!-- Card inputs are not submitted since this is a mockup checkout -->
                        <button type="submit" class="btn-pay shadow-sm">Pay now</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    const baseTotal = <?= $total ?>;
    
    function updateTotal(multiplier, type) {
        const amt = baseTotal * multiplier;
        document.getElementById('displayTotal').innerText = '₱' + amt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('paymentTypeInput').value = type;
    }

    const cardName = document.getElementById('cardName');
    const cardNumber = document.getElementById('cardNumber');
    const cardExp = document.getElementById('cardExp');
    const cardCvc = document.getElementById('cardCvc');

    const mockName = document.getElementById('mockName');
    const mockNumber = document.getElementById('mockNumber');
    const mockExp = document.getElementById('mockExp');

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        if (!cardName.value.trim() || !cardNumber.value.trim() || !cardExp.value.trim() || !cardCvc.value.trim()) {
            e.preventDefault();
            alert('Please fill in all credit card details before proceeding.');
            return false;
        }
    });

    cardName.addEventListener('input', (e) => {
        mockName.innerText = e.target.value.toUpperCase() || 'JOHN WICK';
    });

    cardNumber.addEventListener('input', (e) => {
        let val = e.target.value.replace(/\D/g, ''); 
        let formatted = val.match(/.{1,4}/g)?.join(' ') || '';
        e.target.value = formatted;
        mockNumber.innerText = val.length > 0 ? formatted : '0000 0000 0000 0000';
    });

    cardExp.addEventListener('input', (e) => {
        let val = e.target.value.replace(/\D/g, ''); 
        if (val.length > 2) {
            val = val.substring(0, 2) + '/' + val.substring(2, 4);
        }
        e.target.value = val;
        mockExp.innerText = val || '00/00';
    });

    cardCvc.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '');
    });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>