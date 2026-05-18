<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');

require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../config/nav.php';

$active_nav = 'Venue';
$page_title = 'Venue';

include __DIR__ . '/../includes/top_sidebar.php';
?>
<?php
$total_items = (int)$conn->prepare("SELECT COUNT(*) FROM venue WHERE Status = 'active'")->fetchColumn();
$items_per_page = 6;

$total_pages = (int)ceil($total_items / $items_per_page);
if ($total_pages < 1) {
    $total_pages = 1;
}

$current_page = (!empty($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

$current_page = max(1, min($total_pages, $current_page));

$offset = ($current_page - 1) * $items_per_page;

$stmt = $conn->prepare("SELECT * FROM venue WHERE Status = 'active' LIMIT ? OFFSET ?");
$stmt->bindValue(1, $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$venues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    :root {
        --accent-gold: #A67C52;
        --navy: #1D263B;
    }

    .member-portal-tag {
        font-size: 10px;
        font-weight: 800;
        color: var(--accent-gold);
        letter-spacing: 1px;
    }

    /* Venue Card Custom Styling */
    .venue-card {
        border: 2px solid var(--accent-gold);
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: white;
    }

    .venue-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(166, 124, 82, 0.2);
        cursor: pointer;
    }

    .venue-img-wrapper {
        position: relative;
        padding: 12px;
    }

    .venue-img {
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        width: 100%;
    }

    .venue-badge {
        position: absolute;
        bottom: 24px;
        left: 24px;
        background: var(--accent-gold);
        color: white;
        font-size: 0.7rem;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 700;
    }

    .text-gold {
        color: var(--accent-gold);
    }

    .btn-navy {
        background: var(--navy);
        color: white;
        font-weight: 700;
        font-size: 0.75rem;
        transition: all 0.3s;
    }

    .btn-navy:hover {
        background: var(--accent-gold);
        color: white;
    }

    .search-bar {
        border-radius: 25px;
        padding-left: 45px;
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        color: #aaa;
    }
</style>

<div class="container-fluid d-flex flex-column" style="min-height: calc(100vh - 120px);">
    <div class="flex-grow-1">
        <div class="d-md-flex justify-content-between align-items-end mb-4 gap-3">
            <div>
                <span class="text-tag text-uppercase">Venue</span>
                <h1 class="font-cinzel display-5 fw-bold mt-1">Browse Venues</h1>
                <p class="text-secondary mb-0">Discover the perfect space for your next event.</p>
            </div>
            <div class="position-relative" style="max-width: 400px; width: 100%;">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="form-control search-bar shadow-sm" placeholder="Search venues, events, locations...">
            </div>
        </div>

        <!-- Venue Grid -->
        <?php if (empty($venues)): ?>
            <div class="text-center py-5 mt-5">
                <i class="bi bi-building-x text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3 text-muted">No Venues Available</h3>
                <p class="text-secondary">We couldn't find any active venues at this time. Please check back later.</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                <?php foreach ($venues as $venue): ?>
                    <div class="col">
                        <div class="venue-card h-100 shadow-sm border-0" onclick="window.location.href='picker.php?venue_id=<?= $venue['Venue_id'] ?>';">
                            <div class="venue-img-wrapper">
                                <?php if (!empty($venue['image'])): ?>
                                    <img src="<?= htmlspecialchars($venue['image'], ENT_QUOTES) ?>" alt="Venue" class="venue-img">
                                <?php else: ?>
                                    <div class="venue-img d-flex align-items-center justify-content-center bg-light text-muted">
                                        <i class="bi bi-image" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body p-4 pt-2">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="h6 fw-bold mb-0 text-navy" style="max-width: 70%;"><?= htmlspecialchars($venue['Name'], ENT_QUOTES) ?></h3>
                                    <span class="fw-bold text-navy">₱<?= number_format((float) $venue['Price_per_day'], 2) ?></span>
                                </div>

                                <div class="small text-secondary mb-3">
                                    <div class="mb-1"><i class="fas fa-map-marker-alt text-gold me-2"></i> <?= htmlspecialchars($venue['Location'], ENT_QUOTES) ?></div>
                                    <div class="text-gold fw-bold"><i class="fas fa-users me-1"></i> <?= htmlspecialchars($venue['Capacity'], ENT_QUOTES) ?> Guests</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="mt-auto pt-5">
        <nav aria-label="Venue page navigation">
            <ul class="pagination justify-content-center">

                <!-- Previous Button -->
                <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link border-0 shadow-sm text-navy" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo; Previous</span>
                    </a>
                </li>

                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item mx-1">
                        <a class="page-link border-0 shadow-sm rounded <?= ($i == $current_page) ? 'bg-navy text-white' : 'text-navy' ?>"
                            href="?page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Next Button -->
                <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link border-0 shadow-sm text-navy" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">Next &raquo;</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</div>

<style>
    /* Styling to match your VenueBook aesthetic */
    .pagination .page-link {
        color: var(--navy);
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .pagination .bg-navy {
        background-color: var(--navy) !important;
    }

    .pagination .page-link:hover:not(.active) {
        background-color: var(--accent-gold);
        color: white;
    }
</style>
<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>