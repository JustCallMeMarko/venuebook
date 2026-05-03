<?php require_once __DIR__ . '/../includes/top_sidebar.php'; ?>

<div class="col-12 col-lg-9 p-1 mx-auto">
    <h3 class="font-open">Settings</h3>
    <p class="font-open" style="font-size: 14px;">Customize your profile, personal information, language, and theme settings.</p>

    <form class="p-4 rounded-3 border border-dark-subtle">
        <section class="mb-4">
            <h4 class="font-open">Profile</h4>
            <p class="font-open" style="font-size: 14px;">View and edit your personal information, including your name and profile picture.</p>
            <div class="d-flex gap-4 align-items-center">
                <img src="https://th.bing.com/th/id/OIP.WTz8r0NiRmcRWxwi4nqqWAHaJ7?o=7rm=3&rs=1&pid=ImgDetMain&o=7&rm=3" alt="Profile Picture" class="rounded-circle object-fit-cover" width="70" height="70">
                <button class="btn btn-light border border-dark-subtle" style="height: fit-content;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-upload me-2" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                        <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                    </svg>
                    Upload New Picture
                </button>
            </div>
        </section>
        <section class="mb-4">
            <h4 class="font-open">Personal Information</h4>
            <p class="font-open" style="font-size: 14px;">Manage your information details, including your name, email address, phone number, and company/oraganization name.</p>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName">
                </div>
                <div class="col-12 col-lg-6">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp">
                </div>
                <div class="col-12 col-lg-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="company" class="form-label">Company/Organization Name</label>
                    <input type="text" class="form-control" id="company">
                </div>
            </div>
        </section>
        <section class="mb-4">
            <h4 class="font-open">Account</h4>
            <p class="font-open" style="font-size: 14px;">Update your password and select your preferred language for a personalized experience.</p>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="text" class="form-control" id="password">
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label">Language</label>
                    <div class="dropdown">
                        <button class="btn btn-light border border-dark-subtle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            🇺🇸 English (US)
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">ph Filipino (PH)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <button type="submit" class="btn btn-success btn-disabled mb-4">Save All Changes</button>
        <div class="d-flex flex-column flex-lg-row gap-2">

            <button class="btn btn-dark px-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-left me-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                    <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
                </svg>
                Logout
            </button>
            <button class="btn btn-danger px-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash me-1" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                </svg>
                Delete Account
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/bottom_sidebar.php'; ?>