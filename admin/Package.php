<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

// Database connection
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../config/nav.php';

$active_nav = 'Package';
$page_title = 'Package';

// Get current user ID
$user = get_currnt_user();
$user_id = $user["user_id"];

// Load venue options for package creation
$venues = [];
try {
    $venueStmt = $conn->prepare('SELECT Venue_id, Name FROM venue WHERE User_id = ? ORDER BY Name');
    $venueStmt->execute([$user_id]);
    $venues = $venueStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $venues = [];
}

$success = '';
$errors = [];
$selected_status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'active';
if (!in_array($selected_status, ['active', 'archived'], true)) {
    $selected_status = 'active';
}

// Handle POST request for adding package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
    $name = trim($_POST['name'] ?? '');
    $venue_id = (int)($_POST['venue_id'] ?? 0);
    $price = trim($_POST['price'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['active', 'archived'], true) ? $_POST['status'] : 'active';
    $inclusionsRaw = trim($_POST['inclusions'] ?? '');

    if (empty($name)) $errors[] = "Package name is required.";
    if ($venue_id <= 0) $errors[] = "Valid Venue ID is required.";
    if ($price === '' || !is_numeric($price) || (float)$price < 0) $errors[] = "A valid price is required.";

    // Verify user owns the venue
    if (empty($errors)) {
        $chkVenue = $conn->prepare('SELECT Venue_id FROM venue WHERE Venue_id = ? AND User_id = ?');
        $chkVenue->execute([$venue_id, $user_id]);
        if (!$chkVenue->fetch()) {
            $errors[] = "Venue not found or permission denied.";
        }
    }

    if (empty($errors)) {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare('INSERT INTO packages (Venue_id, Name, Price, Status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$venue_id, $name, $price, $status]);
            $package_id = $conn->lastInsertId();

            if (!empty($inclusionsRaw)) {
                $incList = explode(',', $inclusionsRaw);
                $incStmt = $conn->prepare('INSERT INTO inclusions (package_id, inclusion) VALUES (?, ?)');
                foreach ($incList as $inc) {
                    $incTrim = trim($inc);
                    if ($incTrim !== '') {
                        $incStmt->execute([$package_id, $incTrim]);
                    }
                }
            }
            $conn->commit();
            $success = "Package created successfully.";
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Failed to create package: " . $e->getMessage();
        }
    }
}

