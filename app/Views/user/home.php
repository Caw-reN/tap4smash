<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap4Smash — GOR Badminton</title>
    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.png') ?>?v=<?= filemtime(FCPATH.'favicon.png') ?>">
    <meta name="description" content="Booking lapangan badminton online di GOR Tap4Smash. Cepat, mudah, langsung konfirmasi via WhatsApp.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('css/user.css') ?>">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────────── -->
<nav class="navbar">
    <div class="navbar-brand">
        <?php
            $__logoExts=['png','jpg','jpeg','webp'];
            $__logo=null;
            foreach($__logoExts as $__e){
                if(file_exists(FCPATH.'img/logo.'.$__e)){
                    $__logo=base_url('img/logo.'.$__e).'?v='.filemtime(FCPATH.'img/logo.'.$__e);
                    break;
                }
            }
        ?>
        <?php if($__logo): ?>
        <a href="<?= site_url('/') ?>"><img src="<?= $__logo ?>" alt="Tap4Smash" style="height:38px;width:auto;"></a>
        <?php else: ?>
        <span class="brand-icon"><i class="fa-solid fa-table-tennis-paddle-ball"></i></span>
        <h1>Tap4Smash <span>GOR Sport Center</span></h1>
        <?php endif; ?>
    </div>
    <div class="navbar-links">
        <a href="#lapangan" class="nav-link">Lapangan</a>
        <a href="#cara-booking" class="nav-link">Cara Booking</a>
        <a href="#lokasi" class="nav-link">Lokasi</a>
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
        <h2>SMASH LEBIH <span class="text-highlight">KERAS</span>,<br>BOOKING LEBIH <span class="text-highlight">CEPAT</span></h2>
        <p>Tap4Smash hadir untuk memudahkan kamu booking lapangan badminton secara online. Pilih jadwal kosong, bayar instan, dan langsung main tanpa antre!</p>
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
        <span class="num"><span class="counter" data-target="<?= count($lapangans) ?>">0</span></span>
        <span class="lbl">Lapangan Premium</span>
    </div>
    <div class="strip-stat">
        <span class="num">&lt; <span class="counter" data-target="1">0</span></span>
        <span class="lbl">Menit Proses Booking</span>
    </div>
    <div class="strip-stat">
        <span class="num"><span class="counter" data-target="100">0</span>%</span>
        <span class="lbl">Konfirmasi Otomatis</span>
    </div>
    <div class="strip-stat">
        <span class="num"><span class="counter" data-target="50">0</span>%</span>
        <span class="lbl">Bisa DP Dulu</span>
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
        <?php $isMaint = empty($l['is_active']); ?>
        <div class="lapangan-card <?= $isMaint ? 'maintenance' : '' ?>">
            <div class="lapangan-card-top">
                <?php if (!empty($l['foto'])): ?>
                <img src="<?= base_url('img/lapangans/' . esc($l['foto'])) ?>"
                     alt="<?= esc($l['nama_lapangan']) ?>"
                     style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.85;">
                <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(10,21,48,.2), rgba(10,21,48,.6));"></div>
                <?php else: ?>
                <i class="fa-solid fa-table-tennis-paddle-ball court-icon"></i>
                <?php endif; ?>
                <?php if ($isMaint): ?>
                <span class="card-avail-badge"><i class="fa-solid fa-wrench"></i> Maintenance</span>
                <?php else: ?>
                <span class="card-avail-badge"><i class="fa-solid fa-circle" style="font-size:.45rem;color:#4ade80;"></i> Tersedia</span>
                <?php endif; ?>
            </div>
            <div class="lapangan-card-body">
                <h3><?= esc($l['nama_lapangan']) ?></h3>
                <p class="jenis">
                    <i class="fa-solid fa-layer-group"></i>
                    <?= !empty($l['jenis_lapangan']) ? esc($l['jenis_lapangan']) : 'Lapangan Standar' ?>
                </p>
                <div class="price">
                    Rp <?= number_format($l['harga_per_jam'], 0, ',', '.') ?>
                    <span>/ jam</span>
                </div>
                <?php if ($isMaint): ?>
                <button type="button" class="btn-book-card" tabindex="-1">
                    <i class="fa-solid fa-wrench"></i> Sedang Perawatan
                </button>
                <?php else: ?>
                <a href="<?= site_url('booking?lapangan_id=' . $l['id']) ?>" class="btn-book-card">
                    <i class="fa-solid fa-calendar-plus"></i> Booking Sekarang
                    <i class="fa-solid fa-arrow-right" style="margin-left:auto;font-size:.7rem;opacity:.6;"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ── Cara Booking ─────────────────────────────────────────────── -->
