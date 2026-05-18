<?php
/**
 * Database Setup for Notification System
 * This file contains the SQL statements needed to set up the notifications table
 * 
 * Run this ONCE to initialize the notifications table
 * You can also manually run the SQL in your database admin tool
 */

require_once __DIR__ . '/db.php';

try {
    // Create notifications table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(User_id) ON DELETE CASCADE,
        INDEX idx_user_read (user_id, is_read),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $conn->exec($sql);
    
    // Log success
    error_log("Notification table setup completed successfully");
    
} catch (PDOException $e) {
    error_log("Error setting up notifications table: " . $e->getMessage());
    throw $e;
}
?>
