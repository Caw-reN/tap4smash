<?php $this->extend('admin/layouts/main') ?>
<?php $this->section('content') ?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2 style="margin:0; font-family:'Oswald', sans-serif; color:var(--navy);">Tambah Booking</h2>
    <a href="<?= site_url('admin/bookings') ?>" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="max-width:800px; margin:0 auto; padding:2rem;">
    <form id="createBookingForm" onsubmit="submitBooking(event)">
        <?= csrf_field() ?>
        
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
            
            <!-- Kiri: Info Pemesan & Pembayaran -->
            <div>
                <h4 style="margin-top:0; margin-bottom:1rem; border-bottom:1px solid var(--border); padding-bottom:.5rem;">Data Pemesan</h4>
                
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>Nama Pemesan <span style="color:red">*</span></label>
                    <input type="text" name="nama_pemesan" class="form-control" required>
                </div>
                
                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label>Nomor WhatsApp <span style="color:red">*</span></label>
                    <input type="text" name="nomor_wa" class="form-control" required placeholder="08xxx">
                </div>

                <h4 style="margin-top:0; margin-bottom:1rem; border-bottom:1px solid var(--border); padding-bottom:.5rem;">Pembayaran</h4>
                
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>Status Pembayaran Saat Ini <span style="color:red">*</span></label>
                    <select name="pembayaran" id="pembayaran" class="form-control" required onchange="toggleMetode()">
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="dp">Bayar DP (50%)</option>
                        <option value="lunas">Lunas (100%)</option>
                    </select>
                </div>

                <div class="form-group" id="metodeGroup" style="display:none; margin-bottom:1rem;">
                    <label>Metode Pembayaran</label>
                    <select name="metode" id="metode" class="form-control">
                        <option value="cash">Tunai (Cash / Transfer Manual)</option>
                        <option value="qris">QRIS (Otomatis via PaymentKu)</option>
                    </select>
                </div>
            </div>

            <!-- Kanan: Info Jadwal -->
            <div>
                <h4 style="margin-top:0; margin-bottom:1rem; border-bottom:1px solid var(--border); padding-bottom:.5rem;">Jadwal Lapangan</h4>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label>Lapangan <span style="color:red">*</span></label>
                    <select name="lapangan_id" id="lapangan_id" class="form-control" required onchange="fetchSlots()">
                        <option value="">-- Pilih Lapangan --</option>
                        <?php foreach($lapangans as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= esc($l['nama_lapangan']) ?> - Rp <?= number_format($l['harga_per_jam'],0,',','.') ?>/jam</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>Tanggal Main <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_main" id="tanggal_main" class="form-control" required min="<?= date('Y-m-d') ?>" onchange="fetchSlots()">
                </div>
                
                <div class="form-group">
                    <label>Pilih Jam Main <span style="color:red">*</span></label>
                    <div id="slots-container" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:.5rem; margin-top:.5rem;">
                        <span style="color:var(--text-muted); font-size:.85rem; grid-column:span 3;">Pilih Lapangan dan Tanggal terlebih dahulu.</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top:2rem; text-align:right;">
            <button type="submit" class="btn btn-navy" id="btnSubmit">
                <i class="fa-solid fa-save"></i> Simpan Booking
            </button>
        </div>
    </form>
</div>

<!-- Modal QRIS (diadopsi dari pelunasan) -->
<style>
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

<div class="modal-overlay" id="qris-modal">
    <div class="modal-card">
        <div style="display:flex;align-items:center;justify-content:center;gap:.6rem;margin-bottom:.35rem;">
            <i class="fa-solid fa-qrcode" style="font-size:1.3rem;color:var(--accent);"></i>
            <div class="modal-title" style="margin:0;">Pembayaran QRIS</div>
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
        <button type="button" class="modal-cancel" onclick="cancelQris()">
            <i class="fa-solid fa-xmark"></i> Batal / Bayar Nanti Saja
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
const CSRF_NAME = '<?= csrf_token() ?>';
let CSRF_HASH = '<?= csrf_hash() ?>';

function toggleMetode() {
    const pem = document.getElementById('pembayaran').value;
    const metGroup = document.getElementById('metodeGroup');
    if (pem === 'belum_bayar') {
        metGroup.style.display = 'none';
    } else {
        metGroup.style.display = 'block';
    }
}

async function fetchSlots() {
    const lapId = document.getElementById('lapangan_id').value;
    const tgl = document.getElementById('tanggal_main').value;
    const container = document.getElementById('slots-container');

    if (!lapId || !tgl) {
        container.innerHTML = '<span style="color:var(--text-muted); font-size:.85rem; grid-column:span 3;">Pilih Lapangan dan Tanggal terlebih dahulu.</span>';
        return;
    }

    container.innerHTML = '<span style="color:var(--text-muted); font-size:.85rem; grid-column:span 3;"><i class="fa-solid fa-spinner fa-spin"></i> Memuat jadwal...</span>';

    try {
        const res = await fetch(`<?= site_url('api/slots') ?>?lapangan_id=${lapId}&tanggal=${tgl}`);
        const data = await res.json();
        
        if (!data.success) {
            container.innerHTML = `<span style="color:red; font-size:.85rem; grid-column:span 3;">Gagal memuat jadwal.</span>`;
            return;
        }

        container.innerHTML = '';
        if (data.slots.length === 0) {
            container.innerHTML = '<span style="color:var(--text-muted); font-size:.85rem; grid-column:span 3;">Tidak ada slot tersedia.</span>';
            return;
        }

        data.slots.forEach(slot => {
            const label = document.createElement('label');
            label.style.display = 'flex';
            label.style.alignItems = 'center';
            label.style.gap = '.5rem';
            label.style.fontSize = '.9rem';
            label.style.padding = '.5rem';
            label.style.border = '1px solid var(--border)';
            label.style.borderRadius = 'var(--radius-sm)';
            label.style.cursor = slot.available ? 'pointer' : 'not-allowed';
            label.style.background = slot.available ? 'transparent' : 'var(--surface2)';
            label.style.color = slot.available ? 'var(--text)' : 'var(--text-muted)';

            if (slot.available) {
                label.innerHTML = `<input type="checkbox" name="jam_main[]" value="${slot.jam}"> ${slot.label}`;
            } else {
                label.innerHTML = `<input type="checkbox" disabled> <span style="text-decoration:line-through;">${slot.label}</span>`;
            }

            container.appendChild(label);
        });

    } catch (err) {
        container.innerHTML = `<span style="color:red; font-size:.85rem; grid-column:span 3;">Kesalahan jaringan.</span>`;
    }
}

let pollInterval = null;

async function submitBooking(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmit');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
    btn.disabled = true;

    const form = document.getElementById('createBookingForm');
    const formData = new FormData(form);
    formData.set(CSRF_NAME, CSRF_HASH);

    try {
        const res = await fetch('<?= site_url('admin/bookings/store') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await res.json();
        
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal menyimpan booking.');
            btn.innerHTML = originalText;
            btn.disabled = false;
            return;
        }

        if (data.is_qris) {
            document.getElementById('modal-amount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.amount);
            document.getElementById('qris-modal').classList.add('open');
            
            if (data.qr_url) {
                const img = document.getElementById('modal-qr-img');
                img.onload = () => {
                    document.getElementById('modal-qr-spinner').style.display = 'none';
                    img.style.display = 'block';
                };
                img.src = data.qr_url;
                img.dataset.fallback = data.qr_string || '';
            } else if (data.qr_string) {
                qrImgError(data.qr_string);
            }

            pollInterval = setInterval(() => pollQrisNewBooking(data.booking_id), 3000);
            btn.innerHTML = originalText;
        } else {
            window.location.href = '<?= site_url('admin/bookings') ?>';
        }

    } catch (err) {
        alert('Terjadi kesalahan jaringan.');
        btn.innerHTML = originalText;
        btn.disabled = false;
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
        if (str) qrImgError(str);
        else document.getElementById('modal-qr-spinner').innerHTML = '<span style="color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> Gagal memuat QR</span>';
    }
}

async function pollQrisNewBooking(bookingId) {
    try {
        const res = await fetch('<?= site_url('admin/bookings/qris-new-status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId, [CSRF_NAME]: CSRF_HASH })
        });
        const data = await res.json();
        
        if (data.success && data.paid) {
            clearInterval(pollInterval);
            alert('Pembayaran QRIS berhasil! Booking telah diselesaikan.');
            window.location.href = '<?= site_url('admin/bookings') ?>';
        }
    } catch (err) {}
}

function cancelQris() {
    document.getElementById('qris-modal').classList.remove('open');
    if (pollInterval) clearInterval(pollInterval);
    alert('Pembayaran QRIS dibatalkan. Booking disimpan sebagai Pending (Belum Dibayar).');
    window.location.href = '<?= site_url('admin/bookings') ?>';
}
</script>

<style>
    .form-group label { display: block; font-weight:600; font-size:.85rem; color:var(--navy); margin-bottom:.35rem; }
    .form-control { width: 100%; padding: .65rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; font-size: .9rem; }
    .form-control:focus { border-color: var(--accent); outline: none; }
    
    @media (max-width: 768px) {
        form > div { grid-template-columns: 1fr !important; }
    }
</style>

<?php $this->endSection() ?>
