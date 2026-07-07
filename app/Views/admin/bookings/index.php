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
    <div class="table-card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <span class="table-card-title">
                <i class="fa-solid fa-clipboard-list"></i> Daftar Booking
            </span>
            <span style="font-size:.75rem;color:var(--text-muted); margin-left:.5rem;"><?= count($bookings) ?> data</span>
        </div>
        <a href="<?= site_url('admin/bookings/create') ?>" class="btn btn-volt btn-sm" style="font-size:.8rem;">
            <i class="fa-solid fa-plus"></i> Tambah Booking
        </a>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>Lapangan</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                    <th>Aksi</th>
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
                    <td>
                        <?= esc($b['nama_pemesan']) ?>
                        <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                            <?= esc($b['nomor_wa']) ?>
                        </div>
                    </td>
                    <td><?= esc($b['nama_lapangan']) ?></td>
                    <td class="col-jam">
                        <div style="white-space:nowrap; font-weight:600; color:var(--text); margin-bottom:2px;">
                            <?= date('d M Y', strtotime($b['tanggal_main'])) ?>
                        </div>
                        <?= format_jam_main($b['jam_main']) ?>
                    </td>
                    <td>
                        <?php
                            $badgeClass = '';
                            $badgeText = '';
                            if ($b['status'] === 'success') {
                                if ($b['status_pelunasan'] === 'lunas') {
                                    $badgeClass = 'badge-success';
                                    $badgeText = 'LUNAS';
                                } else {
                                    $badgeClass = 'badge-dp';
                                    $badgeText = 'SUDAH DP';
                                }
                            } elseif ($b['status'] === 'pending') {
                                $badgeClass = 'badge-pending';
                                $badgeText = 'MENUNGGU BAYAR';
                            } elseif ($b['status'] === 'expired') {
                                $badgeClass = 'badge-expired';
                                $badgeText = 'EXPIRED';
                            } elseif ($b['status'] === 'failed') {
                                $badgeClass = 'badge-failed';
                                $badgeText = 'GAGAL';
                            }
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= $badgeText ?>
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline btn-sm" onclick="openDetail(<?= htmlspecialchars(json_encode($b), ENT_QUOTES, 'UTF-8') ?>)">
                            <i class="fa-solid fa-file-lines"></i> Detail
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="confirm-overlay" onclick="closeDetail(event)">
    <div class="confirm-box detail-box" onclick="event.stopPropagation()">
        <h3>Detail Booking</h3>
        
        <div class="detail-grid">
            <strong>Kode Booking</strong>
            <span id="dtl-kode" class="booking-code" style="width:fit-content"></span>
            
            <strong>Nama Pemesan</strong>
            <span id="dtl-pemesan"></span>
            
            <strong>Nomor WA</strong>
            <span id="dtl-wa"></span>
            
            <strong>Lapangan</strong>
            <span id="dtl-lapangan"></span>
            
            <strong>Tgl & Jam Main</strong>
            <span id="dtl-jadwal"></span>
            
            <strong>Skema Byr</strong>
            <span id="dtl-skema"></span>
            
            <strong>Total Harga</strong>
            <span id="dtl-total"></span>
            
            <strong>Telah Dibayar</strong>
            <span id="dtl-dibayar" style="color:var(--green);font-weight:600;"></span>
            
            <strong>Sisa Tagihan</strong>
            <span id="dtl-sisa" style="color:var(--yellow);font-weight:600;"></span>
            
            <strong>Status Booking</strong>
            <span id="dtl-status"></span>
        </div>

        <div class="confirm-actions">
            <button type="button" class="btn btn-outline" onclick="closeDetail()" style="width: 100%; justify-content:center;">Tutup</button>
        </div>
    </div>
</div>

<script>
function formatRupiah(number) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
}

function openDetail(data) {
    document.getElementById('dtl-kode').textContent = data.booking_code;
    document.getElementById('dtl-pemesan').textContent = data.nama_pemesan;
    document.getElementById('dtl-wa').textContent = data.nomor_wa;
    document.getElementById('dtl-lapangan').textContent = data.nama_lapangan;
    
    // Format Date: d M Y
    const dateObj = new Date(data.tanggal_main);
    const dateStr = dateObj.toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});
    document.getElementById('dtl-jadwal').textContent = dateStr + ' | ' + formatJamMain(data.jam_main);
    
    const skemaText = data.skema_pembayaran === 'dp' ? 'DP 50%' : 'Full Payment';
    const statusPelunasanText = data.status_pelunasan === 'lunas' ? '(Lunas)' : '(Belum Lunas)';
    document.getElementById('dtl-skema').textContent = `${skemaText} ${statusPelunasanText}`;
    
    document.getElementById('dtl-total').textContent = formatRupiah(data.total_harga);
    document.getElementById('dtl-dibayar').textContent = formatRupiah(data.jumlah_dibayar);
    document.getElementById('dtl-sisa').textContent = data.sisa_tagihan > 0 ? formatRupiah(data.sisa_tagihan) : '—';
    
    document.getElementById('dtl-status').textContent = data.status.toUpperCase();
    
    document.getElementById('detailModal').classList.add('open');
}

function closeDetail(e) {
    document.getElementById('detailModal').classList.remove('open');
}

function formatJamMain(jamArr) {
    if (!jamArr) return '';
    try {
        let jam = typeof jamArr === 'string' ? JSON.parse(jamArr) : jamArr;
        return jam.map(j => j.substring(0,5)).join(', ');
    } catch(e) {
        return jamArr;
    }
}
</script>

<?php $this->endSection() ?>
