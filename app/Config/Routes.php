<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
// ── User: Frontend ───────────────────────────────────────────────────────────
$routes->get('/',              'Home::index');
$routes->get('booking',        'Home::booking');
$routes->post('booking/proses', 'Home::processBooking');
$routes->get('booking/konfirmasi/(:segment)', 'Home::konfirmasi/$1');
$routes->get('cek-status',     'Home::cekStatus');

// ── User: API Helpers ─────────────────────────────────────────────────────────
$routes->get('api/slots',              'Home::getSlots');
$routes->get('api/lapangan-price',     'Home::getLapanganPrice');
$routes->get('api/payment-status',     'Home::checkPaymentStatus');

// ── Payment Gateway: PaymentKu Webhook (PRD S-01) ────────────────────────────
// CSRF dikecualikan untuk route ini (webhook dari server eksternal)
$routes->post('payment/callback', 'PaymentController::callback');

// ── Payment: QR Payment Page ──────────────────────────────────────────────────
$routes->get('booking/bayar/(:segment)', 'Home::payment/$1');

// ── User: E-Tiket (QR Check-in) ───────────────────────────────────────────────
$routes->get('tiket/(:segment)', 'Home::tiket/$1');


// ── Admin: Auth (tidak dilindungi filter) ────────────────────────────────────
$routes->get( 'admin/login',  'Admin\AuthController::login');
$routes->post('admin/login',  'Admin\AuthController::doLogin');
$routes->post('admin/logout', 'Admin\AuthController::logout');

// ── Admin: Protected routes (dilindungi AdminFilter via Filters.php) ─────────
$routes->group('admin', function ($routes) {

    // Dashboard
    $routes->get('',          'Admin\DashboardController::index');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Booking Management
    $routes->get('bookings',   'Admin\BookingController::index');
    $routes->get('pelunasan',  'Admin\BookingController::pelunasan');
    $routes->post('pelunasan/lunasi/(:num)', 'Admin\BookingController::lunasi/$1');

    // Check-in QR Scanner
    $routes->get( 'checkin',           'Admin\BookingController::checkin');
    $routes->post('checkin/scan',      'Admin\BookingController::scanResult');
    $routes->post('checkin/proses',    'Admin\BookingController::doCheckin');
    $routes->post('checkin/qris-status', 'Admin\BookingController::qrisCheckinStatus');

    // Master Lapangan (CRUD)
    $routes->get( 'lapangan',              'Admin\LapanganController::index');
    $routes->get( 'lapangan/create',       'Admin\LapanganController::create');
    $routes->post('lapangan/store',        'Admin\LapanganController::store');
    $routes->get( 'lapangan/edit/(:num)',   'Admin\LapanganController::edit/$1');
    $routes->post('lapangan/update/(:num)', 'Admin\LapanganController::update/$1');
    $routes->post('lapangan/toggle/(:num)', 'Admin\LapanganController::toggleStatus/$1');
    $routes->post('lapangan/delete/(:num)', 'Admin\LapanganController::delete/$1');

    // WhatsApp Gateway
    $routes->get('whatsapp',        'Admin\WhatsAppController::index');
    $routes->get('whatsapp/status', 'Admin\WhatsAppController::status');
    $routes->get('whatsapp/qr',     'Admin\WhatsAppController::qr');
    $routes->post('whatsapp/logout', 'Admin\WhatsAppController::logout');

    // Settings
    $routes->get('settings',               'Admin\SettingController::index');
    $routes->post('settings/update',       'Admin\SettingController::update');
    $routes->post('settings/update-maps',  'Admin\SettingController::updateMaps');
    $routes->post('settings/upload-logo',  'Admin\SettingController::uploadLogo');
});