// Handle POST request for editing package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_package'])) {
    $package_id = (int)($_POST['package_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $venue_id = (int)($_POST['venue_id'] ?? 0);
    $price = trim($_POST['price'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['active', 'archived'], true) ? $_POST['status'] : 'active';
    $inclusionsRaw = trim($_POST['inclusions'] ?? '');

    if (empty($name)) $errors[] = "Package name is required.";
    if ($venue_id <= 0) $errors[] = "Valid Venue ID is required.";
    if ($price === '' || !is_numeric($price) || (float)$price < 0) $errors[] = "A valid price is required.";

    // Verify user owns the venue
    if (empty($errors)) {
        $chkVenue = $conn->prepare('SELECT Venue_id FROM venue WHERE Venue_id = ? AND User_id = ?');
        $chkVenue->execute([$venue_id, $user_id]);
        if (!$chkVenue->fetch()) {
            $errors[] = "Venue not found or permission denied.";
        }
    }
    
    // Also verify that the package exists and belongs to user
    if (empty($errors)) {
        $chkPkg = $conn->prepare('SELECT Package_id FROM packages p JOIN venue v ON p.Venue_id = v.Venue_id WHERE p.Package_id = ? AND v.User_id = ?');
        $chkPkg->execute([$package_id, $user_id]);
        if (!$chkPkg->fetch()) {
            $errors[] = "Package not found or permission denied.";
        }
    }

    if (empty($errors)) {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare('UPDATE packages SET Venue_id=?, Name=?, Price=?, Status=? WHERE Package_id=?');
            $stmt->execute([$venue_id, $name, $price, $status, $package_id]);
            
            // Update inclusions: delete all and insert new ones
            $conn->prepare('DELETE FROM inclusions WHERE package_id = ?')->execute([$package_id]);

            if (!empty($inclusionsRaw)) {
                $incList = explode(',', $inclusionsRaw);
                $incStmt = $conn->prepare('INSERT INTO inclusions (package_id, inclusion) VALUES (?, ?)');
                foreach ($incList as $inc) {
                    $incTrim = trim($inc);
                    if ($incTrim !== '') {
                        $incStmt->execute([$package_id, $incTrim]);
                    }
                }
            }
            $conn->commit();
            $success = "Package updated successfully.";
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Failed to update package: " . $e->getMessage();
        }
    }
}

// Handle POST request for deleting package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_package'])) {
    $package_id = (int)($_POST['package_id'] ?? 0);
    if ($package_id > 0) {
        $chkPkg = $conn->prepare('SELECT Package_id FROM packages p JOIN venue v ON p.Venue_id = v.Venue_id WHERE p.Package_id = ? AND v.User_id = ?');
        $chkPkg->execute([$package_id, $user_id]);
        if ($chkPkg->fetch()) {
            try {
                $conn->beginTransaction();
                $conn->prepare('DELETE FROM inclusions WHERE package_id = ?')->execute([$package_id]);
                $conn->prepare('DELETE FROM packages WHERE Package_id = ?')->execute([$package_id]);
                $conn->commit();
                $success = "Package deleted successfully.";
            } catch (PDOException $e) {
                $conn->rollBack();
                $errors[] = "Failed to delete package: " . $e->getMessage();
            }
        } else {
            $errors[] = "Package not found or permission denied.";
        }
    }
}

// Fetch stats and packages from database
try {
    // Total packages
    $stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM packages JOIN venue ON packages.Venue_id = venue.Venue_id WHERE venue.User_id = ?");
    $stmt_total->execute([$user_id]);
    $total_packages = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Active packages count
    $stmt_active = $conn->prepare("SELECT COUNT(*) as active FROM packages JOIN venue ON packages.Venue_id = venue.Venue_id WHERE venue.User_id = ? AND packages.Status = 'active'");
    $stmt_active->execute([$user_id]);
    $active_packages = $stmt_active->fetch(PDO::FETCH_ASSOC)['active'] ?? 0;

    // Total revenue (sum of all package prices)
    $stmt_revenue = $conn->prepare("SELECT SUM(packages.Price) as revenue FROM packages JOIN venue ON packages.Venue_id = venue.Venue_id WHERE venue.User_id = ?");
    $stmt_revenue->execute([$user_id]);
    $total_revenue = $stmt_revenue->fetch(PDO::FETCH_ASSOC)['revenue'] ?? 0;

    // Fetch packages with inclusions grouped
    $packageSql = "
        SELECT p.Package_id AS package_id, p.Venue_id AS venue_id, p.Name AS name, p.Price AS price, p.Status AS status,
               GROUP_CONCAT(i.inclusion SEPARATOR ',') as inclusions
        FROM packages p
        JOIN venue v ON p.Venue_id = v.Venue_id
        LEFT JOIN inclusions i ON p.Package_id = i.package_id
        WHERE v.User_id = ? AND p.Status = ?
        GROUP BY p.Package_id
    ";
    $stmt_packages = $conn->prepare($packageSql);
    $stmt_packages->execute([$user_id, $selected_status]);
    $packages = $stmt_packages->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $total_packages = 0;
    $active_packages = 0;
    $total_revenue = 0;
    $packages = [];
}


