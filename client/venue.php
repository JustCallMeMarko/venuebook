<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["organizer"] ?? [];
$active_nav = 'Venue';  
$page_title = 'Venue';

include __DIR__ . '/../includes/top_sidebar.php';
?>
<?php
// Dummy data count (In a real app, use: SELECT COUNT(*) FROM venues)
$total_items = 24; 
$items_per_page = 6;
$total_pages = ceil($total_items / $items_per_page);

// Get current page from URL, default to 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
if ($current_page > $total_pages) $current_page = $total_pages;

// Calculate the offset for a SQL query
// Example: SELECT * FROM venues LIMIT $items_per_page OFFSET $offset
$offset = ($current_page - 1) * $items_per_page;
?>
<style>
    :root {
        --accent-gold: #A67C52;
        --navy: #1D263B;
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
    .text-gold { color: var(--accent-gold); }
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

<div class="container-fluid">
    <!-- Top Bar -->
    <header class="d-md-flex justify-content-between align-items-center mb-5 gap-3">
        <h1 class="h3 fw-bold text-navy font-cinzel mb-3 mb-md-0">VENUES</h1>
        
        <div class="position-relative" style="max-width: 400px; width: 100%;">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="form-control search-bar shadow-sm" placeholder="Search venues, events, locations...">
        </div>
    </header>

    <!-- Venue Grid -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        
        <?php
        // Sample data array - in a real app, this would come from your Database
        $venues = [
            [
                'name' => 'Enchanted Night Venue',
                'price' => '$2,800',
                'location' => 'The Convention Center',
                'rating' => '4.5',
                'img' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=400'
            ],
            [
                'name' => 'Golden Gala Ballroom',
                'price' => '$3,500',
                'location' => 'The Convention Center',
                'rating' => '4.8',
                'img' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=400'
            ],
            [
                'name' => 'Magenta Velvet Suite',
                'price' => '$2,200',
                'location' => 'Downtown Plaza',
                'rating' => '4.2',
                'img' => 'https://meetinazerbaijan.com/storage/2022/03/16/MwG5kF87k5w5dtSX5gZUKIlRWd5zQx84YA0m6HPv.jpg'
            ],
            [
                'name' => 'Lakeside Glass House',
                'price' => '$4,000',
                'location' => 'Shelby North',
                'rating' => '4.9',
                'img' => 'https://www.wedgewoodweddings.com/hs-fs/hubfs/3.0%20Venue%20Images/Mollys%20Lakeside/Mollys%20Lakeside%20Shelby%20NC%20Glass%20House%20Wedding%20Venue%20with%20Waterfront%20Views.jpg?width=800'
            ],
            [
                'name' => 'The Pool Lounge',
                'price' => '$1,800',
                'location' => 'East Side',
                'rating' => '4.0',
                'img' => 'https://memo.thevendry.com/wp-content/uploads/2023/04/The_Pool_Lounge2.jpeg'
            ],
            [
                'name' => 'Vintage Garden',
                'price' => '$2,500',
                'location' => 'Old Town',
                'rating' => '4.6',
                'img' => 'https://i.pinimg.com/originals/3a/6f/72/3a6f722140a8b6900e6f15f7a8fed01f.jpg'
            ]
        ];

        foreach ($venues as $venue): ?>
        <div class="col">
            <div class="venue-card h-100 shadow-sm border-0">
                <div class="venue-img-wrapper">
                    <img src="<?= $venue['img'] ?>" alt="Venue" class="venue-img">
                    <span class="venue-badge">RECOMMENDED</span>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h3 class="h6 fw-bold mb-0 text-navy" style="max-width: 70%;"><?= $venue['name'] ?></h3>
                        <span class="fw-bold text-navy"><?= $venue['price'] ?></span>
                    </div>
                    
                    <div class="small text-muted mb-3">
                        <div class="mb-1"><i class="fas fa-map-marker-alt text-gold me-2"></i> <?= $venue['location'] ?></div>
                        <div class="text-gold fw-bold"><i class="fas fa-star me-1"></i> <?= $venue['rating'] ?></div>
                    </div>

                    <hr class="text-light">

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted"><i class="fas fa-users text-gold me-2"></i> Capacity</span>
                        <button class="btn btn-navy btn-sm px-3">DETAILS</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>
<nav aria-label="Venue page navigation" class="mt-5">
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