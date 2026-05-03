<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["admin"] ?? [];
$active_nav = 'Booking';  
$page_title = 'Booking';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --accent-red: #B23B3B;
        --gold-accent: #C19A6B;
        --navy-dark: #0A1128;
    }
    .font-playfair { font-family: 'Playfair Display', serif; }

    /* Admin Stat Cards */
    .stat-card { 
        background: white; 
        border-radius: 4px; 
        padding: 1.5rem; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.02); 
    }
    .stat-label { font-size: 0.7rem; font-weight: 700; color: #8E8E8E; text-transform: uppercase; letter-spacing: 1px; }
    .stat-value { font-size: 2.2rem; font-weight: 400; display: block; margin-top: 5px; }
    .text-flagged { color: var(--accent-red); }

    /* Table & Status Styling */
    .table-container { background: white; border-radius: 4px; border: 1px solid #EAE5DF; overflow: hidden; }
    .table thead th { background-color: #F9F9F9; font-size: 0.65rem; text-transform: uppercase; padding: 1rem; font-weight: 800; }
    .table tbody td { padding: 1.2rem 1rem; vertical-align: middle; font-size: 0.85rem; }
    
    .client-avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold; margin-right: 10px; }
    
    .badge-pending { background-color: #F3C65F; color: #000; border-radius: 4px; padding: 6px 12px; font-weight: 700; font-size: 0.7rem; }
    .badge-approved { background-color: #E8F5E9; color: #2E7D32; border-radius: 4px; padding: 6px 12px; font-weight: 700; font-size: 0.7rem; }

    /* Workflow Banner */
    .workflow-section { 
        background-color: var(--navy-dark); 
        color: white; padding: 2.5rem; border-radius: 4px; 
    }
    .btn-white { background: white; color: var(--navy-dark); font-weight: 600; border-radius: 2px; padding: 0.8rem 1.5rem; border: none; font-size: 0.8rem; }
    .btn-outline-custom { border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.8rem 1.5rem; font-size: 0.8rem; border-radius: 2px; }
</style>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-md-flex justify-content-between align-items-center mb-5">
        <div>
            <div class="stat-label mb-1">Event Administration</div>
            <h2 class="display-6 fw-normal font-playfair">Booking Approvals</h2>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <input type="text" class="form-control" placeholder="Search Ref or Client..." style="max-width: 250px;">
            <button class="btn btn-outline-secondary btn-sm px-3">Date Range</button>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Pending Requests</span>
                <span class="stat-value">24</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Approved Today</span>
                <span class="stat-value">12</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Flagged Items</span>
                <span class="stat-value text-flagged">03</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card h-100">
                <span class="stat-label">Est. Revenue</span>
                <span class="stat-value">$42.8k</span>
            </div>
        </div>
    </div>

    <!-- Approvals Table -->
    <div class="table-container shadow-sm mb-5">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Client</th>
                        <th>Venue</th>
                        <th>Package</th>
                        <th>Event Date</th>
                        <th>Total</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-muted">#BK-9421</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="client-avatar" style="background-color: #D1E3F8; color: #4A90E2;">ER</div>
                                <strong class="small">Eleanor Rigby</strong>
                            </div>
                        </td>
                        <td class="text-muted small">Grand Ballroom East</td>
                        <td class="text-muted small">Premium Gala</td>
                        <td class="text-muted small">Dec 12, 2026</td>
                        <td><strong class="small">$12,400</strong></td>
                        <td style="color: var(--accent-red); font-size: 0.75rem; font-weight: 600;">Nov 30, 2026</td>
                        <td><span class="badge-pending">PENDING</span></td>
                        <td>
                            <span class="text-success fw-bold cursor-pointer me-2" style="font-size: 0.65rem;">APPROVE</span>
                            <span class="text-danger fw-bold cursor-pointer" style="font-size: 0.65rem;">REJECT</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">#BK-8834</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="client-avatar" style="background-color: #FDEBD0; color: #E67E22;">JM</div>
                                <strong class="small">Julian Mars</strong>
                            </div>
                        </td>
                        <td class="text-muted small">The Glass Pavilion</td>
                        <td class="text-muted small">Corporate Day</td>
                        <td class="text-muted small">Jan 05, 2027</td>
                        <td><strong class="small">$8,250</strong></td>
                        <td class="text-muted small">Dec 15, 2026</td>
                        <td><span class="badge-approved">APPROVED</span></td>
                        <td class="text-muted fst-italic" style="font-size: 0.65rem;">Validated</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Workflow Section -->
    <div class="workflow-section d-md-flex justify-content-between align-items-center mb-5">
        <div class="mb-4 mb-md-0">
            <h4 class="h3 fw-normal mb-3 font-playfair">Elite Approval Workflow</h4>
            <p class="mb-0 opacity-75 small" style="max-width: 600px;">
                Ensure all booking conditions are met before final verification. Status changes will trigger immediate client notifications through the Estate Reserve portal.
            </p>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-white px-4">POLICY</button>
            <button class="btn btn-outline-custom px-4">RESOLUTION</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>