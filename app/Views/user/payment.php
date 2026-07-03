<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran — <?= esc($booking['booking_code']) ?> — Tap4Smash</title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('css/user.css') ?>">
    <style>
        /* ── QR Payment Page Styles ─────────────────────────────── */
        .payment-page {
            min-height: 100vh;
            background: var(--bg);
            display: flex;
            flex-direction: column;
        }

        .payment-wrap {
            max-width: 780px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
            width: 100%;
        }

        /* Grid: QR kiri, detail kanan */
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        /* ── QR Box ─── */
        .qr-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .qris-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .35rem .9rem;
            background: var(--navy-dim);
            border: 1px solid rgba(15,32,68,.15);
            border-radius: 50px;
            font-size: .68rem;
            font-weight: 800;
            color: var(--navy);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 1.5rem;
        }

        .qr-image-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .qr-image-wrap img {
            width: 200px;
            height: 200px;
            border-radius: 12px;
            display: block;
            border: 1px solid var(--border);
            padding: 5px;
            background: #fff;
        }

        /* Fallback saat QR belum tersedia */
        .qr-placeholder {
            width: 200px;
            min-height: 200px;
            background: var(--surface2);
            border: 2px dashed var(--border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            color: var(--text-muted);
            font-size: .8rem;
            padding: 1.25rem;
        }

        .qr-placeholder i {
            font-size: 2.5rem;
            opacity: .4;
        }

        /* Corner decorators */
        .qr-corner {
            position: absolute;
            width: 16px;
            height: 16px;
            border-color: var(--navy);
            border-style: solid;
        }
        .qr-corner.tl { top: -2px; left: -2px; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
        .qr-corner.tr { top: -2px; right: -2px; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
        .qr-corner.bl { bottom: -2px; left: -2px; border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
        .qr-corner.br { bottom: -2px; right: -2px; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }

        .qr-amount {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--navy);
            margin-bottom: .15rem;
            letter-spacing: -.01em;
        }

        .qr-amount-label {
            font-size: .72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .12em;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* ── Countdown ─── */
        .countdown-ring {
            position: relative;
            width: 72px;
            height: 72px;
            margin: 0 auto .75rem;
        }

        .countdown-ring svg {
            transform: rotate(-90deg);
        }

        .countdown-ring .ring-bg {
            fill: none;
            stroke: var(--border);
            stroke-width: 4;
        }

        .countdown-ring .ring-progress {
            fill: none;
            stroke: var(--navy);
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dasharray: 188.5;
            stroke-dashoffset: 0;
            transition: stroke-dashoffset 1s linear, stroke .5s;
        }

        .countdown-ring .ring-label {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: 1rem;
        }

        .countdown-ring .ring-label span { color: var(--navy); }

        /* Scanning animation */
        .scan-line {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--navy), transparent);
            animation: scan 2s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes scan {
            0%   { top: 0; opacity: 1; }
            90%  { top: calc(100% - 2px); opacity: 1; }
            100% { top: 0; opacity: 0; }
        }

        /* Status pulse overlay */
        .payment-status-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,.95);
            border-radius: 16px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            z-index: 10;
            backdrop-filter: blur(4px);
        }

        .payment-status-overlay.show { display: flex; }

        .check-anim {
            width: 64px;
            height: 64px;
            background: var(--green-dim);
            border: 2px solid var(--green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--green);
            animation: popIn .4s cubic-bezier(.34,1.56,.64,1);
        }

        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        /* ── Detail Box ─── */
        .detail-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .detail-box-header {
            background: var(--navy);
            border-bottom: 1px solid var(--border);
            padding: 1rem 1.25rem;
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            display: flex;
            align-items: center;
            gap: .6rem;
            color: #fff;
        }

        .detail-box-header i {
            color: var(--volt);
            font-size: 1rem;
        }

        .detail-box-body { padding: 1.25rem; }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: .6rem 0;
            border-bottom: 1px solid var(--border);
            font-size: .85rem;
            gap: .75rem;
        }

        .detail-item:last-child { border-bottom: none; }
        .detail-item .lbl { color: var(--text-muted); flex-shrink: 0; }
        .detail-item .val { font-weight: 600; text-align: right; color: var(--text); }

        /* ── Instruction steps ─── */
        .steps-mini {
            margin-top: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: .65rem;
        }

        .step-mini {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            font-size: .82rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .step-mini .num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1px solid rgba(15,32,68,.2);
            background: var(--navy-dim);
            color: var(--navy);
            font-family: 'Oswald', sans-serif;
            font-size: .68rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: .1rem;
        }

        /* ── Status bar ─── */
        .status-bar {
            margin-top: 1rem;
            padding: .75rem 1rem;
            background: rgba(245,158,11,.08);
            border: 1px solid rgba(245,158,11,.25);
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .82rem;
            font-weight: 600;
            color: #92400e;
        }

        .status-bar .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #f59e0b;
            animation: pulse-dot 1.2s infinite;
            flex-shrink: 0;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.7); }
        }

        .status-bar.success-bar {
            background: var(--green-dim);
            border-color: rgba(34,197,94,.3);
            color: var(--green);
        }

        .status-bar.success-bar .dot {
            background: var(--green);
            animation: none;
        }

        /* ── Code block ─── */
        .booking-code-pill {
            display: inline-block;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: .9rem;
            color: var(--navy);
            background: var(--navy-dim);
            border: 1px solid rgba(15,32,68,.2);
            padding: .2rem .7rem;
            border-radius: 5px;
            letter-spacing: .08em;
        }
    </style>
