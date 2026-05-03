<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["organizer"] ?? [];
$active_nav = 'Dashboard';  
$page_title = 'Dashboard';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    /* Internal CSS for custom aesthetic touches not covered by Bootstrap */
    :root {
        --gold: #8e734b;
        --navy: #0e1b2d;
    }
    .member-portal-tag { font-size: 10px; font-weight: 800; color: var(--gold); letter-spacing: 1px; }
    .font-cinzel { font-family: 'Cinzel', serif; }
    
    /* Featured Card Styling */
    .featured-card { 
        height: 350px; 
        background: url('https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&q=80&w=1000') center/cover;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }
    .featured-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(transparent, rgba(14, 27, 45, 0.95));
        display: flex; flex-direction: column; justify-content: flex-end;
    }

    /* Timeline styling */
    .activity-timeline { list-style: none; padding-left: 0; }
    .activity-item { position: relative; padding-left: 45px; margin-bottom: 25px; }
    .activity-item::before {
        content: ""; position: absolute; left: 16px; top: 32px; bottom: -25px;
        width: 1px; background: #dee2e6;
    }
    .activity-item:last-child::before { display: none; }
    .act-icon {
        position: absolute; left: 0; top: 0;
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 12px;
    }
</style>

<!-- Main Dashboard Content -->
<div class="container-fluid">
    
    <!-- Header Row -->
    <div class="d-md-flex justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="member-portal-tag text-uppercase">Member Portal</span>
            <h1 class="font-cinzel display-5 fw-bold text-navy mt-1">Welcome back, Cherry</h1>
            <p class="text-muted mb-0">Your curated dashboard for bespoke event planning and exclusive venue access.</p>
        </div>
        <button class="btn btn-dark px-4 py-2 mt-3 mt-md-0">Book Your Next Event &rarr;</button>
    </div>

    <div class="row g-4">
        <!-- Left Column: Featured & Upcoming -->
        <div class="col-12 col-xl-8">
            
            <!-- Featured Card -->
            <div class="featured-card mb-4 shadow-sm">
                <div class="featured-overlay p-4">
                    <span class="text-warning fw-bold small mb-1">CONFIRMED BOOKING</span>
                    <h2 class="text-white font-cinzel">The Skyline Penthouse Gala</h2>
                    <p class="text-light opacity-75 mb-4">Friday, October 24th • 7:00 PM</p>
                    
                    <div class="d-flex flex-wrap align-items-center gap-4 border-top border-secondary pt-3">
                        <div>
                            <label class="d-block text-secondary small text-uppercase">Guest Count</label>
                            <span class="text-white fw-bold">120 Guests</span>
                        </div>
                        <div>
                            <label class="d-block text-secondary small text-uppercase">Status</label>
                            <span class="text-white fw-bold">Finalized</span>
                        </div>
                        <button class="btn btn-light btn-sm ms-auto fw-bold px-3">View Logistics</button>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 fw-bold mb-0">Upcoming Events</h3>
                <a href="#" class="text-decoration-none small fw-bold" style="color: var(--gold);">View All</a>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-warning-subtle text-warning p-2 rounded">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <span class="badge bg-light text-dark border">PENDING</span>
                        </div>
                        <h4 class="h6 fw-bold mb-1">Executive Tasting Dinner</h4>
                        <p class="small text-muted mb-2">Nov 12, 2024 • Maison de Luxe</p>
                        <div class="mt-auto small fw-bold"><i class="fa-solid fa-users me-1"></i> 12 Guests</div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-success-subtle text-success p-2 rounded">
                                <i class="fa-solid fa-wheat-awn"></i>
                            </div>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">CONFIRMED</span>
                        </div>
                        <h4 class="h6 fw-bold mb-1">Anniversary Reception</h4>
                        <p class="small text-muted mb-2">Dec 05, 2024 • The Glass Garden</p>
                        <div class="mt-auto small fw-bold"><i class="fa-solid fa-users me-1"></i> 45 Guests</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Activity & Support -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm p-4" style="background-color: #f8f7f2;">
                <h3 class="h6 fw-bold mb-4">Recent Activity</h3>
                
                <ul class="activity-timeline">
                    <li class="activity-item">
                        <div class="act-icon bg-dark text-white"><i class="fa-solid fa-file-invoice"></i></div>
                        <strong class="d-block small">Invoice Paid</strong>
                        <p class="small text-muted mb-1">Skyline Penthouse - Final Payment</p>
                        <span class="text-uppercase fw-bold text-secondary" style="font-size: 9px;">2 Hours Ago</span>
                    </li>
                    <li class="activity-item">
                        <div class="act-icon bg-info-subtle text-info"><i class="fa-solid fa-comment-dots"></i></div>
                        <strong class="d-block small">Message from Concierge</strong>
                        <p class="small text-muted mb-1">Menu selections updated for Tasting Dinner</p>
                        <span class="text-uppercase fw-bold text-secondary" style="font-size: 9px;">Yesterday</span>
                    </li>
                    <li class="activity-item">
                        <div class="act-icon bg-secondary-subtle text-secondary"><i class="fa-solid fa-calendar-minus"></i></div>
                        <strong class="d-block small">Venue Booking Modified</strong>
                        <p class="small text-muted mb-1">Guest count updated for Anniversary Reception</p>
                        <span class="text-uppercase fw-bold text-secondary" style="font-size: 9px;">Oct 12</span>
                    </li>
                </ul>

                <div class="card border-warning-subtle bg-white p-3 mt-4">
                    <label class="member-portal-tag d-block mb-2">MEMBER SUPPORT</label>
                    <p class="small text-muted mb-3">Your dedicated digital concierge is available 24/7 for tailored assistance.</p>
                    <button class="btn btn-outline-dark btn-sm w-100 fw-bold">Chat with Concierge</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>