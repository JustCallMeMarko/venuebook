<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/notifications.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    $user_id = $_SESSION['user_id'];

    if ($booking_id > 0) {
        // Verify booking belongs to user
        $stmt = $conn->prepare("SELECT Booking_id FROM bookings WHERE Booking_id = ? AND User_id = ?");
        $stmt->execute([$booking_id, $user_id]);
        if ($stmt->fetch()) {
            // Cancel booking
            $update = $conn->prepare("UPDATE bookings SET Booking_status = 'cancelled' WHERE Booking_id = ?");
            $update->execute([$booking_id]);
            
            // Delete associated contract
            $del = $conn->prepare("DELETE FROM contracts WHERE bookingid = ?");
            $del->execute([$booking_id]);
            
            // Notify client of cancellation
            create_cancellation_notification($conn, $booking_id);
            
            // Notify admins of cancellation
            notify_admins_booking_cancelled($conn, $booking_id);
        }
    }
}

header('Location: ../client/booking.php');
exit;
