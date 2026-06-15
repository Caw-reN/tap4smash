<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Admin') ?> — Tap4Smash</title>
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
<div class="admin-wrap">

    <!-- ── Sidebar ── -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <i class="fa-solid fa-table-tennis-paddle-ball logo-icon"></i>
            <h1>Tap4Smash <small>Admin Panel</small></h1>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Utama</div>

            <a href="<?= site_url('admin/dashboard') ?>"
               class="nav-item <?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-gauge-high nav-icon"></i> Dashboard
            </a>

            <div class="nav-section-title">Transaksi</div>

            <a href="<?= site_url('admin/bookings') ?>"
               class="nav-item <?= str_starts_with(uri_string(), 'admin/bookings') ? 'active' : '' ?>">
                <i class="fa-solid fa-clipboard-list nav-icon"></i> Semua Booking
            </a>

            <a href="<?= site_url('admin/pelunasan') ?>"
               class="nav-item <?= str_starts_with(uri_string(), 'admin/pelunasan') ? 'active' : '' ?>">
                <i class="fa-solid fa-credit-card nav-icon"></i> Pelunasan DP
                <?php if (isset($pending_pelunasan_count) && $pending_pelunasan_count > 0): ?>
                    <span class="nav-badge"><?= $pending_pelunasan_count ?></span>
                <?php endif; ?>
            </a>

            <div class="nav-section-title">Manajemen</div>

            <a href="<?= site_url('admin/lapangan') ?>"
               class="nav-item <?= str_starts_with(uri_string(), 'admin/lapangan') ? 'active' : '' ?>">
                <i class="fa-solid fa-building nav-icon"></i> Master Lapangan
            </a>

            <div class="nav-section-title">Sistem</div>

            <a href="<?= site_url('admin/whatsapp') ?>"
               class="nav-item <?= str_starts_with(uri_string(), 'admin/whatsapp') ? 'active' : '' ?>">
                <i class="fa-brands fa-whatsapp nav-icon"></i> WhatsApp Gateway
            </a>

            <a href="<?= site_url('admin/settings') ?>"
               class="nav-item <?= str_starts_with(uri_string(), 'admin/settings') ? 'active' : '' ?>">
                <i class="fa-solid fa-cogs nav-icon"></i> Pengaturan
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar"><?= strtoupper(substr(session()->get('admin_username') ?? 'A', 0, 1)) ?></div>
                <div class="user-info">
                    <div class="user-name"><?= esc(session()->get('admin_username') ?? 'Admin') ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <form action="<?= site_url('admin/logout') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- ── Main ── -->
    <div class="admin-main">
        <header class="admin-header">
            <div>
                <h2><?= esc($page_title ?? 'Admin') ?></h2>
                <div class="breadcrumb">Tap4Smash › <?= esc($page_title ?? '') ?></div>
            </div>
        </header>

        <main class="admin-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

</div>
</body>
</html>
