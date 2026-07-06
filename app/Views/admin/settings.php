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
    <div class="form-card-header" style="display:flex;align-items:center;justify-content:space-between;">
        <span class="table-card-title"><i class="fa-solid fa-wallet"></i> API PaymentKu</span>
        <?php
        $currentMode = $paymentku_mode ?? 'sandbox';
        if ($currentMode === 'production'): ?>
            <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.3rem .75rem;background:rgba(22,163,74,.12);border:1px solid rgba(22,163,74,.3);border-radius:20px;font-size:.7rem;font-weight:700;font-family:'Oswald',sans-serif;letter-spacing:.06em;text-transform:uppercase;color:#14532d;">
                <span style="width:7px;height:7px;border-radius:50%;background:#16a34a;box-shadow:0 0 6px #16a34a;animation:pulse-mode 2s infinite;display:inline-block;"></span>
                PRODUCTION
            </span>
        <?php else: ?>
            <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.3rem .75rem;background:rgba(217,119,6,.12);border:1px solid rgba(217,119,6,.3);border-radius:20px;font-size:.7rem;font-weight:700;font-family:'Oswald',sans-serif;letter-spacing:.06em;text-transform:uppercase;color:#92400e;">
                <span style="width:7px;height:7px;border-radius:50%;background:#d97706;box-shadow:0 0 6px #d97706;animation:pulse-mode 1s infinite;display:inline-block;"></span>
                SANDBOX
            </span>
        <?php endif; ?>
    </div>
    <div class="form-body">

        <form action="<?= site_url('admin/settings/update') ?>" method="post">
            <?= csrf_field() ?>

            <style>
            @keyframes pulse-mode { 0%,100%{opacity:1} 50%{opacity:.35} }
            .mode-toggle-wrap {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: .5rem;
                margin-bottom: 1.75rem;
            }
            .mode-option { display: none; }
            .mode-label {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: .4rem;
                padding: 1rem .75rem;
                border: 2px solid var(--border);
                border-radius: var(--radius);
                cursor: pointer;
                transition: all .18s;
                text-align: center;
                background: var(--surface2);
            }
            .mode-label:hover { border-color: var(--border-dark); }
            .mode-label .mode-icon { font-size: 1.6rem; line-height: 1; }
            .mode-label .mode-name {
                font-family: 'Oswald', sans-serif;
                font-weight: 700;
                font-size: .82rem;
                text-transform: uppercase;
                letter-spacing: .06em;
            }
            .mode-label .mode-desc {
                font-size: .7rem;
                color: var(--text-muted);
                line-height: 1.4;
            }
            /* Sandbox selected */
            .mode-option[value="sandbox"]:checked ~ .mode-labels label[for="mode-sandbox"] {
                border-color: #d97706;
                background: rgba(217,119,6,.08);
                box-shadow: 0 0 0 3px rgba(217,119,6,.15);
            }
            .mode-option[value="sandbox"]:checked ~ .mode-labels label[for="mode-sandbox"] .mode-name { color: #92400e; }
            /* Production selected */
            .mode-option[value="production"]:checked ~ .mode-labels label[for="mode-production"] {
                border-color: #16a34a;
                background: rgba(22,163,74,.08);
                box-shadow: 0 0 0 3px rgba(22,163,74,.15);
            }
            .mode-option[value="production"]:checked ~ .mode-labels label[for="mode-production"] .mode-name { color: #14532d; }

            .secret-wrap {
                position: relative;
                display: flex;
                align-items: stretch;
                gap: 0;
            }
            .secret-wrap input {
                flex: 1;
                border-radius: var(--radius-sm) 0 0 var(--radius-sm) !important;
                font-family: monospace;
            }
            .btn-toggle-secret {
                flex-shrink: 0;
                padding: 0 .85rem;
                background: var(--charcoal);
                border: 1px solid var(--slate);
                border-left: none;
                border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
                cursor: pointer;
                color: var(--text-muted);
                transition: background .15s, color .15s;
                display: flex;
                align-items: center;
                gap: .35rem;
                white-space: nowrap;
                font-family: 'Inter', sans-serif;
                font-size: .78rem;
                font-weight: 600;
            }
            .btn-toggle-secret:hover { background: var(--slate); color: var(--text); }
            </style>

            <!-- Mode Toggle -->
            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--text-muted);margin-bottom:.6rem;font-family:'Oswald',sans-serif;">
                    Mode Operasi <span style="color:var(--red)">*</span>
                </label>

                <!-- Trick: dua radio terpisah, lalu label grid di bawahnya -->
                <div style="position:relative;">
                    <input class="mode-option" type="radio" name="paymentku_mode" id="mode-sandbox"    value="sandbox"    <?= ($paymentku_mode ?? 'sandbox') === 'sandbox' ? 'checked' : '' ?>
                           style="position:absolute;opacity:0;width:0;height:0;">
                    <input class="mode-option" type="radio" name="paymentku_mode" id="mode-production" value="production" <?= ($paymentku_mode ?? 'sandbox') === 'production' ? 'checked' : '' ?>
                           style="position:absolute;opacity:0;width:0;height:0;">

                    <div class="mode-labels mode-toggle-wrap">
                        <label for="mode-sandbox" class="mode-label" id="lbl-sandbox">
                            <span class="mode-icon"><i class="fa-solid fa-flask"></i></span>
                            <span class="mode-name">Sandbox</span>
                            <span class="mode-desc">Untuk pengujian. Tidak ada transaksi nyata. Pembayaran disimulasikan.</span>
                        </label>
                        <label for="mode-production" class="mode-label" id="lbl-production">
                            <span class="mode-icon"><i class="fa-solid fa-rocket"></i></span>
                            <span class="mode-name">Production</span>
                            <span class="mode-desc">Mode live. Pembayaran QRIS nyata. Pastikan API key sudah benar.</span>
                        </label>
                    </div>
                </div>

                <!-- Sandbox Warning -->
                <div id="sandbox-warning" style="background:rgba(217,119,6,.08);border:1px solid rgba(217,119,6,.25);border-radius:var(--radius);padding:.65rem 1rem;font-size:.78rem;color:#92400e;align-items:flex-start;gap:.5rem;margin-top:.75rem;<?= ($paymentku_mode ?? 'sandbox') === 'production' ? 'display:none;' : 'display:flex;' ?>">
                    <i class="fa-solid fa-flask" style="margin-top:.1rem;flex-shrink:0;"></i>
                    <div>
                        <strong>Mode Sandbox aktif.</strong> QR Code akan tetap ditampilkan di halaman pembayaran untuk pengujian/simulasi. Gunakan API Key berawalan <code>sk_test_...</code>.
                    </div>
                </div>
                <!-- Production Warning -->
                <div id="production-warning" style="background:rgba(22,163,74,.08);border:1px solid rgba(22,163,74,.25);border-radius:var(--radius);padding:.65rem 1rem;font-size:.78rem;color:#14532d;align-items:flex-start;gap:.5rem;margin-top:.75rem;<?= ($paymentku_mode ?? 'sandbox') !== 'production' ? 'display:none;' : 'display:flex;' ?>">
                    <i class="fa-solid fa-circle-check" style="margin-top:.1rem;flex-shrink:0;"></i>
                    <div>
                        <strong>Mode Production aktif.</strong> QR Code nyata akan muncul saat booking. Pastikan API Key production (<code>sk_live_...</code>) sudah diisi dengan benar.
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="paymentku_api_key">Secret API Key <span style="color:var(--red)">*</span></label>
                <div class="secret-wrap">
                    <input type="password" id="paymentku_api_key" name="paymentku_api_key"
                           value="<?= esc($paymentku_api_key) ?>"
                           placeholder="sk_live_xxxxxxx atau sk_test_xxxxxxx" required
                           autocomplete="new-password">
                    <button type="button" class="btn-toggle-secret" onclick="toggleSecret('paymentku_api_key', this)">
                        <i class="fa-solid fa-eye"></i> Tampilkan
                    </button>
                </div>
                <div class="hint">Gunakan <code>sk_test_...</code> untuk Sandbox, <code>sk_live_...</code> untuk Production.</div>
            </div>

            <div class="form-group">
                <label for="paymentku_webhook_secret">Webhook Secret <span style="color:var(--red)">*</span></label>
                <div class="secret-wrap">
                    <input type="password" id="paymentku_webhook_secret" name="paymentku_webhook_secret"
                           value="<?= esc($paymentku_webhook_secret ?? '') ?>"
                           placeholder="whsec_xxxxxxx" required
                           autocomplete="new-password">
                    <button type="button" class="btn-toggle-secret" onclick="toggleSecret('paymentku_webhook_secret', this)">
                        <i class="fa-solid fa-eye"></i> Tampilkan
                    </button>
                </div>
                <div class="hint">Secret ini digunakan untuk memverifikasi keamanan notifikasi otomatis dari Paymenku (Webhook).</div>
            </div>

            <div style="margin-top:2rem;">
                <button type="submit" class="btn btn-volt"><i class="fa-solid fa-save"></i> Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSecret(inputId, btn) {
    const input = document.getElementById(inputId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    btn.innerHTML = isHidden
        ? '<i class="fa-solid fa-eye-slash"></i> Sembunyikan'
        : '<i class="fa-solid fa-eye"></i> Tampilkan';
}

// Mode toggle visual feedback
document.querySelectorAll('input[name="paymentku_mode"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        updateModeDisplay(this.value);
    });
});

