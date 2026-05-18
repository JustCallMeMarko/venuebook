<?php
/**
 * Notification Helper Functions
 * This file provides utilities for creating and managing notifications
 */

/**
 * Create a notification for a user
 * @param PDO $conn - Database connection
 * @param int $user_id - The user ID to notify
 * @param string $message - The notification message
 * @return bool - Success or failure
 */
function create_notification($conn, $user_id, $message) {
    try {
        $stmt = $conn->prepare("INSERT INTO notifications (User_id, Message, Is_read, Created_at) VALUES (?, ?, 0, NOW())");
        return $stmt->execute([$user_id, $message]);
    } catch (PDOException $e) {
        error_log("Notification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a booking approval/rejection notification
 * @param PDO $conn - Database connection
 * @param int $booking_id - The booking ID
 * @param string $status - 'approved' or 'rejected'
 * @return bool - Success or failure
 */
function create_booking_status_notification($conn, $booking_id, $status) {
    try {
        $bk_stmt = $conn->prepare("SELECT b.User_id, v.Name as Venue_name FROM bookings b LEFT JOIN venue v ON b.Venue_id = v.Venue_id WHERE b.Booking_id = ?");
        $bk_stmt->execute([$booking_id]);
        $bk_info = $bk_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($bk_info) {
            $venue_name = htmlspecialchars($bk_info['Venue_name'] ?? 'Your Venue');
            $status_text = $status === 'confirmed' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'updated');
            $msg = "Your booking for " . $venue_name . " has been " . $status_text . "!";
            
            return create_notification($conn, $bk_info['User_id'], $msg);
        }
        return false;
    } catch (PDOException $e) {
        error_log("Booking Status Notification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a payment notification
 * @param PDO $conn - Database connection
 * @param int $user_id - The user ID
 * @param float $amount - The payment amount
 * @param string $event_name - The event/venue name
 * @return bool - Success or failure
 */
function create_payment_notification($conn, $user_id, $amount, $event_name) {
    $msg = "Payment of ₱" . number_format($amount, 2) . " received for " . htmlspecialchars($event_name) . ". Thank you!";
    return create_notification($conn, $user_id, $msg);
}

/**
 * Create a booking cancellation notification
 * @param PDO $conn - Database connection
 * @param int $booking_id - The booking ID
 * @return bool - Success or failure
 */
function create_cancellation_notification($conn, $booking_id) {
    try {
        $bk_stmt = $conn->prepare("SELECT b.User_id, v.Name as Venue_name FROM bookings b LEFT JOIN venue v ON b.Venue_id = v.Venue_id WHERE b.Booking_id = ?");
        $bk_stmt->execute([$booking_id]);
        $bk_info = $bk_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($bk_info) {
            $venue_name = htmlspecialchars($bk_info['Venue_name'] ?? 'Your Venue');
            $msg = "Your booking for " . $venue_name . " has been cancelled.";
            
            return create_notification($conn, $bk_info['User_id'], $msg);
        }
        return false;
    } catch (PDOException $e) {
        error_log("Cancellation Notification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Mark all notifications as read for a user
 * @param PDO $conn - Database connection
 * @param int $user_id - The user ID
 * @return bool - Success or failure
 */
function mark_all_notifications_read($conn, $user_id) {
    try {
        $stmt = $conn->prepare("UPDATE notifications SET Is_read = 1 WHERE User_id = ? AND Is_read = 0");
        return $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log("Mark Read Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get unread notification count for a user
 * @param PDO $conn - Database connection
 * @param int $user_id - The user ID
 * @return int - Count of unread notifications
 */
function get_unread_notification_count($conn, $user_id) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE User_id = ? AND Is_read = 0");
        $stmt->execute([$user_id]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Get Unread Count Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get all admin users to send notifications to
 * @param PDO $conn - Database connection
 * @return array - Array of admin user IDs
 */
function get_admin_users($conn) {
    try {
        $stmt = $conn->prepare("SELECT User_id FROM users WHERE Role = 'admin'");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $admins ?? [];
    } catch (PDOException $e) {
        error_log("Get Admin Users Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Create a notification for all admins
 * @param PDO $conn - Database connection
 * @param string $message - The notification message
 * @return bool - Success or failure
 */
function notify_all_admins($conn, $message) {
    $admins = get_admin_users($conn);
    $success = true;
    
    foreach ($admins as $admin_id) {
        if (!create_notification($conn, $admin_id, $message)) {
            $success = false;
        }
    }
    
    return $success;
}

/**
 * Notify admins of a new booking
 * @param PDO $conn - Database connection
 * @param int $booking_id - The booking ID
 * @return bool - Success or failure
 */
function notify_admins_new_booking($conn, $booking_id) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                b.Booking_id,
                u.First_name,
                u.Last_name,
                v.Name as Venue_name,
                b.Event_date,
                b.Guest_count,
                b.Total_price
            FROM bookings b
            LEFT JOIN users u ON b.User_id = u.User_id
            LEFT JOIN venue v ON b.Venue_id = v.Venue_id
            WHERE b.Booking_id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking) {
            $client_name = htmlspecialchars(($booking['First_name'] ?? '') . ' ' . ($booking['Last_name'] ?? ''));
            $venue_name = htmlspecialchars($booking['Venue_name'] ?? 'Unknown Venue');
            $event_date = date('M j, Y', strtotime($booking['Event_date']));
            $msg = "New booking #" . $booking['Booking_id'] . " from " . $client_name . " for " . $venue_name . " on " . $event_date . ". ₱" . number_format($booking['Total_price'], 2) . " — Pending approval.";
            
            return notify_all_admins($conn, $msg);
        }
        return false;
    } catch (PDOException $e) {
        error_log("Notify Admin New Booking Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Notify admins of a payment received
 * @param PDO $conn - Database connection
 * @param int $booking_id - The booking ID
 * @param float $amount - The payment amount
 * @return bool - Success or failure
 */
function notify_admins_payment_received($conn, $booking_id, $amount) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                u.First_name,
                u.Last_name,
                v.Name as Venue_name
            FROM bookings b
            LEFT JOIN users u ON b.User_id = u.User_id
            LEFT JOIN venue v ON b.Venue_id = v.Venue_id
            WHERE b.Booking_id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking) {
            $client_name = htmlspecialchars(($booking['First_name'] ?? '') . ' ' . ($booking['Last_name'] ?? ''));
            $venue_name = htmlspecialchars($booking['Venue_name'] ?? 'Unknown Venue');
            $msg = "Payment of ₱" . number_format($amount, 2) . " received from " . $client_name . " for " . $venue_name . " (Booking #" . $booking_id . ").";
            
            return notify_all_admins($conn, $msg);
        }
        return false;
    } catch (PDOException $e) {
        error_log("Notify Admin Payment Received Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Notify admins of a booking cancellation
 * @param PDO $conn - Database connection
 * @param int $booking_id - The booking ID
 * @return bool - Success or failure
 */
function notify_admins_booking_cancelled($conn, $booking_id) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                u.First_name,
                u.Last_name,
                v.Name as Venue_name
            FROM bookings b
            LEFT JOIN users u ON b.User_id = u.User_id
            LEFT JOIN venue v ON b.Venue_id = v.Venue_id
            WHERE b.Booking_id = ?
        ");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking) {
            $client_name = htmlspecialchars(($booking['First_name'] ?? '') . ' ' . ($booking['Last_name'] ?? ''));
            $venue_name = htmlspecialchars($booking['Venue_name'] ?? 'Unknown Venue');
            $msg = "Booking #" . $booking_id . " from " . $client_name . " for " . $venue_name . " has been cancelled.";
            
            return notify_all_admins($conn, $msg);
        }
        return false;
    } catch (PDOException $e) {
        error_log("Notify Admin Cancellation Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Notify admins of pending approvals
 * @param PDO $conn - Database connection
 * @return int - Number of pending bookings
 */
function notify_admins_pending_approvals($conn) {
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE Booking_status = 'pending'");
        $stmt->execute();
        $pending_count = (int)$stmt->fetchColumn();
        
        if ($pending_count > 0) {
            $msg = "You have " . $pending_count . " pending booking(s) awaiting approval.";
            notify_all_admins($conn, $msg);
        }
        
        return $pending_count;
    } catch (PDOException $e) {
        error_log("Notify Admin Pending Approvals Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Notify admins of overdue payments
 * @param PDO $conn - Database connection
 * @return int - Number of overdue bookings
 */
function notify_admins_overdue_payments($conn) {
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM bookings 
            WHERE Booking_status = 'pending' 
            AND Payment_deadline < NOW()
        ");
        $stmt->execute();
        $overdue_count = (int)$stmt->fetchColumn();
        
        if ($overdue_count > 0) {
            $msg = "Alert: " . $overdue_count . " booking(s) have overdue payment deadline(s).";
            notify_all_admins($conn, $msg);
        }
        
        return $overdue_count;
    } catch (PDOException $e) {
        error_log("Notify Admin Overdue Payments Error: " . $e->getMessage());
        return 0;
    }
}
?>
