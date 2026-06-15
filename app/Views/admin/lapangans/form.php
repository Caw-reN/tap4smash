<?php $this->extend('admin/layouts/main') ?>
<?php $this->section('content') ?>

<div class="form-card">
    <div class="form-card-header">
        <span style="font-size:1.25rem; color:var(--volt);"><i class="fa-solid fa-building"></i></span>
        <h2 class="form-card-title"><?= esc($page_title) ?></h2>
    </div>

    <form method="post"
          action="<?= $lapangan ? site_url('admin/lapangan/update/' . $lapangan['id']) : site_url('admin/lapangan/store') ?>">
        <?= csrf_field() ?>

        <div class="form-body">

            <?php if (! empty($errors)): ?>
            <div class="alert alert-error" style="margin-bottom:1.25rem; display:flex; gap:0.75rem; align-items:flex-start;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-top:0.2rem;"></i>
                <ul style="list-style:none;padding:0;margin:0;">
                    <?php foreach ($errors as $e): ?>
                    <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="nama_lapangan">Nama Lapangan <span style="color:var(--red)">*</span></label>
                <input type="text"
                       id="nama_lapangan"
                       name="nama_lapangan"
                       value="<?= esc($lapangan['nama_lapangan'] ?? old('nama_lapangan')) ?>"
                       placeholder="Contoh: Lapangan A, Lapangan VIP"
                       maxlength="50"
                       required>
                <div class="hint">Maks 50 karakter. Gunakan nama yang mudah dikenali pelanggan.</div>
            </div>

            <div class="form-group">
                <label for="jenis_lapangan">Jenis/Spesifikasi Lapangan</label>
                <input type="text"
                       id="jenis_lapangan"
                       name="jenis_lapangan"
                       value="<?= esc($lapangan['jenis_lapangan'] ?? old('jenis_lapangan')) ?>"
                       placeholder="Contoh: Vinyl, Sintetis, Karpet, Lantai Kayu"
                       maxlength="100">
                <div class="hint">Opsional. Jelaskan tipe lantai atau spesifikasi lapangan.</div>
            </div>

            <div class="form-group">
                <label for="harga_per_jam">Harga per Jam (Rp) <span style="color:var(--red)">*</span></label>
                <input type="number"
                       id="harga_per_jam"
                       name="harga_per_jam"
                       value="<?= esc($lapangan['harga_per_jam'] ?? old('harga_per_jam')) ?>"
                       placeholder="50000"
                       min="1000"
                       step="1000"
                       required>
                <div class="hint">Harga dalam Rupiah, tanpa titik atau koma. Contoh: 50000</div>
            </div>

        </div>

        <div class="form-footer">
            <button type="submit" class="btn btn-volt">
                <?php if ($lapangan): ?>
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                <?php else: ?>
                    <i class="fa-solid fa-plus"></i> Tambah Lapangan
                <?php endif; ?>
            </button>
            <a href="<?= site_url('admin/lapangan') ?>" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php $this->endSection() ?>
