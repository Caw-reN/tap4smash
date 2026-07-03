<?php $this->extend('admin/layouts/main') ?>
<?php $this->section('content') ?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2><i class="fa-solid fa-cogs" style="color:var(--volt);margin-right:.5rem;"></i> Pengaturan Sistem</h2>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success" style="margin-bottom:1.5rem;">
    <i class="fa-solid fa-check-circle"></i>
    <span><?= session()->getFlashdata('success') ?></span>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-error" style="margin-bottom:1.5rem;">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span><?= session()->getFlashdata('error') ?></span>
</div>
<?php endif; ?>

<!-- ── Logo Upload ───────────────────────────────────────────── -->
<div class="form-card" style="max-width:800px; margin-bottom:1.5rem;">
    <div class="form-card-header">
        <span class="table-card-title"><i class="fa-solid fa-image"></i> Logo Aplikasi</span>
    </div>
    <div class="form-body">
        <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1.5rem;">
            Logo akan tampil di navbar halaman publik. Gunakan file <strong>PNG dengan background transparan</strong> untuk hasil terbaik.
            Format yang didukung: <code>PNG, JPG, WEBP</code>. Ukuran maksimum: <strong>2MB</strong>.
        </p>

        <?php
            $logoExts = ['png','jpg','jpeg','webp'];
            $currentLogo = null;
            foreach ($logoExts as $ext) {
                if (file_exists(FCPATH . 'img/logo.' . $ext)) {
                    $currentLogo = base_url('img/logo.' . $ext) . '?v=' . filemtime(FCPATH . 'img/logo.' . $ext);
                    break;
                }
            }
        ?>

        <?php if ($currentLogo): ?>
        <div style="background:#1a1f2b;border:1px solid var(--slate);border-radius:var(--radius);padding:1.5rem;display:inline-flex;align-items:center;justify-content:center;margin-bottom:.75rem;min-width:200px;">
            <img src="<?= $currentLogo ?>" alt="Logo aktif" style="height:48px;width:auto;max-width:280px;">
        </div>
        <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:1.25rem;">
            <i class="fa-solid fa-circle-check" style="color:var(--green);"></i> Logo aktif saat ini
        </div>
        <?php else: ?>
        <div style="background:var(--charcoal);border:1px dashed var(--slate);border-radius:var(--radius);padding:1.5rem;text-align:center;color:var(--text-muted);margin-bottom:1.25rem;font-size:.82rem;">
            <i class="fa-solid fa-image" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.3;"></i>
            Belum ada logo terpasang
        </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/settings/upload-logo') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="logo_file">Pilih file logo baru</label>
                <input type="file" id="logo_file" name="logo_file"
                       accept=".png,.jpg,.jpeg,.webp"
                       style="padding:.5rem;cursor:pointer;"
                       onchange="previewLogo(this)">
                <div class="hint">Disarankan PNG transparan, ukuran 400×200px atau lebih besar</div>
            </div>

            <div id="logoPreviewWrap" style="display:none;margin-bottom:1rem;">
                <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:.4rem;">Preview sebelum upload:</div>
                <div style="background:#1a1f2b;border:1px solid var(--slate);border-radius:var(--radius);padding:1rem;display:inline-flex;">
                    <img id="logoPreview" src="" alt="preview" style="height:48px;width:auto;max-width:260px;">
                </div>
            </div>

            <button type="submit" class="btn btn-volt">
                <i class="fa-solid fa-upload"></i> Upload Logo
            </button>
        </form>
    </div>
</div>

