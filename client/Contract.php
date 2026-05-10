<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('client');

include __DIR__ . '/../config/nav.php';

$active_nav = 'Booking';  
$page_title = 'Booking';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --vb-bg-color: #fbf8f3;
        --vb-accent-tan: #b38b6d;
        --vb-border-light: #e0e0e0;
    }

    /* Contract Box Styling */
    .contract-paper {
        background: white;
        border: 1px solid var(--vb-border-light);
        border-radius: 8px;
        padding: 4rem;
        max-width: 900px;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    
    .vb-serif { font-family: 'Bodoni Moda', serif; }
    
    .info-label { 
        font-size: 0.75rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 1px;
        color: var(--vb-accent-tan);
        margin-bottom: 0.5rem;
    }

    .section-header { 
        font-size: 1.2rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        margin-top: 2.5rem; 
        margin-bottom: 1rem; 
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 5px;
    }

    .contract-text { 
        line-height: 1.8; 
        color: #444; 
        margin-bottom: 1.5rem; 
        font-size: 0.95rem;
    }

    .sig-line { 
        border-top: 1px solid #333; 
        margin-top: 5rem; 
        text-align: center; 
        padding-top: 0.8rem; 
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .btn-print { 
        background-color: #12192a; 
        color: white; 
        font-size: 0.85rem; 
        padding: 0.5rem 1.5rem; 
        border-radius: 4px; 
        border: none; 
        transition: opacity 0.2s;
    }
    .btn-print:hover { opacity: 0.9; color: white; }

    /* Print logic */
    @media print {
        .sidebar, .vb-page-header, .notif-bell { display: none !important; }
        .vb-main-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .contract-paper { border: none !important; box-shadow: none !important; padding: 0 !important; max-width: 100% !important; }
    }
</style>

<div class="container-fluid">
    <!-- Header with Print Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">Legal Agreement</h1>
        <button class="btn-print shadow-sm" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Print Contract
        </button>
    </div>

    <!-- The "Paper" Document -->
    <div class="contract-paper mb-5">
        <div class="d-flex align-items-center mb-4">
            <img src="/venuebook/assets/images/Logo.svg" style="width: 28px; margin-right: 12px;">
            <span class="vb-serif fw-bold" style="font-size: 1.4rem; letter-spacing: 1px;">VENUEBOOK</span>
        </div>
        
        <hr class="mb-5 opacity-10">

        <!-- Parties Involved -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="info-label">Client / Licensee</div>
                <div class="fw-bold fs-5">John Wick</div>
                <div class="small text-muted">Continental Corp.</div>
            </div>
            <div class="col-6">
                <div class="info-label">Administrator</div>
                <div class="fw-bold fs-5">Mary Santos</div>
                <div class="small text-muted">Estate Operations Manager</div>
            </div>
        </div>

        <!-- Agreement Text -->
        <h2 class="section-header vb-serif">I. General Agreement</h2>
        <p class="contract-text">
            The Client hereby agrees to pay the Administrator the total amount of <strong>$50,000.00</strong> on or before the specified event date (<strong>September 20, 2026</strong>). It is understood that if the Client fails to settle the remaining balance by this date, the initial downpayment shall be deemed non-refundable as per company policy.
        </p>

        <h2 class="section-header vb-serif">II. Venue Assignment</h2>
        <p class="contract-text">
            <strong>The Grand Ballroom</strong><br>
            Continental Hotel, 100 Beaver St, New York City, NY 10005.
        </p>

        <h2 class="section-header vb-serif">III. Service Package</h2>
        <p class="contract-text">
            <strong>Elite Wedding & Event Package:</strong> Comprehensive venue access for 12 hours, customized gourmet catering for 200 guests, full AV support, and dedicated event concierge.
        </p>

        <h2 class="section-header vb-serif">IV. Event Schedule</h2>
        <p class="contract-text">
            Scheduled Date: <strong>Saturday, September 20, 2026</strong><br>
            Access Time: 10:00 AM — 11:59 PM
        </p>

        <!-- Signatures -->
        <div class="row mt-5 pt-4">
            <div class="col-5">
                <div class="sig-line text-muted">Client's Signature</div>
            </div>
            <div class="col-2"></div>
            <div class="col-5">
                <div class="sig-line text-muted">Administrator's Signature</div>
            </div>
        </div>
        
        <div class="text-center mt-5">
            <small class="text-muted" style="font-size: 10px;">Generated electronically via VenueBook Estate Management System</small>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>