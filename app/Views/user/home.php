<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap4Smash — GOR Badminton & Tenis Meja</title>
    <meta name="description" content="Booking lapangan badminton dan tenis meja online di GOR Tap4Smash. Cepat, mudah, langsung konfirmasi via WhatsApp.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,700;0,900;1,900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('css/user.css') ?>">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────────── -->
<nav class="navbar">
    <div class="navbar-brand">
        <span class="brand-icon"><i class="fa-solid fa-table-tennis-paddle-ball"></i></span>
        <h1>Tap4Smash <span>GOR Sport Center</span></h1>
    </div>
    <div class="navbar-links">
        <a href="#lapangan" class="nav-link">Lapangan</a>
        <a href="#cara-booking" class="nav-link">Cara Booking</a>
        <a href="<?= site_url('cek-status') ?>" class="nav-link">Cek Status</a>
        <a href="<?= site_url('booking') ?>" class="btn-book-nav">
            <i class="fa-solid fa-calendar-plus"></i> Booking Sekarang
        </a>
    </div>
</nav>

<!-- ── Hero ────────────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-grid"></div>
    <div style="position:relative;z-index:1;">
        <div class="hero-badge">
            <i class="fa-solid fa-bolt"></i> Booking Online 24 Jam
        </div>
        <h2>Main Badminton &<br><em>Tenis Meja</em> Kapan Saja</h2>
        <p>GOR Tap4Smash hadir untuk memudahkan kamu booking lapangan olahraga favorit secara online. Pilih waktu, bayar, langsung main!</p>
        <div class="hero-actions">
            <a href="<?= site_url('booking') ?>" class="btn-primary">
                <i class="fa-solid fa-calendar-plus"></i> Booking Sekarang
            </a>
            <a href="<?= site_url('cek-status') ?>" class="btn-secondary">
                <i class="fa-solid fa-magnifying-glass"></i> Cek Status Booking
            </a>
        </div>
    </div>
</section>

<!-- ── Stats Strip ──────────────────────────────────────────────── -->
<div class="stats-strip">
    <div class="strip-stat">
        <span class="num"><?= count($lapangans) ?>+</span>
        <span class="lbl">Lapangan Tersedia</span>
    </div>
    <div class="strip-stat">
        <span class="num">24/7</span>
        <span class="lbl">Booking Online</span>
    </div>
    <div class="strip-stat">
        <span class="num">WA</span>
        <span class="lbl">Konfirmasi Otomatis</span>
    </div>
    <div class="strip-stat">
        <span class="num">DP</span>
        <span class="lbl">Bisa Bayar Setengah</span>
    </div>
</div>

<!-- ── Daftar Lapangan ──────────────────────────────────────────── -->
<section class="section" id="lapangan">
    <div class="section-label"><i class="fa-solid fa-building"></i> Fasilitas Kami</div>
    <h2 class="section-title">Pilihan Lapangan</h2>
    <p class="section-desc">Semua lapangan kami dilengkapi fasilitas modern dan siap digunakan untuk latihan maupun kompetisi.</p>

    <?php if (empty($lapangans)): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Belum ada lapangan aktif. Silakan hubungi pengelola GOR.
    </div>
    <?php else: ?>
    <div class="lapangan-grid">
        <?php foreach ($lapangans as $idx => $l): ?>
        <div class="lapangan-card">
            <div class="lapangan-card-top">
                <i class="fa-solid fa-table-tennis-paddle-ball court-icon"></i>
                <span class="lapangan-num">Lapangan <?= str_pad($idx + 1, 2, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="lapangan-card-body">
                <h3><?= esc($l['nama_lapangan']) ?></h3>
                <p class="jenis">
                    <?php if (! empty($l['jenis_lapangan'])): ?>
                        <i class="fa-solid fa-tag" style="margin-right:4px;color:var(--text-muted);font-size:.7rem;"></i>
                        <?= esc($l['jenis_lapangan']) ?>
                    <?php else: ?>
                        <i class="fa-solid fa-tag" style="margin-right:4px;color:var(--text-muted);font-size:.7rem;"></i>
                        Lapangan Standar
                    <?php endif; ?>
                </p>
                <div class="price">
                    Rp <?= number_format($l['harga_per_jam'], 0, ',', '.') ?>
                    <span>/ jam</span>
                </div>
                <a href="<?= site_url('booking?lapangan_id=' . $l['id']) ?>" class="btn-book-card">
                    <i class="fa-solid fa-calendar-plus"></i> Booking Lapangan Ini
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ── Cara Booking ─────────────────────────────────────────────── -->
<section style="background:var(--surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
    <div class="section" id="cara-booking">
        <div class="section-label"><i class="fa-solid fa-circle-info"></i> Panduan</div>
        <h2 class="section-title">Cara Booking</h2>
        <p class="section-desc">Proses booking cepat dan mudah. Hanya 4 langkah untuk mengamankan lapanganmu!</p>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">1</div>
                <h3>Pilih Lapangan</h3>
                <p>Pilih lapangan yang tersedia sesuai kebutuhanmu — badminton atau tenis meja.</p>
            </div>
            <div class="step-card">
                <div class="step-num">2</div>
                <h3>Tentukan Jadwal</h3>
                <p>Pilih tanggal dan jam main. Sistem akan otomatis menampilkan slot yang masih kosong.</p>
            </div>
            <div class="step-card">
                <div class="step-num">3</div>
                <h3>Pilih Pembayaran</h3>
                <p>Bayar lunas atau cukup DP 50% terlebih dahulu. Pelunasan bisa di kasir GOR.</p>
            </div>
            <div class="step-card">
                <div class="step-num">4</div>
                <h3>Konfirmasi WA</h3>
                <p>Setelah pembayaran dikonfirmasi admin, kamu akan mendapat notifikasi via WhatsApp.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ─────────────────────────────────────────────────────── -->
<section style="padding:4rem 2rem;text-align:center;background:radial-gradient(ellipse 60% 60% at 50% 100%, rgba(204,255,0,.08) 0%, transparent 70%);">
    <h2 style="font-family:'Montserrat',sans-serif;font-weight:900;font-size:1.8rem;text-transform:uppercase;margin-bottom:1rem;">
        Siap Untuk Bermain?
    </h2>
    <p style="color:var(--text-muted);margin-bottom:2rem;font-size:.92rem;">Amankan slot lapanganmu sekarang sebelum penuh!</p>
    <a href="<?= site_url('booking') ?>" class="btn-primary" style="font-size:1rem;padding:1rem 2.5rem;">
        <i class="fa-solid fa-calendar-plus"></i> Booking Sekarang
    </a>
</section>

<!-- ── Footer ──────────────────────────────────────────────────── -->
<footer class="footer">
    <p>
        <i class="fa-solid fa-table-tennis-paddle-ball" style="color:var(--volt);margin-right:.3rem;"></i>
        <strong style="color:var(--volt);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?>
    </p>
    <p style="margin-top:.4rem;">
        <a href="<?= site_url('cek-status') ?>" style="color:var(--text-muted);margin-right:1rem;">Cek Status Booking</a>
        <a href="<?= site_url('admin/login') ?>" style="color:var(--text-muted);">Admin Panel</a>
    </p>
</footer>

</body>
</html>
