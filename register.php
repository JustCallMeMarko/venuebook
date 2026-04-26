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
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <style>
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
            opacity: 1;
        }
    </style>
</head>

<body class="bgs-primary">
    <div class="bgs-primary d-flex align-items-center justify-content-center vh-100">
        <div class="bgs-background p-4 rounded-2 col-11 col-sm-10 col-md-8 col-lg-6 col-xl-4 col-xxl-3">
            <h2 class="font-open text-white m-0 h4">Create your account</h2>
            <p class="font-open text-white opacity-75 fs-6">Get started — it's free to register</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <p class="font-open text-white mt-2 mb-2 fw-semibold opacity-75" style="font-size: 12px;">I AM A</p>
            <div class="row">
                <div class="col-6 mb-3">
                    <div style="cursor: pointer; --bs-border-opacity: .5;" class="role-card d-flex p-3 flex-column align-items-center border border-primary bg-primary bg-opacity-25 rounded h-100" data-role="Event organizer" onclick="selectRole(this)">
                        <span>📁</span>
                        <span class="font-open text-center fw-medium text-light mt-2" style="font-size: 12px;">Event organizer</span>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div style="cursor: pointer; --bs-border-opacity: .5;" class="role-card d-flex p-3 flex-column align-items-center border rounded h-100" data-role="Venue coordinator" onclick="selectRole(this)">
                        <span>🏢</span>
                        <span class="font-open text-center fw-medium text-light mt-2" style="font-size: 12px;">Venue coordinator</span>
                    </div>
                </div>
            </div>

            <form action="register.php" method="POST">
                <input type="hidden" name="role" id="role_input" value="Event organizer">
                <div class="row mb-2">
                    <div class="col-6">
                        <label for="exampleInputFirstName" class="form-label font-open text-white mb-2 fw-semibold opacity-75" style="font-size: 12px;">First Name</label>
                        <input type="text" class="form-control bg-transparent text-white border-light" style="--bs-border-opacity: .5;" id="exampleInputFirstName" name="first_name" placeholder="Juan" required>
                    </div>
                    <div class="col-6">
                        <label for="exampleInputLastName" class="form-label font-open text-white mb-2 fw-semibold opacity-75" style="font-size: 12px;">Last Name</label>
                        <input type="text" class="form-control bg-transparent text-white border-light" style="--bs-border-opacity: .5;" id="exampleInputLastName" name="last_name" placeholder="Dela Cruz" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="exampleInputEmail" class="form-label font-open text-white mb-2 fw-semibold opacity-75" style="font-size: 12px;">Email address</label>
                    <input type="email" class="form-control bg-transparent text-white border-light" style="--bs-border-opacity: .5;" id="exampleInputEmail" name="email" placeholder="juan.delacruz@example.com" required>
                </div>
                <div class="mb-2">
                    <label for="exampleInputPhone" class="form-label font-open text-white mb-2 fw-semibold opacity-75" style="font-size: 12px;">Phone Number</label>
                    <input type="tel" class="form-control bg-transparent text-white border-light" style="--bs-border-opacity: .5;" id="exampleInputPhone" name="phone_num" placeholder="123-456-7890" required>
                </div>
                <div class="mb-2">
                    <label for="exampleInputPassword1" class="form-label font-open text-white mb-2 fw-semibold opacity-75" style="font-size: 12px;">Password</label>
                    <input type="password" class="form-control bg-transparent text-white border-light" style="--bs-border-opacity: .5;" id="exampleInputPassword1" name="password" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-light w-100 mt-4">Submit</button>
            </form>

            <p class="font-open text-white text-center mt-2 mb-0 fw-semibold opacity-75" style="font-size: 12px;">Already have an account? <a href="login.php" class="text-decoration-none" style="color: #79A6CC;">Login</a></p>
        </div>
    </div>

    <script>
        function selectRole(element) {
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-25');
                card.classList.add('border', 'border-light');
            });
            element.classList.remove('border-light');
            element.classList.add('border', 'border-primary', 'bg-primary', 'bg-opacity-25');
            // Update the hidden input field based on the selected role
            document.getElementById('role_input').value = element.getAttribute('data-role');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const selected = document.querySelector('.role-card[data-role="Event organizer"]');
            if (selected) selectRole(selected);
        });
    </script>

</body>

</html>