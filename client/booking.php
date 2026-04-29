<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/venuebook/assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" href="/venuebook/favicon.ico" type="image/x-icon">
    <title>VenueBook Client Booking</title>
</head>

<body class="bgs-primary">
    <div class="row gx-0">
        <header class="col-12 col-lg-3 bg-light px-4 py-3 border-end border-dark-subtle d-flex flex-column gap-3 sticky-top custom-sidebar">
            <div class="d-flex justify-content-between">
                <a class="navbar-brand font-cinzel fs-3 mb-lg-2" href="/venuebook/client/dashboard.php" style="display: flex; align-items: center; font-weight: bold;">
                    <img src="/venuebook/assets/images/Logo.svg" alt="Logo" width="30" height="30" style="margin-right: 6px;">
                    VenueBook
                </a>
                <button class="d-lg-none bg-transparent border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-expanded="false" aria-label="Open navigation menu">
                    <img src="/venuebook/assets/images/Burger.svg" alt="Menu Icon" width="26" height="26">
                </button>
            </div>

            <nav class="collapse d-lg-flex flex-column h-100 justify-content-between w-100" id="mobileNav">
                <div class="d-flex flex-column gap-2 pt-2 pb-3 border-bottom border-dark-subtle">
                    <a href="/venuebook/client/dashboard.php" class="sidebar-link p-2 text-decoration-none fw-semibold d-inline-flex align-items-center">
                        <span class="sidebar-icon sidebar-icon-dashboard me-2" aria-hidden="true"></span>
                        Dashboard</a>
                    <a href="/venuebook/client/venue.php" class="sidebar-link p-2 text-decoration-none fw-semibold d-inline-flex align-items-center">
                        <span class="sidebar-icon sidebar-icon-venue me-2" aria-hidden="true"></span>
                        Venue</a>
                    <a href="/venuebook/client/booking.php" class="sidebar-selected p-2 text-decoration-none fw-semibold d-inline-flex align-items-center">
                        <span class="sidebar-icon sidebar-icon-booking me-2" aria-hidden="true"></span>
                        Booking</a>
                </div>

                <div class="pt-3 mt-lg-auto">
                    <div class="d-flex align-items-center w-100 justify-content-between sidebar-link p-2" style="cursor: pointer;">
                        <div class="d-flex align-items-center">
                            <img src="/venuebook/assets/images/person.svg" alt="User Icon" width="40" height="40" class="me-2 bg-dark rounded-1">
                            <div>
                                <h6 class="mb-0 font-mont">Mark Cantimbuhan</h6>
                                <p class="mb-0 font-mont">Event Organizer</p>
                            </div>
                        </div>
                        <button class="btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" stroke="currentColor" stroke-width="0.6" stroke-linecap="round" stroke-linejoin="round" class="bi bi-bell" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                            </svg>
                        </button>
                    </div>
                </div>
            </nav>

        </header>

        <main class="col-12 col-lg-9 p-4 mx-auto">
            <!-- CONTENT HERE -->
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>