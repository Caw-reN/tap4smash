<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Admin') ?> — Tap4Smash Admin</title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body>
<div class="admin-wrap">

    <!-- ── Sidebar ── -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <?php
                $logoExts = ['png', 'jpg', 'jpeg', 'webp'];
                $sidebarLogo = null;
                foreach ($logoExts as $ext) {
                    if (file_exists(FCPATH . 'img/logo.' . $ext)) {
                        $sidebarLogo = base_url('img/logo.' . $ext) . '?v=' . filemtime(FCPATH . 'img/logo.' . $ext);
                        break;
                    }
                }
            ?>
            <?php if ($sidebarLogo): ?>
                <img src="<?= $sidebarLogo ?>" alt="Logo" style="height:36px;width:auto;max-width:160px;object-fit:contain;">
            <?php else: ?>
                <i class="fa-solid fa-table-tennis-paddle-ball logo-icon"></i>
                <h1>Tap4Smash <small>Admin Panel</small></h1>
            <?php endif; ?>
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

            <a href="<?= site_url('admin/checkin') ?>"
               class="nav-item <?= str_starts_with(uri_string(), 'admin/checkin') ? 'active' : '' ?>">
                <i class="fa-solid fa-qrcode nav-icon"></i> Check-in Scanner
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

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ── Main ── -->
    <div class="admin-main">
        <header class="admin-header">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <button type="button" class="btn-sidebar-toggle" id="btnSidebarToggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h2><?= esc($page_title ?? 'Admin') ?></h2>
                    <div class="breadcrumb">Tap4Smash › <?= esc($page_title ?? '') ?></div>
                </div>
            </div>
        </header>

        <main class="admin-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

</div>

<!-- Custom Alert Overlay -->
<div class="ui-alert-overlay" id="uiAlertOverlay">
    <div class="ui-alert-box" id="uiAlertBox">
        <div class="ui-alert-icon-wrap" id="uiAlertIconWrap">
            <i class="fa-solid fa-circle-check" id="uiAlertIcon"></i>
        </div>
        <h3 class="ui-alert-title" id="uiAlertTitle">Notifikasi</h3>
        <p class="ui-alert-message" id="uiAlertMessage">Pesan disini</p>
        <button type="button" class="btn btn-primary" onclick="closeUiAlert()" style="width: 100%; justify-content: center; font-size: 0.85rem; padding: 0.6rem;">OK</button>
    </div>
</div>

<style>
    .ui-alert-overlay { display: none; position: fixed; inset: 0; background: rgba(15,32,68,.55); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px); padding: 1rem; }
    .ui-alert-overlay.open { display: flex; animation: fadeIn 0.2s ease-out; }
    .ui-alert-box { background: var(--surface); border: 1px solid var(--border); padding: 2rem; max-width: 340px; width: 100%; border-radius: var(--radius); box-shadow: var(--shadow-lg); text-align: center; transform: scale(0.95); transition: transform 0.2s ease-out; }
    .ui-alert-overlay.open .ui-alert-box { transform: scale(1); }
    .ui-alert-icon-wrap { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; font-size: 1.75rem; }
    .ui-alert-icon-wrap.success { background: var(--green-dim); color: var(--green); }
    .ui-alert-icon-wrap.error { background: var(--red-dim); color: var(--red); }
    .ui-alert-icon-wrap.warning { background: var(--yellow-dim); color: var(--yellow); }
    .ui-alert-icon-wrap.info { background: var(--blue-dim); color: var(--blue); }
    .ui-alert-title { font-family: 'Oswald', sans-serif; font-weight: 700; text-transform: uppercase; font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--navy); }
    .ui-alert-message { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; line-height: 1.5; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<script>
function showAlert(message, type = 'success', title = null) {
    const overlay = document.getElementById('uiAlertOverlay');
    const iconWrap = document.getElementById('uiAlertIconWrap');
    const icon = document.getElementById('uiAlertIcon');
    const titleEl = document.getElementById('uiAlertTitle');
    const msgEl = document.getElementById('uiAlertMessage');

    iconWrap.className = 'ui-alert-icon-wrap ' + type;
    
    let defaultTitle = 'Notifikasi';
    if (type === 'success') {
        icon.className = 'fa-solid fa-circle-check';
        defaultTitle = 'Berhasil';
    } else if (type === 'error') {
        icon.className = 'fa-solid fa-circle-xmark';
        defaultTitle = 'Gagal';
    } else if (type === 'warning') {
        icon.className = 'fa-solid fa-triangle-exclamation';
        defaultTitle = 'Peringatan';
    } else if (type === 'info') {
        icon.className = 'fa-solid fa-circle-info';
        defaultTitle = 'Informasi';
    }

    titleEl.textContent = title || defaultTitle;
    msgEl.textContent = message;

    overlay.classList.add('open');
    
    return new Promise((resolve) => {
        window._uiAlertResolve = resolve;
    });
}

function closeUiAlert() {
    document.getElementById('uiAlertOverlay').classList.remove('open');
    if (window._uiAlertResolve) {
        window._uiAlertResolve();
        window._uiAlertResolve = null;
    }
}

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar && overlay) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    }
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 900) {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar) sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('open');
            }
        });
    });
});
</script>
</body>
</html>
