<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Booking — Tap4Smash</title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('css/user.css') ?>">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <?php $__logoExts=['png','jpg','jpeg','webp']; $__logo=null; foreach($__logoExts as $__e){if(file_exists(FCPATH.'img/logo.'.$__e)){$__logo=base_url('img/logo.'.$__e).'?v='.filemtime(FCPATH.'img/logo.'.$__e);break;}} ?>
        <?php if($__logo): ?><a href="<?= site_url('/') ?>"><img src="<?= $__logo ?>" alt="Tap4Smash" style="height:38px;width:auto;"></a><?php else: ?><a href="<?= site_url('/') ?>" style="color:inherit;"><span class="brand-icon"><i class="fa-solid fa-table-tennis-paddle-ball"></i></span></a><?php endif; ?>
    </div>
    <div class="navbar-links">
        <a href="<?= site_url('/') ?>" class="nav-link">Home</a>
        <a href="<?= site_url('booking') ?>" class="btn-book-nav">
            <i class="fa-solid fa-calendar-plus"></i> Booking
        </a>
    </div>
</nav>

<div class="cek-wrap" style="padding-top:3rem;padding-bottom:3rem;">

    <div style="text-align:center;margin-bottom:2.5rem;">
        <div style="width:56px;height:56px;background:var(--volt-dim);border:1px solid var(--volt-border);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:1.4rem;color:var(--volt);">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h2 style="font-family:'Oswald',sans-serif;font-weight:700;font-size:1.5rem;text-transform:uppercase;">
            Cek Status Booking
        </h2>
        <p style="color:var(--text-muted);font-size:.85rem;margin-top:.3rem;">
            Masukkan kode booking kamu untuk melihat status terkini.
        </p>
    </div>

    <!-- Search Form -->
    <form method="get" action="<?= site_url('cek-status') ?>">
        <div style="display:flex;gap:.5rem;margin-bottom:2rem;">
            <input type="text"
                   name="kode"
                   value="<?= esc($kode ?? '') ?>"
                   placeholder="Masukkan kode booking (contoh: ABC12345)"
                   style="flex:1;padding:.7rem 1rem;background:var(--surface);border:1px solid var(--border);color:var(--text);font-family:'Inter',sans-serif;font-size:.9rem;border-radius:var(--radius);outline:none;transition:border-color .15s;"
                   onfocus="this.style.borderColor='var(--volt)'"
                   onblur="this.style.borderColor='var(--border)'"
                   autofocus>
            <button type="submit"
                    style="padding:.7rem 1.4rem;background:var(--volt);color:#000;font-family:'Oswald',sans-serif;font-weight:600;font-size:.85rem;text-transform:uppercase;letter-spacing:.04em;border:none;border-radius:var(--radius);cursor:pointer;white-space:nowrap;transition:background .15s;"
                    onmouseover="this.style.background='#b8e800'" onmouseout="this.style.background='var(--volt)'">
                <i class="fa-solid fa-search"></i> Cek
            </button>
        </div>
    </form>

    <!-- Result -->
    <?php if ($kode && ! $booking): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-xmark" style="flex-shrink:0;margin-top:.1rem;"></i>
        <div>
            <strong>Booking tidak ditemukan.</strong>
            Pastikan kode booking kamu benar (huruf kapital semua).
        </div>
    </div>
    <?php endif; ?>

    <?php if ($booking): ?>

    <!-- Status Badge -->
    <div style="text-align:center;margin-bottom:1.5rem;">
        <?php
        $statusMap = [
            'success' => ['class' => 'status-success', 'icon' => 'fa-circle-check',     'text' => 'Terkonfirmasi'],
            'pending' => ['class' => 'status-pending',  'icon' => 'fa-clock',            'text' => 'Menunggu Pembayaran'],
            'expired' => ['class' => 'status-expired',  'icon' => 'fa-circle-xmark',     'text' => 'Kadaluarsa'],
        ];
        $s = $statusMap[$booking['status']] ?? $statusMap['expired'];
        ?>
        <div class="status-badge-large <?= $s['class'] ?>">
            <i class="fa-solid <?= $s['icon'] ?>"></i> <?= $s['text'] ?>
        </div>
    </div>

    <div class="booking-card">
        <div class="booking-card-header" style="padding:1.5rem;">
            <p style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.12em;margin-bottom:.35rem;">Kode Booking</p>
            <div class="booking-code-display"><?= esc($booking['booking_code']) ?></div>
        </div>

        <div class="booking-details">
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-building" style="width:16px;text-align:center;margin-right:.4rem;"></i>Lapangan</span>
                <span class="val"><?= esc($lapangan['nama_lapangan'] ?? '—') ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-calendar" style="width:16px;text-align:center;margin-right:.4rem;"></i>Tanggal</span>
                <span class="val"><?= date('l, d F Y', strtotime($booking['tanggal_main'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-clock" style="width:16px;text-align:center;margin-right:.4rem;"></i>Waktu</span>
                <span class="val"><?= format_jam_main($booking['jam_main']) ?> WIB</span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-user" style="width:16px;text-align:center;margin-right:.4rem;"></i>Nama</span>
                <span class="val"><?= esc($booking['nama_pemesan']) ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-money-bill" style="width:16px;text-align:center;margin-right:.4rem;"></i>Total</span>
                <span class="val">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-credit-card" style="width:16px;text-align:center;margin-right:.4rem;"></i>Skema</span>
                <span class="val"><?= $booking['skema_pembayaran'] === 'dp' ? 'DP 50%' : 'Full Payment' ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-circle-check" style="width:16px;text-align:center;margin-right:.4rem;"></i>Pelunasan</span>
                <span class="val">
                    <?php if ($booking['status_pelunasan'] === 'lunas'): ?>
                        <span style="color:var(--green);font-weight:700;"><i class="fa-solid fa-check"></i> Lunas</span>
                    <?php else: ?>
                        <span style="color:var(--yellow);font-weight:700;"><i class="fa-solid fa-clock"></i> Belum Lunas</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <!-- Alert DP -->
        <?php if ($booking['status'] === 'success' && $booking['status_pelunasan'] === 'belum_lunas'): ?>
        <div style="padding:1.25rem 1.5rem;background:rgba(245,158,11,.07);border-top:1px solid rgba(245,158,11,.2);">
            <div class="alert alert-warning" style="margin:0;">
                <i class="fa-solid fa-triangle-exclamation" style="flex-shrink:0;margin-top:.1rem;"></i>
                <span>Kamu masih memiliki sisa tagihan <strong>Rp <?= number_format($booking['total_harga'] * 0.5, 0, ',', '.') ?></strong>. Harap lunasi di kasir GOR sebelum bermain.</span>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /booking-card -->

    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:1.5rem;">
        <a href="<?= site_url('booking/konfirmasi/' . esc($booking['booking_code'])) ?>"
           style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.25rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);font-size:.82rem;font-weight:600;color:var(--text);transition:all .15s;"
           onmouseover="this.style.borderColor='var(--volt)'" onmouseout="this.style.borderColor='var(--border)'">
            <i class="fa-solid fa-receipt"></i> Lihat Detail Lengkap
        </a>
    </div>

    <?php endif; ?>

</div>

<footer class="footer">
    <p>
        <i class="fa-solid fa-table-tennis-paddle-ball" style="color:var(--volt);margin-right:.3rem;"></i>
        <strong style="color:var(--volt);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?>
    </p>
</footer>

</body>
</html>
