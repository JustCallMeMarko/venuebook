<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/notifications.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../client/Venue.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$venue_id = isset($_POST['venue_id']) ? (int)$_POST['venue_id'] : 0;
$package_id = !empty($_POST['package_id']) ? (int)$_POST['package_id'] : null;
$guest_count = isset($_POST['guest_count']) ? (int)$_POST['guest_count'] : 0;
$event_date = isset($_POST['event_date']) ? $_POST['event_date'] : '';
$payment_type = isset($_POST['payment_type']) && $_POST['payment_type'] === 'downpayment' ? 'downpayment' : 'full';

if ($venue_id <= 0 || empty($event_date)) {
    $_SESSION['error'] = "Invalid booking details.";
    header('Location: ../client/Venue.php');
    exit;
}

// 1. Recalculate totals from database securely
$stmt = $conn->prepare('SELECT Price_per_day FROM venue WHERE Venue_id = ?');
$stmt->execute([$venue_id]);
$venue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venue) {
    $_SESSION['error'] = "Venue not found.";
    header('Location: ../client/Venue.php');
    exit;
}

$package_price = 0;
if ($package_id) {
    $stmt = $conn->prepare('SELECT Price FROM packages WHERE Package_id = ? AND Venue_id = ?');
    $stmt->execute([$package_id, $venue_id]);
    $pkg = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($pkg) {
        $package_price = (float)$pkg['Price'];
    } else {
        $package_id = null; // invalid package for this venue
    }
}

$venue_price = (float)$venue['Price_per_day'];
$subtotal = $venue_price + $package_price;
$service_fee = 250.00;
$total_price = $subtotal + $service_fee;

// 2. Insert into bookings table
try {
    $status = ($payment_type === 'full') ? 'confirmed' : 'pending';
    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (User_id, Venue_id, Package_id, Event_date, Guest_count, Total_price, Payment_deadline, Booking_status) 
        VALUES (?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 7 DAY), ?)
    ");
    
    $stmt->execute([
        $user_id,
        $venue_id,
        $package_id,
        $event_date,
        $guest_count,
        $total_price,
        $status
    ]);
    $booking_id = $conn->lastInsertId();

    // 3. Insert into payments table
    $amount_to_pay = ($payment_type === 'downpayment') ? $total_price / 2 : $total_price;
    $stmt = $conn->prepare("
        INSERT INTO payments (Booking_id, User_id, Amount, Type, Status, Paid_at) 
        VALUES (?, ?, ?, ?, 'completed', NOW())
    ");
    $stmt->execute([$booking_id, $user_id, $amount_to_pay, $payment_type]);

    // Get venue name for notification
    $venue_stmt = $conn->prepare("SELECT Name FROM venue WHERE Venue_id = ?");
    $venue_stmt->execute([$venue_id]);
    $venue_data = $venue_stmt->fetch(PDO::FETCH_ASSOC);
    $venue_name = $venue_data['Name'] ?? 'Your Event';
    
    // Notify client of payment
    create_payment_notification($conn, $user_id, $amount_to_pay, $venue_name);
    
    // Notify admins of new booking
    notify_admins_new_booking($conn, $booking_id);
    
    // Notify admins of payment received
    notify_admins_payment_received($conn, $booking_id, $amount_to_pay);

    // 4. Insert into contracts table
    $stmt = $conn->prepare("INSERT INTO contracts (userid, bookingid) VALUES (?, ?)");
    $stmt->execute([$user_id, $booking_id]);

    // Redirect to a success page or Bookings history
    if ($status === 'confirmed') {
        $_SESSION['success'] = "Payment successful! Your booking is now confirmed.";
    } else {
        $_SESSION['success'] = "Payment successful! Your booking is now pending until the balance is settled.";
    }
    header('Location: ../client/booking.php');
    exit;
    
} catch (PDOException $e) {
    error_log("Booking Insert Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while processing your booking. Please try again.";
    header('Location: ../client/Venue.php');
    exit;
}