include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --accent-gold: #A67C52;
        --merriweather: 'Merriweather', serif;
    }

    .member-portal-tag {
        font-size: 10px;
        font-weight: 800;
        color: var(--accent-gold);
        letter-spacing: 1px;
    }

    .merriweather {
        font-family: var(--merriweather);
    }

    /* Stats Cards Styling */
    .stat-card {
        background-color: #ffffff;
        border: 1px solid #ECE8E3;
        border-radius: 12px;
        padding: 24px;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-label {
        font-size: 10px;
        font-weight: 800;
        color: #8E8C89;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-value {
        font-family: var(--merriweather);
        font-size: 32px;
        color: var(--accent-gold);
        margin: 8px 0;
    }

    .stat-icon-bg {
        position: absolute;
        bottom: 10px;
        right: 10px;
        opacity: 0.05;
        font-size: 60px;
        pointer-events: none;
    }

    /* Registry Table Styling */
    .package-container {
        background-color: white;
        border: 1px solid #ECE8E3;
        border-radius: 12px;
        overflow: hidden;
    }

    /* Package card visual tweaks */
    .package-container .card {
        border: 1px solid #ECE8E3;
        border-radius: 10px;
        background: #ffffff;
    }

    .nav-tabs-custom {
        display: flex;
        gap: 30px;
        list-style: none;
        padding: 0;
        margin: 0;
        border-bottom: 1px solid #ECE8E3;
    }

    .tab-link {
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        color: #B5B3B0;
        padding: 1.5rem 0;
        display: inline-block;
        letter-spacing: 0.5px;
    }

    .tab-link.active {
        color: #1A1918;
        border-bottom: 3px solid #1A1918;
    }

    .tab-count {
        font-size: 10px;
        margin-left: 6px;
        color: #A0A0A0;
        font-weight: 700;
    }

    .btn-create {
        background-color: #0F1219;
        color: white;
        font-size: 11px;
        font-weight: 800;
        padding: 10px 22px;
        border-radius: 6px;
        border: none;
        letter-spacing: 1px;
    }

    .table thead th {
        background-color: #F4F4F2;
        font-size: 10px;
        font-weight: 800;
        color: #8E8C89;
        text-transform: uppercase;
        padding: 20px 24px;
    }

    .table tbody td {
        padding: 28px 24px;
        vertical-align: middle;
    }

    .pkg-name-main {
        font-family: var(--merriweather);
        font-size: 16px;
        font-weight: 700;
        color: #0E1F33;
    }

    .pill-inclusion {
        background-color: #EFECE8;
        color: #8E8C89;
        font-size: 10px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
        margin-right: 5px;
    }

    .price-text {
        font-family: var(--merriweather);
        font-size: 18px;
        color: var(--accent-gold);
        font-weight: 700;
    }

    .status-tag {
        font-size: 9px;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 100px;
        border: 1px solid transparent;
    }

    .status-active {
        background-color: #F4F7F2;
        color: #5D7A5D;
        border-color: #E5EADF;
    }

    .status-archived {
        background-color: #F7F7F7;
        color: #666;
        border-color: #EDEDED;
    }
</style>

<div class="container-fluid">

    <div class="mb-4">
        <span class="member-portal-tag text-uppercase">Package</span>
        <h1 class="font-cinzel display-5 fw-bold text-navy mt-1">Package Registry</h1>
        <p class="text-muted mb-0">Comprehensive management of service tiers across estates.</p>
    </div>

    <!-- Alerts -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-4 rounded-3 shadow-sm border-0">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success mb-4 rounded-3 shadow-sm border-0">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success, ENT_QUOTES) ?>
        </div>
    <?php endif; ?>

    <!-- Metrics Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm d-flex flex-column justify-content-between" style="min-height:120px;">
                <div>
                    <div class="stat-label">Total Packages</div>
                    <div class="stat-value"><?= number_format($total_packages) ?></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">All packages</div>
                    <i class="bi bi-box-seam stat-icon-bg"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm d-flex flex-column justify-content-between" style="min-height:120px;">
                <div>
                    <div class="stat-label">Active Packages</div>
                    <div class="stat-value"><?= number_format($active_packages) ?></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">Currently active</div>
                    <i class="bi bi-bank stat-icon-bg"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm d-flex flex-column justify-content-between" style="min-height:120px;">
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">₱<?= number_format((float)$total_revenue, 2) ?></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">All packages combined</div>
                    <i class="bi bi-currency-dollar stat-icon-bg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Controls -->
    <div class="d-flex justify-content-between align-items-center mt-5">
        <ul class="nav-tabs-custom">
            <li>
                <a href="?status=active" class="tab-link <?= $selected_status === 'active' ? 'active' : '' ?>">
                    ACTIVE PACKAGES <span class="tab-count"><?= (int)$active_packages ?></span>
                </a>
            </li>
            <li>
                <a href="?status=archived" class="tab-link <?= $selected_status === 'archived' ? 'active' : '' ?>">
                    ARCHIVED <span class="tab-count"><?= max(0, (int)$total_packages - (int)$active_packages) ?></span>
                </a>
            </li>
        </ul>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addPackageModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
            </svg>
            Create Package
        </button>
    </div>

    <!-- Package Table -->
    <div class="package-container shadow-sm mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Package ID</th>
                        <th>Venue ID</th>
                        <th>Package Name</th>
                        <th>Inclusions</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($packages)): ?>
                        <?php foreach ($packages as $package): ?>
                        <tr>
                            <td class="small fw-bold text-secondary"><?php echo htmlspecialchars($package['package_id'] ?? 'N/A'); ?></td>
                            <td class="small fw-extrabold text-dark"><?php echo htmlspecialchars($package['venue_id'] ?? 'N/A'); ?></td>
                            <td>
                                <div class="pkg-name-main"><?php echo htmlspecialchars($package['name'] ?? 'Unnamed'); ?></div>
                            </td>
                            <td>
                                <?php 
                                // Display inclusions if available
                                if (!empty($package['inclusions'])) {
                                    $inclusions = explode(',', $package['inclusions']);
                                    foreach (array_slice($inclusions, 0, 3) as $inclusion) {
                                        echo '<span class="pill-inclusion">' . trim($inclusion) . '</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td class="price-text">₱<?php echo number_format($package['price'] ?? 0, 2); ?></td>
                            <td><span class="status-tag <?php echo ($package['status'] ?? 'active') === 'active' ? 'status-active' : 'status-archived'; ?>"><?php echo strtoupper($package['status'] ?? 'active'); ?></span></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPackageModal" 
                                        data-pkg-id="<?php echo $package['package_id']; ?>"
                                        data-pkg-name="<?php echo htmlspecialchars($package['name'] ?? '', ENT_QUOTES); ?>"
                                        data-pkg-venue="<?php echo $package['venue_id']; ?>"
                                        data-pkg-price="<?php echo htmlspecialchars($package['price'] ?? '', ENT_QUOTES); ?>"
                                        data-pkg-status="<?php echo htmlspecialchars($package['status'] ?? '', ENT_QUOTES); ?>"
                                        data-pkg-inclusions="<?php echo htmlspecialchars($package['inclusions'] ?? '', ENT_QUOTES); ?>">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePackageModal"
                                        data-pkg-id="<?php echo $package['package_id']; ?>"
                                        data-pkg-name="<?php echo htmlspecialchars($package['name'] ?? '', ENT_QUOTES); ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No packages found. Create your first package to get started.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Table Footer -->
        <div class="bg-light p-3 px-4 d-flex justify-content-between align-items-center">
            <div class="small fw-bold text-muted">
                Showing <?= count($packages) ?> <?= $selected_status === 'archived' ? 'archived' : 'active' ?> packages of <?= $total_packages ?> total
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-white btn-sm border shadow-sm px-2"><i class="bi bi-chevron-left text-muted"></i></button>
                <button class="btn btn-white btn-sm border shadow-sm px-2"><i class="bi bi-chevron-right text-muted"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Add Package Modal -->
<div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title" id="addPackageModalLabel">
                    <span class="member-portal-tag">Create New Package</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addPackageForm" method="post">
                    <input type="hidden" name="add_package" value="1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="packageName" class="form-label fw-bold">Package Name</label>
                                <input type="text" class="form-control" id="packageName" name="name" required placeholder="e.g., Platinum Gala">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="packagePrice" class="form-label fw-bold">Price ($)</label>
                                <input type="number" class="form-control" id="packagePrice" name="price" step="0.01" min="0" required placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="venueId" class="form-label fw-bold">Venue ID</label>
                        <select class="form-select" id="venueId" name="venue_id" required>
                            <option value="">-- Select Venue --</option>
                            <?php foreach ($venues as $venue): ?>
                                <option value="<?= htmlspecialchars($venue['Venue_id']) ?>"><?= htmlspecialchars($venue['Name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="packageInclusions" class="form-label fw-bold">Inclusions</label>
                        <textarea class="form-control" id="packageInclusions" name="inclusions" rows="3" placeholder="Enter inclusions separated by commas&#10;e.g., 5-Course, Open Bar, Valet"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="packageStatus" class="form-label fw-bold">Status</label>
                        <select class="form-select" id="packageStatus" name="status">
                            <option value="active" selected>Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <small><strong>Note:</strong> Multiple inclusions should be separated by a comma.</small>
                    </div>
                    <?php if (empty($venues)): ?>
                        <div class="alert alert-warning" role="alert">
                            <small>No venues currently exist in your account. You must create a venue first.</small>
                        </div>
                    <?php endif; ?>
                    
                    <div class="modal-footer bg-light px-0 pb-0 mb-0 mt-4 border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark" <?php echo empty($venues) ? 'disabled' : ''; ?>>Create Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1" aria-labelledby="editPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title" id="editPackageModalLabel">
                    <span class="member-portal-tag">Edit Package</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editPackageForm" method="post">
                    <input type="hidden" name="edit_package" value="1">
                    <input type="hidden" name="package_id" id="editPackageId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editPackageName" class="form-label fw-bold">Package Name</label>
                                <input type="text" class="form-control" id="editPackageName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editPackagePrice" class="form-label fw-bold">Price (₱)</label>
                                <input type="number" class="form-control" id="editPackagePrice" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="editVenueId" class="form-label fw-bold">Venue</label>
                        <select class="form-select" id="editVenueId" name="venue_id" required>
                            <option value="">-- Select Venue --</option>
                            <?php foreach ($venues as $venue): ?>
                                <option value="<?= htmlspecialchars($venue['Venue_id']) ?>"><?= htmlspecialchars($venue['Name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="editPackageInclusions" class="form-label fw-bold">Inclusions</label>
                        <textarea class="form-control" id="editPackageInclusions" name="inclusions" rows="3" placeholder="Enter inclusions separated by commas"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editPackageStatus" class="form-label fw-bold">Status</label>
                        <select class="form-select" id="editPackageStatus" name="status">
                            <option value="active">Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    
                    <div class="modal-footer bg-light px-0 pb-0 mb-0 mt-4 border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Package Modal -->
<div class="modal fade" id="deletePackageModal" tabindex="-1" aria-labelledby="deletePackageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <input type="hidden" name="delete_package" value="1">
                <input type="hidden" name="package_id" id="deletePackageId">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 2rem;"></i>
                    </div>
                    <p class="fs-6 fw-bold mb-2">Delete package <strong id="deletePackageNameDisplay"></strong>?</p>
                    <p class="text-muted small">This will permanently remove the package and all its inclusions.</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editPackageModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editPackageId').value = btn.dataset.pkgId;
    document.getElementById('editPackageName').value = btn.dataset.pkgName;
    document.getElementById('editVenueId').value = btn.dataset.pkgVenue;
    document.getElementById('editPackagePrice').value = btn.dataset.pkgPrice;
    document.getElementById('editPackageStatus').value = btn.dataset.pkgStatus;
    document.getElementById('editPackageInclusions').value = btn.dataset.pkgInclusions;
});

document.getElementById('deletePackageModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('deletePackageId').value = btn.dataset.pkgId;
    document.getElementById('deletePackageNameDisplay').textContent = btn.dataset.pkgName;
});
</script>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>