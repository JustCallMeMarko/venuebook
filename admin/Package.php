<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["admin"] ?? [];
$active_nav = 'Package';  
$page_title = 'Package';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --accent-brown: #967253;
        --merriweather: 'Merriweather', serif;
    }
    .merriweather { font-family: var(--merriweather); }

    /* Stats Cards Styling */
    .stat-card {
        background-color: #FAF6F1;
        border: 1px solid #ECE8E3;
        border-radius: 12px;
        padding: 24px;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .stat-label { font-size: 10px; font-weight: 800; color: #8E8C89; text-transform: uppercase; letter-spacing: 1px; }
    .stat-value { font-family: var(--merriweather); font-size: 32px; color: var(--accent-brown); margin: 8px 0; }
    .stat-icon-bg { position: absolute; bottom: 10px; right: 10px; opacity: 0.05; font-size: 60px; pointer-events: none; }

    /* Registry Table Styling */
    .package-container {
        background-color: white;
        border: 1px solid #ECE8E3;
        border-radius: 12px;
        overflow: hidden;
    }
    .nav-tabs-custom { display: flex; gap: 30px; list-style: none; padding: 0; margin: 0; border-bottom: 1px solid #ECE8E3; }
    .tab-link {
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        color: #B5B3B0;
        padding: 1.5rem 0;
        display: inline-block;
        letter-spacing: 0.5px;
    }
    .tab-link.active { color: #1A1918; border-bottom: 3px solid #1A1918; }
    
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
    .table tbody td { padding: 28px 24px; vertical-align: middle; }
    
    .pkg-name-main { font-family: var(--merriweather); font-size: 16px; font-weight: 700; color: #0E1F33; }
    .pill-inclusion { background-color: #EFECE8; color: #8E8C89; font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 4px; margin-right: 5px; }
    .price-text { font-family: var(--merriweather); font-size: 18px; color: var(--accent-brown); font-weight: 700; }

    .status-tag { font-size: 9px; font-weight: 800; padding: 5px 12px; border-radius: 100px; border: 1px solid transparent; }
    .status-active { background-color: #F4F7F2; color: #5D7A5D; border-color: #E5EADF; }
    .status-archived { background-color: #F7F7F7; color: #666; border-color: #EDEDED; }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-md-flex justify-content-between align-items-start mb-5">
        <div>
            <h1 class="display-5 merriweather mb-1 text-dark">Package Registry</h1>
            <p class="text-muted">Comprehensive management of service tiers across estates.</p>
        </div>
        <div class="text-md-end mt-3 mt-md-0">
            <div class="stat-label mb-1">Estate Status</div>
            <div style="color: var(--accent-brown); font-weight: 700;">
                <i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i>Operational
            </div>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Total SKUs</div>
                <div class="stat-value">42</div>
                <div class="stat-subtext small text-muted">Across 8 global venues</div>
                <i class="bi bi-box-seam stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Revenue Impact</div>
                <div class="stat-value">$1.2M</div>
                <div class="stat-subtext small text-muted">Projected Q3</div>
                <i class="bi bi-bank stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="stat-card shadow-sm">
                <div class="stat-label">Active Ratio</div>
                <div class="stat-value">86%</div>
                <div class="stat-subtext small text-muted">High inventory health</div>
                <i class="bi bi-check2-circle stat-icon-bg"></i>
            </div>
        </div>
    </div>

    <!-- Table Controls -->
    <div class="d-flex justify-content-between align-items-center mt-5">
        <ul class="nav-tabs-custom">
            <li><a href="#" class="tab-link active">ACTIVE PACKAGES</a></li>
            <li><a href="#" class="tab-link">ARCHIVED</a></li>
            <li><a href="#" class="tab-link">DRAFTS</a></li>
        </ul>
        <button class="btn-create">+ CREATE</button>
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
                    <tr>
                        <td class="small fw-bold text-secondary">PKG-PLT-001</td>
                        <td class="small fw-extrabold text-dark">VNU-LON-RESERVE</td>
                        <td>
                            <div class="pkg-name-main">Platinum Gala</div>
                            <div class="small fw-bold text-muted text-uppercase" style="font-size: 9px;">Premium Selection</div>
                        </td>
                        <td>
                            <span class="pill-inclusion">5-Course</span>
                            <span class="pill-inclusion">Open Bar</span>
                            <span class="pill-inclusion">Valet</span>
                        </td>
                        <td class="price-text">$225.00</td>
                        <td><span class="status-tag status-active">ACTIVE</span></td>
                        <td class="text-center text-muted cursor-pointer"><i class="bi bi-three-dots"></i></td>
                    </tr>
                    <tr>
                        <td class="small fw-bold text-secondary">PKG-GLD-042</td>
                        <td class="small fw-extrabold text-dark">VNU-NYC-LOFT</td>
                        <td>
                            <div class="pkg-name-main">Gold Catering</div>
                            <div class="small fw-bold text-muted text-uppercase" style="font-size: 9px;">Signature Choice</div>
                        </td>
                        <td>
                            <span class="pill-inclusion">3-Course</span>
                            <span class="pill-inclusion">Wine Pairing</span>
                        </td>
                        <td class="price-text">$165.00</td>
                        <td><span class="status-tag status-active">ACTIVE</span></td>
                        <td class="text-center text-muted cursor-pointer"><i class="bi bi-three-dots"></i></td>
                    </tr>
                    <tr>
                        <td class="small fw-bold text-secondary">PKG-SLV-019</td>
                        <td class="small fw-extrabold text-dark">VNU-PAR-ESTATE</td>
                        <td>
                            <div class="pkg-name-main">Silver Buffet</div>
                            <div class="small fw-bold text-muted text-uppercase" style="font-size: 9px;">Essential Selection</div>
                        </td>
                        <td>
                            <span class="pill-inclusion">Buffet</span>
                            <span class="pill-inclusion">Coffee</span>
                        </td>
                        <td class="price-text">$95.00</td>
                        <td><span class="status-tag status-archived">ARCHIVED</span></td>
                        <td class="text-center text-muted cursor-pointer"><i class="bi bi-three-dots"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Table Footer -->
        <div class="bg-light p-3 px-4 d-flex justify-content-between align-items-center">
            <div class="small fw-bold text-muted">Showing 1 to 3 of 42 packages</div>
            <div class="d-flex gap-2">
                <button class="btn btn-white btn-sm border shadow-sm px-2"><i class="bi bi-chevron-left text-muted"></i></button>
                <button class="btn btn-white btn-sm border shadow-sm px-2"><i class="bi bi-chevron-right text-muted"></i></button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>