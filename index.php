<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$first_name = $_SESSION['first_name'] ?? 'User';
$last_name = $_SESSION['last_name'] ?? '';
$role = $_SESSION['role'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
</head>

<body>
    <h2>Role: <?= htmlspecialchars($role) ?></h2>
    <br>
    <h2>Hi, <?= htmlspecialchars($first_name) ?> <?= htmlspecialchars($last_name) ?></h2>
    <br>
    <a href="logout.php"><button>Logout</button></a>
</body>

</html>