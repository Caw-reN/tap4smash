<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket <?= esc($booking['booking_code']) ?> — Tap4Smash</title>
    <meta name="description" content="E-Tiket booking lapangan Tap4Smash GOR Sport Center.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('css/user.css') ?>">
    <!-- QR Code generator (client-side, no PHP lib needed) -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <style>
        :root {
            --volt: #d4f500;
            --volt-dim: rgba(212,245,0,.08);
            --volt-border: rgba(212,245,0,.2);
            --bg: #0a0a0a;
            --surface: #141414;
            --surface2: #1c1c1c;
            --border: rgba(255,255,255,.08);
            --text: #f0f0f0;
            --text-muted: #888;
            --green: #22c55e;
            --yellow: #f59e0b;
            --radius: 10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding-bottom: 3rem;
        }

        /* ── Navbar ── */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1.5rem;
            background: rgba(10,10,10,.95);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-brand { display: flex; align-items: center; gap: .6rem; text-decoration: none; }
        .navbar-brand .brand-icon { font-size: 1.3rem; color: var(--volt); }
        .navbar-brand h1 { font-family: 'Montserrat', sans-serif; font-size: 1.1rem; font-weight: 900; color: var(--text); }
        .navbar-brand h1 span { font-weight: 500; font-size: .8rem; color: var(--text-muted); margin-left: .2rem; }

        /* ── Layout ── */
        .tiket-wrap {
            max-width: 480px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* ── Header Tiket ── */
        .tiket-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .tiket-header .badge-confirmed {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(34,197,94,.1);
            border: 1px solid rgba(34,197,94,.3);
            color: var(--green);
            font-size: .78rem;
            font-weight: 700;
            padding: .35rem .9rem;
            border-radius: 99px;
            margin-bottom: 1rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .tiket-header .badge-dp {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(245,158,11,.1);
            border: 1px solid rgba(245,158,11,.3);
            color: var(--yellow);
            font-size: .78rem;
            font-weight: 700;
            padding: .35rem .9rem;
            border-radius: 99px;
            margin-bottom: 1rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .tiket-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.1rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text);
        }

        /* ── Card Tiket ── */
        .tiket-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 0 40px rgba(212,245,0,.04);
        }

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
            width: 20px;
            height: 20px;
            background: var(--bg);
            border-radius: 50%;
            flex-shrink: 0;
        }
        .tiket-separator .dashed-line {
            flex: 1;
            border-top: 2px dashed var(--border);
        }

        /* Bagian atas: info booking */
        .tiket-top {
            padding: 1.5rem;
        }
        .booking-code-big {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--volt);
            letter-spacing: .08em;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .detail-grid {
            display: grid;
            gap: .6rem;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .5rem;
            font-size: .83rem;
            padding: .5rem 0;
            border-bottom: 1px solid var(--border);
        }
        .detail-item:last-child { border-bottom: none; }
        .detail-item .lbl {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: .4rem;
            flex-shrink: 0;
            min-width: 110px;
        }
        .detail-item .lbl i { width: 14px; text-align: center; }
        .detail-item .val {
            font-weight: 600;
            text-align: right;
        }

        /* Bagian bawah: QR Code */
        .tiket-bottom {
            padding: 1.5rem;
            text-align: center;
            background: var(--surface2);
        }
        .qr-label {
            font-size: .72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 1rem;
        }
        .qr-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 12px;
            padding: .75rem;
            margin-bottom: 1rem;
        }
        #qr-canvas { display: block; }
        .qr-hint {
            font-size: .75rem;
            color: var(--text-muted);
            line-height: 1.5;
        }
        .qr-hint strong { color: var(--volt); }

        /* Alert DP */
        .dp-warning {
            margin: 1rem 1.5rem;
            background: rgba(245,158,11,.08);
            border: 1px solid rgba(245,158,11,.25);
            border-radius: var(--radius);
            padding: .9rem 1rem;
            display: flex;
            gap: .65rem;
            align-items: flex-start;
            font-size: .82rem;
            color: var(--yellow);
        }
        .dp-warning i { margin-top: .15rem; flex-shrink: 0; }

        /* Checked-in badge */
        .checkin-done {
            margin: 0 1.5rem 1.5rem;
            background: rgba(34,197,94,.08);
            border: 1px solid rgba(34,197,94,.25);
            border-radius: var(--radius);
            padding: .9rem 1rem;
            display: flex;
            gap: .65rem;
            align-items: center;
            font-size: .82rem;
            color: var(--green);
            font-weight: 600;
        }

        /* Aksi */
        .tiket-actions {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .65rem 1.1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: .82rem;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
            transition: border-color .15s, background .15s;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        .btn-ghost:hover { border-color: var(--volt); background: var(--volt-dim); }

        /* Footer */
        .footer { text-align: center; margin-top: 2rem; font-size: .78rem; color: var(--text-muted); padding: 1rem; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <span class="brand-icon"><i class="fa-solid fa-table-tennis-paddle-ball"></i></span>
        <h1><a href="<?= site_url('/') ?>" style="color:inherit;text-decoration:none;">Tap4Smash <span>GOR Sport Center</span></a></h1>
    </div>
</nav>

<div class="tiket-wrap">

    <!-- Header -->
    <div class="tiket-header">
        <?php if ($booking['is_checked_in']): ?>
            <div class="badge-confirmed"><i class="fa-solid fa-circle-check"></i> Sudah Check-in</div>
        <?php elseif ($booking['status_pelunasan'] === 'belum_lunas'): ?>
            <div class="badge-dp"><i class="fa-solid fa-clock"></i> Belum Lunas — Tunjukkan ke Petugas</div>
        <?php else: ?>
            <div class="badge-confirmed"><i class="fa-solid fa-qrcode"></i> E-Tiket Aktif</div>
        <?php endif; ?>
        <div class="tiket-title">E-Tiket Booking</div>
    </div>

    <!-- Kartu Tiket -->
    <div class="tiket-card">

        <!-- Info Booking -->
        <div class="tiket-top">
            <div class="booking-code-big"><?= esc($booking['booking_code']) ?></div>

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
                    <span class="val" style="color:<?= $booking['status_pelunasan'] === 'lunas' ? 'var(--green)' : 'var(--yellow)' ?>; font-weight:700;">
                        <?php if ($booking['status_pelunasan'] === 'lunas'): ?>
                            <i class="fa-solid fa-check"></i> Lunas
                        <?php else: ?>
                            <i class="fa-solid fa-clock"></i> Belum Lunas
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <?php if ($booking['is_checked_in']): ?>
        <!-- Sudah check-in -->
        <div class="checkin-done">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div>Sudah check-in pada <strong><?= date('H:i, d M Y', strtotime($booking['checkin_at'])) ?></strong></div>
        </div>
        <?php elseif ($booking['status_pelunasan'] === 'belum_lunas'): ?>
        <!-- Peringatan DP -->
        <div class="dp-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>Kamu masih memiliki sisa tagihan <strong>Rp <?= number_format($booking['sisa_tagihan'], 0, ',', '.') ?></strong>. Bayar ke petugas GOR sebelum masuk.</div>
        </div>
        <?php endif; ?>

        <!-- Separator -->
        <div class="tiket-separator">
            <div class="dashed-line"></div>
        </div>

        <!-- QR Code -->
        <div class="tiket-bottom">
            <div class="qr-label"><i class="fa-solid fa-qrcode"></i> &nbsp; Scan untuk Check-in</div>
            <div class="qr-container">
                <canvas id="qr-canvas"></canvas>
            </div>
            <div class="qr-hint">
                Tunjukkan QR ini ke <strong>petugas GOR</strong><br>untuk konfirmasi kehadiran kamu.
            </div>
        </div>

    </div><!-- /tiket-card -->

    <!-- Aksi -->
    <div class="tiket-actions">
        <a href="<?= site_url('cek-status?kode=' . esc($booking['booking_code'])) ?>" class="btn-ghost">
            <i class="fa-solid fa-magnifying-glass"></i> Cek Status
        </a>
        <a href="<?= site_url('/') ?>" class="btn-ghost">
            <i class="fa-solid fa-house"></i> Beranda
        </a>
        <button onclick="window.print()" class="btn-ghost">
            <i class="fa-solid fa-print"></i> Print Tiket
        </button>
    </div>

</div>

<footer class="footer">
    <i class="fa-solid fa-table-tennis-paddle-ball" style="color:var(--volt);margin-right:.3rem;"></i>
    <strong style="color:var(--volt);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?>
</footer>

<script>
    // Generate QR Code dari URL tiket ini sendiri
    const tiketUrl = <?= json_encode($tiket_url) ?>;

    QRCode.toCanvas(document.getElementById('qr-canvas'), tiketUrl, {
        width: 220,
        margin: 0,
        color: { dark: '#111827', light: '#ffffff' },
    }, function(err) {
        if (err) console.error('QR Error:', err);
    });
</script>

</body>
</html>
