<?php

// ─── Helper checks (no redirect — just return true/false) ──────────

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool {
    return ($_SESSION['role'] ?? '') === 'admin';
}

function is_client(): bool {
    return ($_SESSION['role'] ?? '') === 'organizer';
}

function get_current_user(): array {
    return [
        'user_id'    => $_SESSION['user_id']    ?? null,
        'role'       => $_SESSION['role']        ?? null,
        'first_name' => $_SESSION['first_name']  ?? '',
        'last_name'  => $_SESSION['last_name']   ?? '',
    ];
}

?>