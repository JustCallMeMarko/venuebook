<?php
include __DIR__ . '/../config/nav.php';

$active_nav = 'Venue';  
$page_title = 'Step 2 - Package Selection';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --vb-dark-blue: #131E2D;
        --vb-light-brown: #9E8062;
        --vb-muted-brown: #8D765E;
        --vb-active-bg: #FAF6ED;
    }

    /* Progress Bar */
    .step-container { display: flex; justify-content: space-between; position: relative; margin: 0 auto 3.5rem; max-width: 800px; }
    .step-container::before { content: ''; position: absolute; top: 15px; left: 40px; right: 40px; height: 1px; background: var(--vb-dark-blue); z-index: 0; }
    .step-item { position: relative; z-index: 1; text-align: center; width: 80px; }
    .step-circle { 
        width: 30px; height: 30px; background: #E2DFD9; border-radius: 50%; 
        margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; 
        font-weight: bold; font-size: 12px; 
    }
    .step-item.active .step-circle { background: var(--vb-dark-blue); color: white; }
    .step-item.completed .step-circle { background: #A2BB92; color: white; }
    .step-text { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

    /* Package Cards */
    .vb-card { background: white; border-radius: 12px; border: 1px solid transparent; margin-bottom: 1.2rem; padding: 1.5rem; transition: 0.3s; cursor: pointer; }
    .vb-card:hover { border-color: #eee; shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .vb-card.selected { border-color: var(--vb-light-brown); position: relative; background-color: #fff; box-shadow: 0 4px 15px rgba(158, 128, 98, 0.1); }
    .package-img { width: 150px; height: 150px; border-radius: 8px; object-fit: cover; }
    .pkg-name { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; }
    .recommended-badge { 
        background: var(--vb-light-brown); color: white; padding: 3px 10px; 
        border-radius: 4px; font-size: 10px; font-weight: 700; 
        text-transform: uppercase; position: absolute; top: -12px; left: 185px; 
    }

    /* Summary Sidebar */
    .summary-card { background: white; border: 1px solid #E2DFD9; border-radius: 12px; padding: 1.8rem; position: sticky; top: 20px; }
    .summary-label { font-size: 10px; font-weight: 700; color: #888; text-transform: uppercase; margin-bottom: 2px; }
    .summary-val { font-weight: 600; margin-bottom: 1rem; font-size: 0.95rem; }
    .dotted-divider { border-top: 1px dotted #ccc; margin: 1.5rem 0; }
    .btn-next { background: var(--vb-dark-blue); color: white; width: 100%; padding: 12px; border-radius: 8px; border: none; font-weight: 600; transition: 0.3s; }
    .btn-next:hover { background: #1e2d44; }
</style>

<div class="container-fluid px-4">
    <!-- Progress Stepper -->
    <div class="step-container">
        <div class="step-item completed">
            <div class="step-circle"><i class="bi bi-check2"></i></div>
            <div class="step-text">Venue</div>
        </div>
        <div class="step-item active">
            <div class="step-circle">2</div>
            <div class="step-text">Package</div>
        </div>
        <div class="step-item">
            <div class="step-circle">3</div>
            <div class="step-text">Payment</div>
        </div>
    </div>

    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold font-playfair mb-2">Choose Your Event Package</h1>
        <p class="text-muted">Tailored experiences for the Grand Heritage Ballroom</p>
    </div>

    <div class="row gx-lg-5">
        <!-- Left Column: Package Options -->
        <div class="col-12 col-lg-8">
            
            <!-- Option 1 -->
            <div class="vb-card d-md-flex gap-4 shadow-sm">
                <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&q=80&w=300" class="package-img mb-3 mb-md-0" alt="Essential">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="pkg-name">The Essential Soirée</span>
                        <span class="fw-bold fs-5">$1,200</span>
                    </div>
                    <p class="text-muted small">A sophisticated baseline for intimate gatherings.</p>
                    <div class="row g-2 mt-2">
                        <div class="col-md-6 small text-muted"><i class="bi bi-check2 me-2 text-success"></i> 4 Hours Venue Access</div>
                        <div class="col-md-6 small text-muted"><i class="bi bi-check2 me-2 text-success"></i> Standard AV Equipment</div>
                    </div>
                </div>
            </div>

            <!-- Option 2 (Selected/Recommended) -->
            <div class="vb-card selected d-md-flex gap-4 shadow-sm">
                <span class="recommended-badge">Recommended</span>
                <i class="bi bi-check-circle-fill position-absolute" style="top:20px; right:20px; color:var(--vb-light-brown); font-size: 24px;"></i>
                <img src="https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&q=80&w=300" class="package-img mb-3 mb-md-0" alt="Signature">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="pkg-name">Signature Grandeur</span>
                        <span class="fw-bold fs-5 text-brown" style="color: var(--vb-light-brown);">$2,800</span>
                    </div>
                    <p class="text-muted small">Our most popular choice, featuring premium catering and custom lighting.</p>
                    <div class="row g-2 mt-2">
                        <div class="col-md-6 small text-muted"><i class="bi bi-check2 me-2 text-success"></i> 8 Hours Venue Access</div>
                        <div class="col-md-6 small text-muted"><i class="bi bi-check2 me-2 text-success"></i> 3-Course Gourmet Menu</div>
                    </div>
                </div>
            </div>

            <!-- Option 3 -->
            <div class="vb-card d-md-flex gap-4 shadow-sm">
                <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=300" class="package-img mb-3 mb-md-0" alt="Elite">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="pkg-name">The Elite Gala</span>
                        <span class="fw-bold fs-5">$5,500</span>
                    </div>
                    <p class="text-muted small">Total exclusivity with bespoke luxury services and valet for all guests.</p>
                    <div class="row g-2 mt-2">
                        <div class="col-md-6 small text-muted"><i class="bi bi-check2 me-2 text-success"></i> Unlimited Access</div>
                        <div class="col-md-6 small text-muted"><i class="bi bi-check2 me-2 text-success"></i> Full Event Concierge</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Summary Sidebar -->
        <div class="col-12 col-lg-4">
            <aside class="summary-card shadow-sm mt-4 mt-lg-0">
                <h2 class="h5 fw-bold font-playfair border-bottom pb-3 mb-4">Booking Summary</h2>
                
                <div class="summary-label">Selected Venue</div>
                <div class="summary-val">Grand Heritage Ballroom</div>
                
                <div class="summary-label">Selected Package</div>
                <div class="summary-val">Signature Grandeur</div>
                
                <div class="summary-label">Date</div>
                <div class="summary-val text-dark">February 22, 2026</div>

                <div class="dotted-divider"></div>

                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Venue Hire</span>
                    <span>$5,000.00</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-2">
                    <span>Package Service</span>
                    <span>$2,800.00</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-3">
                    <span>Service Fee (5%)</span>
                    <span>$390.00</span>
                </div>

                <div class="d-flex justify-content-between align-items-center fw-bold fs-5 mb-4 py-3 border-top">
                    <span>Total Est.</span>
                    <span style="color: var(--vb-dark-blue); font-size: 1.4rem;">$8,190.00</span>
                </div>

                <button class="btn-next mb-3">
                    Next: Date & Guests <i class="bi bi-arrow-right ms-2"></i>
                </button>
                
                <div class="text-center">
                    <a href="venue_selection.php" class="text-dark text-decoration-none small fw-bold opacity-75">Back to Venue Selection</a>
                </div>

                <div class="mt-4 p-3 rounded" style="background:#FAF6ED; font-size: 11px; color:#7D7263; border: 1px solid #E2DFD9;">
                    <i class="bi bi-info-circle me-1"></i> Prices are estimates based on standard guest counts. Final adjustments applied in step 3.
                </div>
            </aside>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>