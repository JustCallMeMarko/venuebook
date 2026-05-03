<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["admin"] ?? [];
$active_nav = 'Venue';  
$page_title = 'Venue';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --color-accent: #B08C68;
        --font-serif: 'Libre Baskerville', serif;
    }

    /* Content Header & Buttons */
    .content-title { font-family: var(--font-serif); font-size: 26px; font-weight: 400; }
    .btn-add-venue { 
        background-color: var(--color-accent); color: white; border: none;
        border-radius: 4px; font-size: 12px; font-weight: 600; padding: 8px 16px;
    }
    .btn-add-venue:hover { background-color: #9C7A58; color: white; }

    /* Admin Metric Cards */
    .metric-card { border: none; border-radius: 4px; padding: 20px; background: #fff; }
    .metric-label { color: #707070; font-size: 10px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
    .metric-value { font-size: 34px; font-family: var(--font-serif); color: #2C2C2C; }

    /* Table Styling */
    .table-card { background: #fff; border-radius: 4px; overflow: hidden; border: none; }
    .table-title { font-size: 11px; font-weight: 600; color: #A0A0A0; letter-spacing: 1px; }
    
    .table thead th {
        font-size: 10px; color: #707070; background-color: #EBEAE8;
        font-weight: 600; letter-spacing: 1px; text-transform: uppercase;
        padding: 12px 30px; border: none;
    }
    .table tbody td { font-size: 12px; padding: 15px 30px; vertical-align: middle; border-bottom: 1px solid #F0F0F0; }
    
    .venue-name { font-family: var(--font-serif); font-size: 13px; font-weight: 400; }
    .text-price { font-weight: 600; color: var(--color-accent); }

    /* Status Badges */
    .status-badge { font-size: 9px; font-weight: 700; border-radius: 4px; padding: 3px 8px; text-transform: uppercase; }
    .status-badge.active { background-color: #6DC297; color: white; }
    .status-badge.inactive { background-color: #D6D5D2; color: #707070; }

    /* Pagination */
    .page-btn {
        background-color: #E6E6E6; color: #707070; width: 28px; height: 28px;
        border-radius: 4px; display: inline-flex; justify-content: center;
        align-items: center; text-decoration: none; font-size: 10px;
    }
    .page-btn.active { background-color: var(--color-accent); color: white; }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="content-title">Venue Management</h1>
            <p class="text-muted small mb-0" style="max-width: 600px;">
                Central management for all Estate Reserve properties, providing granular control over capacity, location details, and active status.
            </p>
        </div>
        <button class="btn btn-add-venue mt-3 mt-md-0"><i class="bi bi-plus-lg me-2"></i>ADD VENUE</button>
    </div>

    <!-- Metrics Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="metric-card shadow-sm">
                <div class="metric-label">Total Venues</div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="metric-value">42</div>
                    <i class="bi bi-building text-secondary opacity-25 h4 mb-0"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="metric-card shadow-sm">
                <div class="metric-label">Active Venues</div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="metric-value">38</div>
                    <i class="bi bi-check-circle-fill text-success opacity-50 h4 mb-0"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="metric-card shadow-sm">
                <div class="metric-label">Inactive / Draft</div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="metric-value">4</div>
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
                    <!-- Static Row 1 -->
                    <tr>
                        <td class="fw-bold">VEN-001</td>
                        <td>
                            <div class="venue-name">CRYSTAL PAVILION</div>
                            <div class="text-muted" style="font-size: 10px;">Elegant glass structure...</div>
                        </td>
                        <td class="text-muted">East Wing, Floor 4</td>
                        <td>800 Guests</td>
                        <td class="text-price">$4,500.00</td>
                        <td><span class="status-badge active">Active</span></td>
                        <td class="text-center text-muted cursor-pointer"><i class="bi bi-three-dots"></i></td>
                    </tr>
                    <!-- Static Row 2 -->
                    <tr>
                        <td class="fw-bold">VEN-002</td>
                        <td>
                            <div class="venue-name">THE OAK LIBRARY</div>
                            <div class="text-muted" style="font-size: 10px;">Classic wood paneling...</div>
                        </td>
                        <td class="text-muted">West Tower, Floor 2</td>
                        <td>40 Guests</td>
                        <td class="text-price">$1,200.00</td>
                        <td><span class="status-badge inactive">Inactive</span></td>
                        <td class="text-center text-muted cursor-pointer"><i class="bi bi-three-dots"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination Footer -->
        <div class="p-3 px-4 d-flex justify-content-between align-items-center bg-white border-top">
            <div class="text-muted" style="font-size: 9px; font-weight: 700; letter-spacing: 0.5px;">SHOWING 1 TO 2 OF 42 VENUES</div>
            <div class="d-flex gap-2">
                <a href="#" class="page-btn"><i class="bi bi-chevron-left"></i></a>
                <a href="#" class="page-btn active">1</a>
                <a href="#" class="page-btn">2</a>
                <a href="#" class="page-btn"><i class="bi bi-chevron-right"></i></a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>