<section class="steps-section" id="cara-booking">
    <div class="section">
        <h2 class="section-title">Cara Booking Lapangan</h2>
        <p class="section-desc">Cuma 6 langkah — dari pilih lapangan sampai langsung main di GOR!</p>

        <div class="steps-timeline">

            <div class="step-item">
                <div class="step-dot"><i class="fa-solid fa-table-tennis-paddle-ball"></i><span class="step-num-badge">1</span></div>
                <div class="step-body">
                    <h3 class="step-title">Pilih lapangan yang mau kamu main</h3>
                    <p class="step-desc">Klik tombol Booking Sekarang di lapangan pilihanmu.</p>
                </div>
            </div>

            <div class="step-item">
                <div class="step-dot"><i class="fa-solid fa-calendar-days"></i><span class="step-num-badge">2</span></div>
                <div class="step-body">
                    <h3 class="step-title">Pilih kapan mau main</h3>
                    <p class="step-desc">Pilih tanggal & jam. Slot yang sudah penuh otomatis terkunci.</p>
                </div>
            </div>

            <div class="step-item">
                <div class="step-dot"><i class="fa-solid fa-user"></i><span class="step-num-badge">3</span></div>
                <div class="step-body">
                    <h3 class="step-title">Isi nama &amp; nomor WhatsApp</h3>
                    <p class="step-desc">Nomor WA aktif kamu buat nerima konfirmasi booking.</p>
                </div>
            </div>

            <div class="step-item">
                <div class="step-dot"><i class="fa-solid fa-qrcode"></i><span class="step-num-badge">4</span></div>
                <div class="step-body">
                    <h3 class="step-title">Scan QRIS &amp; bayar</h3>
                    <p class="step-desc">Scan kode QRIS yang muncul, bayar sesuai total. Cepet!</p>
                </div>
            </div>

            <div class="step-item">
                <div class="step-dot"><i class="fa-brands fa-whatsapp"></i><span class="step-num-badge">5</span></div>
                <div class="step-body">
                    <h3 class="step-title">Tiket langsung dikirim ke WA</h3>
                    <p class="step-desc">Sistem otomatis kirim tiket digital ke WA kamu begitu bayar berhasil.</p>
                </div>
            </div>

            <div class="step-item">
                <div class="step-dot step-dot--accent"><i class="fa-solid fa-qrcode"></i><span class="step-num-badge">6</span></div>
                <div class="step-body">
                    <h3 class="step-title">Datang &amp; scan QR — langsung main!</h3>
                    <p class="step-desc">Tunjukin tiket ke petugas, scan QR, selesai!</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── Lokasi Kami ──────────────────────────────────────── -->