function updateModeDisplay(mode) {
    const isSandbox = mode === 'sandbox';
    const boxSandbox    = document.getElementById('sandbox-warning');
    const boxProduction = document.getElementById('production-warning');
    if (boxSandbox)    boxSandbox.style.display    = isSandbox ? 'flex' : 'none';
    if (boxProduction) boxProduction.style.display = isSandbox ? 'none' : 'flex';

    const lblSandbox    = document.getElementById('lbl-sandbox');
    const lblProduction = document.getElementById('lbl-production');

    // Reset
    [lblSandbox, lblProduction].forEach(function(l) {
        if (!l) return;
        l.style.borderColor = '';
        l.style.background  = '';
        l.style.boxShadow   = '';
        l.querySelector('.mode-name').style.color = '';
    });

    if (mode === 'sandbox' && lblSandbox) {
        lblSandbox.style.borderColor = '#d97706';
        lblSandbox.style.background  = 'rgba(217,119,6,.08)';
        lblSandbox.style.boxShadow   = '0 0 0 3px rgba(217,119,6,.15)';
        lblSandbox.querySelector('.mode-name').style.color = '#92400e';
    } else if (mode === 'production' && lblProduction) {
        lblProduction.style.borderColor = '#16a34a';
        lblProduction.style.background  = 'rgba(22,163,74,.08)';
        lblProduction.style.boxShadow   = '0 0 0 3px rgba(22,163,74,.15)';
        lblProduction.querySelector('.mode-name').style.color = '#14532d';
    }
}

// Init on load
updateModeDisplay('<?= esc($paymentku_mode ?? 'sandbox') ?>');
</script>


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
