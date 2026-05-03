<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["organizer"] ?? [];
$active_nav = 'Booking';  
$page_title = 'Booking';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --bg-cream: #f8f6f1;
        --navy: #0e1b2d;
        --accent-gold: #8e734b;
    }
    .font-cinzel { font-family: 'Cinzel', serif; }
    
    /* Booking Card Styles */
    .booking-card {
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .booking-card.overdue {
        border-left: 5px solid #dc3545;
    }
    .venue-thumb {
        width: 140px; height: 110px;
        object-fit: cover;
        border-radius: 8px;
    }
    .info-label {
        font-size: 10px;
        font-weight: 800;
        color: #b0b0b0;
        display: block;
        margin-bottom: 2px;
    }

    /* Widget Styles */
    .widget-card {
        border-radius: 12px;
        border: none;
    }
    .bg-navy { background-color: var(--navy); }
    .bg-soft-gold { background-color: #f1f0e9; }
    
    /* Timeline Dots */
    .timeline-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <header class="mb-5">
        <h1 class="font-cinzel display-6 fw-bold text-navy">My Bookings</h1>
        <p class="text-muted">Manage your corporate events, track payment deadlines, and finalize contracts.</p>
    </header>

    <div class="row g-4">
        <!-- Left: Booking List -->
        <div class="col-12 col-xl-8">
            
            <!-- Card 1: Confirmed -->
            <div class="card booking-card border-0 shadow-sm mb-4 p-4">
                <div class="d-md-flex gap-4">
                    <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=400" class="venue-thumb mb-3 mb-md-0" alt="Venue">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h3 class="h5 fw-bold mb-0">Q4 Annual Leadership Summit</h3>
                            <span class="badge bg-light text-secondary border px-2 py-1">CONFIRMED</span>
                        </div>
                        <p class="small text-muted mb-3">Grand Horizon Ballroom • Dec 14, 2026</p>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="info-label text-uppercase">Booking Status</label>
                                <p class="small fw-bold mb-0 text-success">● Venue Secured</p>
                            </div>
                            <div class="col-6">
                                <label class="info-label text-uppercase">Payment Status</label>
                                <p class="small fw-bold mb-0" style="color: var(--accent-gold);">◓ Partial - $12,500 Remaining</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-dark btn-sm px-4">Make Payment</button>
                    <button class="btn btn-outline-secondary btn-sm px-4">View Contract</button>
                </div>
            </div>

            <!-- Card 2: Overdue -->
            <div class="card booking-card overdue border-0 shadow-sm mb-4 p-4">
                <div class="d-md-flex gap-4">
                    <img src="https://meetinazerbaijan.com/storage/2022/03/16/MwG5kF87k5w5dtSX5gZUKIlRWd5zQx84YA0m6HPv.jpg" class="venue-thumb mb-3 mb-md-0" alt="Venue">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h3 class="h5 fw-bold mb-0 text-danger">Tech Innovators Gala</h3>
                            <span class="badge bg-danger-subtle text-danger px-2 py-1">PAYMENT OVERDUE</span>
                        </div>
                        <p class="small text-muted mb-3">Skyline Terrace • Nov 22, 2026</p>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="info-label text-uppercase">Booking Status</label>
                                <p class="small fw-bold mb-0">⏳ Pending Confirmation</p>
                            </div>
                            <div class="col-6">
                                <label class="info-label text-uppercase text-danger">Payment Deadline</label>
                                <p class="small fw-bold mb-0 text-danger">⚠ Expired: Oct 28, 2026</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-danger btn-sm px-4">! Settle Balance</button>
                    <button class="btn btn-outline-secondary btn-sm px-4">View Contract</button>
                </div>
            </div>
        </div>

        <!-- Right: Widgets -->
        <div class="col-12 col-xl-4">
            
            <!-- Payment Widget -->
            <div class="card widget-card bg-navy text-white p-4 mb-4">
                <h3 class="h6 fw-bold mb-4">Payment Overview</h3>
                <div class="d-flex justify-content-between mb-2 small opacity-75">
                    <span>Total Committed</span>
                    <strong>$48,200</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 small opacity-75">
                    <span>Amount Paid</span>
                    <strong>$32,000</strong>
                </div>
                <hr class="border-secondary mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="small fw-bold">Balance Due</span>
                    <span class="h4 fw-bold mb-0" style="color: #d4af37;">$16,200</span>
                </div>
                <button class="btn btn-light btn-sm w-100 fw-bold py-2">Download Tax Receipt</button>
            </div>

            <!-- Deadlines Widget -->
            <div class="card widget-card bg-soft-gold p-4">
                <h3 class="h6 fw-bold mb-4">Key Deadlines</h3>
                <div class="d-flex align-items-start mb-4">
                    <span class="timeline-dot bg-danger mt-1"></span>
                    <div>
                        <span class="text-muted fw-bold" style="font-size: 10px;">NOV 05</span>
                        <p class="small fw-bold mb-0">Catering Selection Due</p>
                        <small class="text-muted">Q4 Summit</small>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-2">
                    <span class="timeline-dot bg-primary mt-1"></span>
                    <div>
                        <span class="text-muted fw-bold" style="font-size: 10px;">NOV 12</span>
                        <p class="small fw-bold mb-0">Guest List Finalization</p>
                        <small class="text-muted">Tech Innovators Gala</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>