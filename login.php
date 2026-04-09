<?php
session_start();
require_once __DIR__ . '/config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT User_id, First_name, Last_name, Role, Password_hash FROM USERS WHERE Email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['User_id'];
            $_SESSION['first_name'] = $user['First_name'];
            $_SESSION['last_name'] = $user['Last_name'];
            $_SESSION['role'] = $user['Role'];
            
            // Redirect based on role or to dashboard
            header("Location: index.php"); 
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VenueBook Login</title>
    <link rel="icon" type="image/png" href="logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 0.9rem; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <img src="assets/images/Logo.svg" alt="Logo" class="logo-img">
            <span class="logo-text">VENUEBOOK</span>
        </div>
    </header>

    <main class="login-container">
        <div class="login-card">
            <h2>Welcome Back</h2>
            <p class="subtitle">Sign in to your account to continue</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@company.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>

                <div class="form-footer">
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="sign-in-btn">Login</button>
            </form>

            <p class="signup-text">Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </main>

</body>
</html>
