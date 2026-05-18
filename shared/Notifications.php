<?php
require_once __DIR__ . '/../includes/auth.php';
require_any_role(['admin', 'client']);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/notifications.php';

$user = get_currnt_user();
$user_id = $user['user_id'] ?? null;

$active_nav = 'Notifications';
$page_title = 'Notifications';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read']) && $user_id) {
    mark_all_notifications_read($conn, $user_id);
    $_SESSION['success'] = 'All notifications marked as read.';
    header('Location: Notifications.php');
    exit;
}

$stmt = $conn->prepare('SELECT * FROM notifications WHERE User_id = ? ORDER BY Created_at DESC');
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark as read after loading the list
if ($user_id) {
    mark_all_notifications_read($conn, $user_id);
}

include __DIR__ . '/../includes/top_sidebar.php';
?>

<div class="container-fluid" style="min-height: calc(100vh - 120px);">
    <div class="d-md-flex justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="text-tag text-uppercase" style="font-size: 10px; font-weight: 800; color: #A67C52; letter-spacing: 1px;">Updates</span>
            <h1 class="font-cinzel display-5 fw-bold mt-1">Notifications</h1>
            <p class="text-secondary mb-0">Stay informed about your booking approvals, payments, and system alerts.</p>
        </div>
        <form method="post" class="mt-3 mt-md-0">
            <button type="submit" name="mark_all_read" value="1" class="btn btn-outline-secondary btn-sm">Mark all read</button>
        </form>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm p-4">
                <?php if (empty($notifications)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">You have no notifications yet.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($notifications as $n): ?>
                            <?php $is_unread = empty($n['Is_read']); ?>
                            <div class="list-group-item px-3 py-3 d-flex align-items-start <?= $is_unread ? 'bg-primary-subtle border-start border-primary border-4' : 'border-bottom' ?>" style="border-radius: 4px; margin-bottom: 8px;">
                                <div class="bg-navy text-white rounded-circle d-flex align-items-center justify-content-center me-3 mt-1 shadow-sm" style="width: 40px; height: 40px; min-width: 40px; background-color: #0e1b2d;">
                                    <i class="bi bi-bell"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 <?= $is_unread ? 'fw-bold text-dark' : 'text-secondary' ?>"><?= htmlspecialchars($n['Message']) ?></h6>
                                        <small class="text-muted text-nowrap ms-2 fw-semibold" style="font-size: 0.75rem;"><?= date('M j, g:i A', strtotime($n['Created_at'])) ?></small>
                                    </div>
                                    <?php if ($is_unread): ?>
                                        <span class="badge bg-primary mt-1">New</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>