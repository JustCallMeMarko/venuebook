<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

include __DIR__ . '/../config/nav.php';

$active_nav = 'Venue';
$page_title = 'Venue';

include __DIR__ . '/../includes/top_sidebar.php';

$user = get_currnt_user();
$user_id = $user['user_id'];

$success = '';
$errors = [];

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$items_per_page = 5;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_venue'])) {
        $name = trim($_POST['venue_name'] ?? '');
        $description = trim($_POST['venue_description'] ?? '');
        $location = trim($_POST['venue_location'] ?? '');
        $capacity = (int) ($_POST['venue_capacity'] ?? 0);
        $price = trim($_POST['venue_price'] ?? '');
        $status = in_array($_POST['venue_status'] ?? '', ['active', 'inactive'], true) ? $_POST['venue_status'] : 'active';

        $package_name = trim($_POST['package_name'] ?? '');
        $package_price = trim($_POST['package_price'] ?? '');
        $package_inclusions = trim($_POST['package_inclusions'] ?? '');

        if ($name === '') {
            $errors[] = 'Venue name is required.';
        }
        if ($location === '') {
            $errors[] = 'Venue location is required.';
        }
        if ($capacity <= 0) {
            $errors[] = 'Venue capacity must be greater than 0.';
        }
        if ($price === '' || !is_numeric($price) || (float) $price < 0) {
            $errors[] = 'A valid price per day is required.';
        }
        if ($package_name === '') {
            $errors[] = 'Package name is required.';
        }
        if ($package_price === '' || !is_numeric($package_price) || (float) $package_price < 0) {
            $errors[] = 'A valid package price is required.';
        }

        if (empty($errors)) {
            try {
                $conn->beginTransaction();

                $venue_stmt = $conn->prepare('INSERT INTO venue (User_id, Name, Description, Location, Capacity, Price_per_day, Status) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $venue_stmt->execute([
                    $user_id,
                    $name,
                    $description,
                    $location,
                    $capacity,
                    $price,
                    $status,
                ]);

                $venue_id = $conn->lastInsertId();

                $package_stmt = $conn->prepare('INSERT INTO packages (Venue_id, Name, Price, Status) VALUES (?, ?, ?, ?)');
                $package_stmt->execute([
                    $venue_id,
                    $package_name,
                    $package_price,
                    'active',
                ]);

                $package_id = $conn->lastInsertId();

                if ($package_inclusions !== '') {
                    $inclusions = array_filter(array_map('trim', explode(',', $package_inclusions)));
                    $inclusion_stmt = $conn->prepare('INSERT INTO inclusions (package_id, inclusion) VALUES (?, ?)');
                    foreach ($inclusions as $inclusion) {
                        $inclusion_stmt->execute([$package_id, $inclusion]);
                    }
                }

                $conn->commit();
                $success = 'New venue, package, and inclusions were saved successfully.';
            } catch (PDOException $e) {
                $conn->rollBack();
                $errors[] = 'Unable to save the venue. Please try again later.';
            }
        }
    }

    if (isset($_POST['delete_venue'])) {
        $delete_id = (int) ($_POST['delete_venue'] ?? 0);
        if ($delete_id > 0) {
            $verify_stmt = $conn->prepare('SELECT Venue_id FROM venue WHERE Venue_id = ? AND User_id = ?');
            $verify_stmt->execute([$delete_id, $user_id]);

            if ($verify_stmt->fetch()) {
                try {
                    $conn->beginTransaction();

                    $delete_inclusions = $conn->prepare('DELETE inclusions FROM inclusions JOIN packages ON inclusions.package_id = packages.Package_id WHERE packages.Venue_id = ?');
                    $delete_inclusions->execute([$delete_id]);

                    $delete_packages = $conn->prepare('DELETE FROM packages WHERE Venue_id = ?');
                    $delete_packages->execute([$delete_id]);

                    $delete_venue = $conn->prepare('DELETE FROM venue WHERE Venue_id = ?');
                    $delete_venue->execute([$delete_id]);

                    $conn->commit();
                    $success = 'Venue deleted successfully.';
                } catch (PDOException $e) {
                    $conn->rollBack();
                    $errors[] = 'Unable to delete the venue. Please try again later.';
                }
            } else {
                $errors[] = 'Venue not found or you do not have permission to delete it.';
            }
        }
    }
}

$total_stmt = $conn->prepare('SELECT COUNT(*) FROM venue WHERE User_id = ?');
$total_stmt->execute([$user_id]);
$total_venues = (int) $total_stmt->fetchColumn();

$active_stmt = $conn->prepare("SELECT COUNT(*) FROM venue WHERE User_id = ? AND Status = 'active'");
$active_stmt->execute([$user_id]);
$active_venues = (int) $active_stmt->fetchColumn();

$inactive_stmt = $conn->prepare("SELECT COUNT(*) FROM venue WHERE User_id = ? AND Status = 'inactive'");
$inactive_stmt->execute([$user_id]);
$inactive_venues = (int) $inactive_stmt->fetchColumn();

