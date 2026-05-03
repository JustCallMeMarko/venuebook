<?php

$nav_config = [

    'admin' => [
        ['key' => 'Dashboard',    'label' => 'Dashboard',    'href' => '/venuebook/admin/index.php'],
        ['key' => 'Venue',       'label' => 'Venue',       'href' => '/venuebook/admin/Venue.php'],
        ['key' => 'Package',     'label' => 'Package',     'href' => '/venuebook/admin/Package.php'],
        ['key' => 'Booking',     'label' => 'Booking',    'href' => '/venuebook/admin/Booking.php']
    ],

    'organizer' => [
        ['key' => 'Dashboard',   'label' => 'Dashboard',  'href' => '/venuebook/client/index.php'],
        ['key' => 'Venue',       'label' => 'Venue',      'href' => '/venuebook/client/Venue.php'],
        ['key' => 'Booking',     'label' => 'Booking',    'href' => '/venuebook/client/Booking.php'],
    ],

];
?>