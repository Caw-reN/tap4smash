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
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.scanner-box-header {
    padding: 1.25rem 1.5rem;
    background: var(--navy);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: .6rem;
    font-family: 'Oswald', sans-serif;
    font-size: .85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #fff;
}
.scanner-box-header i { color: var(--accent); }

#reader {
    width: 100%;
    min-height: 280px;
    background: var(--navy);
}
/* Kosmetik html5-qrcode: sembunyikan tombol bawaan */
#reader__dashboard_section_csr { display: none !important; }
#reader__status_span { display: none !important; }
#reader__header_message { display: none !important; }
#reader img { display: none; }
#reader video { width: 100% !important; max-height: 300px; object-fit: cover; }

/* Scanner Controls */
.scanner-controls {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: .85rem;
    background: var(--surface);
}
.scanner-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .75rem 1rem;
    border: none;
    border-radius: var(--radius);
    font-family: 'Oswald', sans-serif;
    font-size: .85rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    cursor: pointer;
    transition: all .15s;
    width: 100%;
}
.scanner-btn-primary {
    background: var(--accent);
    color: #000;
    box-shadow: var(--shadow-sm);
}
.scanner-btn-primary:hover { background: #99ee00; box-shadow: 0 4px 12px rgba(170,238,0,.3); transform: translateY(-1px); }
.scanner-btn-danger {
    background: #ef4444;
    color: #fff;
    box-shadow: var(--shadow-sm);
}
.scanner-btn-danger:hover { background: #dc2626; box-shadow: 0 4px 12px rgba(239,68,68,.3); transform: translateY(-1px); }

/* Divider */
.scanner-divider {
    display: flex;
    align-items: center;
    gap: .5rem;
    color: var(--text-muted);
    font-size: .72rem;
    font-family: 'Oswald', sans-serif;
    text-transform: uppercase;
    letter-spacing: .1em;
}
.scanner-divider::before,
.scanner-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* Manual input */
.manual-input-group {
    display: flex;
    gap: .5rem;
}
.manual-input {
    flex: 1;
    padding: .7rem .95rem;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-family: 'Inter', sans-serif;
    font-size: .875rem;
    text-transform: uppercase;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.manual-input:focus {
    border-color: var(--navy);
    box-shadow: 0 0 0 3px var(--navy-dim);
}
.manual-input::placeholder { text-transform: none; font-size: .83rem; color: var(--text-muted); }
.manual-btn {
    padding: .7rem 1.15rem;
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-weight: 700;
    cursor: pointer;
    transition: background .15s;
    font-size: .9rem;
}
.manual-btn:hover { background: var(--navy-mid); }

/* ── Hasil Scan ── */
.result-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.result-idle {
    padding: 3.5rem 1.5rem;
    text-align: center;
    color: var(--text-muted);
}
.result-idle i { font-size: 3rem; margin-bottom: 1rem; display: block; opacity: .25; color: var(--navy); }
.result-idle p { font-size: .85rem; line-height: 1.6; }

/* Booking Info */
.result-booking-header {
    padding: 1.25rem 1.5rem;
    background: var(--navy);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}
.result-booking-code {
    font-family: 'Oswald', sans-serif;
    font-size: 1.15rem;
    font-weight: 800;
    color: #000;
    letter-spacing: .06em;
    background: var(--accent);
    padding: .25rem .75rem;
    border-radius: var(--radius-sm);
    border: none;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .28rem .75rem;
    border-radius: 50px;
    font-family: 'Oswald', sans-serif;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
}
.pill-lunas  { background: #f0fdf4; color: #16a34a; border: 1px solid #22c55e; }
.pill-belum  { background: #fffbeb; color: #d97706; border: 1px solid #f59e0b; }
.pill-checked { background: #eff6ff; color: #2563eb; border: 1px solid #3b82f6; }

.result-details { padding: 1.25rem 1.5rem; }
.result-detail-row {
    display: flex;
    justify-content: space-between;
    font-size: .85rem;
    padding: .6rem 0;
    border-bottom: 1px solid var(--border);
    gap: .5rem;
    color: var(--text-mid);
}
.result-detail-row:last-child { border-bottom: none; }
.result-detail-row .lbl { color: var(--text-muted); display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
.result-detail-row .lbl i { width: 14px; text-align: center; font-size: .78rem; color: var(--navy); }
.result-detail-row .val { font-weight: 600; text-align: right; color: var(--text); }

/* Action area */
.result-actions {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    gap: .75rem;
    background: var(--surface2);
}
.action-row { display: flex; gap: .75rem; }
.action-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: .8rem 1rem;
    border: none;
    border-radius: var(--radius);
    font-family: 'Oswald', sans-serif;
    font-size: .82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    cursor: pointer;
    transition: all .15s;
    box-shadow: var(--shadow-sm);
}
.action-btn-success {
    background: var(--accent);
    color: #000;
}
.action-btn-success:hover { background: #99ee00; box-shadow: 0 4px 12px rgba(170,238,0,.3); transform: translateY(-1px); }
.action-btn-cash {
    background: var(--accent);
    color: #000;
}
.action-btn-cash:hover { background: #99ee00; box-shadow: 0 4px 12px rgba(170,238,0,.3); transform: translateY(-1px); }
.action-btn-qris {
    background: var(--navy);
    color: #fff;
}
.action-btn-qris:hover { background: var(--navy-light); box-shadow: 0 4px 12px rgba(15,32,68,.3); transform: translateY(-1px); }

/* Custom Banners & Notifications */
.dp-payment-banner, .success-banner, .error-banner {
    margin: 1.25rem 1.5rem;
    padding: 1rem 1.25rem;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
}
.banner-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: var(--navy);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.banner-icon-success {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: var(--accent);
    color: var(--navy);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(170,238,0,.3);
}
.banner-icon-error {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: var(--navy);
    color: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(239,68,68,.25);
}
.banner-content { flex: 1; text-align: left; }
.banner-title {
    font-family: 'Oswald', sans-serif;
    font-size: .95rem;
    font-weight: 700;
    color: var(--navy);
    letter-spacing: .04em;
    margin-bottom: .15rem;
}
.banner-desc {
    font-size: .82rem;
    color: var(--text);
    line-height: 1.4;
    font-weight: 500;
}

/* ── Modal QRIS ── */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,32,68,.55);
    backdrop-filter: blur(6px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.modal-overlay.open { display: flex; }
.modal-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 2rem 1.75rem;
    max-width: 420px;
    width: 100%;
    text-align: center;
    box-shadow: var(--shadow-lg);
}
.modal-title {
    font-family: 'Oswald', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--navy);
    margin-bottom: .35rem;
}
.modal-subtitle {
    font-size: .8rem;
    color: var(--text-muted);
    margin-bottom: .75rem;
}
.modal-amount {
    font-family: 'Oswald', sans-serif;
    font-size: 1.9rem;
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 1rem;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .5rem 1rem;
    display: inline-block;
}
.modal-qr-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 2px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow-md);
}
.modal-qr-wrap img { width: 220px; height: 220px; display: block; object-fit: contain; }
.modal-qr-wrap canvas { display: block; }
#modal-qr-spinner {
    width: 220px;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: .85rem;
    gap: .5rem;
    flex-direction: column;
}
.modal-instruction {
    font-size: .8rem;
    color: var(--text-muted);
    margin-bottom: .75rem;
    line-height: 1.5;
    background: var(--surface2);
    border-radius: var(--radius-sm);
    padding: .6rem .85rem;
    border: 1px solid var(--border);
}
.modal-polling {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    font-size: .82rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}
.modal-polling .spinner {
    width: 14px; height: 14px;
    border: 2px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.modal-cancel {
    background: transparent;
    border: 1px solid var(--border-dark);
    color: var(--text-muted);
    padding: .65rem 1.25rem;
    border-radius: var(--radius);
    cursor: pointer;
    font-family: 'Oswald', sans-serif;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    transition: all .15s;
    width: 100%;
}
.modal-cancel:hover { border-color: var(--navy); color: var(--navy); background: var(--surface2); }

/* Tombol scan ulang */
.btn-rescan {
    background: var(--surface);
    border: 1px solid var(--border-dark);
    color: var(--navy);
    padding: .7rem 1rem;
    border-radius: var(--radius);
    cursor: pointer;
    font-family: 'Oswald', sans-serif;
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    transition: all .15s;
    width: 100%;
    justify-content: center;
}
.btn-rescan:hover { border-color: var(--navy); color: #fff; background: var(--navy); }
</style>

<!-- Toast Notification -->
<div id="toast" style="
    position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(80px);
    background: var(--surface); border: 1px solid var(--border); color: var(--text);
    padding: .75rem 1.25rem; border-radius: var(--radius); font-size: .85rem; font-weight: 600;
    white-space: nowrap; z-index: 9999;
    box-shadow: var(--shadow-lg);
    transition: transform .3s ease, opacity .3s ease; opacity: 0;
    display: flex; align-items: center; gap: .5rem; font-family: 'Inter', sans-serif;
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

                <div class="scanner-divider">atau input manual</div>

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
        <div style="display:flex;align-items:center;justify-content:center;gap:.6rem;margin-bottom:.35rem;">
            <i class="fa-solid fa-qrcode" style="font-size:1.3rem;color:var(--accent);"></i>
            <div class="modal-title" style="margin:0;">Pelunasan via QRIS</div>
        </div>
        <div class="modal-subtitle">Tunjukkan QR di bawah kepada customer untuk di-scan</div>
        <div class="modal-amount" id="modal-amount">—</div>
        <div class="modal-qr-wrap">
            <!-- Spinner saat loading -->
            <div id="modal-qr-spinner">
                <div class="spinner" style="width:28px;height:28px;border-width:3px;"></div>
                <span style="font-size:.78rem;">Memuat QR...</span>
            </div>
            <!-- Img dari qr_url PaymentKu (prioritas utama) -->
            <img id="modal-qr-img" src="" alt="QRIS" style="display:none;" onerror="qrImgError()">
            <!-- Canvas fallback dari qr_string / pay_url -->
            <canvas id="modal-qr-canvas" style="display:none;"></canvas>
        </div>
        <div class="modal-instruction">
            <i class="fa-solid fa-circle-info" style="color:var(--navy);"></i>
            Scan dengan aplikasi dompet digital atau mobile banking mana saja.
            Sistem akan otomatis mengkonfirmasi setelah pembayaran selesai.
        </div>
        <div class="modal-polling">
            <div class="spinner"></div>
            <span>Menunggu konfirmasi pembayaran...</span>
        </div>
        <button class="modal-cancel" onclick="cancelQris()">
            <i class="fa-solid fa-xmark"></i> Batal / Gunakan Metode Lain
        </button>
    </div>
</div>

<!-- ── Modal Konfirmasi CASH Pelunasan ── -->
<div class="modal-overlay" id="cash-confirm-modal">
    <div class="modal-card" style="max-width: 420px;">
        <div class="modal-title" style="color:var(--navy); display:flex; align-items:center; justify-content:center; gap:.6rem; font-size:1.1rem; margin-bottom:.75rem;">
            <i class="fa-solid fa-money-bill-wave" style="color:var(--accent); font-size:1.35rem;"></i> KONFIRMASI TUNAI
        </div>
        <div style="font-size: .88rem; color: var(--text-muted); margin: .5rem 0 1.5rem; line-height: 1.5;">
            Apakah Anda sudah menerima pembayaran tunai sebesar <strong id="cash-modal-amount" style="color:#dc2626; font-size:1.05rem;"></strong> untuk booking <strong id="cash-modal-code" style="color:var(--navy);"></strong>?
        </div>
        <div style="display: flex; gap: .75rem; justify-content: center;">
            <button type="button" class="action-btn action-btn-cash" id="btn-confirm-cash" style="flex: 1.4; font-size: .88rem; padding: .85rem .75rem; white-space: nowrap; justify-content: center;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.1rem;"></i> TERIMA & CHECK-IN
            </button>
            <button type="button" class="action-btn" onclick="closeCashModal()" style="flex: 0.8; font-size: .88rem; padding: .85rem .75rem; background: var(--surface2); color: var(--navy); border: 1px solid var(--border); white-space: nowrap; justify-content: center;">
                <i class="fa-solid fa-xmark" style="font-size: 1.1rem; color: var(--text-muted);"></i> BATAL
            </button>
        </div>
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
    const data = await res.json().catch(() => ({}));

    if (!res.ok) {
        if (data && data.already_in && data.booking) {
            showResultAlreadyIn(data);
            return;
        }
        showResultError(data.message || 'Terjadi kesalahan.');
        return;
    }

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
        <div class="error-banner">
            <div class="banner-icon-error">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div class="banner-content">
                <div class="banner-title">INFORMASI CHECK-IN</div>
                <div class="banner-desc">${msg}</div>
            </div>
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
        <div class="success-banner">
            <div class="banner-icon-success">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div class="banner-content">
                <div class="banner-title">STATUS CHECK-IN</div>
                <div class="banner-desc">${data.message}</div>
            </div>
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
                <button class="action-btn action-btn-cash" onclick="confirmCash(${b.id}, '${b.booking_code}', '${sisa}')">
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
                <span class="val" style="color:#dc2626;font-weight:800;font-size:1rem;">${sisa}</span>
            </div>` : ''}
        </div>
        ${needsPay ? `<div class="dp-payment-banner">
            <div class="banner-icon">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="banner-content">
                <div class="banner-title">MENUNGGU PELUNASAN TAGIHAN</div>
                <div class="banner-desc">User ini membayar DP 50%. Pilih metode pelunasan di bawah sebelum check-in.</div>
            </div>
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
        <div class="success-banner">
            <div class="banner-icon-success">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="banner-content">
                <div class="banner-title">CHECK-IN BERHASIL</div>
                <div class="banner-desc">${msg}</div>
            </div>
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

    // Tampilkan nominal
    document.getElementById('modal-amount').textContent =
        'Rp ' + new Intl.NumberFormat('id-ID').format(data.sisa_tagihan);

    // Reset tampilan QR
    const spinner  = document.getElementById('modal-qr-spinner');
    const qrImg    = document.getElementById('modal-qr-img');
    const qrCanvas = document.getElementById('modal-qr-canvas');
    spinner.style.display  = 'flex';
    qrImg.style.display    = 'none';
    qrCanvas.style.display = 'none';
    qrImg.src = '';
    qrImg.dataset.qrstring = data.qr_string || data.pay_url || '';

    document.getElementById('qris-modal').classList.add('open');

    // Prioritas 1: qr_url dari PaymentKu (gambar QRIS asli)
    if (data.qr_url) {
        qrImg.onload = function() {
            spinner.style.display  = 'none';
            qrImg.style.display    = 'block';
        };
        qrImg.src = data.qr_url;
    }
    // Prioritas 2: qr_string EMVCo → generate via QRCode.js
    else if (data.qr_string || data.pay_url) {
        const qrValue = data.qr_string || data.pay_url;
        QRCode.toCanvas(qrCanvas, qrValue, {
            width: 220, margin: 1,
            color: { dark: '#0F2044', light: '#ffffff' },
        }, function(err) {
            spinner.style.display  = 'none';
            qrCanvas.style.display = err ? 'none' : 'block';
            if (err) {
                spinner.style.display = 'flex';
                spinner.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color:#dc2626;font-size:1.5rem;"></i><span style="font-size:.78rem;color:#dc2626;">Gagal memuat QR.</span>';
            }
        });
    } else {
        // Tidak ada QR sama sekali
        spinner.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color:#dc2626;font-size:1.5rem;"></i><span style="font-size:.78rem;color:#dc2626;">QR tidak tersedia.</span>';
    }

    // Polling tiap 2 detik
    pollInterval = setInterval(pollQrisStatus, 2000);
}

// Fallback jika qr_url gagal dimuat (broken image)
function qrImgError() {
    const spinner  = document.getElementById('modal-qr-spinner');
    const qrImg    = document.getElementById('modal-qr-img');
    const qrCanvas = document.getElementById('modal-qr-canvas');
    qrImg.style.display = 'none';
    // Fallback ke qr_string via canvas — ambil dari atribut data
    const qrValue = qrImg.dataset.qrstring;
    if (qrValue) {
        QRCode.toCanvas(qrCanvas, qrValue, {
            width: 220, margin: 1,
            color: { dark: '#0F2044', light: '#ffffff' },
        }, function() {
            spinner.style.display  = 'none';
            qrCanvas.style.display = 'block';
        });
    } else {
        spinner.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color:#dc2626;font-size:1.5rem;"></i><span style="font-size:.78rem;color:#dc2626;">QR tidak dapat dimuat.</span>';
        spinner.style.display = 'flex';
    }
}

async function pollQrisStatus() {
    try {
        const res  = await apiFetch(QRIS_URL, { booking_id: qrisBookingId });
        const data = await res.json();

        if (data.done) {
            clearInterval(pollInterval);
            pollInterval = null;
            closeQrisModal();
            showToast('✅ ' + (data.message || 'Pembayaran berhasil! User berhasil check-in!'), 'success');
            showSuccessResult(data.message || 'Pembayaran QRIS terkonfirmasi. User berhasil check-in!');
        }
    } catch (e) {
        // Abaikan error network sementara, lanjutkan polling
        console.warn('[pollQrisStatus] network error, will retry:', e);
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

// ── Modal konfirmasi CASH ─────────────────────────────────────────────────────

function confirmCash(bookingId, code, sisa) {
    document.getElementById('cash-modal-amount').textContent = sisa;
    document.getElementById('cash-modal-code').textContent = code;
    const btn = document.getElementById('btn-confirm-cash');
    btn.onclick = function() {
        closeCashModal();
        doCheckin(bookingId, 'cash');
    };
    document.getElementById('cash-confirm-modal').classList.add('open');
}

function closeCashModal() {
    document.getElementById('cash-confirm-modal').classList.remove('open');
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
            'Content-Type':    'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN':    CSRF_HASH,
        },
        body: JSON.stringify(body),
    });
}

let toastTimer = null;
function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    const colors = {
        success: { bg: 'rgba(22,163,74,.08)',  border: 'rgba(22,163,74,.3)',  color: '#14532d' },
        error:   { bg: 'rgba(220,38,38,.08)',  border: 'rgba(220,38,38,.3)',  color: '#991b1b' },
        warning: { bg: 'rgba(217,119,6,.08)',  border: 'rgba(217,119,6,.3)',  color: '#78350f' },
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
