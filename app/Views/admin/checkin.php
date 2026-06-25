<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* ── Check-in Scanner Styles ── */
.checkin-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: flex-start;
}
@media (max-width: 900px) {
    .checkin-grid { grid-template-columns: 1fr; }
}

/* Scanner Box */
.scanner-box {
    background: var(--surface, #141414);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    overflow: hidden;
}
.scanner-box-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,.08);
    display: flex;
    align-items: center;
    gap: .6rem;
    font-size: .9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #aaa;
}
.scanner-box-header i { color: var(--volt, #d4f500); }

#reader {
    width: 100%;
    min-height: 280px;
    background: #000;
}
/* Kosmetik html5-qrcode: sembunyikan tombol bawaan */
#reader__dashboard_section_csr { display: none !important; }
#reader__status_span { display: none !important; }
#reader__header_message { display: none !important; }
#reader img { display: none; }
#reader video { width: 100% !important; max-height: 300px; object-fit: cover; }

/* Scanner Controls */
.scanner-controls {
    padding: 1rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: .75rem;
}
.scanner-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .7rem 1rem;
    border: none;
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
    font-family: inherit;
    width: 100%;
}
.scanner-btn-primary {
    background: var(--volt, #d4f500);
    color: #000;
}
.scanner-btn-primary:hover { background: #c5e300; }
.scanner-btn-danger {
    background: rgba(239,68,68,.15);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,.3);
}
.scanner-btn-danger:hover { background: rgba(239,68,68,.25); }

/* Manual input */
.manual-input-group {
    display: flex;
    gap: .5rem;
}
.manual-input {
    flex: 1;
    padding: .65rem .9rem;
    background: #1a1a1a;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px;
    color: #f0f0f0;
    font-family: 'Courier New', monospace;
    font-size: .9rem;
    text-transform: uppercase;
    outline: none;
    transition: border-color .15s;
}
.manual-input:focus { border-color: var(--volt, #d4f500); }
.manual-input::placeholder { text-transform: none; font-family: inherit; font-size: .85rem; color: #555; }
.manual-btn {
    padding: .65rem 1rem;
    background: var(--volt, #d4f500);
    color: #000;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s;
    font-family: inherit;
    font-size: .85rem;
    white-space: nowrap;
}
.manual-btn:hover { background: #c5e300; }

/* ── Hasil Scan ── */
.result-box {
    background: var(--surface, #141414);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    overflow: hidden;
}
.result-idle {
    padding: 3rem 1.5rem;
    text-align: center;
    color: #555;
}
.result-idle i { font-size: 2.5rem; margin-bottom: 1rem; display: block; }
.result-idle p { font-size: .85rem; }

/* Booking Info */
.result-booking-header {
    padding: 1.25rem;
    background: linear-gradient(135deg, rgba(212,245,0,.06), rgba(0,0,0,0));
    border-bottom: 1px solid rgba(255,255,255,.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}
.result-booking-code {
    font-family: 'Courier New', monospace;
    font-size: 1.2rem;
    font-weight: 900;
    color: var(--volt, #d4f500);
    letter-spacing: .06em;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .25rem .75rem;
    border-radius: 99px;
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.pill-lunas { background: rgba(34,197,94,.12); color: #22c55e; border: 1px solid rgba(34,197,94,.25); }
.pill-belum { background: rgba(245,158,11,.12); color: #f59e0b; border: 1px solid rgba(245,158,11,.25); }
.pill-checked { background: rgba(99,102,241,.12); color: #818cf8; border: 1px solid rgba(99,102,241,.25); }

.result-details { padding: 1rem 1.25rem; }
.result-detail-row {
    display: flex;
    justify-content: space-between;
    font-size: .83rem;
    padding: .45rem 0;
    border-bottom: 1px solid rgba(255,255,255,.05);
    gap: .5rem;
}
.result-detail-row:last-child { border-bottom: none; }
.result-detail-row .lbl { color: #777; display: flex; align-items: center; gap: .4rem; flex-shrink: 0; }
.result-detail-row .lbl i { width: 14px; text-align: center; font-size: .75rem; }
.result-detail-row .val { font-weight: 600; text-align: right; }

/* Action area */
.result-actions {
    padding: 1.25rem;
    border-top: 1px solid rgba(255,255,255,.08);
    display: flex;
    flex-direction: column;
    gap: .75rem;
}
.action-row { display: flex; gap: .75rem; }
.action-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .85rem 1rem;
    border: none;
    border-radius: 10px;
    font-size: .85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    font-family: inherit;
}
.action-btn-success {
    background: var(--volt, #d4f500);
    color: #000;
}
.action-btn-success:hover { background: #c5e300; transform: translateY(-1px); }
.action-btn-cash {
    background: rgba(34,197,94,.15);
    color: #22c55e;
    border: 1px solid rgba(34,197,94,.3);
}
.action-btn-cash:hover { background: rgba(34,197,94,.25); }
.action-btn-qris {
    background: rgba(99,102,241,.15);
    color: #818cf8;
    border: 1px solid rgba(99,102,241,.3);
}
.action-btn-qris:hover { background: rgba(99,102,241,.25); }

/* Alert messages */
.result-alert {
    margin: 1rem 1.25rem;
    padding: .85rem 1rem;
    border-radius: 10px;
    font-size: .83rem;
    display: flex;
    gap: .6rem;
    align-items: flex-start;
    line-height: 1.5;
}
.alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.25); color: #22c55e; }
.alert-warning { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.25); color: #f59e0b; }
.alert-error   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.25);  color: #ef4444; }

/* ── Modal QRIS ── */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.75);
    backdrop-filter: blur(6px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.modal-overlay.open { display: flex; }
.modal-card {
    background: #141414;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 16px;
    padding: 2rem;
    max-width: 380px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.6);
}
.modal-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: .35rem;
}
.modal-subtitle {
    font-size: .8rem;
    color: #777;
    margin-bottom: 1.25rem;
}
.modal-amount {
    font-size: 1.5rem;
    font-weight: 900;
    color: var(--volt, #d4f500);
    font-family: 'Montserrat', sans-serif;
    margin-bottom: 1.25rem;
}
.modal-qr-wrap {
    display: inline-flex;
    background: #fff;
    border-radius: 12px;
    padding: .75rem;
    margin-bottom: 1rem;
}
.modal-qr-wrap img { max-width: 200px; height: auto; display: block; }
.modal-qr-wrap canvas { display: block; }
.modal-polling {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    font-size: .82rem;
    color: #777;
    margin-bottom: 1.25rem;
}
.modal-polling .spinner {
    width: 14px; height: 14px;
    border: 2px solid rgba(212,245,0,.2);
    border-top-color: var(--volt, #d4f500);
    border-radius: 50%;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.modal-cancel {
    background: transparent;
    border: 1px solid rgba(255,255,255,.1);
    color: #888;
    padding: .6rem 1.25rem;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    font-size: .8rem;
    transition: all .15s;
}
.modal-cancel:hover { border-color: rgba(255,255,255,.25); color: #ccc; }

/* Tombol scan ulang */
.btn-rescan {
    background: transparent;
    border: 1px solid rgba(255,255,255,.1);
    color: #aaa;
    padding: .65rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    font-size: .83rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    transition: all .15s;
    width: 100%;
    justify-content: center;
}
.btn-rescan:hover { border-color: var(--volt, #d4f500); color: var(--volt, #d4f500); }
</style>

<!-- Toast Notification -->
<div id="toast" style="
    position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(80px);
    background: #1c1c1c; border: 1px solid rgba(255,255,255,.1); color: #f0f0f0;
    padding: .75rem 1.25rem; border-radius: 10px; font-size: .85rem; font-weight: 600;
    white-space: nowrap; z-index: 9999;
    box-shadow: 0 8px 32px rgba(0,0,0,.4);
    transition: transform .3s ease, opacity .3s ease; opacity: 0;
    display: flex; align-items: center; gap: .5rem;
"></div>

<div class="checkin-grid">

    <!-- ── KIRI: Scanner ── -->
    <div>
        <div class="scanner-box">
            <div class="scanner-box-header">
                <i class="fa-solid fa-camera"></i> Kamera Scanner
            </div>

            <!-- Viewport kamera -->
            <div id="reader"></div>

            <!-- Kontrol scanner & input manual -->
            <div class="scanner-controls">
                <button id="btn-start-scan" class="scanner-btn scanner-btn-primary">
                    <i class="fa-solid fa-camera"></i> Mulai Scan Kamera
                </button>
                <button id="btn-stop-scan" class="scanner-btn scanner-btn-danger" style="display:none;">
                    <i class="fa-solid fa-stop"></i> Hentikan Kamera
                </button>

                <div style="display:flex;align-items:center;gap:.5rem;color:#555;font-size:.75rem;">
                    <div style="flex:1;height:1px;background:rgba(255,255,255,.07);"></div>
                    atau input manual
                    <div style="flex:1;height:1px;background:rgba(255,255,255,.07);"></div>
                </div>

                <div class="manual-input-group">
                    <input type="text" id="manual-input" class="manual-input"
                           placeholder="Masukkan kode booking..." maxlength="20"
                           oninput="this.value = this.value.toUpperCase()">
                    <button class="manual-btn" onclick="submitManual()">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ── KANAN: Hasil ── -->
    <div>
        <div class="result-box" id="result-box">
            <!-- Idle state -->
            <div class="result-idle" id="result-idle">
                <i class="fa-solid fa-qrcode"></i>
                <p>Arahkan kamera ke QR Code user<br>atau masukkan kode booking secara manual.</p>
            </div>

            <!-- Loaded state (hidden by default) -->
            <div id="result-content" style="display:none;"></div>
        </div>
    </div>

</div>

<!-- ── Modal QRIS Pelunasan ── -->
<div class="modal-overlay" id="qris-modal">
    <div class="modal-card">
        <div class="modal-title">💳 Pelunasan via QRIS</div>
        <div class="modal-subtitle">Minta user untuk scan QR berikut</div>
        <div class="modal-amount" id="modal-amount">—</div>
        <div class="modal-qr-wrap">
            <canvas id="modal-qr-canvas"></canvas>
        </div>
        <div class="modal-polling">
            <div class="spinner"></div>
            <span>Menunggu pembayaran...</span>
        </div>
        <button class="modal-cancel" onclick="cancelQris()">
            <i class="fa-solid fa-xmark"></i> Batal / Gunakan Metode Lain
        </button>
    </div>
</div>

<!-- html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<!-- QRCode.js for QRIS modal -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
const CSRF_NAME  = '<?= csrf_token() ?>';
const CSRF_HASH  = '<?= csrf_hash() ?>';
const SCAN_URL   = '<?= site_url('admin/checkin/scan') ?>';
const PROSES_URL = '<?= site_url('admin/checkin/proses') ?>';
const QRIS_URL   = '<?= site_url('admin/checkin/qris-status') ?>';

let scanner       = null;
let scannerActive = false;
let lastScanned   = null;
let pollInterval  = null;
let qrisBookingId = null;

// ── QR Scanner ────────────────────────────────────────────────────────────────

document.getElementById('btn-start-scan').addEventListener('click', startScanner);
document.getElementById('btn-stop-scan').addEventListener('click', stopScanner);

function startScanner() {
    if (scannerActive) return;

    scanner = new Html5Qrcode('reader');
    scanner.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onQrSuccess,
        () => {}
    ).then(() => {
        scannerActive = true;
        document.getElementById('btn-start-scan').style.display = 'none';
        document.getElementById('btn-stop-scan').style.display  = '';
    }).catch(err => {
        showToast('⚠️ Kamera tidak dapat diakses: ' + err, 'warning');
    });
}

function stopScanner() {
    if (scanner && scannerActive) {
        scanner.stop().then(() => {
            scannerActive = false;
            document.getElementById('btn-start-scan').style.display = '';
            document.getElementById('btn-stop-scan').style.display  = 'none';
        });
    }
}

function onQrSuccess(decodedText) {
    // Ekstrak booking_code dari URL tiket atau raw text
    let code = decodedText.trim();
    const match = code.match(/tiket\/([A-Z0-9\-]+)/i);
    if (match) code = match[1].toUpperCase();

    if (code === lastScanned) return; // jangan proses duplikat
    lastScanned = code;

    // Beri feedback suara singkat (opsional)
    try { new Audio('data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAA').play(); } catch(e) {}

    lookupBooking(code);
}

// ── Lookup Booking ────────────────────────────────────────────────────────────

function submitManual() {
    const code = document.getElementById('manual-input').value.trim().toUpperCase();
    if (!code) return;
    lastScanned = code;
    lookupBooking(code);
}

document.getElementById('manual-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') submitManual();
});

async function lookupBooking(code) {
    showResultLoading();

    const res = await apiFetch(SCAN_URL, { booking_code: code });

    if (!res.ok) {
        const data = await res.json();
        showResultError(data.message || 'Terjadi kesalahan.');
        return;
    }

    const data = await res.json();

    if (data.already_in) {
        showResultAlreadyIn(data);
        return;
    }

    showResultFound(data);
}

// ── Render Results ────────────────────────────────────────────────────────────

function showResultLoading() {
    document.getElementById('result-idle').style.display    = 'none';
    document.getElementById('result-content').style.display = '';
    document.getElementById('result-content').innerHTML = `
        <div style="padding:3rem;text-align:center;color:#777;">
            <div class="modal-polling" style="justify-content:center;">
                <div class="spinner"></div> <span>Mencari booking...</span>
            </div>
        </div>`;
}

function showResultError(msg) {
    document.getElementById('result-idle').style.display    = 'none';
    document.getElementById('result-content').style.display = '';
    document.getElementById('result-content').innerHTML = `
        <div class="result-alert alert-error" style="margin:1.25rem;">
            <i class="fa-solid fa-circle-xmark"></i>
            <div>${msg}</div>
        </div>
        <div style="padding:0 1.25rem 1.25rem;">
            <button class="btn-rescan" onclick="resetResult()">
                <i class="fa-solid fa-rotate-left"></i> Scan Ulang
            </button>
        </div>`;
}

function showResultAlreadyIn(data) {
    const b = data.booking;
    document.getElementById('result-idle').style.display    = 'none';
    document.getElementById('result-content').style.display = '';
    document.getElementById('result-content').innerHTML = `
        <div class="result-booking-header">
            <div class="result-booking-code">${b.booking_code}</div>
            <span class="status-pill pill-checked"><i class="fa-solid fa-check-double"></i> Sudah Check-in</span>
        </div>
        <div class="result-alert alert-success" style="margin:1.25rem;">
            <i class="fa-solid fa-circle-check"></i>
            <div>${data.message}</div>
        </div>
        <div style="padding:0 1.25rem 1.25rem;">
            <button class="btn-rescan" onclick="resetResult()">
                <i class="fa-solid fa-rotate-left"></i> Scan User Berikutnya
            </button>
        </div>`;
}

function showResultFound(data) {
    const b       = data.booking;
    const needsPay = data.needs_payment;
    const sisa    = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.sisa_tagihan);

    const tanggal = new Date(b.tanggal_main).toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    const skema   = b.skema_pembayaran === 'dp' ? 'DP 50%' : 'Full Payment';
    const total   = 'Rp ' + new Intl.NumberFormat('id-ID').format(b.total_harga);

    const pelunasanBadge = needsPay
        ? `<span class="status-pill pill-belum"><i class="fa-solid fa-clock"></i> Belum Lunas</span>`
        : `<span class="status-pill pill-lunas"><i class="fa-solid fa-check"></i> Lunas</span>`;

    const actionButtons = needsPay
        ? `<div class="action-row">
                <button class="action-btn action-btn-cash" onclick="doCheckin(${b.id}, 'cash')">
                    <i class="fa-solid fa-money-bill"></i> Cash &nbsp;<small>(${sisa})</small>
                </button>
                <button class="action-btn action-btn-qris" onclick="doCheckin(${b.id}, 'qris')">
                    <i class="fa-solid fa-qrcode"></i> QRIS &nbsp;<small>(${sisa})</small>
                </button>
           </div>`
        : `<button class="action-btn action-btn-success" onclick="doCheckin(${b.id}, null)">
                <i class="fa-solid fa-circle-check"></i> Konfirmasi Check-in
           </button>`;

    document.getElementById('result-idle').style.display    = 'none';
    document.getElementById('result-content').style.display = '';
    document.getElementById('result-content').innerHTML = `
        <div class="result-booking-header">
            <div class="result-booking-code">${b.booking_code}</div>
            ${pelunasanBadge}
        </div>
        <div class="result-details">
            <div class="result-detail-row">
                <span class="lbl"><i class="fa-solid fa-user"></i> Nama</span>
                <span class="val">${b.nama_pemesan}</span>
            </div>
            <div class="result-detail-row">
                <span class="lbl"><i class="fa-solid fa-building"></i> Lapangan</span>
                <span class="val">${b.nama_lapangan}</span>
            </div>
            <div class="result-detail-row">
                <span class="lbl"><i class="fa-solid fa-calendar"></i> Tanggal</span>
                <span class="val">${tanggal}</span>
            </div>
            <div class="result-detail-row">
                <span class="lbl"><i class="fa-solid fa-clock"></i> Waktu</span>
                <span class="val">${data.jam_fmt} WIB</span>
            </div>
            <div class="result-detail-row">
                <span class="lbl"><i class="fa-solid fa-credit-card"></i> Skema</span>
                <span class="val">${skema}</span>
            </div>
            <div class="result-detail-row">
                <span class="lbl"><i class="fa-solid fa-money-bill"></i> Total</span>
                <span class="val">${total}</span>
            </div>
            ${needsPay ? `<div class="result-detail-row">
                <span class="lbl"><i class="fa-solid fa-hourglass-half"></i> Sisa Tagihan</span>
                <span class="val" style="color:#f59e0b;font-weight:700;">${sisa}</span>
            </div>` : ''}
        </div>
        ${needsPay ? `<div class="result-alert alert-warning" style="margin:.75rem 1.25rem 0;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>User ini membayar DP. Pilih metode pelunasan sebelum check-in.</div>
        </div>` : ''}
        <div class="result-actions">
            ${actionButtons}
            <button class="btn-rescan" onclick="resetResult()">
                <i class="fa-solid fa-rotate-left"></i> Scan Ulang
            </button>
        </div>`;
}

// ── Check-in Actions ──────────────────────────────────────────────────────────

async function doCheckin(bookingId, method) {
    const res  = await apiFetch(PROSES_URL, { booking_id: bookingId, method: method });
    const data = await res.json();

    if (!data.success) {
        showToast('❌ ' + (data.message || 'Terjadi kesalahan.'), 'error');
        return;
    }

    if (data.mode === 'qris_pending') {
        // Tampilkan modal QRIS
        openQrisModal(data, bookingId);
        return;
    }

    // mode === 'done'
    showToast(data.message, 'success');
    showSuccessResult(data.message);
}

function showSuccessResult(msg) {
    document.getElementById('result-idle').style.display    = 'none';
    document.getElementById('result-content').style.display = '';
    document.getElementById('result-content').innerHTML = `
        <div class="result-alert alert-success" style="margin:1.25rem;">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div><strong>${msg}</strong></div>
        </div>
        <div style="padding:0 1.25rem 1.25rem;">
            <button class="btn-rescan" onclick="resetResult()">
                <i class="fa-solid fa-rotate-left"></i> Scan User Berikutnya
            </button>
        </div>`;
    lastScanned = null;
}

// ── Modal QRIS ────────────────────────────────────────────────────────────────

function openQrisModal(data, bookingId) {
    qrisBookingId = bookingId;

    document.getElementById('modal-amount').textContent =
        'Rp ' + new Intl.NumberFormat('id-ID').format(data.sisa_tagihan);

    // Generate QR
    const canvas = document.getElementById('modal-qr-canvas');
    const qrValue = data.pay_url || data.qr_string || '';
    QRCode.toCanvas(canvas, qrValue || 'tap4smash-qris', {
        width: 200, margin: 0,
        color: { dark: '#111827', light: '#ffffff' },
    });

    document.getElementById('qris-modal').classList.add('open');

    // Polling tiap 3 detik
    pollInterval = setInterval(pollQrisStatus, 3000);
}

async function pollQrisStatus() {
    const res  = await apiFetch(QRIS_URL, { booking_id: qrisBookingId });
    const data = await res.json();

    if (data.done) {
        clearInterval(pollInterval);
        pollInterval = null;
        closeQrisModal();
        showToast(data.message || '✅ Pembayaran berhasil!', 'success');
        showSuccessResult(data.message || '✅ User berhasil check-in!');
    }
}

function cancelQris() {
    if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
    closeQrisModal();
}

function closeQrisModal() {
    document.getElementById('qris-modal').classList.remove('open');
    qrisBookingId = null;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function resetResult() {
    lastScanned = null;
    document.getElementById('result-idle').style.display    = '';
    document.getElementById('result-content').style.display = 'none';
    document.getElementById('manual-input').value = '';
}

async function apiFetch(url, body) {
    return fetch(url, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_HASH,
        },
        body: JSON.stringify(body),
    });
}

let toastTimer = null;
function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    const colors = {
        success: { bg: '#1a2e1a', border: 'rgba(34,197,94,.3)', color: '#22c55e' },
        error:   { bg: '#2e1a1a', border: 'rgba(239,68,68,.3)',  color: '#ef4444' },
        warning: { bg: '#2e2a1a', border: 'rgba(245,158,11,.3)', color: '#f59e0b' },
    };
    const c = colors[type] || colors.success;
    el.style.background   = c.bg;
    el.style.borderColor  = c.border;
    el.style.color        = c.color;
    el.innerHTML          = msg;
    el.style.transform    = 'translateX(-50%) translateY(0)';
    el.style.opacity      = '1';
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        el.style.transform = 'translateX(-50%) translateY(80px)';
        el.style.opacity   = '0';
    }, 3500);
}
</script>

<?= $this->endSection() ?>
