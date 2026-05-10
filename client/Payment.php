<?php
include __DIR__ . '/../config/nav.php';

// $nav_items  = $nav_config[$_SESSION['role']] ?? [];
$nav_items  = $nav_config["organizer"] ?? [];
$active_nav = 'Venue';  
$page_title = 'Step 3 - Payment';

include __DIR__ . '/../includes/top_sidebar.php';
?>

<style>
    :root {
        --vb-accent-brown: #C19A6B;
        --vb-dark-panel: #17181C;
    }

    /* Progress Steps */
    .steps-container { display: flex; justify-content: center; margin-bottom: 60px; }
    .steps-wrapper { position: relative; display: flex; justify-content: space-between; width: 100%; max-width: 500px; }
    .step-line { position: absolute; top: 14px; left: 0; right: 0; height: 1px; background: #000; z-index: 1; }
    .step-circle { 
        width: 28px; height: 28px; background: #0F172A; color: white; border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; z-index: 2; position: relative; 
    }
    .step-label { position: absolute; top: 35px; font-size: 9px; font-weight: 800; text-transform: uppercase; width: 60px; text-align: center; left: 50%; transform: translateX(-50%); color: #6C757D; }

    /* Credit Card Mockup */
    .credit-card {
        max-width: 380px; background: #3C4245; border-radius: 15px; padding: 25px; color: white;
        aspect-ratio: 1.58 / 1; display: flex; flex-direction: column; justify-content: space-between;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .card-numbers { font-family: 'Space Mono', monospace; font-size: 1.25rem; letter-spacing: 3px; display: flex; justify-content: space-between; }

    /* Custom Inputs */
    .custom-form-group { max-width: 380px; border: 1px solid #DEE2E6; border-radius: 8px; background: white; overflow: hidden; }
    .custom-input-row { display: flex; align-items: center; border-bottom: 1px solid #F0F0F0; }
    .custom-input-row:last-child { border-bottom: none; }
    .input-icon { width: 50px; text-align: center; color: #CCC; }
    .custom-form-group input { border: none; padding: 15px 0; font-size: 14px; width: 100%; outline: none; }

    /* Summary Panel */
    .summary-panel { 
        background-color: var(--vb-dark-panel); color: #A0A0A0; padding: 60px 40px; 
        display: flex; flex-direction: column; border-radius: 12px; height: 100%;
    }
    .total-amount { font-size: 3rem; color: white; font-weight: 500; margin-bottom: 40px; }
    .btn-pay { background: #FAF9F7; border: none; padding: 15px; border-radius: 6px; font-weight: 600; color: #333; width: 100%; transition: 0.3s; }
    .btn-pay:hover { background: #fff; transform: translateY(-2px); }
</style>

<div class="container-fluid">
    <!-- Progress Steps -->
    <div class="steps-container">
        <div class="steps-wrapper">
            <div class="step-line"></div>
            <div class="step-circle">1 <div class="step-label">Venue</div></div>
            <div class="step-circle">2 <div class="step-label">Package</div></div>
            <div class="step-circle">3 <div class="step-label text-dark">Payment</div></div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Left: Payment Form -->
        <div class="col-12 col-xl-7">
            <h2 class="fw-semibold mb-4">Choose a payment method</h2>
            <div class="d-flex gap-2 mb-5">
                <button class="btn btn-outline-dark px-4 active">Card</button>
                <button class="btn btn-outline-secondary px-4">Cash</button>
                <button class="btn btn-outline-secondary px-4">PayPal</button>
            </div>

            <h5 class="mb-3 small fw-bold text-uppercase opacity-75">Card details</h5>
            
            <!-- Credit Card Graphic -->
            <div class="credit-card mb-4">
                <div class="d-flex justify-content-between">
                    <div style="width: 45px; height: 32px; background: #E6AE6A; border-radius: 4px;"></div>
                    <i class="bi bi-wifi text-white fs-4"></i>
                </div>
                <div class="card-numbers my-3">
                    <span>6767</span><span>****</span><span>****</span><span>6767</span>
                </div>
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <div class="small text-uppercase">John Wick</div>
                        <div class="smaller opacity-75">06/07</div>
                    </div>
                    <div class="d-flex">
                        <div style="width: 24px; height: 24px; background: #EB001B; border-radius: 50%; margin-right: -10px;"></div>
                        <div style="width: 24px; height: 24px; background: #F79E1B; border-radius: 50%; opacity: 0.8;"></div>
                    </div>
                </div>
            </div>

            <!-- Card Inputs -->
            <div class="mb-2 small fw-bold">Cardholder Name <i class="bi bi-pencil-square ms-1"></i></div>
            <div class="custom-form-group shadow-sm">
                <div class="custom-input-row">
                    <div class="input-icon"><i class="bi bi-person"></i></div>
                    <input type="text" value="John Wick">
                </div>
                <div class="custom-input-row">
                    <div class="input-icon"><i class="bi bi-credit-card"></i></div>
                    <input type="text" placeholder="1234 1234 1234 1234">
                </div>
                <div class="d-flex">
                    <div class="flex-fill border-end custom-input-row">
                        <input type="text" placeholder="EXP" class="ps-4">
                    </div>
                    <div class="flex-fill custom-input-row">
                        <input type="text" placeholder="CVC" class="ps-4">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Payment Summary -->
        <div class="col-12 col-xl-5">
            <aside class="summary-panel shadow-lg">
                <h3 class="mb-5">Payment Summary</h3>
                
                <div class="d-flex justify-content-between mb-3">
                    <span>Subtotal</span>
                    <span class="text-white fw-medium">$6,760.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Discounts</span>
                    <span class="text-success fw-medium">-$0.00</span>
                </div>
                
                <hr class="border-secondary my-4 opacity-25">
                
                <div class="d-flex justify-content-between mb-3">
                    <span>Processing Fee</span>
                    <span class="text-white fw-medium">$7.00</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Taxes</span>
                    <span class="text-white fw-medium">$0.00</span>
                </div>
                
                <div class="mt-auto">
                    <div class="small fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">Order Total</div>
                    <div class="total-amount font-mono">$6,767.00</div>
                    <form action="actions/process_payment.php" method="POST">
                        <button type="submit" class="btn-pay shadow-sm">Pay now</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/bottom_sidebar.php'; ?>