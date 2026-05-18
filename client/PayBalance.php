<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');
require_once __DIR__ . '/../config/db.php';

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$user_id = $_SESSION['user_id'] ?? null;

if ($booking_id <= 0 || !$user_id) {
    header('Location: booking.php');
    exit;
}

// Fetch booking
$stmt = $conn->prepare("SELECT b.*, v.Name as Venue_name, (SELECT COALESCE(SUM(Amount),0) FROM payments WHERE Booking_id = b.Booking_id AND Status = 'completed') as Total_paid FROM bookings b LEFT JOIN venue v ON b.Venue_id = v.Venue_id WHERE b.Booking_id = ? AND b.User_id = ?");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: booking.php');
    exit;
}

// Determine if paying full remaining balance or a downpayment
$payment_type = isset($_GET['payment_type']) ? $_GET['payment_type'] : '';
$total_price = (float)$booking['Total_price'];
$total_paid = (float)$booking['Total_paid'];

if ($payment_type === 'downpayment') {
    $target_amount = $total_price * 0.5;
} else {
    $target_amount = $total_price;
}

$balance = $target_amount - $total_paid;
if ($balance <= 0) {
    $_SESSION['error'] = 'No outstanding balance to pay.';
    header('Location: booking.php');
    exit;
}

include __DIR__ . '/../config/nav.php';
include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root { --vb-accent-brown: #C19A6B; --vb-dark-panel: #17181C; }
    .steps-container { max-width: 650px; margin: 0 auto 2rem; position: relative; display: flex; justify-content: space-between; align-items: center; }
    .step-line { position: absolute; height: 2px; background: #e2e2e1; width: 100%; top: 20px; z-index: 1; }
    .step-unit { position: relative; z-index: 2; text-align: center; background: var(--primary-color); padding: 0 15px; }
    .step-circle { width: 40px; height: 40px; background: #e2e2e1; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #888; margin: 0 auto 8px; }
    .step-unit.active .step-circle { background: #0c182a; color: #fff; }
    .credit-card { max-width: 380px; background: #3C4245; border-radius: 15px; padding: 25px; color: white; aspect-ratio: 1.58 / 1; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .card-numbers { font-family: 'Space Mono', monospace; font-size: 1.25rem; letter-spacing: 3px; display: flex; justify-content: space-between; }
    .custom-form-group { max-width: 380px; border: 1px solid #DEE2E6; border-radius: 8px; background: white; overflow: hidden; }
    .custom-input-row { display: flex; align-items: center; border-bottom: 1px solid #F0F0F0; }
    .custom-input-row:last-child { border-bottom: none; }
    .input-icon { width: 50px; text-align: center; color: #CCC; }
    .custom-form-group input { border: none; padding: 15px 0; font-size: 14px; width: 100%; outline: none; }
    .summary-panel { background-color: var(--vb-dark-panel); color: #A0A0A0; padding: 30px 25px; display: flex; flex-direction: column; border-radius: 12px; position: sticky; top: 20px; }
    .total-amount { font-size: 2.5rem; color: white; font-weight: 500; margin-bottom: 25px; }
    .btn-pay { background: #FAF9F7; border: none; padding: 15px; border-radius: 6px; font-weight: 600; color: #333; width: 100%; transition: 0.3s; }
</style>

<div class="container-fluid">
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="row g-5">
        <div class="col-12 col-xl-7">
            <h2 class="fw-semibold mb-4"><?php echo $payment_type === 'downpayment' ? 'Pay Downpayment' : 'Pay Remaining Balance'; ?></h2>

            <div class="d-flex gap-2 mb-5">
                <button class="btn btn-outline-dark px-4 active">Card</button>
                <button class="btn btn-outline-secondary px-4" disabled>Cash</button>
                <button class="btn btn-outline-secondary px-4" disabled>PayPal</button>
            </div>

            <h5 class="mb-3 small fw-bold text-uppercase opacity-75">Card details</h5>
            <div class="credit-card mb-4">
                <div class="d-flex justify-content-between">
                    <div style="width: 45px; height: 32px; background: #E6AE6A; border-radius: 4px;"></div>
                    <i class="bi bi-wifi text-white fs-4"></i>
                </div>
                <div class="card-numbers my-3" id="mockNumber">0000 0000 0000 0000</div>
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <div class="small text-uppercase" id="mockName">CARDHOLDER</div>
                        <div class="smaller opacity-75" id="mockExp">00/00</div>
                    </div>
                    <div class="d-flex">
                        <div style="width: 24px; height: 24px; background: #EB001B; border-radius: 50%; margin-right: -10px;"></div>
                        <div style="width: 24px; height: 24px; background: #F79E1B; border-radius: 50%; opacity: 0.8;"></div>
                    </div>
                </div>
            </div>

            <div class="custom-form-group shadow-sm mb-4">
                <div class="custom-input-row">
                    <div class="input-icon"><i class="bi bi-person"></i></div>
                    <input type="text" id="cardName" name="card_name" form="payBalanceForm" placeholder="Cardholder name">
                </div>
                <div class="custom-input-row">
                    <div class="input-icon"><i class="bi bi-credit-card"></i></div>
                    <input type="text" id="cardNumber" name="card_number" form="payBalanceForm" maxlength="19" placeholder="1234 1234 1234 1234">
                </div>
                <div class="d-flex">
                    <div class="flex-fill border-end custom-input-row">
                        <input type="text" id="cardExp" name="card_exp" form="payBalanceForm" placeholder="MM/YY" maxlength="5" class="ps-4">
                    </div>
                    <div class="flex-fill custom-input-row">
                        <input type="text" id="cardCvc" name="card_cvc" form="payBalanceForm" placeholder="CVC" maxlength="4" class="ps-4">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <aside class="summary-panel shadow-lg">
                <h3 class="mb-5">Payment Summary</h3>
                <div class="d-flex justify-content-between mb-3">
                    <span>Booking</span>
                    <span class="text-white fw-medium">#<?= htmlspecialchars($booking['Booking_id']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Venue</span>
                    <span class="text-white fw-medium"><?= htmlspecialchars($booking['Venue_name']) ?></span>
                </div>
                <hr class="border-secondary my-4 opacity-25">
                <div class="d-flex justify-content-between mb-3">
                    <span>Amount Due</span>
                    <span class="text-white fw-medium">₱<?= number_format($balance, 2) ?></span>
                </div>
                <div class="mt-auto">
                    <div class="small fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">Amount to Pay</div>
                    <div class="total-amount font-mono" id="displayTotal">₱<?= number_format($balance, 2) ?></div>
                    <form action="../actions/pay_balance.php" method="POST" id="payBalanceForm">
                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['Booking_id']) ?>">
                        <button type="submit" class="btn-pay shadow-sm">Pay now</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</div>

<script>
    const baseTotal = <?= $balance ?>;
    const cardName = document.getElementById('cardName');
    const cardNumber = document.getElementById('cardNumber');
    const cardExp = document.getElementById('cardExp');
    const cardCvc = document.getElementById('cardCvc');

    const mockName = document.getElementById('mockName');
    const mockNumber = document.getElementById('mockNumber');
    const mockExp = document.getElementById('mockExp');

    document.getElementById('payBalanceForm').addEventListener('submit', function(e) {
        if (!cardName.value.trim() || !cardNumber.value.trim() || !cardExp.value.trim() || !cardCvc.value.trim()) {
            e.preventDefault();
            alert('Please fill in all credit card details before proceeding.');
            return false;
        }
    });

    cardName.addEventListener('input', (e) => { mockName.innerText = e.target.value.toUpperCase() || 'CARDHOLDER'; });
    cardNumber.addEventListener('input', (e) => { let val = e.target.value.replace(/\D/g, ''); let formatted = val.match(/.{1,4}/g)?.join(' ') || ''; e.target.value = formatted; mockNumber.innerText = formatted || '0000 0000 0000 0000'; });
    cardExp.addEventListener('input', (e) => { let val = e.target.value.replace(/\D/g, ''); if (val.length > 2) { val = val.substring(0,2) + '/' + val.substring(2,4); } e.target.value = val; mockExp.innerText = val || '00/00'; });
    cardCvc.addEventListener('input', (e) => { e.target.value = e.target.value.replace(/\D/g, ''); });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>