$total_pages = max(1, (int) ceil($total_venues / $items_per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}

$offset = ($page - 1) * $items_per_page;

$venues_stmt = $conn->prepare('SELECT * FROM venue WHERE User_id = ? ORDER BY Venue_id DESC LIMIT ? OFFSET ?');
$venues_stmt->bindValue(1, $user_id, PDO::PARAM_INT);
$venues_stmt->bindValue(2, $items_per_page, PDO::PARAM_INT);
$venues_stmt->bindValue(3, $offset, PDO::PARAM_INT);
$venues_stmt->execute();
$venues = $venues_stmt->fetchAll(PDO::FETCH_ASSOC);

$showing_start = $total_venues > 0 ? $offset + 1 : 0;
$showing_end = min($offset + $items_per_page, $total_venues);
?>

<style>
    :root {
        --accent-gold: #A67C52;
        --font-serif: 'Libre Baskerville', serif;
    }

    .member-portal-tag {
        font-size: 10px;
        font-weight: 800;
        color: var(--accent-gold);
        letter-spacing: 1px;
    }

    /* Content Header & Buttons */
    .content-title {
        font-family: var(--font-serif);
        font-size: 26px;
        font-weight: 400;
    }

    /* Admin Metric Cards */
    .metric-card {
        border: none;
        border-radius: 4px;
        padding: 20px;
        background: #fff;
    }

    .metric-label {
        color: #707070;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .metric-value {
        font-size: 34px;
        font-family: var(--font-serif);
        color: #2C2C2C;
    }

    /* Table Styling */
    .table-card {
        background: #fff;
        border-radius: 4px;
        overflow: hidden;
        border: none;
    }

    .table-title {
        font-size: 11px;
        font-weight: 600;
        color: #A0A0A0;
        letter-spacing: 1px;
    }

    .table thead th {
        font-size: 10px;
        color: #707070;
        background-color: #EBEAE8;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 12px 30px;
        border: none;
    }

    .table tbody td {
        font-size: 12px;
        padding: 15px 30px;
        vertical-align: middle;
        border-bottom: 1px solid #F0F0F0;
    }

    .venue-name {
        font-family: var(--font-serif);
        font-size: 13px;
        font-weight: 400;
    }

    .text-price {
        font-weight: 600;
        color: var(--accent-gold);
    }

    /* Status Badges */
    .status-badge {
        font-size: 9px;
        font-weight: 700;
        border-radius: 4px;
        padding: 3px 8px;
        text-transform: uppercase;
    }

    .status-badge.active {
        background-color: #6DC297;
        color: white;
    }

    .status-badge.inactive {
        background-color: #D6D5D2;
        color: #707070;
    }

    .status-badge.archived {
        background-color: #F0E1C9;
        color: #8E6000;
    }

    /* Pagination */
    .page-btn {
        background-color: #E6E6E6;
        color: #707070;
        width: 28px;
        height: 28px;
        border-radius: 4px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        font-size: 10px;
    }

    .page-btn.active {
        background-color: var(--accent-gold);
        color: white;
    }

    .page-btn.disabled {
        opacity: 0.45;
        pointer-events: none;
    }

    .btn-delete-action {
        height: 24px;
        padding: 0 0.7rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.35px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        border-width: 1px;
    }

    .btn-delete-action:hover,
    .btn-delete-action:focus {
        background-color: #F8D7DA;
        color: #842029;
    }

    .delete-modal .modal-content {
        border-radius: 18px;
    }

    .delete-modal .modal-body {
        text-align: center;
        padding: 2rem 2rem 1.25rem;
    }

    .delete-modal-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(176, 42, 55, 0.12);
        color: #B02A37;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        margin-bottom: 1rem;
    }

    .delete-modal p {
        margin-bottom: 0.5rem;
    }

    .delete-modal .modal-footer {
        border-top: none;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem 1.75rem 1.5rem;
    }

    .delete-modal .btn-secondary {
        min-width: 110px;
    }

    .modal-dialog {
        max-width: 840px;
    }

    .modal-content {
        border-radius: 18px;
    }

    .modal-header {
        border-bottom: none;
        padding: 1.75rem 1.75rem 0.75rem;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .modal-footer {
        border-top: none;
        padding: 1rem 1.75rem 1.5rem;
    }
</style>

<div class="container-fluid">
    <div class="d-md-flex justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="member-portal-tag text-uppercase">Venue</span>
            <h1 class="font-cinzel display-5 fw-bold text-navy mt-1">Venue Management</h1>
            <p class="text-muted mb-0">Central management for all Estate Reserve properties, providing granular control over capacity, location details, and active status.</p>
        </div>
        <button type="button" class="btn btn-dark mt-3 mt-md-0" data-bs-toggle="modal" data-bs-target="#addVenueModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
            </svg>
            Add Venue
        </button>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success, ENT_QUOTES) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Metrics Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="metric-card shadow-sm">
                <div class="metric-label">Total Venues</div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="metric-value"><?= $total_venues ?></div>
                    <i class="bi bi-building text-secondary opacity-25 h4 mb-0"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="metric-card shadow-sm">
                <div class="metric-label">Active Venues</div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="metric-value"><?= $active_venues ?></div>
                    <i class="bi bi-check-circle-fill text-success opacity-50 h4 mb-0"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="metric-card shadow-sm">
                <div class="metric-label">Inactive / Draft</div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="metric-value"><?= $inactive_venues ?></div>
                    <i class="bi bi-eye-slash text-secondary opacity-25 h4 mb-0"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card shadow-sm mb-5">
        <div class="p-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <div class="table-title">MY VENUES</div>
            <i class="bi bi-filter cursor-pointer"></i>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Venue ID</th>
                        <th>Name & Description</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Price Per Day</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($venues)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No venues found yet. Start by adding a new venue.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($venues as $venue): ?>
                            <tr>
                                <td class="fw-bold">VEN-<?= str_pad($venue['Venue_id'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="venue-name"><?= htmlspecialchars($venue['Name'], ENT_QUOTES) ?></div>
                                    <div class="text-muted" style="font-size: 10px;"><?= htmlspecialchars(mb_strimwidth($venue['Description'], 0, 60, '...'), ENT_QUOTES) ?></div>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($venue['Location'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($venue['Capacity'], ENT_QUOTES) ?> Guests</td>
                                <td class="text-price">₱<?= number_format((float) $venue['Price_per_day'], 2) ?></td>
                                <td><span class="status-badge <?= $venue['Status'] === 'active' ? 'active' : 'inactive' ?>"><?= strtoupper($venue['Status']) ?></span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-delete-action btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteVenueModal" data-venue-id="<?= $venue['Venue_id'] ?>" data-venue-name="<?= htmlspecialchars($venue['Name'], ENT_QUOTES) ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-3 px-4 d-flex justify-content-between align-items-center bg-white border-top">
            <div class="text-muted" style="font-size: 9px; font-weight: 700; letter-spacing: 0.5px;">SHOWING <?= $showing_start ?> TO <?= $showing_end ?> OF <?= $total_venues ?> VENUES</div>
            <div class="d-flex gap-2">
                <a href="?page=<?= max(1, $page - 1) ?>" class="page-btn<?= $page <= 1 ? ' disabled' : '' ?>"><i class="bi bi-chevron-left"></i></a>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="page-btn<?= $i === $page ? ' active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <a href="?page=<?= min($total_pages, $page + 1) ?>" class="page-btn<?= $page >= $total_pages ? ' disabled' : '' ?>"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Add Venue Modal -->
<div class="modal fade" id="addVenueModal" tabindex="-1" aria-labelledby="addVenueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVenueModalLabel">Add New Venue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <input type="hidden" name="add_venue" value="1">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Venue Name</label>
                                <input type="text" name="venue_name" class="form-control" value="<?= htmlspecialchars($_POST['venue_name'] ?? '', ENT_QUOTES) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="venue_description" rows="4" class="form-control"><?= htmlspecialchars($_POST['venue_description'] ?? '', ENT_QUOTES) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" name="venue_location" class="form-control" value="<?= htmlspecialchars($_POST['venue_location'] ?? '', ENT_QUOTES) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Capacity</label>
                                <input type="number" name="venue_capacity" class="form-control" min="1" value="<?= htmlspecialchars($_POST['venue_capacity'] ?? '1', ENT_QUOTES) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price Per Day</label>
                                <input type="number" name="venue_price" step="0.01" class="form-control" value="<?= htmlspecialchars($_POST['venue_price'] ?? '', ENT_QUOTES) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Visibility</label>
                                <select name="venue_status" class="form-select">
                                    <option value="active" <?= ($_POST['venue_status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= ($_POST['venue_status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6 class="mb-3">Package Details</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Package Name</label>
                                <input type="text" name="package_name" class="form-control" value="<?= htmlspecialchars($_POST['package_name'] ?? '', ENT_QUOTES) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Package Price</label>
                                <input type="number" name="package_price" class="form-control" step="0.01" value="<?= htmlspecialchars($_POST['package_price'] ?? '', ENT_QUOTES) ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Package Inclusions</label>
                        <textarea name="package_inclusions" rows="3" class="form-control" placeholder="Separate inclusions with commas"><?= htmlspecialchars($_POST['package_inclusions'] ?? '', ENT_QUOTES) ?></textarea>
                        <div class="form-text">Example: Buffet dinner, Open bar, Stage setup</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save Venue</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Venue Modal -->
<div class="modal fade" id="deleteVenueModal" tabindex="-1" aria-labelledby="deleteVenueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered delete-modal">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <input type="hidden" name="delete_venue" id="deleteVenueId" value="">
                <div class="modal-body">
                    <div class="delete-modal-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <p class="fs-6 fw-bold mb-2">Delete venue <strong id="deleteVenueName"></strong>?</p>
                    <p class="text-muted small">This action will permanently remove the venue and any related packages and inclusions.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteModal = document.getElementById('deleteVenueModal');
        if (!deleteModal) return;

        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var venueId = button.getAttribute('data-venue-id');
            var venueName = button.getAttribute('data-venue-name');

            deleteModal.querySelector('#deleteVenueId').value = venueId;
            deleteModal.querySelector('#deleteVenueName').textContent = venueName;
        });
    });
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>