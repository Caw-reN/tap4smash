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
                        <div style="display:flex;gap:.4rem;">
                            <button type="button"
                                    class="btn btn-green btn-sm" style="flex:1;"
                                    onclick="confirmLunasi(<?= $b['id'] ?>, '<?= esc($b['booking_code']) ?>', 'Rp <?= number_format($b['sisa_tagihan'], 0, ',', '.') ?>')">
                                <i class="fa-solid fa-money-bill"></i> Tunai
                            </button>
                            <button type="button"
                                    class="btn btn-volt btn-sm" style="flex:1;"
                                    onclick="doPelunasanQris(<?= $b['id'] ?>, 'Rp <?= number_format($b['sisa_tagihan'], 0, ',', '.') ?>', this)">
                                <i class="fa-solid fa-qrcode"></i> QRIS
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

<!-- Confirm Modal -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <h3><i class="fa-solid fa-bolt"></i> Konfirmasi Pelunasan</h3>
        <p id="confirmMsg">Yakin tandai booking ini sebagai lunas?</p>
        <div class="confirm-actions" style="display: flex; gap: .75rem; justify-content: center;">
            <form id="lunasiForm" method="post" action="" style="flex: 1.4; margin: 0;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-green" style="width: 100%; white-space: nowrap; justify-content: center;">
                    <i class="fa-solid fa-circle-check" style="font-size: 1.05rem;"></i> TANDAI LUNAS
                </button>
            </form>
            <button type="button" class="btn btn-outline" onclick="closeConfirm()" style="flex: 0.8; white-space: nowrap; justify-content: center;">
                <i class="fa-solid fa-xmark" style="font-size: 1.05rem;"></i> BATAL
            </button>
        </div>
    </div>
</div>

