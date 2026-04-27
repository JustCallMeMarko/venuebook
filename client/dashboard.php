<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/venuebook/assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" href="/venuebook/favicon.ico" type="image/x-icon">
    <title>VenueBook Client Dashboard</title>
</head>

<body class="bgs-primary">
    <div class="row gx-0">
        <div class="col-12 col-lg-3 bg-light px-4 py-3 border-end border-dark-subtle d-flex flex-column gap-3 sticky-top custom-sidebar">
            <div class="d-flex justify-content-between">
                <a class="navbar-brand font-cinzel fs-3 mb-lg-2" href="/venuebook/client/dashboard.php" style="display: flex; align-items: center; font-weight: bold;">
                    <img src="/venuebook/assets/images/Logo.svg" alt="Logo" width="30" height="30" style="margin-right: 6px;">
                    VenueBook
                </a>
                <button class="d-lg-none bg-transparent border-0 p-0" type="button">
                    <img src="/venuebook/assets/images/Burger.svg" alt="Menu Icon" width="26" height="26">
                </button>
            </div>

            <nav class="d-none d-lg-flex flex-row flex-lg-column gap-2">
                <a href="/venuebook/client/dashboard.php" class="sidebar-selected p-2 text-decoration-none fw-semibold d-inline-flex align-items-center">
                    <img src="/venuebook/assets/images/Dashboard.svg" alt="Dashboard Icon" class="me-2" style="width: 16px; height: 16px;">
                    Dashboard</a>
                <a href="/venuebook/client/venue.php" class="sidebar-link p-2 text-decoration-none fw-semibold d-inline-flex align-items-center">
                    <img src="/venuebook/assets/images/Venue.svg" alt="Venue Icon" class="me-2" style="width: 16px; height: 16px;">
                    Venue</a>
                <a href="/venuebook/client/booking.php" class="sidebar-link p-2 text-decoration-none fw-semibold d-inline-flex align-items-center">
                    <img src="/venuebook/assets/images/Booking.svg" alt="Booking Icon" class="me-2" style="width: 16px; height: 16px;">
                    Booking</a>
            </nav>
        </div>

        <div class="col-12 col-lg-9 p-4 mx-auto">
            <!-- CONTENT HERE -->
        </div>
    </div>
</body>

</html>