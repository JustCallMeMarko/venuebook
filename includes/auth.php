<?php
require_once __DIR__ . '/../config/app.php';

// Configuring cookies
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME, 
    'path'     => '/',
    'secure'   => true,   
    'httponly' => true,   
    'samesite' => 'Lax',  
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── Guard functions ───────────────────────────────────────────────

/**
 * Require the user to be logged in.
 * If not, redirect to login and remember where they were trying to go.
 * 
 * Usage: require_login(); at top of any protected page.
 */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        // Save the page they were trying to reach so we can send them
        // back after login instead of always dumping them on the dashboard.
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header('Location: ' . BASE_URL . '/pages/shared/login.php?redirect=' . $redirect);
        exit;
    }
}

/**
 * Require the user to have a specific role.
 * Always call require_login() before this.
 *
 * Usage: require_role('admin');
 *        require_role('organizer');
 */
function require_role(string $role): void {
    if (($_SESSION['role'] ?? '') !== $role) {
        header('Location: ' . BASE_URL . '/pages/shared/403.php');
        exit;
    }
}

/**
 * Require the user to have one of several roles.
 * Useful if admins and organizers can both access a page.
 *
 * Usage: require_any_role(['admin', 'organizer']);
 */
function require_any_role(array $roles): void {
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        header('Location: ' . BASE_URL . '/pages/shared/403.php');
        exit;
    }
}

?>