<style>
    .confirm-overlay { display:none; position:fixed; inset:0; background:rgba(15,32,68,.45); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(2px); }
    .confirm-overlay.open { display:flex; }
    .confirm-box { background:var(--surface); border:1px solid var(--border); padding:2rem; max-width:380px; width:90%; border-radius:var(--radius); box-shadow:var(--shadow-lg); }
    .confirm-box h3 { font-family:'Oswald',sans-serif; font-weight:700; text-transform:uppercase; font-size:.95rem; margin-bottom:.75rem; color:var(--navy); display:flex; align-items:center; gap:.5rem; }
    .confirm-box p { font-size:.82rem; color:var(--text-muted); margin-bottom:1.5rem; }
    .confirm-actions { display:flex; gap:.75rem; }

    /* Modal QRIS */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,32,68,.55); backdrop-filter: blur(6px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem; }
    .modal-overlay.open { display: flex; }
    .modal-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 2rem 1.75rem; max-width: 420px; width: 100%; text-align: center; box-shadow: var(--shadow-lg); }
    .modal-title { font-family: 'Oswald', sans-serif; font-size: 1.1rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--navy); margin-bottom: .35rem; }
    .modal-subtitle { font-size: .8rem; color: var(--text-muted); margin-bottom: .75rem; }
    .modal-amount { font-family: 'Oswald', sans-serif; font-size: 1.9rem; font-weight: 700; color: var(--navy); margin-bottom: 1rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: .5rem 1rem; display: inline-block; }
    .modal-qr-wrap { display: inline-flex; align-items: center; justify-content: center; background: #fff; border: 2px solid var(--border); border-radius: var(--radius); padding: 1rem; margin-bottom: 1rem; box-shadow: var(--shadow-md); }
    .modal-qr-wrap img { width: 220px; height: 220px; display: block; object-fit: contain; }
    .modal-qr-wrap canvas { display: block; }
    #modal-qr-spinner { width: 220px; height: 220px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: .85rem; gap: .5rem; flex-direction: column; }
    .modal-instruction { font-size: .8rem; color: var(--text-muted); margin-bottom: .75rem; line-height: 1.5; background: var(--surface2); border-radius: var(--radius-sm); padding: .6rem .85rem; border: 1px solid var(--border); }
    .modal-polling { display: flex; align-items: center; justify-content: center; gap: .5rem; font-size: .82rem; color: var(--text-muted); margin-bottom: 1rem; }
    .modal-polling .spinner { width: 14px; height: 14px; border: 2px solid var(--border); border-top-color: var(--accent); border-radius: 50%; animation: spin .7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .modal-cancel { background: transparent; border: 1px solid var(--border-dark); color: var(--text-muted); padding: .65rem 1.25rem; border-radius: var(--radius); cursor: pointer; font-family: 'Oswald', sans-serif; font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; transition: all .15s; width: 100%; }
    .modal-cancel:hover { border-color: var(--navy); color: var(--navy); background: var(--surface2); }
</style>

<!-- Modal QRIS Pelunasan -->
<div class="modal-overlay" id="qris-modal">
    <div class="modal-card">
        <div style="display:flex;align-items:center;justify-content:center;gap:.6rem;margin-bottom:.35rem;">
            <i class="fa-solid fa-qrcode" style="font-size:1.3rem;color:var(--accent);"></i>
            <div class="modal-title" style="margin:0;">Pelunasan via QRIS</div>
        </div>
        <div class="modal-subtitle">Tunjukkan QR di bawah kepada customer untuk di-scan</div>
        <div class="modal-amount" id="modal-amount">—</div>
        <div class="modal-qr-wrap">
            <div id="modal-qr-spinner">
                <div class="spinner" style="width:28px;height:28px;border-width:3px;"></div>
                <span style="font-size:.78rem;">Memuat QR...</span>
            </div>
            <img id="modal-qr-img" src="" alt="QRIS" style="display:none;" onerror="qrImgError()">
            <canvas id="modal-qr-canvas" style="display:none;"></canvas>
        </div>
        <div class="modal-instruction">
            <i class="fa-solid fa-circle-info" style="color:var(--navy);"></i>
            Sistem akan otomatis mengkonfirmasi dan me-refresh halaman setelah pembayaran selesai.
        </div>
        <div class="modal-polling">
            <div class="spinner"></div>
            <span>Menunggu konfirmasi pembayaran...</span>
        </div>
        <button class="modal-cancel" onclick="cancelQris()">
            <i class="fa-solid fa-xmark"></i> Batal / Gunakan Tunai
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
const CSRF_NAME = '<?= csrf_token() ?>';
const CSRF_HASH = '<?= csrf_hash() ?>';

function confirmLunasi(id, code, sisa) {
    document.getElementById('confirmMsg').textContent =
        `Booking ${code} — Sisa tagihan ${sisa} sudah diterima tunai?`;
    document.getElementById('lunasiForm').action = `<?= site_url('admin/pelunasan/lunasi/') ?>${id}`;
    document.getElementById('confirmOverlay').classList.add('open');
}
function closeConfirm() {
    document.getElementById('confirmOverlay').classList.remove('open');
}

let pollInterval = null;
let currentQrisBookingId = null;

async function doPelunasanQris(bookingId, sisaFormatted, btn) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i>`;
    btn.disabled = true;

    try {
        const res = await fetch('<?= site_url('admin/pelunasan/qris-init') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId, [CSRF_NAME]: CSRF_HASH })
        });
        
        const data = await res.json();
        btn.innerHTML = originalHtml;
        btn.disabled = false;

        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal membuat QRIS.');
            return;
        }

        // Tampilkan modal
        document.getElementById('modal-amount').textContent = sisaFormatted;
        document.getElementById('qris-modal').classList.add('open');
        
        document.getElementById('modal-qr-spinner').style.display = 'flex';
        document.getElementById('modal-qr-img').style.display = 'none';
        document.getElementById('modal-qr-canvas').style.display = 'none';
        
        currentQrisBookingId = bookingId;

        // Render QR
        if (data.qr_url) {
            const img = document.getElementById('modal-qr-img');
            img.onload = () => {
                document.getElementById('modal-qr-spinner').style.display = 'none';
                img.style.display = 'block';
            };
            img.src = data.qr_url;
            img.dataset.fallback = data.qr_string || data.pay_url || '';
        } else if (data.qr_string || data.pay_url) {
            qrImgError(data.qr_string || data.pay_url);
        }

        // Start polling
        pollInterval = setInterval(() => pollQrisPelunasan(bookingId), 3000);

    } catch (err) {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        alert('Terjadi kesalahan jaringan.');
    }
}

function qrImgError(fallbackString) {
    document.getElementById('modal-qr-img').style.display = 'none';
    const canvas = document.getElementById('modal-qr-canvas');
    if (fallbackString && typeof fallbackString === 'string') {
        document.getElementById('modal-qr-spinner').style.display = 'none';
        canvas.style.display = 'block';
        QRCode.toCanvas(canvas, fallbackString, { width: 220, margin: 1, color: { dark: '#0f2044', light: '#ffffff' } }, err => {
            if (err) console.error(err);
        });
    } else {
        const str = document.getElementById('modal-qr-img').dataset.fallback;
        if (str) {
            qrImgError(str);
        } else {
            document.getElementById('modal-qr-spinner').innerHTML = '<span style="color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> Gagal memuat QR</span>';
        }
    }
}

async function pollQrisPelunasan(bookingId) {
    try {
        const res = await fetch('<?= site_url('admin/pelunasan/qris-status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId, [CSRF_NAME]: CSRF_HASH })
        });
        const data = await res.json();
        
        if (data.success && data.paid) {
            clearInterval(pollInterval);
            alert(data.message);
            window.location.reload();
        }
    } catch (err) {}
}

function cancelQris() {
    document.getElementById('qris-modal').classList.remove('open');
    if (pollInterval) clearInterval(pollInterval);
    currentQrisBookingId = null;
}
</script>

<?php $this->endSection() ?>
