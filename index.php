<!DOCTYPE html>
<html lang="en" class="bg-dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>VenueBook</title>
</head>

<body class="bg-dark">
    <header class="position-fixed w-100" style="z-index: 999;">
        <nav class="navbar navbar-expand-lg bgs-primary border-bottom border-dark-subtle">
            <div class="container-fluid">
                <a class="navbar-brand font-cinzel" href="#" style="display: flex; align-items: center; font-weight: bold;">
                    <img src="assets/images/Logo.svg" alt="Logo" width="30" height="30" style="margin-right: 6px;">
                    VenueBook
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link font-open" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-open" href="#venues">Venues</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-open" aria-current="page" href="#how">How</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-open" aria-current="page" href="#providers">For Providers</a>
                        </li>
                    </ul>
                    <button class="btn btn-dark rounded-pill px-3 login-btn" style="display: flex; align-items: center; gap: 8px;">
                        Get Started
                        <img src="assets/images/Arrow_right.svg" alt="Arrow Right Icon" width="18" height="18">
                    </button>
                </div>
            </div>
        </nav>
    </header>
    <main class="bgs-primary rounded-bottom-4">
        <section id="home" class="pt-5 mb-5 container">
            <div class="d-flex flex-column">
                <h1 class="font-cinzel fw-semibold display-3 w-100 w-lg-50">Book the perfect venue. <br class="d-none d-lg-inline"><strong class="fw-bold">Bundle everything</strong> <br class="d-none d-lg-inline">in one place.</h1>
                <button class="btn btn-dark rounded-pill ps-4 pe-3 py-3 fs-6 login-btn" style="display: flex; align-items: center; gap: 8px; width: fit-content;">
                    Book a venue
                    <img src="assets/images/Arrow_right.svg" alt="Arrow Right Icon" width="20" height="20">
                </button>
                <img src="assets/images/Landing_table.png" alt="Corporate Event Table" class="img-fluid mt-3 mt-lg-0 align-self-center align-self-lg-end">
            </div>
        </section>
        <section id="venues" class="container mb-5 pt-5">
            <h2 class="font-cinzel fw-bold m-0">Trending Venues</h2>
            <p class="font-open">Heres our top 3 venues!</p>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100">
                        <img src="https://img.freepik.com/free-photo/3d-rendering-seminar-meeting-banquet-hall-room_105762-1773.jpg?semt=ais_hybrid&w=740&q=80" class="card-img-top" alt="Event Hall" style="height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge text-bg-success mb-2">Recommended</span>
                            <h5 class="card-title fw-semibold">Minimalist Event Hall</h5>
                            <p class="card-text">Capacity: 200 people</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100">
                        <img src="https://www.wtcmanila.com.ph/wp-content/uploads/2019/12/Factors-to-Consider-in-Choosing-an-Event-Venue.png" class="card-img-top" alt="Event Hall" style="height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge text-bg-success mb-2">Recommended</span>
                            <h5 class="card-title fw-semibold">Elegant Banquet Hall</h5>
                            <p class="card-text">Capacity: 200 people</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100">
                        <img src="https://www.wtcmanila.com.ph/wp-content/uploads/2019/12/Factors-to-Consider-in-Choosing-an-Event-Venue.png" class="card-img-top" alt="Event Hall" style="height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge text-bg-success mb-2">Recommended</span>
                            <h5 class="card-title fw-semibold">Modern Conference Room</h5>
                            <p class="card-text">Capacity: 200 people</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="how" class="container mb-5 pt-5">
            <h2 class="font-cinzel fw-bold m-0">Three steps to your next corporate event</h2>
            <p class="font-open">From discovery to confirmed booking in minutes — not days.</p>
            <div class="bgs-secondary w-100 rounded-4 p-4">
                <div class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <div class="h-100 d-flex flex-column bg-light p-3 rounded-3">
                            <h3 class="font-open fw-semibold">Browse & check availability</h3>
                            <p class="font-open fw-medium">Search venues by capacity, location, and event type. View real-time availability calendars to find open dates instantly.</p>
                            <img src="assets/images/Card1.png" alt="Feature Card Image 1">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <div class="h-100 d-flex flex-column bg-light p-3 rounded-3">
                            <h3 class="font-open fw-semibold">Pick a bundle</h3>
                            <p class="font-open fw-medium">Choose a pre-built package that combines the venue's AV setup with a catering service. One place, one booking.</p>
                            <img src="assets/images/Card2.png" alt="Feature Card Image 2">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex flex-column bg-light p-3 rounded-3">
                            <h3 class="font-open fw-semibold">Book & pay downpayment</h3>
                            <div class="row">
                                <p class="col-12 col-lg-6 font-open fw-medium mb-0">Submit your booking request. Once both providers approve, pay a 50% downpayment to lock your date. Track the balance anytime.</p>
                            </div>

                            <div class="row">
                                <div class="col-12 col-lg-6 d-flex flex-column mb-4">
                                    <p class="font-open fw-medium opacity-75 mb-0 mt-2" style="font-size: 12px;">Partnered with</p>
                                    <div class="flex">
                                        <img src="https://download.logo.wine/logo/Apple_Pay/Apple_Pay-Logo.wine.png" alt="Apple Pay Logo" class="img-fluid mb-2" style="width: 15%;">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Google_Pay_Logo.svg/1280px-Google_Pay_Logo.svg.png" alt="Google Pay Logo" class="img-fluid mb-2" style="width: 13%;">
                                    </div>
                                    <button class="btn btn-dark px-4 login-btn" style="width: fit-content;">Book Now</button>
                                </div>
                                <div class="col-12 col-lg-6 mb-4">
                                    <img src="assets/images/Card.png" alt="Card Image" class="img-fluid w-75 mx-auto d-block">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="providers" class="container py-5">
            <h2 class="font-cinzel fw-bold m-0">Grow your bookings on VenueBook</h2>
            <p class="font-open">VenueBook puts your services in front of corporate event organizers.</p>
            <div class="bgs-secondary w-100 rounded-4 px-4 pt-4">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-4">
                        <img src="https://www.notta.ai/pictures/corporate-meeting.png" alt="Coporate Meeting" class="img-fluid object-fit-cover h-100 rounded-3 border border-dark-subtle">
                    </div>
                    <div class="col-12 col-lg-6 mb-4">
                        <div class="d-flex bg-light flex-column h-100 p-3 rounded-3">
                            <h3 class="fs-2">🏛</h3>
                            <h3 class="fs-2 font-open fw-semibold">Venue providers</h3>
                            <p class="font-open">List your spaces, configure your AV packages, and manage your availability calendar — all from one dashboard. A hotel can list multiple rooms as separate venues.</p>
                            <ul class="list-group list-group-numbered">
                                <li class="list-group-item list-group-item-success">List multiple venues under one account</li>
                                <li class="list-group-item list-group-item-success">Set availability and block dates yourself</li>
                                <li class="list-group-item list-group-item-success">Configure packages</li>
                                <li class="list-group-item list-group-item-success">Approve or reject booking requests</li>
                            </ul>
                            <button class="btn btn-dark mt-4 login-btn" style="width: fit-content;">List your venue
                                <img src="assets/images/Arrow_right.svg" alt="Arrow Right Icon" width="18" height="18" style="margin-left: 8px;">
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer class="bg-dark container py-4">
        <div class="row">
            <a class="col-12 col-lg-3 text-decoration-none font-cinzel text-light" href="#" style="display: flex; align-items: center; font-weight: bold;">
                <img src="assets/images/Logo.svg" alt="Logo" width="30" height="30" style="margin-right: 6px;">
                VenueBook
            </a>
            <div class="col-12 col-lg-6 d-flex flex-column flex-lg-row justify-content-lg-center align-items-lg-center gap-3 mt-3 mt-lg-0">
                <a class="text-decoration-none font-open text-light opacity-75" href="#">Home</a>
                <a class="text-decoration-none font-open text-light opacity-75" href="#venues">Venues</a>
                <a class="text-decoration-none font-open text-light opacity-75" href="#how">How it works</a>
                <a class="text-decoration-none font-open text-light opacity-75" href="#providers">For Providers</a>
            </div>
            <p class="col-12 col-lg-3 text-light mt-3 mt-lg-0 text-lg-center mb-0">
                &copy; 2026 VenueBook. All rights reserved.
            </p>
        </div>

    </footer>
    <script>
        const loginButtons = document.querySelectorAll('.login-btn');
        loginButtons.forEach(button => {
            button.onclick = function() {
                window.location.href = "register.php";
            };
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>