<?php
// includes/auth.php

require_once __DIR__ . '/../config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        // ✅ false during local development (HTTP).
        // Change to true when deployed on HTTPS.
        'secure'   => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ─── Guard functions ───────────────────────────────────────────────

/**
 * Redirect to login if not logged in.
 * Saves the current URL so the user lands back here after login.
 */
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        $redirect = urlencode($_SERVER['REQUEST_URI']);
        header('Location: ' . BASE_URL . '/login.php?redirect=' . $redirect);
        exit;
    }
}

/**
 * Require a specific role. Calls require_login() automatically.
 * Usage: require_role('admin') or require_role('client')
 */
function require_role(string $role): void {
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) {
        // ✅ Consistent path — pick one and use it everywhere
        header('Location: ' . BASE_URL . '/shared/403.php');
        exit;
    }
}

/**
 * Require one of several roles. Calls require_login() automatically.
 * Usage: require_any_role(['admin', 'client'])
 */
function require_any_role(array $roles): void {
    require_login();
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        header('Location: ' . BASE_URL . '/shared/403.php');
        exit;
    }
}

// ─── Helper checks (return bool, never redirect) ───────────────────

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool {
    return ($_SESSION['role'] ?? '') === ROLE_ADMIN;
}

function is_client(): bool {
    return ($_SESSION['role'] ?? '') === ROLE_CLIENT;
}

function get_currnt_user(): array {
    return [
        'user_id'    => $_SESSION['user_id']    ?? null,
        'role'       => $_SESSION['role']        ?? null,
        'first_name' => $_SESSION['first_name']  ?? '',
        'last_name'  => $_SESSION['last_name']   ?? '',
    ];
}
?>