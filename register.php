<?php
session_start();
require_once __DIR__ . '/config/db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_num = trim($_POST['phone_num'] ?? '');
    $role_input = $_POST['role'] ?? '';
    
    // Map the selected string to the ENUM values in the USERS table
    $role = 'Customer'; // default
    if ($role_input === 'Venue coordinator') {
        $role = 'Venue Owner';
    } elseif ($role_input === 'Event organizer') {
        $role = 'Customer';
    }
    
    $password = $_POST['password'] ?? '';
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT User_id FROM USERS WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO USERS (First_name, Last_name, Email, Phone_num, Role, Password_hash) VALUES (?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$first_name, $last_name, $email, $phone_num, $role, $password_hash]);
                $success = "Registration successful! You can now login.";
            } catch (PDOException $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VenueBook | Create Account</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/register.css">
    <style>
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 0.9rem; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <img src="assets/images/Logo.svg" alt="Logo" class="logo-img">
            <span class="logo-text">VENUEBOOK</span>
        </div>
    </header>

    <div class="main-wrapper">
        <div class="signup-card">
            <h2>Create your account</h2>
            <p class="subtitle">Get started — it's free to register</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <p class="section-label">I am a</p>
            <div class="role-container">
                <div class="role-card active" data-role="Event organizer" onclick="selectRole(this)">
                    <span class="icon">📁</span>
                    <span class="role-title">Event organizer</span>
                </div>
                <div class="role-card" data-role="Venue coordinator" onclick="selectRole(this)">
                    <span class="icon">🏢</span>
                    <span class="role-title">Venue coordinator</span>
                </div>
            </div>

            <form action="register.php" method="POST">
                <input type="hidden" name="role" id="role_input" value="Event organizer">
                <div class="form-row">
                    <div class="input-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" placeholder="Juan" required>
                    </div>
                    <div class="input-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" placeholder="Cruz" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@company.com" required>
                </div>

                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_num" placeholder="09123456789">
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="********" required>
                </div>

                <div class="forgot-link-wrapper">
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="submit-btn">Create account</button>
            </form>

            <p class="bottom-text">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>

    <script>
        function selectRole(element) {
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('active');
            });
            element.classList.add('active');
            // Update the hidden input field based on the selected role
            document.getElementById('role_input').value = element.getAttribute('data-role');
        }
    </script>

</body>
</html>
