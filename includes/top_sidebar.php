<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/nav.php';
require_once __DIR__ . '/../config/notifications.php';

$unread_notifications = 0;
if (isset($_SESSION['user_id'])) {
    global $conn;
    if (isset($conn)) {
        $unread_notifications = get_unread_notification_count($conn, $_SESSION['user_id']);
    }
}

$page_title   = $page_title   ?? 'VenueBook';
$active_nav   = $active_nav   ?? '';
$nav_items  = $nav_config[$_SESSION['role']] ?? [];
$settings_url = '/venuebook/shared/Settings.php';
$notif_url = '/venuebook/client/notifications.php';

$profile_image = $_SESSION['profile_image'] ?? '';
if (empty($profile_image) && isset($_SESSION['user_id']) && isset($conn)) {
    try {
        $profileStmt = $conn->prepare('SELECT profile_image FROM users WHERE User_id = ?');
        $profileStmt->execute([$_SESSION['user_id']]);
        $profile_image = (string) ($profileStmt->fetchColumn() ?: '');
        if (!empty($profile_image)) {
            $_SESSION['profile_image'] = $profile_image;
        }
    } catch (Throwable $e) {
        $profile_image = '';
    }
}

$profile_avatar = !empty($profile_image) ? $profile_image : '/venuebook/assets/images/person.svg';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/venuebook/assets/css/global.css">
    <link rel="stylesheet" href="/venuebook/assets/css/venuebook.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/venuebook/favicon.ico" type="image/x-icon">
    <title><?= htmlspecialchars($page_title) ?> — VenueBook</title>
    <style>
        /* ── Mobile ── */
        @media (max-width: 991.98px) {
            .custom-sidebar {
                position: sticky;
                top: 0;
                z-index: 1030;
            }

            /*
             * THE FIX: Bootstrap animates height from 0px → Xpx during .collapsing.
             * We only need to speed up that one transition.
             * Do NOT touch height:auto — it breaks the calculation Bootstrap needs.
             */
            #mobileNav.collapsing {
                transition: height 0.2s ease !important;
            }
        }

        /* ── Desktop ── */
        @media (min-width: 992px) {
            .custom-sidebar {
                height: 100vh;
                position: sticky;
                top: 0;
                overflow-y: auto;
            }

            #mobileNav {
                flex: 1;
                display: flex !important;
                flex-direction: column;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body class="bgs-primary">
    <div class="row gx-0">

        <header class="col-12 col-lg-3 bg-light px-4 py-3 border-end border-dark-subtle d-flex flex-column gap-3 custom-sidebar">

            <div class="d-flex justify-content-between align-items-center">
                <div class="navbar-brand font-cinzel fs-3" style="display:flex;align-items:center;font-weight:bold;">
                    <img src="/venuebook/assets/images/Logo.svg" alt="Logo" width="30" height="30" style="margin-right:6px;">
                    VenueBook
                </div>
                <button class="d-lg-none bg-transparent border-0 p-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mobileNav"
                    aria-controls="mobileNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <img src="/venuebook/assets/images/Burger.svg" alt="Menu" width="26" height="26">
                </button>
            </div>

            <nav class="collapse d-lg-flex flex-column justify-content-between w-100" id="mobileNav">

                <div class="d-flex flex-column gap-2 pt-2 pb-3 border-bottom border-dark-subtle">
                    <?php foreach ($nav_items as $item): ?>
                        <a href="<?= htmlspecialchars($item['href']) ?>"
                            class="<?= $active_nav === $item['key'] ? 'sidebar-selected' : 'sidebar-link' ?> p-2 text-decoration-none fw-semibold d-inline-flex align-items-center">
                            <span class="sidebar-icon sidebar-icon-<?= htmlspecialchars($item['key']) ?> me-2" aria-hidden="true"></span>
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="pt-3">
                    <div class="d-flex align-items-center w-100 justify-content-between sidebar-link p-2"
                        style="cursor:pointer;"
                        role="button"
                        tabindex="0"
                        onclick="window.location.href='<?= htmlspecialchars($settings_url) ?>';">
                        <div class="d-flex align-items-center gap-2">
                                <img src="<?= htmlspecialchars($profile_avatar) ?>" alt="Profile" width="40" height="40" class="rounded-2 object-fit-cover border border-dark-subtle" aria-hidden="true">
                            <div>
                                <h6 class="mb-0 font-mont fw-semibold"><?= htmlspecialchars(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')) ?></h6>
                                <p class="mb-0 font-mont" style="font-size:12px;">
                                    <?= ($_SESSION['role'] ?? '') === 'admin' ? 'Administrator' : 'Event Organizer' ?>
                                </p>
                            </div>
                        </div>
                        <button class="btn p-1 position-relative" aria-label="Notifications" onclick="window.location.href='<?= htmlspecialchars($notif_url) ?>'; event.stopPropagation();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" stroke="currentColor" stroke-width="0.6" class="bi bi-bell" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                            </svg>
                            <?php if ($unread_notifications > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem; padding: 0.25em 0.4em;">
                                    <?= $unread_notifications > 99 ? '99+' : $unread_notifications ?>
                                    <span class="visually-hidden">unread messages</span>
                                </span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>

            </nav>
        </header>

        <main class="col-12 col-lg-9 p-4 mx-auto">