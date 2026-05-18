# VenueBook

VenueBook is a PHP and MySQL venue booking system for managing venues, packages, bookings, payments, notifications, and user profiles.

## Features

- Public landing page, login, and registration.
- Client booking flow with venue selection, package selection, and payments.
- Admin dashboard with booking, venue, and package management.
- DB-backed notifications for clients and admins.
- Profile settings with profile image upload.
- Contract generation for approved bookings with paid downpayment.
- Booking and package cards styled for quick scanning and management.

## Main Folders

```text
venuebook/
├── actions/          # booking/payment/logout handlers
├── admin/            # admin dashboard, venues, packages, bookings
├── assets/           # CSS, JS, images, uploads
├── client/           # client dashboard, booking flow, contracts, notifications
├── config/           # DB connection, nav config, notifications helpers
├── includes/         # shared auth + sidebar layout
├── shared/           # settings, forbidden page, shared utilities
├── index.php         # public landing page
├── login.php         # login form and authentication
├── register.php      # account registration
└── README.md
```

## Setup

1. Copy the project into your XAMPP `htdocs` folder.
2. Create a MySQL database named `venuebook`.
3. Update `config/db.php` with your local DB credentials.
4. Import or create the required tables for users, venue, packages, bookings, payments, contracts, and notifications.
5. Start Apache and MySQL in XAMPP.
6. Open the app at `http://localhost/venuebook`.

## Notes

- Uploaded venue and profile images are stored in `assets/uploads/` and are ignored by git.

## Common Pages

- `admin/index.php` - admin dashboard.
- `client/notifications.php` - notification inbox.
- `shared/Settings.php` - profile and account settings.