<script>
function previewLogo(input) {
    var wrap = document.getElementById('logoPreviewWrap');
    var img  = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { img.src = e.target.result; wrap.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else {
        wrap.style.display = 'none';
    }
}
</script>

<!-- ── API PaymentKu ─────────────────────────────────────────── -->
<div class="form-card" style="max-width: 800px;">
    <div class="form-card-header">
        <span class="table-card-title"><i class="fa-solid fa-wallet"></i> API PaymentKu</span>
    </div>
    <div class="form-body">
        <p style="font-size: .85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
            Masukkan Secret API Key dari dashboard Paymenku Anda. Kunci ini digunakan untuk memproses pembayaran secara otomatis. 
            Format kunci biasanya berawalan <code>sk_live_...</code> untuk produksi atau <code>sk_test_...</code> untuk Sandbox.
        </p>

        <form action="<?= site_url('admin/settings/update') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label for="paymentku_api_key">Secret API Key <span style="color:var(--red)">*</span></label>
                <input type="text" id="paymentku_api_key" name="paymentku_api_key" value="<?= esc($paymentku_api_key) ?>" placeholder="sk_live_xxxxxxx" required style="font-family: monospace;">
            </div>

            <div class="form-group">
                <label for="paymentku_webhook_secret">Webhook Secret <span style="color:var(--red)">*</span></label>
                <input type="text" id="paymentku_webhook_secret" name="paymentku_webhook_secret" value="<?= esc($paymentku_webhook_secret ?? '') ?>" placeholder="whsec_xxxxxxx" required style="font-family: monospace;">
                <div class="hint">Secret ini digunakan untuk memverifikasi keamanan notifikasi otomatis dari Paymenku (Webhook).</div>
            </div>

            <div class="form-group">
                <label for="paymentku_base_url">Base URL API <span style="color:var(--red)">*</span></label>
                <input type="text" id="paymentku_base_url" name="paymentku_base_url" value="<?= esc($paymentku_base_url) ?>" placeholder="https://paymenku.com/api/v1" required>
                <div class="hint">Biarkan *default* <code>https://paymenku.com/api/v1</code> kecuali Anda diberi URL khusus.</div>
            </div>

            <div style="margin-top:2rem;">
                <button type="submit" class="btn btn-volt"><i class="fa-solid fa-save"></i> Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<!-- ── Lokasi GOR (Google Maps) ─────────────────────────── -->
<div class="form-card" style="max-width:800px; margin-top:1.5rem;">
    <div class="form-card-header">
        <span class="table-card-title"><i class="fa-solid fa-location-dot"></i> Lokasi GOR (Google Maps)</span>
    </div>
    <div class="form-body">
        <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1.5rem;">
            Tampilkan peta lokasi GOR di halaman utama. Gunakan <strong>Embed URL</strong> dari Google Maps:
            buka Google Maps → cari lokasi → klik <strong>Share</strong> → pilih tab <strong>Embed a map</strong>
            → copy URL yang ada di dalam <code>src="..."</code>.
        </p>

        <form action="<?= site_url('admin/settings/update-maps') ?>" method="post" id="formMaps">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="maps_label">Nama Lokasi <span style="color:var(--red)">*</span></label>
                <input type="text" id="maps_label" name="maps_label"
                       value="<?= esc($maps_label) ?>"
                       placeholder="GOR Tap4Smash"
                       required>
                <div class="hint">Nama yang ditampilkan di landing page dan link "Buka di Google Maps".</div>
            </div>

            <div class="form-group">
                <label for="maps_address">Alamat Lengkap</label>
                <input type="text" id="maps_address" name="maps_address"
                       value="<?= esc($maps_address) ?>"
                       placeholder="Jl. Contoh No.123, Kota, Provinsi">
                <div class="hint">Teks alamat yang tampil di bawah nama lokasi. Kosongkan jika tidak perlu.</div>
            </div>

            <div class="form-group">
                <label for="maps_embed_url">Google Maps Embed URL</label>
                <input type="url" id="maps_embed_url" name="maps_embed_url"
                       value="<?= esc($maps_embed_url) ?>"
                       placeholder="https://www.google.com/maps/embed?pb=..."
                       oninput="previewMaps(this.value)">
                <div class="hint">
                    Kosongkan untuk menyembunyikan section peta di halaman publik.
                    <a href="https://www.google.com/maps" target="_blank" rel="noopener"
                       style="color:var(--volt);font-weight:600;">Buka Google Maps ↗</a>
                </div>
            </div>

            <!-- Preview area -->
            <div id="mapsPreviewWrap" style="<?= !empty($maps_embed_url) ? '' : 'display:none;' ?> margin-bottom:1.25rem;">
                <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:.5rem;">
                    <i class="fa-solid fa-eye"></i> Preview peta:
                </div>
                <div style="border-radius:8px;overflow:hidden;border:1px solid var(--slate);position:relative;">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:var(--volt);z-index:1;"></div>
                    <iframe id="mapsPreviewFrame"
                            src="<?= esc($maps_embed_url) ?>"
                            width="100%" height="280"
                            style="border:none;display:block;"
                            allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:2rem;">
                <button type="submit" class="btn btn-volt">
                    <i class="fa-solid fa-save"></i> Simpan Pengaturan Lokasi
                </button>
                <button type="button" class="btn" onclick="previewMapsNow()"
                        style="background:var(--charcoal);color:var(--text);border:1px solid var(--slate);">
                    <i class="fa-solid fa-eye"></i> Preview
                </button>
                <?php if (!empty($maps_embed_url)): ?>
                <a href="<?= site_url('/') ?>#lokasi" target="_blank"
                   style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1rem;font-size:.78rem;font-weight:700;font-family:'Oswald',sans-serif;letter-spacing:.04em;text-transform:uppercase;color:var(--text-muted);border:1px solid var(--slate);border-radius:var(--radius-sm);text-decoration:none;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat di Landing Page
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
function previewMaps(url) {
    var wrap  = document.getElementById('mapsPreviewWrap');
    var frame = document.getElementById('mapsPreviewFrame');
    if (url && url.includes('google.com/maps')) {
        frame.src = url;
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}

function previewMapsNow() {
    var url = document.getElementById('maps_embed_url').value.trim();
    previewMaps(url);
}
</script>

<?php $this->endSection() ?>
