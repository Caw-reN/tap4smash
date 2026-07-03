<?php $this->extend('admin/layouts/main') ?>
<?php $this->section('content') ?>

<!-- Stats Dashboard — Hierarchical Layout -->

<!-- ═══ TIER 1: Hero Cards — KPI Utama Harian ═══ -->
<div class="stats-hero-row">

    <div class="stat-card stat-hero green">
        <div class="stat-hero-bg-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div class="stat-top">
            <div class="stat-label stat-label-lg">Pemasukan Hari Ini</div>
            <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        </div>
        <div class="stat-value stat-hero-value">Rp <?= number_format($total_revenue_hari, 0, ',', '.') ?></div>
        <div class="stat-hero-sub">Total pendapatan yang masuk hari ini</div>
    </div>

    <div class="stat-card stat-hero volt">
        <div class="stat-hero-bg-icon"><i class="fa-solid fa-calendar-day"></i></div>
        <div class="stat-top">
            <div class="stat-label stat-label-lg">Booking Hari Ini</div>
            <div class="stat-icon"><i class="fa-solid fa-calendar-day"></i></div>
        </div>
        <div class="stat-value stat-hero-value"><?= $total_booking_hari ?></div>
        <div class="stat-hero-sub">Reservasi terkonfirmasi untuk hari ini</div>
    </div>

</div>

<!-- ═══ TIER 2: Alert Cards — Perlu Tindakan ═══ -->
<div class="stats-alert-row">

    <div class="stat-card stat-alert yellow">
        <div class="stat-top">
            <div class="stat-label stat-label-md">Menunggu Pelunasan</div>
            <div class="stat-icon"><i class="fa-solid fa-credit-card"></i></div>
        </div>
        <div class="stat-value stat-alert-value"><?= $pending_pelunasan ?></div>
        <div class="stat-alert-tag"><i class="fa-solid fa-triangle-exclamation"></i> Perlu ditindaklanjuti</div>
    </div>

    <div class="stat-card stat-alert blue">
        <div class="stat-top">
            <div class="stat-label stat-label-md">Pending Pembayaran</div>
            <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
        </div>
        <div class="stat-value stat-alert-value"><?= $pending_payment ?></div>
        <div class="stat-alert-tag"><i class="fa-solid fa-clock"></i> Menunggu konfirmasi</div>
    </div>

    <div class="stat-card stat-alert navy">
        <div class="stat-top">
            <div class="stat-label stat-label-md">Check-in Hari Ini</div>
            <div class="stat-icon"><i class="fa-solid fa-door-open"></i></div>
        </div>
        <div class="stat-value stat-alert-value"><?= $checkin_hari ?></div>
        <div class="stat-alert-tag"><i class="fa-solid fa-check-circle"></i> Sudah masuk GOR</div>
    </div>

</div>

<!-- ═══ TIER 3: Info Cards — Data Rekap & Statis ═══ -->
<div class="stats-info-row">

    <div class="stat-card stat-info green">
        <div class="stat-top">
            <div class="stat-label stat-label-sm">Revenue Bulan Ini</div>
            <div class="stat-icon stat-icon-sm"><i class="fa-solid fa-chart-line"></i></div>
        </div>
        <div class="stat-value stat-info-value">Rp <?= number_format($revenue_bulan, 0, ',', '.') ?></div>
        <div class="stat-info-sub"><?= date('F Y') ?></div>
    </div>

    <div class="stat-card stat-info volt">
        <div class="stat-top">
            <div class="stat-label stat-label-sm">Booking Bulan Ini</div>
            <div class="stat-icon stat-icon-sm"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
        <div class="stat-value stat-info-value"><?= $total_booking_bulan ?></div>
        <div class="stat-info-sub">transaksi sukses bulan ini</div>
    </div>

    <div class="stat-card stat-info navy">
        <div class="stat-top">
            <div class="stat-label stat-label-sm">Total Lapangan</div>
            <div class="stat-icon stat-icon-sm"><i class="fa-solid fa-building"></i></div>
        </div>
        <div class="stat-value stat-info-value"><?= $total_lapangan ?></div>
        <div class="stat-info-sub">lapangan terdaftar</div>
    </div>

</div>


<!-- Shortcut Actions -->
<div style="display:flex;gap:.75rem;margin-bottom:2rem;flex-wrap:wrap;">
    <?php if ($pending_pelunasan > 0): ?>
    <a href="<?= site_url('admin/pelunasan') ?>" class="btn btn-yellow">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <?= $pending_pelunasan ?> Transaksi Menunggu Pelunasan
    </a>
    <?php endif; ?>
    <a href="<?= site_url('admin/lapangan/create') ?>" class="btn btn-volt">
        <i class="fa-solid fa-plus"></i> Tambah Lapangan
    </a>
    <a href="<?= site_url('admin/bookings') ?>" class="btn btn-outline">
        <i class="fa-solid fa-clipboard-list"></i> Lihat Semua Booking
    </a>
</div>

<!-- Recent Bookings -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="fa-solid fa-clipboard-list"></i> Booking Terbaru
        </span>
        <a href="<?= site_url('admin/bookings') ?>" class="btn btn-outline btn-sm">
            Lihat Semua <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Skema</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_bookings)): ?>
                <tr class="empty-row">
                    <td colspan="7">
                        <i class="fa-solid fa-inbox" style="font-size:1.5rem;margin-bottom:.5rem;display:block;color:var(--slate-light)"></i>
                        Belum ada booking hari ini
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($recent_bookings as $b): ?>
                <tr>
                    <td><span class="booking-code"><?= esc($b['booking_code']) ?></span></td>
                    <td><?= esc($b['nama_pemesan']) ?></td>
                    <td><?= esc($b['nama_lapangan']) ?></td>
                    <td><?= date('d M Y', strtotime($b['tanggal_main'])) ?></td>
                    <td><?= format_jam_main($b['jam_main']) ?></td>
                    <td>
                        <span class="badge <?= $b['skema_pembayaran'] === 'dp' ? 'badge-dp' : 'badge-full' ?>">
                            <?= $b['skema_pembayaran'] === 'dp' ? 'DP 50%' : 'Lunas' ?>
                        </span>
                    </td>
                    <td><span class="badge badge-success">Sukses</span></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->endSection() ?>
