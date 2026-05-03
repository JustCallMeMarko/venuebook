<?php
session_start();

// Sample notifications (replace with DB queries as needed)
$notifications = [
    ['title' => 'Booking confirmed', 'body' => 'Your booking for Hall A on May 10 was confirmed.', 'time' => '2h ago', 'type' => 'booking'],
    ['title' => 'New message', 'body' => 'You have a new message from the venue manager.', 'time' => '1d ago', 'type' => 'message'],
    ['title' => 'Payment received', 'body' => 'Payment for Invoice #1234 has been processed.', 'time' => '3d ago', 'type' => 'payment'],
];

?>
<?php require_once __DIR__ . '/../includes/top_sidebar.php'; ?>

<div class="container">
    <main class="col-12 col-lg-9 p-4 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">Notifications</h1>
            <button class="btn btn-outline-secondary btn-sm me-2">Mark all read</button>

        </div>

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($notifications as $n): ?>
                                <li class="list-group-item d-flex gap-3 align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white" style="width:44px;height:44px;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M12 2L15 8H9L12 2Z" fill="currentColor" />
                                                <circle cx="12" cy="14" r="6" fill="currentColor" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($n['title']) ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($n['body']) ?></div>
                                        </div>
                                        <div class="text-end text-muted small ms-3"><?= htmlspecialchars($n['time']) ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <aside class="col-12 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="h6">Notification Settings</h2>
                        <p class="small text-muted">Control which notifications you receive.</p>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailNotif" checked>
                            <label class="form-check-label small" for="emailNotif">Email notifications</label>
                        </div>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="smsNotif">
                            <label class="form-check-label small" for="smsNotif">SMS notifications</label>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/bottom_sidebar.php'; ?>