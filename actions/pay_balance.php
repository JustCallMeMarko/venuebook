<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../client/booking.php');
    exit;
}

$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
$user_id = $_SESSION['user_id'] ?? null;

if ($booking_id <= 0 || !$user_id) {
    $_SESSION['error'] = "Invalid request.";
    header('Location: ../client/booking.php');
    exit;
}

try {
    // Verify booking belongs to user and get current totals
    $stmt = $conn->prepare("SELECT b.Booking_id, b.User_id, b.Total_price, b.Booking_status, b.Venue_id,
        (SELECT COALESCE(SUM(Amount),0) FROM payments WHERE Booking_id = b.Booking_id AND Status = 'completed') as Total_paid
        FROM bookings b WHERE b.Booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking || (int)$booking['User_id'] !== (int)$user_id) {
        $_SESSION['error'] = "Booking not found or access denied.";
        header('Location: ../client/booking.php');
        exit;
    }

    $balance = (float)$booking['Total_price'] - (float)$booking['Total_paid'];
    if ($balance <= 0) {
        $_SESSION['error'] = "No outstanding balance.";
        header('Location: ../client/booking.php');
        exit;
    }

    // Basic validation for posted card fields (mock processing)
    $card_name = trim($_POST['card_name'] ?? '');
    $card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
    $card_exp = trim($_POST['card_exp'] ?? '');
    $card_cvc = trim($_POST['card_cvc'] ?? '');

    if (!$card_name || !$card_number || !$card_exp || !$card_cvc) {
        $_SESSION['error'] = 'Please fill in all payment details.';
        header('Location: ../client/PayBalance.php?booking_id=' . $booking_id);
        exit;
    }

    // Minimal validation: ensure the inputs have expected character types
    if ($card_number === '' || !ctype_digit($card_number)) {
        $_SESSION['error'] = 'Card number must contain only digits.';
        header('Location: ../client/PayBalance.php?booking_id=' . $booking_id);
        exit;
    }

    if ($card_exp === '' || !preg_match('/^[0-9\/]+$/', $card_exp)) {
        $_SESSION['error'] = 'Expiry must contain only digits and "/".';
        header('Location: ../client/PayBalance.php?booking_id=' . $booking_id);
        exit;
    }

    if ($card_cvc === '' || !ctype_digit($card_cvc)) {
        $_SESSION['error'] = 'CVC must contain only digits.';
        header('Location: ../client/PayBalance.php?booking_id=' . $booking_id);
        exit;
    }

    // NOTE: This is a mock payment processing step. Replace with gateway integration.
    // Insert payment record for remaining balance
    $pay_stmt = $conn->prepare("INSERT INTO payments (Booking_id, User_id, Amount, Type, Status, Paid_at) VALUES (?, ?, ?, 'final', 'completed', NOW())");
    $pay_stmt->execute([$booking_id, $user_id, $balance]);

    // Update booking status to confirmed
    $upd = $conn->prepare("UPDATE bookings SET Booking_status = 'confirmed' WHERE Booking_id = ?");
    $upd->execute([$booking_id]);

    // Notify client and admins
    $venue_stmt = $conn->prepare("SELECT Name FROM venue WHERE Venue_id = ?");
    $venue_stmt->execute([$booking['Venue_id']]);
    $venue = $venue_stmt->fetch(PDO::FETCH_ASSOC);
    $venue_name = $venue['Name'] ?? 'Your Event';

    create_payment_notification($conn, $user_id, $balance, $venue_name);
    notify_admins_payment_received($conn, $booking_id, $balance);

    $_SESSION['success'] = "Payment successful. Your booking is now confirmed.";
    header('Location: ../client/booking.php');
    exit;

} catch (PDOException $e) {
    error_log('Pay Balance Error: ' . $e->getMessage());
    $_SESSION['error'] = "An error occurred while processing your payment.";
    header('Location: ../client/booking.php');
    exit;
}
?>