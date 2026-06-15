<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// ── Admin: WhatsApp Gateway ──────────────────────────────────────────────────
$routes->group('admin', function ($routes) {
    $routes->get('whatsapp',          'Admin\WhatsAppController::index');
    $routes->get('whatsapp/status',   'Admin\WhatsAppController::status');
    $routes->get('whatsapp/qr',       'Admin\WhatsAppController::qr');
});
