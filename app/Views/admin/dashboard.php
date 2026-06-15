<?php $this->extend('admin/layouts/main') ?>
<?php $this->section('content') ?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card volt">
        <div class="stat-icon"><i class="fa-solid fa-calendar-day"></i></div>
        <div class="stat-value"><?= $total_booking_hari ?></div>
        <div class="stat-label">Booking Hari Ini</div>
    </div>
    
    <div class="stat-card green">
        <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div class="stat-value">Rp <?= number_format($total_revenue_hari, 0, ',', '.') ?></div>
        <div class="stat-label">Pemasukan Hari Ini</div>
    </div>
    
    <div class="stat-card yellow">
        <div class="stat-icon"><i class="fa-solid fa-credit-card"></i></div>
        <div class="stat-value"><?= $pending_pelunasan ?></div>
        <div class="stat-label">Menunggu Pelunasan</div>
    </div>
    
    <div class="stat-card blue">
        <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
        <div class="stat-value"><?= $pending_payment ?></div>
        <div class="stat-label">Pending Pembayaran</div>
    </div>
    
    <div class="stat-card volt">
        <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
        <div class="stat-value"><?= $total_lapangan ?></div>
        <div class="stat-label">Total Lapangan</div>
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
