## Folder Structure

```
venuebook/
├── actions/
│   └── post and get scripts like login and register
│
├── includes/
│   ├── auth.php            # session_start() + role-based redirect guard
│   ├── header.php          # shared <nav> HTML -- not finalized
│   └── footer.php          # shared footer HTML -- not finalized
│
├── config/
│   └── db.php              # database config
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── calendar.js     # availability date picker
│   │   ├── packages.js     # bundle builder (food + AV selector)
│   │   └── validate.js     # client-side form validation
│   └── images/
│
├── admin/
│   ├── dashboard.php
│   ├── manage_venues.php
│   ├── manage_packages.php
│   └── all_bookings.php
│
├── client/
│   ├── dashboard.php
│   ├── venues.php
│   ├── manage_packages.php
│   └── my_bookings.php
│
├── index.php               # public landing page
├── login.php               # GET = form, POST = authenticate
├── register.php            # GET = form, POST = create user
├── notifications.php       # alerts list
└── profile.php             # edit account details
```