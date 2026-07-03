<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Booking <?= esc($booking['booking_code']) ?> — Tap4Smash</title>
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
</nav>

<div class="konfirmasi-wrap" style="padding-top:2.5rem;padding-bottom:3rem;">

    <?php if ($booking['status'] === 'expired'): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-clock" style="flex-shrink:0;"></i>
        Booking ini sudah kadaluarsa. Silakan buat booking baru.
    </div>
    <?php endif; ?>

    <div class="booking-card">
        <!-- Header -->
        <div class="booking-card-header">
            <?php if ($booking['status'] === 'success'): ?>
            <div class="check-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h2>Booking Terkonfirmasi!</h2>
            <p>Pembayaran kamu telah dikonfirmasi oleh admin.</p>
            <?php else: ?>
            <div class="check-icon" style="color:var(--yellow);"><i class="fa-solid fa-clock"></i></div>
            <h2 style="color:var(--yellow);">Menunggu Pembayaran</h2>
            <p>Segera selesaikan pembayaran sebelum waktu habis.</p>
            <?php endif; ?>
            <div class="booking-code-display"><?= esc($booking['booking_code']) ?></div>
            <p style="margin-top:.5rem;font-size:.72rem;color:var(--text-muted);">Simpan kode ini untuk mengecek status booking kamu</p>
        </div>

        <!-- Detail Booking -->
        <div class="booking-details">
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-building" style="width:16px;text-align:center;margin-right:.4rem;"></i>Lapangan</span>
                <span class="val"><?= esc($lapangan['nama_lapangan']) ?></span>
            </div>
            <?php if (!empty($lapangan['jenis_lapangan'])): ?>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-tag" style="width:16px;text-align:center;margin-right:.4rem;"></i>Jenis</span>
                <span class="val"><?= esc($lapangan['jenis_lapangan']) ?></span>
            </div>
            <?php endif; ?>
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
                <span class="lbl"><i class="fa-brands fa-whatsapp" style="width:16px;text-align:center;margin-right:.4rem;"></i>WhatsApp</span>
                <span class="val"><?= esc($booking['nomor_wa']) ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-credit-card" style="width:16px;text-align:center;margin-right:.4rem;"></i>Skema</span>
                <span class="val"><?= $booking['skema_pembayaran'] === 'dp' ? 'DP 50%' : 'Full Payment' ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-money-bill" style="width:16px;text-align:center;margin-right:.4rem;"></i>Total Harga</span>
                <span class="val">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></span>
            </div>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-hand-holding-dollar" style="width:16px;text-align:center;margin-right:.4rem;"></i>Yang Dibayar</span>
                <span class="val" style="color:var(--volt);">Rp <?= number_format($booking['total_harga'] * ($booking['skema_pembayaran'] === 'dp' ? 0.5 : 1), 0, ',', '.') ?></span>
            </div>
            <?php if ($booking['skema_pembayaran'] === 'dp'): ?>
            <div class="detail-row">
                <span class="lbl"><i class="fa-solid fa-hourglass-half" style="width:16px;text-align:center;margin-right:.4rem;"></i>Sisa di Kasir</span>
                <span class="val" style="color:var(--yellow);">Rp <?= number_format($booking['total_harga'] * 0.5, 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Instruksi Pembayaran (hanya jika pending) -->
        <?php if ($booking['status'] === 'pending'): ?>
        <div class="payment-instructions">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> Instruksi Pembayaran</h3>

            <!-- Countdown -->
            <?php
            $expiresAt  = strtotime($booking['expires_at']);
            $remaining  = max(0, $expiresAt - time());
            ?>
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;padding:.65rem 1rem;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);border-radius:5px;font-size:.82rem;color:var(--yellow);">
                <i class="fa-solid fa-clock"></i>
                <span>Sisa waktu pembayaran: <strong id="countdown">—</strong></span>
            </div>

            <div class="rekening-box">
                <div class="bank">Bank Transfer BCA</div>
                <div class="no-rek">1234-5678-90</div>
                <div class="an">a.n. Tap4Smash GOR Sport Center</div>
            </div>

            <ol>
                <li>Transfer sebesar <strong>Rp <?= number_format($booking['total_harga'] * ($booking['skema_pembayaran'] === 'dp' ? 0.5 : 1), 0, ',', '.') ?></strong> ke rekening di atas.</li>
                <li>Sertakan kode booking <strong><?= esc($booking['booking_code']) ?></strong> di kolom berita/keterangan.</li>
                <li>Hubungi admin via WhatsApp atau tunggu konfirmasi otomatis.</li>
                <li>Booking aktif setelah admin mengkonfirmasi pembayaran.</li>
            </ol>
        </div>

        <script>
        const expiresAt = <?= $expiresAt ?> * 1000;
        const countdownEl = document.getElementById('countdown');
        function updateCountdown() {
            const left = Math.max(0, expiresAt - Date.now());
            const m    = Math.floor(left / 60000);
            const s    = Math.floor((left % 60000) / 1000);
            countdownEl.textContent = m + ':' + String(s).padStart(2, '0');
            if (left === 0) {
                countdownEl.textContent = 'Waktu habis!';
                clearInterval(timer);
                setTimeout(() => location.reload(), 1500);
            }
        }
        updateCountdown();
        const timer = setInterval(updateCountdown, 1000);
        </script>
        <?php endif; ?>

    </div><!-- /booking-card -->

    <!-- Action Buttons -->
    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:1.5rem;">
        <a href="<?= site_url('cek-status?kode=' . esc($booking['booking_code'])) ?>"
           style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.25rem;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);font-size:.82rem;font-weight:600;color:var(--text);transition:all .15s;"
           onmouseover="this.style.borderColor='var(--volt)'" onmouseout="this.style.borderColor='var(--border)'">
            <i class="fa-solid fa-magnifying-glass"></i> Cek Status Booking
        </a>
        <a href="<?= site_url('booking') ?>"
           style="display:inline-flex;align-items:center;gap:.5rem;padding:.7rem 1.25rem;background:var(--volt);border-radius:var(--radius);font-size:.82rem;font-weight:700;color:#000;font-family:'Oswald',sans-serif;transition:all .15s;"
           onmouseover="this.style.background='#b8e800'" onmouseout="this.style.background='var(--volt)'">
            <i class="fa-solid fa-plus"></i> Booking Lagi
        </a>
    </div>

</div>

<footer class="footer">
    <p>
        <i class="fa-solid fa-table-tennis-paddle-ball" style="color:var(--volt);margin-right:.3rem;"></i>
        <strong style="color:var(--volt);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?>
    </p>
</footer>

</body>
</html>
