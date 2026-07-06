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

<div style="margin-bottom:1.25rem;">
    <a href="<?= site_url('admin/lapangan/create') ?>" class="btn btn-volt">
        <i class="fa-solid fa-plus"></i> Tambah Lapangan
    </a>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">
            <i class="fa-solid fa-building"></i> Daftar Lapangan
        </span>
        <span style="font-size:.75rem;color:var(--text-muted);"><?= count($lapangans) ?> lapangan</span>
    </div>
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nama Lapangan</th>
                    <th>Jenis</th>
                    <th>Harga / Jam</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lapangans)): ?>
                <tr class="empty-row">
                    <td colspan="6">
                        <i class="fa-solid fa-inbox" style="font-size:1.5rem;margin-bottom:.5rem;display:block;color:var(--slate-light)"></i>
                        Belum ada lapangan. Tambahkan lapangan pertama!
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($lapangans as $l): ?>
                <tr>
                    <td style="color:var(--text-muted);"><?= $l['id'] ?></td>
                    <td>
                        <?php if (!empty($l['foto'])): ?>
                        <img src="<?= base_url('img/lapangans/' . esc($l['foto'])) ?>"
                             alt="<?= esc($l['nama_lapangan']) ?>"
                             style="width:56px;height:40px;object-fit:cover;border-radius:4px;border:1px solid var(--border-dark);display:block;">
                        <?php else: ?>
                        <span style="display:flex;align-items:center;justify-content:center;width:56px;height:40px;border-radius:4px;border:1px dashed var(--border-dark);color:var(--text-muted);font-size:.7rem;">
                            <i class="fa-solid fa-image"></i>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= esc($l['nama_lapangan']) ?></strong></td>
                    <td style="color:var(--text-muted);"><?= esc($l['jenis_lapangan'] ?? '-') ?: '-' ?></td>
                    <td>Rp <?= number_format($l['harga_per_jam'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge <?= $l['is_active'] ? 'badge-aktif' : 'badge-maint' ?>">
                            <?php if ($l['is_active']): ?>
                                <i class="fa-solid fa-circle-check"></i> Aktif
                            <?php else: ?>
                                <i class="fa-solid fa-wrench"></i> Maintenance
                            <?php endif; ?>
                        </span>
                    </td>
                    <td style="color:var(--text-muted);font-size:.77rem;">
                        <?= date('d M Y', strtotime($l['created_at'])) ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                            <!-- Edit -->
                            <a href="<?= site_url('admin/lapangan/edit/' . $l['id']) ?>"
                               class="btn btn-outline btn-sm">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>

                            <!-- Toggle Status -->
                            <form method="post" action="<?= site_url('admin/lapangan/toggle/' . $l['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm <?= $l['is_active'] ? 'btn-yellow' : 'btn-green' ?>">
                                    <?php if ($l['is_active']): ?>
                                        <i class="fa-solid fa-wrench"></i> Maintenance
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle-check"></i> Aktifkan
                                    <?php endif; ?>
                                </button>
                            </form>

                            <!-- Delete -->
                            <button type="button"
                                    class="btn btn-red btn-sm"
                                    onclick="confirmDelete(<?= $l['id'] ?>, '<?= esc($l['nama_lapangan']) ?>')">
                                <i class="fa-solid fa-trash-can"></i> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="confirm-overlay" id="deleteOverlay">
    <div class="confirm-box">
        <h3><i class="fa-solid fa-trash-can"></i> Hapus Lapangan?</h3>
        <p id="deleteMsg">Data lapangan ini akan dihapus permanen.</p>
        <div class="confirm-actions">
            <form id="deleteForm" method="post" action="">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-red">
                    <i class="fa-solid fa-trash-can"></i> Ya, Hapus
                </button>
            </form>
            <button type="button" class="btn btn-outline" onclick="closeDelete()">Batal</button>
        </div>
    </div>
</div>

<style>
    .confirm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:200; align-items:center; justify-content:center; }
    .confirm-overlay.open { display:flex; }
    .confirm-box { background:#1F2937; border:1px solid #374151; border-top:3px solid #EF4444; padding:2rem; max-width:380px; width:90%; }
    .confirm-box h3 { font-family:'Oswald',sans-serif; font-weight:700; text-transform:uppercase; font-size:.9rem; margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem; }
    .confirm-box p { font-size:.82rem; color:#9CA3AF; margin-bottom:1.5rem; }
    .confirm-actions { display:flex; gap:.75rem; }
</style>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteMsg').textContent = `Hapus "${name}"? Pastikan tidak ada booking aktif.`;
    document.getElementById('deleteForm').action = `<?= site_url('admin/lapangan/delete/') ?>${id}`;
    document.getElementById('deleteOverlay').classList.add('open');
}
function closeDelete() {
    document.getElementById('deleteOverlay').classList.remove('open');
}
</script>

<?php $this->endSection() ?>
