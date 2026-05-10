<?php
session_start();

// Include nav config
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["organizer"] ?? [];
$active_nav = 'Venue';  
$page_title = 'Step 1 - Venue Selection';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --primary-color: #0c182a;
        --secondary-color: #bf9b74;
        --secondary-light: #f7f3ec;
        --bg-main: #f8f8f7;
    }
    .font-crimson { font-family: 'Crimson Pro', serif; }
    .font-playfair { font-family: 'Playfair Display SC', serif; }

    /* Timeline Styling */
    .steps-container { max-width: 650px; margin: 0 auto 4rem; position: relative; display: flex; justify-content: space-between; align-items: center; }
    .step-line { position: absolute; height: 2px; background: #e2e2e1; width: 100%; top: 20px; z-index: 1; }
    .step-unit { position: relative; z-index: 2; text-align: center; background: var(--bg-main); padding: 0 15px; }
    .step-circle { 
        width: 40px; height: 40px; background: #e2e2e1; border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 700; color: #888; margin: 0 auto 8px; 
    }
    .step-unit.active .step-circle { background: var(--primary-color); color: #fff; }
    .step-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; }
    .step-unit.active .step-label { color: var(--primary-color); }

    /* Main Card & Form */
    .venue-main-card { background: #fff; padding: 2.5rem; border-radius: 12px; }
    .venue-img-large { width: 100%; max-width: 220px; height: 220px; object-fit: cover; border-radius: 10px; }
    .form-section { background: var(--secondary-light); border: 1px solid #eaddca; padding: 2rem; border-radius: 12px; }

    /* Summary Panel */
    .summary-panel { background: #fdfcf9; border: 1px solid #e8e8e8; padding: 2.5rem; border-radius: 12px; position: sticky; top: 20px; }
    .btn-action { background: var(--primary-color); color: #fff; width: 100%; padding: 1rem; border-radius: 8px; font-weight: 600; border: none; transition: 0.3s; }
    .btn-action:hover { background: #152945; color: #fff; }
</style>

<div class="container-fluid">
    <!-- Timeline Steps -->
    <div class="steps-container">
        <div class="step-line"></div>
        <div class="step-unit active">
            <div class="step-circle shadow-sm">1</div>
            <div class="step-label">Venue</div>
        </div>
        <div class="step-unit">
            <div class="step-circle shadow-sm">2</div>
            <div class="step-label">Package</div>
        </div>
        <div class="step-unit">
            <div class="step-circle shadow-sm">3</div>
            <div class="step-label">Payment</div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Left: Details & Form -->
        <div class="col-12 col-xl-8">
            <div class="venue-main-card shadow-sm border-0 mb-4">
                <div class="d-md-flex align-items-start mb-4">
                    <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=400" class="venue-img-large me-md-4 mb-3 mb-md-0 shadow-sm" alt="Venue">
                    <div>
                        <div class="d-flex align-items-baseline flex-wrap">
                            <h2 class="font-crimson display-6 mb-0 me-3">The Grand Garden Venue</h2>
                            <span class="text-secondary fw-bold fs-4">$1,200</span>
                        </div>
                        <p class="text-muted small mt-2">A sophisticated baseline for intimate gatherings and corporate mixers.</p>
                        <div class="row mt-3 g-2">
                            <div class="col-6 small"><i class="far fa-check-circle text-warning me-2"></i> 4 Hours Access</div>
                            <div class="col-6 small"><i class="far fa-check-circle text-warning me-2"></i> AV Equipment</div>
                            <div class="col-12 small"><i class="far fa-check-circle text-warning me-2"></i> Coffee & Tea Service</div>
                        </div>
                    </div>
                </div>

                <!-- Form Inputs -->
                <div class="form-section shadow-sm">
                    <form action="process_step_1.php" method="POST">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Company Name</label>
                                <input type="text" name="company" class="form-control py-2 border-0 shadow-sm" value="John Wick Inc.">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Guest Capacity</label>
                                <div class="input-group shadow-sm">
                                    <input type="text" name="capacity" class="form-control py-2 border-0" value="150 Guests">
                                    <span class="input-group-text bg-white border-0"><i class="fas fa-users text-muted"></i></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Preferred Date</label>
                                <input type="date" name="event_date" class="form-control py-2 border-0 shadow-sm" value="2026-05-20">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Event Time</label>
                                <div class="input-group shadow-sm">
                                    <input type="text" name="event_time" class="form-control py-2 border-0" value="5:00 PM - 11:00 PM">
                                    <span class="input-group-text bg-white border-0"><i class="far fa-clock text-muted"></i></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Summary Sidebar -->
        <div class="col-12 col-xl-4">
            <aside class="summary-panel shadow-sm">
                <h4 class="font-crimson mb-4 pb-2 border-bottom">Booking Summary</h4>
                
                <div class="mb-4">
                    <label class="text-muted fw-bold d-block mb-1" style="font-size:0.7rem; letter-spacing:0.5px; text-transform:uppercase;">Selected Venue</label>
                    <div class="fw-bold">Grand Heritage Ballroom</div>
                    <small class="text-muted">Capacity: 250 Guests • $5,000 Base</small>
                </div>

                <div class="py-3 border-top border-bottom" style="border-style: dashed !important; border-color: #dee2e6 !important;">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Venue Hire</span>
                        <span class="fw-bold">$5,000.00</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Service Fee (5%)</span>
                        <span class="fw-bold">$390.00</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <span class="fw-bold">Estimated Total</span>
                    <h3 class="fw-bold mb-0 text-navy">$8,190.00</h3>
                </div>

                <button class="btn-action shadow-sm">
                    Proceed to Package <i class="fas fa-arrow-right ms-2"></i>
                </button>
                
                <div class="mt-4 p-3 bg-white rounded border small text-muted d-flex align-items-start shadow-sm">
                    <i class="fas fa-info-circle text-warning me-2 mt-1"></i>
                    <span>Prices are estimates. Final totals will be adjusted based on the catering package selected in the next step.</span>
                </div>
            </aside>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>