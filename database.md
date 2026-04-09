-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS venuebook;
USE venuebook;

-- 1. Create Users Table
CREATE TABLE USERS (
    User_id INT AUTO_INCREMENT PRIMARY KEY,
    First_name VARCHAR(255) NOT NULL,
    Last_name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Phone_num VARCHAR(20),
    Role ENUM('Admin', 'Venue Owner', 'Customer') NOT NULL,
    Password_hash VARCHAR(255) NOT NULL,
    Created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Create Venue Table
CREATE TABLE VENUE (
    Venue_id INT AUTO_INCREMENT PRIMARY KEY,
    User_id INT NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Description TEXT,
    Location VARCHAR(255),
    Capacity INT,
    Price_per_day DECIMAL(10, 2),
    Status ENUM('Active', 'Inactive', 'Under Maintenance'),
    FOREIGN KEY (User_id) REFERENCES USERS(User_id) ON DELETE CASCADE
);

-- 3. Create Availability Blocks Table
CREATE TABLE AVAILABILITY_BLOCKS (
    Block_id INT AUTO_INCREMENT PRIMARY KEY,
    Venue_id INT NOT NULL,
    Blocked_date DATE NOT NULL,
    Reason VARCHAR(255),
    FOREIGN KEY (Venue_id) REFERENCES VENUE(Venue_id) ON DELETE CASCADE
);

-- 4. Create Packages Table
CREATE TABLE PACKAGES (
    Package_id INT AUTO_INCREMENT PRIMARY KEY,
    Venue_id INT NOT NULL,
    Name VARCHAR(255) NOT NULL,
    Inclusions TEXT,
    Price DECIMAL(10, 2),
    Status ENUM('Available', 'Discontinued'),
    FOREIGN KEY (Venue_id) REFERENCES VENUE(Venue_id) ON DELETE CASCADE
);

-- 5. Create Bookings Table
CREATE TABLE BOOKINGS (
    Booking_id INT AUTO_INCREMENT PRIMARY KEY,
    User_id INT NOT NULL,
    Venue_id INT NOT NULL,
    Package_id INT,
    Event_date DATE NOT NULL,
    Guest_count INT,
    Total_price DECIMAL(10, 2),
    Payment_deadline DATETIME,
    Booking_status ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed'),
    Created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (User_id) REFERENCES USERS(User_id),
    FOREIGN KEY (Venue_id) REFERENCES VENUE(Venue_id),
    FOREIGN KEY (Package_id) REFERENCES PACKAGES(Package_id)
);

-- 6. Create Approvals Table
CREATE TABLE APPROVALS (
    Approval_id INT AUTO_INCREMENT PRIMARY KEY,
    Booking_id INT NOT NULL,
    User_id INT NOT NULL,
    Status ENUM('Approved', 'Rejected', 'Pending'),
    Reason TEXT,
    Decided_at DATETIME,
    FOREIGN KEY (Booking_id) REFERENCES BOOKINGS(Booking_id) ON DELETE CASCADE,
    FOREIGN KEY (User_id) REFERENCES USERS(User_id)
);

-- 7. Create Payments Table
CREATE TABLE PAYMENTS (
    Payment_id INT AUTO_INCREMENT PRIMARY KEY,
    Booking_id INT NOT NULL,
    User_id INT NOT NULL,
    Amount DECIMAL(10, 2) NOT NULL,
    Type VARCHAR(50),
    Status ENUM('Pending', 'Completed', 'Failed', 'Refunded'),
    Paid_at DATETIME,
    FOREIGN KEY (Booking_id) REFERENCES BOOKINGS(Booking_id) ON DELETE CASCADE,
    FOREIGN KEY (User_id) REFERENCES USERS(User_id)
);

-- 8. Create Notifications Table
CREATE TABLE NOTIFICATIONS (
    Notification_id INT AUTO_INCREMENT PRIMARY KEY,
    User_id INT NOT NULL,
    Booking_id INT,
    Type VARCHAR(50),
    Message TEXT,
    Is_read BOOLEAN DEFAULT FALSE,
    Created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (User_id) REFERENCES USERS(User_id) ON DELETE CASCADE,
    FOREIGN KEY (Booking_id) REFERENCES BOOKINGS(Booking_id)
);

-- 9. Create Contracts Table
CREATE TABLE CONTRACTS (
    contractid INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL,
    bookingid INT NOT NULL,
    FOREIGN KEY (userid) REFERENCES USERS(User_id),
    FOREIGN KEY (bookingid) REFERENCES BOOKINGS(Booking_id) ON DELETE CASCADE
);