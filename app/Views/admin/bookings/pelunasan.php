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

<div style="margin-bottom:1.25rem;padding:1rem 1.25rem;background:var(--yellow-dim,rgba(245,158,11,.12));border:1px solid rgba(245,158,11,.3);border-left:3px solid var(--yellow,#F59E0B);font-size:.82rem;color:#FDE68A;display:flex;gap:.6rem;align-items:flex-start;border-radius:6px;">
    <i class="fa-solid fa-triangle-exclamation" style="margin-top:2px;flex-shrink:0;"></i>
    <span>
        Pelanggan yang memilih <strong>DP 50%</strong> wajib melunasi sisa tagihan di kasir GOR sebelum bermain.
        Klik <strong>"Lunasi di Tempat"</strong> setelah menerima pembayaran tunai.
    </span>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="fa-solid fa-credit-card"></i> Tagihan Belum Lunas
        </span>
        <span style="font-size:.75rem;color:var(--text-muted);"><?= count($bookings) ?> transaksi</span>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>No. WA</th>
                    <th>Lapangan</th>
                    <th>Tanggal Main</th>
                    <th>Jam</th>
                    <th>Total</th>
                    <th>Sudah Dibayar</th>
                    <th>Sisa Tagihan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                <tr class="empty-row">
                    <td colspan="10">
                        <i class="fa-solid fa-circle-check" style="font-size:1.5rem;margin-bottom:.5rem;display:block;color:var(--green)"></i>
                        Tidak ada tagihan yang menunggu pelunasan!
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
                    <td>Rp <?= number_format($b['total_harga'], 0, ',', '.') ?></td>
                    <td style="color:var(--green);">Rp <?= number_format($b['jumlah_dibayar'], 0, ',', '.') ?></td>
                    <td>
                        <strong style="color:var(--yellow);">Rp <?= number_format($b['sisa_tagihan'], 0, ',', '.') ?></strong>
                    </td>
                    <td>
                        <button type="button"
                                class="btn btn-green btn-sm"
                                onclick="confirmLunasi(<?= $b['id'] ?>, '<?= esc($b['booking_code']) ?>', 'Rp <?= number_format($b['sisa_tagihan'], 0, ',', '.') ?>')">
                            <i class="fa-solid fa-check"></i> Lunasi di Tempat
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Confirm Modal -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <h3><i class="fa-solid fa-bolt"></i> Konfirmasi Pelunasan</h3>
        <p id="confirmMsg">Yakin tandai booking ini sebagai lunas?</p>
        <div class="confirm-actions">
            <form id="lunasiForm" method="post" action="">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-green">
                    <i class="fa-solid fa-check"></i> Ya, Tandai Lunas
                </button>
            </form>
            <button type="button" class="btn btn-outline" onclick="closeConfirm()">Batal</button>
        </div>
    </div>
</div>

<style>
    .confirm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:200; align-items:center; justify-content:center; }
    .confirm-overlay.open { display:flex; }
    .confirm-box { background:#1F2937; border:1px solid #374151; border-top:3px solid #22C55E; padding:2rem; max-width:380px; width:90%; }
    .confirm-box h3 { font-family:'Montserrat',sans-serif; font-weight:900; text-transform:uppercase; font-size:.9rem; margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem; }
    .confirm-box p { font-size:.82rem; color:#9CA3AF; margin-bottom:1.5rem; }
    .confirm-actions { display:flex; gap:.75rem; }
</style>

<script>
function confirmLunasi(id, code, sisa) {
    document.getElementById('confirmMsg').textContent =
        `Booking ${code} — Sisa tagihan ${sisa} sudah diterima tunai?`;
    document.getElementById('lunasiForm').action = `<?= site_url('admin/pelunasan/lunasi/') ?>${id}`;
    document.getElementById('confirmOverlay').classList.add('open');
}
function closeConfirm() {
    document.getElementById('confirmOverlay').classList.remove('open');
}
</script>

<?php $this->endSection() ?>
