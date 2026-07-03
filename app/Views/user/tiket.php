<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket <?= esc($booking['booking_code']) ?> — Tap4Smash</title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <meta name="description" content="E-Tiket booking lapangan Tap4Smash GOR Sport Center.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('css/user.css') ?>">
    <style>
        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Layout ── */
        .tiket-wrap {
            max-width: 480px;
            width: 100%;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
            flex: 1 0 auto;
        }

        /* ── Header Tiket ── */
        .tiket-header {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .tiket-header .badge-confirmed {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: var(--accent);
            color: var(--navy);
            font-size: .8rem;
            font-weight: 700;
            padding: .4rem 1.1rem;
            border-radius: 99px;
            margin-bottom: .75rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(170,238,0,.3);
        }
        .tiket-header .badge-dp {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(217,119,6,.15);
            border: 1px solid rgba(217,119,6,.35);
            color: #d97706;
            font-size: .8rem;
            font-weight: 700;
            padding: .4rem 1.1rem;
            border-radius: 99px;
            margin-bottom: .75rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .tiket-title {
            font-family: 'Oswald', sans-serif;
            font-size: 1.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--navy);
        }

        /* ── Card Tiket ── */
        .tiket-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        /* Bagian atas: info booking */
        .tiket-top {
            padding: 1.75rem;
        }
        .booking-code-big {
            font-family: 'Oswald', sans-serif;
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: .06em;
            text-align: center;
            margin-bottom: 1.5rem;
            padding: .75rem;
            background: var(--surface2);
            border-radius: 10px;
            border: 1px dashed var(--border-dark);
        }

        .detail-grid {
            display: grid;
            gap: .5rem;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            font-size: .9rem;
            padding: .6rem 0;
            border-bottom: 1px solid var(--border);
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-item .lbl {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: .5rem;
            font-weight: 500;
            flex-shrink: 0;
        }
        .detail-item .lbl i { width: 16px; text-align: center; color: var(--navy); }
        .detail-item .val {
            font-weight: 600;
            color: var(--navy);
            text-align: right;
        }

        /* Checked-in badge */
        .checkin-done {
            margin: 0 1.75rem 1.5rem;
            background: rgba(22,163,74,.1);
            border: 1px solid rgba(22,163,74,.25);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            display: flex;
            gap: .75rem;
            align-items: center;
            font-size: .88rem;
            color: #16a34a;
            font-weight: 600;
        }

        /* Alert DP */
        .dp-warning {
            margin: 0 1.75rem 1.5rem;
            background: rgba(217,119,6,.1);
            border: 1px solid rgba(217,119,6,.25);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            font-size: .88rem;
            color: #d97706;
            line-height: 1.5;
        }
        .dp-warning i { margin-top: .15rem; flex-shrink: 0; font-size: 1.1rem; }

        /* Garis putus-putus pemisah tiket */
        .tiket-separator {
            display: flex;
            align-items: center;
            gap: 0;
            position: relative;
            overflow: visible;
        }
        .tiket-separator::before, .tiket-separator::after {
            content: '';
            width: 24px;
            height: 24px;
            background: var(--bg);
            border-radius: 50%;
            flex-shrink: 0;
            border: 1px solid var(--border);
        }
        .tiket-separator::before { margin-left: -12px; }
        .tiket-separator::after  { margin-right: -12px; }
        .tiket-separator .dashed-line {
            flex: 1;
            border-top: 2px dashed var(--border-dark);
        }

        /* Bagian bawah: QR Code */
        .tiket-bottom {
            padding: 1.75rem;
            text-align: center;
            background: var(--surface2);
        }
        .qr-label {
            font-family: 'Oswald', sans-serif;
            font-size: .95rem;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 1.25rem;
            font-weight: 600;
        }
        .qr-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 1rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }
        #qr-canvas { display: block; }
        .qr-hint {
            font-size: .85rem;
            color: var(--text-mid);
            line-height: 1.5;
            font-weight: 500;
        }
        .qr-hint strong { color: var(--navy); font-weight: 700; }

        /* Aksi */
        .tiket-actions {
            display: flex;
            gap: .75rem;
            margin-top: 1.5rem;
        }
        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .75rem 1.5rem;
            background: var(--navy);
            color: #fff;
            border-radius: var(--radius);
            font-family: 'Oswald', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-decoration: none;
            transition: all .2s;
            flex: 1;
        }
        .btn-action-primary:hover { background: var(--navy-light); box-shadow: var(--shadow-md); transform: translateY(-1px); }

        .btn-action-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .75rem 1.5rem;
            background: var(--surface);
            border: 1px solid var(--border-dark);
            color: var(--navy);
            border-radius: var(--radius);
            font-family: 'Oswald', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-decoration: none;
            transition: all .2s;
            flex: 1;
        }
        .btn-action-secondary:hover { background: var(--surface2); border-color: var(--navy); transform: translateY(-1px); }

        /* Footer */
        .footer { text-align: center; margin-top: auto; font-size: .8rem; color: var(--text-muted); padding: 2rem 1rem 1.5rem; flex-shrink: 0; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <?php $__logoExts=['png','jpg','jpeg','webp']; $__logo=null; foreach($__logoExts as $__e){if(file_exists(FCPATH.'img/logo.'.$__e)){$__logo=base_url('img/logo.'.$__e).'?v='.filemtime(FCPATH.'img/logo.'.$__e);break;}} ?>
        <?php if($__logo): ?><a href="<?= site_url('/') ?>"><img src="<?= $__logo ?>" alt="Tap4Smash"></a><?php else: ?><a href="<?= site_url('/') ?>" style="color:inherit;text-decoration:none;"><span class="brand-icon"><i class="fa-solid fa-table-tennis-paddle-ball"></i></span></a><?php endif; ?>
    </div>
</nav>

<div class="tiket-wrap">

    <!-- Header -->
    <div class="tiket-header">
        <?php if ($booking['is_checked_in']): ?>
            <div class="badge-confirmed" style="background:#16a34a; color:#fff;"><i class="fa-solid fa-circle-check"></i> Sudah Check-in</div>
        <?php elseif ($booking['status_pelunasan'] === 'belum_lunas'): ?>
            <div class="badge-dp"><i class="fa-solid fa-clock"></i> Belum Lunas — Tunjukkan ke Petugas</div>
        <?php else: ?>
            <div class="badge-confirmed"><i class="fa-solid fa-qrcode"></i> E-Tiket Aktif</div>
        <?php endif; ?>
        <div class="tiket-title">E-Tiket Booking</div>
    </div>

    <!-- Kartu Tiket -->
    <div class="tiket-card">

        <!-- Info Booking (Atas: Kode Booking & QR Code) -->
        <div class="tiket-top">
            <div class="booking-code-big"><?= esc($booking['booking_code']) ?></div>

            <div style="text-align:center; margin-top:.5rem;">
                <div class="qr-label"><i class="fa-solid fa-qrcode"></i> &nbsp; Scan untuk Check-in</div>
                <div class="qr-container" style="margin-bottom:1rem;">
                    <?php if ($qr_data_uri): ?>
                        <img src="<?= $qr_data_uri ?>" alt="QR Check-in <?= esc($booking['booking_code']) ?>" style="width:200px;height:200px;display:block;">
                    <?php else: ?>
                        <div style="width:200px;height:200px;display:flex;align-items:center;justify-content:center;color:#888;font-size:.75rem;text-align:center;padding:1rem;">
                            <i class="fa-solid fa-triangle-exclamation" style="display:block;font-size:2rem;margin-bottom:.5rem;"></i>
                            Gagal memuat QR.<br>Tunjukkan kode booking ke petugas.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="qr-hint">
                    Tunjukkan QR ini ke <strong>petugas GOR</strong> untuk konfirmasi kehadiran kamu.
                </div>
            </div>
        </div>

        <!-- Separator -->
        <div class="tiket-separator">
            <div class="dashed-line"></div>
        </div>

        <!-- Detail Booking (Bawah) -->
        <div class="tiket-bottom" style="text-align:left;">
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="lbl"><i class="fa-solid fa-user"></i> Nama</span>
                    <span class="val"><?= esc($booking['nama_pemesan']) ?></span>
                </div>
                <div class="detail-item">
                    <span class="lbl"><i class="fa-solid fa-building"></i> Lapangan</span>
                    <span class="val"><?= esc($lapangan['nama_lapangan'] ?? '—') ?></span>
                </div>
                <div class="detail-item">
                    <span class="lbl"><i class="fa-solid fa-calendar"></i> Tanggal</span>
                    <span class="val"><?= date('d M Y', strtotime($booking['tanggal_main'])) ?></span>
                </div>
                <div class="detail-item">
                    <span class="lbl"><i class="fa-solid fa-clock"></i> Waktu</span>
                    <span class="val"><?= esc($jam_main_fmt) ?> WIB</span>
                </div>
                <div class="detail-item">
                    <span class="lbl"><i class="fa-solid fa-credit-card"></i> Skema</span>
                    <span class="val"><?= $booking['skema_pembayaran'] === 'dp' ? 'DP 50%' : 'Full Payment' ?></span>
                </div>
                <div class="detail-item">
                    <span class="lbl"><i class="fa-solid fa-money-bill"></i> Total</span>
                    <span class="val">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></span>
                </div>
                <div class="detail-item">
                    <span class="lbl"><i class="fa-solid fa-circle-check"></i> Pelunasan</span>
                    <span class="val" style="color:<?= $booking['status_pelunasan'] === 'lunas' ? '#16a34a' : '#d97706' ?>; font-weight:700;">
                        <?php if ($booking['status_pelunasan'] === 'lunas'): ?>
                            <i class="fa-solid fa-check"></i> Lunas
                        <?php else: ?>
                            <i class="fa-solid fa-clock"></i> Belum Lunas
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <?php if ($booking['is_checked_in']): ?>
            <!-- Sudah check-in -->
            <div class="checkin-done" style="margin: 1.5rem 0 0;">
                <i class="fa-solid fa-circle-check fa-lg"></i>
                <div>Sudah check-in pada <strong><?= date('H:i, d M Y', strtotime($booking['checkin_at'])) ?></strong></div>
            </div>
            <?php elseif ($booking['status_pelunasan'] === 'belum_lunas'): ?>
            <!-- Peringatan DP -->
            <div class="dp-warning" style="margin: 1.5rem 0 0;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>Kamu masih memiliki sisa tagihan <strong>Rp <?= number_format($booking['sisa_tagihan'], 0, ',', '.') ?></strong>. Bayar ke petugas GOR sebelum masuk.</div>
            </div>
            <?php endif; ?>
        </div>

    </div><!-- /tiket-card -->

    <!-- Aksi -->
    <div class="tiket-actions">
        <a href="<?= site_url('cek-status?kode=' . esc($booking['booking_code'])) ?>" class="btn-action-primary">
            <i class="fa-solid fa-magnifying-glass"></i> CEK STATUS
        </a>
        <a href="<?= site_url('/') ?>" class="btn-action-secondary">
            <i class="fa-solid fa-house"></i> BERANDA
        </a>
    </div>

</div>

<footer class="footer">
    <p><strong style="color:var(--navy);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?></p>
</footer>

</body>
</html>
