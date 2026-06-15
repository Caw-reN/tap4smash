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

<?php $this->endSection() ?>
