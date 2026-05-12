<?php
require_once __DIR__ . '/includes/auth.php';  
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . (is_admin() ? '/admin/index.php' : '/client/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare('SELECT User_id, First_name, Last_name, Role, Password_hash FROM users WHERE Email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['Password_hash'])) {

            session_regenerate_id(true);

            $_SESSION['user_id']    = $user['User_id'];
            $_SESSION['first_name'] = $user['First_name'];
            $_SESSION['last_name']  = $user['Last_name'];
            $_SESSION['role']       = $user['Role']; 

            if ($user['Role'] === ROLE_ADMIN) {
                header('Location: ' . BASE_URL . '/admin/index.php');
            } else {
                header('Location: ' . BASE_URL . '/client/index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VenueBook | Login</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="icon" href="<?= BASE_URL ?>/favicon.ico" type="image/x-icon">
    <style>
        .alert-error {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 0.9rem;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }
    </style>
</head>

<body class="bgs-primary">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="bgs-background p-4 rounded-2 col-11 col-sm-10 col-md-8 col-lg-6 col-xl-4 col-xxl-3">

            <h2 class="font-open text-white m-0 h4">Welcome Back</h2>
            <p class="font-open text-white opacity-75 fs-6">Sign in to continue</p>

            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <!-- Carry redirect destination through POST -->
                <input type="hidden" name="redirect" value="<?= $redirect ?>">

                <div class="mb-2">
                    <label for="email" class="form-label font-open text-white mb-2 fw-semibold opacity-75" style="font-size:12px;">Email address</label>
                    <input type="email" class="form-control bg-transparent text-white border-light"
                        style="--bs-border-opacity:.5;" id="email" name="email"
                        placeholder="juan.delacruz@example.com" required autofocus>
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label font-open text-white mb-2 fw-semibold opacity-75" style="font-size:12px;">Password</label>
                    <input type="password" class="form-control bg-transparent text-white border-light"
                        style="--bs-border-opacity:.5;" id="password" name="password"
                        placeholder="********" required>
                </div>

                <a href="#" class="text-decoration-none d-block text-end fw-semibold" style="font-size:12px;color:#79A6CC;">Forgot Password?</a>

                <button type="submit" class="btn btn-light w-100 mt-4">Sign In</button>
            </form>

            <p class="font-open text-white text-center mt-2 mb-0 fw-semibold opacity-75" style="font-size:12px;">
                Don't have an account?
                <a href="register.php" class="text-decoration-none" style="color:#79A6CC;">Register</a>
            </p>
        </div>
    </div>
</body>

</html>