<?php $this->extend('admin/layouts/main') ?>
<?php $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success">
    <i class="fa-solid fa-circle-check"></i> <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-error">
    <i class="fa-solid fa-triangle-exclamation"></i> <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<!-- Filter Bar -->
<form method="get" action="">
<div class="filter-bar">
    <div class="filter-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" value="<?= esc($filters['tanggal'] ?? '') ?>">
    </div>
    <div class="filter-group">
        <label>Lapangan</label>
        <select name="lapangan_id">
            <option value="">Semua Lapangan</option>
            <?php foreach ($lapangans as $l): ?>
            <option value="<?= $l['id'] ?>" <?= ($filters['lapangan_id'] ?? '') == $l['id'] ? 'selected' : '' ?>>
                <?= esc($l['nama_lapangan']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label>Status</label>
        <select name="status">
            <option value="">Semua Status</option>
            <option value="pending"  <?= ($filters['status'] ?? '') === 'pending'  ? 'selected' : '' ?>>Pending</option>
            <option value="success"  <?= ($filters['status'] ?? '') === 'success'  ? 'selected' : '' ?>>Sukses</option>
            <option value="expired"  <?= ($filters['status'] ?? '') === 'expired'  ? 'selected' : '' ?>>Expired</option>
            <option value="failed"   <?= ($filters['status'] ?? '') === 'failed'   ? 'selected' : '' ?>>Gagal</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Pelunasan</label>
        <select name="status_pelunasan">
            <option value="">Semua</option>
            <option value="lunas"       <?= ($filters['status_pelunasan'] ?? '') === 'lunas'       ? 'selected' : '' ?>>Lunas</option>
            <option value="belum_lunas" <?= ($filters['status_pelunasan'] ?? '') === 'belum_lunas' ? 'selected' : '' ?>>Belum Lunas</option>
        </select>
    </div>
    <button type="submit" class="btn btn-volt" style="align-self:flex-end;">
        <i class="fa-solid fa-filter"></i> Filter
    </button>
    <a href="<?= site_url('admin/bookings') ?>" class="btn btn-outline" style="align-self:flex-end;">
        <i class="fa-solid fa-rotate-left"></i> Reset
    </a>
</div>
</form>

<!-- Table -->
<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="fa-solid fa-clipboard-list"></i> Daftar Booking
        </span>
        <span style="font-size:.75rem;color:var(--text-muted);"><?= count($bookings) ?> data</span>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>No. WA</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Skema</th>
                    <th>Dibayar</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th>Pelunasan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                <tr class="empty-row">
                    <td colspan="11">
                        <i class="fa-solid fa-inbox" style="font-size:1.5rem;margin-bottom:.5rem;display:block;color:var(--slate-light)"></i>
                        Tidak ada data booking
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><span class="booking-code"><?= esc($b['booking_code']) ?></span></td>
                    <td><?= esc($b['nama_pemesan']) ?></td>
                    <td style="color:var(--text-muted);font-size:.77rem;"><?= esc($b['nomor_wa']) ?></td>
                    <td><?= esc($b['nama_lapangan']) ?></td>
                    <td><?= date('d M Y', strtotime($b['tanggal_main'])) ?></td>
                    <td><?= format_jam_main($b['jam_main']) ?></td>
                    <td>
                        <span class="badge <?= $b['skema_pembayaran'] === 'dp' ? 'badge-dp' : 'badge-full' ?>">
                            <?= $b['skema_pembayaran'] === 'dp' ? 'DP 50%' : 'Lunas' ?>
                        </span>
                    </td>
                    <td>Rp <?= number_format($b['jumlah_dibayar'], 0, ',', '.') ?></td>
                    <td>
                        <?php if ($b['sisa_tagihan'] > 0): ?>
                            <span style="color:var(--yellow);">Rp <?= number_format($b['sisa_tagihan'], 0, ',', '.') ?></span>
                        <?php else: ?>
                            <span style="color:var(--text-muted);">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $statusMap = ['pending'=>'badge-pending','success'=>'badge-success','expired'=>'badge-expired','failed'=>'badge-failed']; ?>
                        <span class="badge <?= $statusMap[$b['status']] ?? '' ?>">
                            <?= ucfirst($b['status']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $b['status_pelunasan'] === 'lunas' ? 'badge-lunas' : 'badge-belum' ?>">
                            <?= $b['status_pelunasan'] === 'lunas' ? 'Lunas' : 'Belum' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->endSection() ?>
