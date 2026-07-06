<?php $this->extend('admin/layouts/main') ?>
<?php $this->section('content') ?>

<div class="form-card">
    <div class="form-card-header">
        <span style="font-size:1.25rem; color:var(--volt);"><i class="fa-solid fa-building"></i></span>
        <h2 class="form-card-title"><?= esc($page_title) ?></h2>
    </div>

    <form method="post"
          enctype="multipart/form-data"
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

            <!-- ── Upload Foto ─────────────────────────────── -->
            <div class="form-group">
                <label for="foto">Foto Lapangan</label>

                <?php if (! empty($lapangan['foto'])): ?>
                <div id="foto-preview-wrap" style="margin-bottom:.85rem;">
                    <img id="foto-preview"
                         src="<?= base_url('img/lapangans/' . esc($lapangan['foto'])) ?>"
                         alt="Foto Lapangan"
                         style="width:100%;max-width:360px;height:180px;object-fit:cover;border-radius:6px;border:1px solid var(--border-dark);display:block;">
                </div>
                <?php else: ?>
                <div id="foto-preview-wrap" style="display:none;margin-bottom:.85rem;">
                    <img id="foto-preview" src="" alt="Preview Foto"
                         style="width:100%;max-width:360px;height:180px;object-fit:cover;border-radius:6px;border:1px solid var(--border-dark);display:block;">
                </div>
                <?php endif; ?>

                <label for="foto" id="foto-drop-area" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: .4rem;
                    padding: 1.5rem;
                    border: 2px dashed var(--border-dark);
                    border-radius: 6px;
                    background: var(--surface);
                    cursor: pointer;
                    transition: border-color .15s, background .15s;
                    text-transform: none;
                    letter-spacing: 0;
                    font-weight: 400;
                    color: var(--text-muted);
                    font-size: .82rem;
                ">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size:1.6rem;color:var(--volt);"></i>
                    <span id="foto-label">Klik untuk pilih foto, atau drag & drop di sini</span>
                    <span style="font-size:.7rem;color:var(--text-muted);">JPG, PNG, WEBP — maks 2 MB</span>
                </label>
                <input type="file"
                       id="foto"
                       name="foto"
                       accept="image/jpeg,image/png,image/webp"
                       style="display:none;">

                <?php if (! empty($lapangan['foto'])): ?>
                <div style="margin-top:.6rem;display:flex;align-items:center;gap:.5rem;">
                    <input type="checkbox" id="hapus_foto" name="hapus_foto" value="1" style="accent-color:var(--red);width:14px;height:14px;">
                    <label for="hapus_foto" style="font-size:.75rem;color:var(--red);text-transform:none;letter-spacing:0;font-weight:500;cursor:pointer;">
                        <i class="fa-solid fa-trash-can"></i> Hapus foto saat ini
                    </label>
                </div>
                <?php endif; ?>
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

<script>
(function () {
    const input     = document.getElementById('foto');
    const preview   = document.getElementById('foto-preview');
    const previewWrap = document.getElementById('foto-preview-wrap');
    const label     = document.getElementById('foto-label');
    const dropArea  = document.getElementById('foto-drop-area');
    const hapusChk  = document.getElementById('hapus_foto');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            previewWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
        label.textContent = file.name;
        // Uncheck hapus_foto kalau pilih foto baru
        if (hapusChk) hapusChk.checked = false;
    }

    input.addEventListener('change', () => {
        if (input.files[0]) showPreview(input.files[0]);
    });

    // Drag & drop
    dropArea.addEventListener('dragover', e => {
        e.preventDefault();
        dropArea.style.borderColor = 'var(--volt)';
        dropArea.style.background  = 'rgba(170,238,0,.05)';
    });
    dropArea.addEventListener('dragleave', () => {
        dropArea.style.borderColor = '';
        dropArea.style.background  = '';
    });
    dropArea.addEventListener('drop', e => {
        e.preventDefault();
        dropArea.style.borderColor = '';
        dropArea.style.background  = '';
        const file = e.dataTransfer.files[0];
        if (file) {
            // Isi input file secara programatik lewat DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showPreview(file);
        }
    });

    // Kalau checkbox hapus dicentang, sembunyikan preview
    if (hapusChk) {
        hapusChk.addEventListener('change', () => {
            if (hapusChk.checked) {
                previewWrap.style.display = 'none';
                label.textContent = 'Klik untuk pilih foto, atau drag & drop di sini';
                input.value = '';
            }
        });
    }
})();
</script>

<?php $this->endSection() ?>