</head>
<body class="payment-page">

<nav class="navbar">
    <div class="navbar-brand">
        <?php $__logoExts=['png','jpg','jpeg','webp']; $__logo=null; foreach($__logoExts as $__e){if(file_exists(FCPATH.'img/logo.'.$__e)){$__logo=base_url('img/logo.'.$__e).'?v='.filemtime(FCPATH.'img/logo.'.$__e);break;}} ?>
        <?php if($__logo): ?><a href="<?= site_url('/') ?>"><img src="<?= $__logo ?>" alt="Tap4Smash" style="height:38px;width:auto;"></a><?php else: ?><a href="<?= site_url('/') ?>" style="color:inherit;"><span class="brand-icon"><i class="fa-solid fa-table-tennis-paddle-ball"></i></span></a><?php endif; ?>
    </div>
</nav>

<div class="payment-wrap">

    <!-- Page header -->
    <div style="margin-bottom:1.75rem;">
        <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:var(--navy);margin-bottom:.4rem;">
            <i class="fa-solid fa-qrcode"></i> Pembayaran QRIS
        </div>
        <h2 style="font-family:'Oswald',sans-serif;font-weight:700;font-size:1.5rem;text-transform:uppercase;margin-bottom:.3rem;color:var(--text);">
            Scan QR untuk Membayar
        </h2>
        <p style="color:var(--text-muted);font-size:.85rem;">
            Kode Booking: <span class="booking-code-pill"><?= esc($booking['booking_code']) ?></span>
        </p>
    </div>

    <div class="payment-grid">

        <!-- ── Kolom kiri: QR ── -->
        <div class="qr-box" id="qrBox">
            <!-- Status overlay (muncul saat payment berhasil) -->
            <div class="payment-status-overlay" id="successOverlay">
                <div class="check-anim"><i class="fa-solid fa-check"></i></div>
                <div style="font-family:'Oswald',sans-serif;font-weight:700;color:var(--green);font-size:1rem;text-transform:uppercase;">Pembayaran Berhasil!</div>
                <div style="font-size:.82rem;color:var(--text-muted);">Mengalihkan ke konfirmasi...</div>
            </div>

            <div class="qris-badge">
                <i class="fa-solid fa-shield-check"></i> QRIS — Semua E-Wallet & Mobile Banking
            </div>

            <!-- QR Image -->
            <div class="qr-image-wrap">
                <?php
                // Pastikan selalu ada QR URL, fallback menggunakan token transaksi PaymentKu
                $finalQrUrl = !empty($qr_url) ? $qr_url : (!empty($booking['payment_token']) ? 'https://paymenku.com/api/qris/' . $booking['payment_token'] : '');
                ?>
                <?php if (! empty($finalQrUrl)): ?>
                    <img src="<?= esc($finalQrUrl) ?>" alt="QR Code Pembayaran" id="qrImage">
                <?php elseif (! empty($qr_string)): ?>
                    <!-- Render QR dari string via API QR generator -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qr_string) ?>&color=000000&bgcolor=FFFFFF&margin=10&format=png"
                         alt="QR Code Pembayaran" id="qrImage">
                <?php elseif (! empty($error_message)): ?>
                    <div class="qr-placeholder" id="qrPlaceholder" style="color: var(--red);">
                        <i class="fa-solid fa-triangle-exclamation" style="opacity:1; color:var(--red);"></i>
                        <span style="font-weight:700;">Gagal Memuat QR</span>
                        <span style="font-size:.75rem;color:var(--text-muted); text-align:center; padding: 0 1rem;"><?= esc($error_message) ?></span>
                    </div>
                <?php else: ?>
                    <div class="qr-placeholder" id="qrPlaceholder">
                        <i class="fa-solid fa-qrcode"></i>
                        <span style="font-weight:700; color:var(--text);">Memuat QR...</span>
                        <span style="font-size:.75rem;color:var(--text-muted);">Menghubungi PaymentKu</span>
                    </div>
                <?php endif; ?>
                <!-- Scanning animation (only when QR present) -->
                <?php if (! empty($finalQrUrl) || ! empty($qr_string)): ?>
                <div class="scan-line" id="scanLine"></div>
                <?php endif; ?>
                <!-- Decorative corners -->
                <div class="qr-corner tl"></div>
                <div class="qr-corner tr"></div>
                <div class="qr-corner bl"></div>
                <div class="qr-corner br"></div>
            </div>

            <!-- Amount -->
            <div class="qr-amount">Rp <?= number_format($jumlah_bayar, 0, ',', '.') ?></div>
            <div class="qr-amount-label">
                <?= $booking['skema_pembayaran'] === 'dp' ? 'DP 50% dari Total' : 'Pembayaran Penuh' ?>
            </div>

            <!-- Countdown ring -->
            <?php
            $expiresTs  = strtotime($booking['expires_at']);
            $totalSecs  = 15 * 60; // 15 menit
            $remaining  = max(0, $expiresTs - time());
            $circumference = 2 * M_PI * 30; // r=30 → 188.5
            ?>
            <div class="countdown-ring" id="countdownRing">
                <svg width="72" height="72" viewBox="0 0 72 72">
                    <circle class="ring-bg"       cx="36" cy="36" r="30"/>
                    <circle class="ring-progress" cx="36" cy="36" r="30" id="ringProgress"
                            style="stroke-dashoffset: <?= $circumference * (1 - $remaining / $totalSecs) ?>"/>
                </svg>
                <div class="ring-label"><span id="countdownText">--:--</span></div>
            </div>
            <div style="font-size:.75rem;color:var(--text-muted);margin-top:-.3rem;">Sisa waktu pembayaran</div>
        </div>

        <!-- ── Kolom kanan: Detail ── -->
        <div>
            <!-- Status bar -->
            <div class="status-bar" id="statusBar">
                <div class="dot"></div>
                <span id="statusText">Menunggu pembayaran...</span>
            </div>

            <!-- Detail transaksi -->
            <div class="detail-box" style="margin-top:1rem;">
                <div class="detail-box-header">
                    <i class="fa-solid fa-receipt"></i> Detail Transaksi
                </div>
                <div class="detail-box-body">
                    <div class="detail-item">
                        <span class="lbl">Lapangan</span>
                        <span class="val"><?= esc($lapangan['nama_lapangan']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="lbl">Tanggal</span>
                        <span class="val"><?= date('d M Y', strtotime($booking['tanggal_main'])) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="lbl">Waktu</span>
                        <span class="val"><?= esc($jam_main_fmt) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="lbl">Nama</span>
                        <span class="val"><?= esc($booking['nama_pemesan']) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="lbl">Skema</span>
                        <span class="val"><?= $booking['skema_pembayaran'] === 'dp' ? 'DP 50%' : 'Full Payment' ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="lbl">Total Harga</span>
                        <span class="val">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="lbl" style="font-weight:700;">Bayar Sekarang</span>
                        <span class="val" style="color:var(--navy);font-family:'Oswald',sans-serif;font-size:1.05rem;font-weight:700;">
                            Rp <?= number_format($jumlah_bayar, 0, ',', '.') ?>
                        </span>
                    </div>
                    <?php if ($booking['skema_pembayaran'] === 'dp'): ?>
                    <div class="detail-item">
                        <span class="lbl">Sisa di Kasir</span>
                        <span class="val" style="color:var(--yellow);font-weight:700;">Rp <?= number_format($booking['total_harga'] * 0.5, 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cara bayar -->
            <div class="detail-box" style="margin-top:1rem;">
                <div class="detail-box-header">
                    <i class="fa-solid fa-circle-info"></i> Cara Pembayaran
                </div>
                <div class="detail-box-body">
                    <div class="steps-mini">
                        <div class="step-mini">
                            <div class="num">1</div>
                            <span>Buka aplikasi <strong style="color:var(--text);">e-wallet atau mobile banking</strong> kamu (GoPay, OVO, DANA, BCA, BRI, dll.).</span>
                        </div>
                        <div class="step-mini">
                            <div class="num">2</div>
                            <span>Pilih menu <strong style="color:var(--text);">Scan QR / QRIS</strong>.</span>
                        </div>
                        <div class="step-mini">
                            <div class="num">3</div>
                            <span>Arahkan kamera ke QR di sebelah kiri dan pastikan nominal benar.</span>
                        </div>
                        <div class="step-mini">
                            <div class="num">4</div>
                            <span>Konfirmasi pembayaran. Status di halaman ini akan <strong style="color:var(--navy);">otomatis diperbarui</strong>.</span>
                        </div>
                    </div>
                    <div style="margin-top:1.25rem;padding:.75rem 1rem;background:var(--navy-dim);border:1px solid rgba(15,32,68,.15);border-radius:8px;font-size:.78rem;color:var(--navy);display:flex;align-items:center;gap:.6rem;">
                        <i class="fa-solid fa-clock" style="font-size:1rem;"></i>
                        <span>QR berlaku <strong><?= ceil($remaining / 60) ?> menit</strong> lagi. Jangan tutup halaman ini!</span>
                    </div>
                </div>
            </div>

        </div><!-- /kolom kanan -->
    </div><!-- /grid -->

    <!-- Bottom link -->
    <div style="text-align:center;margin-top:2.5rem;font-size:.85rem;color:var(--text-muted);">
        Ada masalah?
        <a href="<?= site_url('cek-status?kode=' . esc($booking['booking_code'])) ?>"
           style="color:var(--navy);font-weight:700;text-decoration:underline;">Cek Status Booking</a>
    </div>

</div><!-- /payment-wrap -->

<footer class="footer">
    <p>
        <i class="fa-solid fa-table-tennis-paddle-ball" style="color:var(--navy);margin-right:.3rem;"></i>
        <strong style="color:var(--navy);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?>
    </p>
</footer>

<script>
// ── Config ────────────────────────────────────────────────────
const BOOKING_CODE    = '<?= esc($booking['booking_code']) ?>';
const EXPIRES_AT      = <?= $expiresTs ?> * 1000;
const TOTAL_SECS      = <?= $totalSecs ?>;
const CIRCUMFERENCE   = <?= $circumference ?>;
const REDIRECT_URL    = '<?= site_url('booking/konfirmasi/' . esc($booking['booking_code'])) ?>';
const STATUS_API      = '<?= site_url('api/payment-status') ?>';

let paid = false;

// ── Countdown Timer ───────────────────────────────────────────
const textEl      = document.getElementById('countdownText');
const progressEl  = document.getElementById('ringProgress');

function updateCountdown() {
    const leftMs   = Math.max(0, EXPIRES_AT - Date.now());
    const leftSecs = Math.floor(leftMs / 1000);
    const m = Math.floor(leftSecs / 60);
    const s = leftSecs % 60;

    textEl.textContent = `${m}:${String(s).padStart(2, '0')}`;

    // Ring progress
    const ratio  = leftSecs / TOTAL_SECS;
    const offset = CIRCUMFERENCE * (1 - ratio);
    progressEl.style.strokeDashoffset = offset;

    // Warna ring saat hampir habis
    if (ratio < 0.2) {
        progressEl.style.stroke = '#EF4444';
        textEl.style.color      = '#EF4444';
    } else if (ratio < 0.4) {
        progressEl.style.stroke = '#F59E0B';
        textEl.style.color      = '#F59E0B';
    }

    if (leftMs === 0 && ! paid) {
        textEl.textContent = '0:00';
        stopScanLine();
        document.getElementById('statusText').textContent = 'Waktu habis. Booking kadaluarsa.';
        clearInterval(pollTimer);
        clearInterval(countdownTimer);
    }
}

const countdownTimer = setInterval(updateCountdown, 1000);
updateCountdown();

// ── Auto Poll Status ──────────────────────────────────────────
async function pollStatus() {
    if (paid) return;
    try {
        const res  = await fetch(`${STATUS_API}?kode=${BOOKING_CODE}`);
        const data = await res.json();

        if (data.status === 'success') {
            paid = true;
            clearInterval(pollTimer);
            clearInterval(countdownTimer);
            stopScanLine();
            showSuccessOverlay();
            updateStatusBar('success');
            setTimeout(() => window.location.href = REDIRECT_URL, 2200);
        } else if (data.status === 'expired' || data.status === 'failed') {
            clearInterval(pollTimer);
            clearInterval(countdownTimer);
            document.getElementById('statusText').textContent = 'Pembayaran gagal / kadaluarsa.';
        }
    } catch (e) {
        // Gagal poll — tidak perlu action
    }
}

// Poll setiap 4 detik
const pollTimer = setInterval(pollStatus, 4000);
pollStatus(); // Langsung cek saat load

// ── UI Helpers ────────────────────────────────────────────────
function showSuccessOverlay() {
    document.getElementById('successOverlay').classList.add('show');
}

function stopScanLine() {
    const sl = document.getElementById('scanLine');
    if (sl) sl.style.display = 'none';
}

function updateStatusBar(status) {
    const bar = document.getElementById('statusBar');
    if (status === 'success') {
        bar.className = 'status-bar success-bar';
        document.getElementById('statusText').textContent = '✓ Pembayaran dikonfirmasi!';
    }
}
</script>

</body>
</html>