<section class="lokasi-section" id="lokasi">
    <div class="section" style="max-width:1100px;">
        <div class="section-label"><i class="fa-solid fa-location-dot"></i> Temukan Kami</div>
        <h2 class="section-title">Lokasi GOR</h2>
        <?php if (!empty($maps_label) || !empty($maps_address)): ?>
        <div class="lokasi-meta">
            <?php if (!empty($maps_label)): ?>
            <div class="lokasi-meta-item">
                <i class="fa-solid fa-building"></i>
                <span><?= esc($maps_label) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($maps_address)): ?>
            <div class="lokasi-meta-item">
                <i class="fa-solid fa-map-pin"></i>
                <span><?= esc($maps_address) ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="maps-wrapper">
            <?php if (!empty($maps_embed_url)): ?>
            <iframe
                src="<?= esc($maps_embed_url) ?>"
                class="maps-iframe"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Lokasi <?= esc($maps_label ?: 'GOR Tap4Smash') ?>">
            </iframe>
            <?php else: ?>
            <div class="maps-placeholder">
                <div class="maps-placeholder-inner">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <p>Lokasi belum dikonfigurasi</p>
                    <span>Hubungi pengelola GOR untuk informasi lokasi</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($maps_embed_url)): ?>
        <?php
            // Ekstrak koordinat dari embed URL untuk link Google Maps langsung
            // Format: https://www.google.com/maps/embed?pb=...
            $mapsDirectUrl = '';
            if (strpos($maps_embed_url, 'google.com/maps/embed') !== false) {
                $mapsDirectUrl = str_replace('/maps/embed', '/maps', $maps_embed_url);
                $mapsDirectUrl = preg_replace('/[?&]pb=[^&]*/', '', $mapsDirectUrl);
                $mapsDirectUrl = 'https://maps.google.com/';
            }
        ?>
        <div class="maps-actions">
            <a href="https://maps.google.com/maps?q=<?= urlencode($maps_label . ($maps_address ? ', ' . $maps_address : '')) ?>"
               target="_blank" rel="noopener" class="btn-maps-open">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Buka di Google Maps
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ── CTA ─────────────────────────────────────────────────────── -->
<section style="padding:4rem 2rem;text-align:center;background:radial-gradient(ellipse 60% 60% at 50% 100%, rgba(204,255,0,.08) 0%, transparent 70%);">
    <h2 style="font-family:'Oswald',sans-serif;font-weight:700;font-size:1.8rem;text-transform:uppercase;margin-bottom:1rem;">
        Siap Untuk Bermain?
    </h2>
    <p style="color:var(--text-muted);margin-bottom:2rem;font-size:.92rem;">Amankan slot lapanganmu sekarang sebelum penuh!</p>
    <a href="<?= site_url('booking') ?>" class="btn-primary" style="font-size:1rem;padding:1rem 2.5rem;">
        <i class="fa-solid fa-calendar-plus"></i> Booking Sekarang
    </a>
</section>

<!-- ── Footer ──────────────────────────────────────────── -->
<footer class="footer">
    <p>
        <i class="fa-solid fa-table-tennis-paddle-ball" style="color:var(--accent);margin-right:.3rem;"></i>
        <strong style="color:var(--accent);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?>
    </p>
    <p style="margin-top:.4rem;">
        <a href="<?= site_url('cek-status') ?>" style="color:rgba(255,255,255,.45);margin-right:1rem;">Cek Status Booking</a>
        <a href="<?= site_url('admin/login') ?>" style="color:rgba(255,255,255,.45);">Admin Panel</a>
    </p>
</footer>

<!-- ── Script Animasi Counter ─────────────────────────────────── -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");
    
    const animate = (counter) => {
        const target = +counter.getAttribute("data-target");
        const duration = 2000; // Durasi animasi 2 detik
        
        let startTime = null;
        const step = (timestamp) => {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);
            
            // Efek Ease-out (melambat di akhir)
            const easeProgress = progress * (2 - progress);
            
            counter.innerText = Math.floor(easeProgress * target);
            
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                counter.innerText = target;
            }
        };
        window.requestAnimationFrame(step);
    };

    // Trigger animasi ketika elemen terlihat di layar
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animate(entry.target);
                obs.unobserve(entry.target); // Mainkan sekali saja
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
});
</script>

</body>
</html>
