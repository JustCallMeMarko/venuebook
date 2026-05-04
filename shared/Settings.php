<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/nav.php';

// User ID variable - use session if available, otherwise fallback to 13
$current_user_id = $_SESSION['user_id'] ?? 13;

// Configure navigation items for the sidebar
$role = $_SESSION['role'] ?? 'Customer';
$nav_key = ($role === 'Admin' || $role === 'Venue Owner') ? 'admin' : 'organizer';
$nav_items = $nav_config[$nav_key] ?? [];
$active_nav = 'Settings';
$page_title = 'Settings';

$success_msg = '';
$error_msg = '';

// Handle Save Changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $company = $_POST['company'] ?? '';
    $new_password = $_POST['password'] ?? '';

    try {
        // Fetch current data to handle empty inputs (if using placeholders)
        $stmt = $conn->prepare("SELECT * FROM USERS WHERE User_id = ?");
        $stmt->execute([$current_user_id]);
        $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update fields (if empty, keep old values)
        $first_name = !empty($first_name) ? $first_name : $current_user['First_name'];
        $last_name = !empty($last_name) ? $last_name : $current_user['Last_name'];
        $email = !empty($email) ? $email : $current_user['Email'];
        $phone = !empty($phone) ? $phone : $current_user['Phone_num'];
        $company = !empty($company) ? $company : $current_user['Company_name'];

        if (!empty($new_password)) {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE USERS SET First_name = ?, Last_name = ?, Email = ?, Phone_num = ?, Company_name = ?, Password_hash = ? WHERE User_id = ?");
            $stmt->execute([$first_name, $last_name, $email, $phone, $company, $password_hash, $current_user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE USERS SET First_name = ?, Last_name = ?, Email = ?, Phone_num = ?, Company_name = ? WHERE User_id = ?");
            $stmt->execute([$first_name, $last_name, $email, $phone, $company, $current_user_id]);
        }
        
        // Update session variables so the sidebar reflects changes immediately
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        
        $success_msg = "Changes saved successfully!";
    } catch (PDOException $e) {
        $error_msg = "Error updating profile: " . $e->getMessage();
    }
}

// Fetch all user info for placeholders (excluding password and profile picture)
try {
    $stmt = $conn->prepare("SELECT First_name, Last_name, Email, Phone_num, Role, Created_at, Company_name, Language FROM USERS WHERE User_id = ?");
    $stmt->execute([$current_user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Error fetching data: " . $e->getMessage();
}

require_once __DIR__ . '/../includes/top_sidebar.php'; 
?>

<div class="col-12 col-lg-9 p-1 mx-auto">
    <h3 class="font-open">Settings</h3>
    <p class="font-open" style="font-size: 14px;">Customize your profile, personal information, language, and theme settings.</p>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form class="p-4 rounded-3 border border-dark-subtle" method="POST">
        <section class="mb-4">
            <h4 class="font-open">Profile</h4>
            <p class="font-open" style="font-size: 14px;">View and edit your personal information, including your name and profile picture.</p>
            <div class="d-flex gap-4 align-items-center">
                <img src="https://th.bing.com/th/id/OIP.WTz8r0NiRmcRWxwi4nqqWAHaJ7?o=7rm=3&rs=1&pid=ImgDetMain&o=7&rm=3" alt="Profile Picture" class="rounded-circle object-fit-cover" width="70" height="70">
                <button type="button" class="btn btn-light border border-dark-subtle" style="height: fit-content;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-upload me-2" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                    </svg>
                    Upload New Picture
                </button>
            </div>
        </section>
        <section class="mb-4">
            <h4 class="font-open">Personal Information</h4>
            <p class="font-open" style="font-size: 14px;">Manage your information details, including your name, email address, phone number, and company/organization name.</p>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="first_name" placeholder="<?php echo htmlspecialchars($user['First_name'] ?? ''); ?>">
                </div>
                <div class="col-12 col-lg-6">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="last_name" placeholder="<?php echo htmlspecialchars($user['Last_name'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>">
                </div>
                <div class="col-12 col-lg-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="<?php echo htmlspecialchars($user['Phone_num'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="company" class="form-label">Company/Organization Name</label>
                    <input type="text" class="form-control" id="company" name="company" placeholder="<?php echo htmlspecialchars($user['Company_name'] ?? ''); ?>">
                </div>
            </div>
        </section>
        <section class="mb-4">
            <h4 class="font-open">Account</h4>
            <p class="font-open" style="font-size: 14px;">Update your password and select your preferred language for a personalized experience.</p>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="********">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Language</label>
                    <div class="dropdown">
                        <button class="btn btn-light border border-dark-subtle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($user['Language'] ?? '🇺🇸 English (US)'); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">ph Filipino (PH)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <button type="submit" class="btn btn-success mb-4">Save All Changes</button>
        <div class="d-flex flex-column flex-lg-row gap-2">

            <button type="button" class="btn btn-dark px-4" onclick="window.location.href='/venuebook/logout.php'">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-left me-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                    <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
                </svg>
                Logout
            </button>
            <button type="button" class="btn btn-danger px-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash me-1" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                </svg>
                Delete Account
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/bottom_sidebar.php'; ?>