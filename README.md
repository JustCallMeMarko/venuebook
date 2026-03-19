Folder Structure:
    venuebook/
    │
    ├── includes/
    │   ├── db.php          ← PDO connection (require this in every file)
    │   ├── auth.php        ← session_start() + role-based redirect guard
    │   ├── header.php      ← shared <nav> HTML, included at top of every page
    │   └── footer.php      ← shared footer HTML
    │
    ├── assets/
    │   ├── css/
    │   │   └── style.css
    │   └── js/
    │       ├── calendar.js    ← availability date picker
    │       ├── packages.js    ← bundle builder (food + AV selector)
    │       └── validate.js    ← client-side form validation
    │
    ├── admin/
    │   ├── dashboard.php
    │   ├── manage_venues.php
    │   ├── manage_packages.php
    │   └── all_bookings.php
    │
    ├── catering/
    │   ├── dashboard.php
    │   ├── menu_editor.php
    │   └── order_tracker.php
    │
    ├── index.php               ← public landing page
    ├── login.php               ← GET = form, POST = authenticate
    ├── register.php            ← GET = form, POST = create user
    ├── logout.php              ← destroy session, redirect to login
    ├── browse_venues.php       ← public venue catalog
    ├── dashboard.php           ← organizer home (requires login)
    ├── venue_detail.php        ← venue info + availability calendar
    ├── book_venue.php          ← package builder + booking POST
    ├── booking_confirm.php     ← receipt / confirmation page
    ├── my_bookings.php         ← organizer's booking history
    ├── payment.php             ← downpayment submission
    ├── notifications.php       ← alerts list
    └── profile.php             ← edit account details