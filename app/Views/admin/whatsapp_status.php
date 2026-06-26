<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
        .wa-status-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            min-height: calc(100vh - 150px);
        }

        /* ── Card Utama ───────────────────────────────────────── */
        .wa-card {
            width: 100%;
            max-width: 480px;
            background: var(--slate-dark);
            border: 1px solid var(--slate);
            border-radius: 6px;
            overflow: hidden;
        }

        .card-header {
            background: var(--charcoal);
            border-bottom: 2px solid var(--volt);
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .card-header .icon {
            font-size: 2rem;
            line-height: 1;
            color: var(--volt);
        }

        .card-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #fff;
            line-height: 1.2;
        }

        .card-header h1 span {
            display: block;
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--volt);
            letter-spacing: 0.15em;
            font-family: 'Inter', sans-serif;
        }

        .card-body {
            padding: 2rem;
        }

        /* ── Status Badge ─────────────────────────────────────── */
        .status-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border: 1px solid var(--slate);
            background: var(--charcoal);
            margin-bottom: 1.75rem;
            border-radius: 6px;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-dot.connected   { background: var(--green);  box-shadow: 0 0 8px var(--green); animation: pulse-green 2s infinite; }
        .status-dot.scanning    { background: var(--yellow); box-shadow: 0 0 8px var(--yellow); animation: pulse-yellow 1s infinite; }
        .status-dot.offline     { background: var(--red);    box-shadow: 0 0 8px var(--red); }
        .status-dot.loading     { background: var(--slate);  }

        @keyframes pulse-green  { 0%,100%{opacity:1} 50%{opacity:.4} }
        @keyframes pulse-yellow { 0%,100%{opacity:1} 50%{opacity:.3} }

        .status-text {
            font-size: 0.875rem;
            font-weight: 500;
            color: #E5E7EB;
            flex: 1;
        }

        .status-badge {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
        }

        .badge-connected { background: rgba(34,197,94,.15); color: var(--green); border: 1px solid rgba(34,197,94,.3); }
        .badge-scanning  { background: rgba(245,158,11,.15); color: var(--yellow); border: 1px solid rgba(245,158,11,.3); }
        .badge-offline   { background: rgba(239,68,68,.15); color: var(--red); border: 1px solid rgba(239,68,68,.3); }

        /* ── Phone Number ─────────────────────────────────────── */
        .phone-info {
            display: none;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
            padding: 0.875rem 1.25rem;
            background: rgba(204,255,0,.05);
            border: 1px solid rgba(204,255,0,.2);
            font-size: 0.85rem;
            color: var(--volt);
            font-weight: 600;
            border-radius: 6px;
        }

        /* ── QR Section ───────────────────────────────────────── */
        .qr-section {
            text-align: center;
        }

        .qr-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
        }

        .qr-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 284px;
            height: 284px;
            background: #fff;
            padding: 0;
            position: relative;
            margin-bottom: 1.25rem;
            border-radius: 6px;
            overflow: hidden;
        }

        .qr-box img {
            width: 280px;
            height: 280px;
            display: block;
        }

        .qr-box .qr-overlay {
            position: absolute;
            inset: 0;
            background: rgba(17,24,39,.85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .spinner {
            width: 36px;
            height: 36px;
            border: 3px solid var(--slate);
            border-top-color: var(--volt);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .qr-hint {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 240px;
        }

        .qr-timer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .qr-timer span {
            color: var(--volt);
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            min-width: 20px;
            text-align: center;
        }

        /* ── Connected State ──────────────────────────────────── */
        .connected-state {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 2.5rem 0;
        }

        .connected-state .check-icon {
            font-size: 3.5rem;
            animation: pop-in .4s ease;
            color: var(--green);
        }

        @keyframes pop-in { from{transform:scale(.5);opacity:0} to{transform:scale(1);opacity:1} }

        .connected-state h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 1.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--green);
        }

        .connected-state p {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-align: center;
        }

        /* ── Offline / Error State ────────────────────────────── */
        .offline-state {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: 2rem 0;
            text-align: center;
        }

        .offline-state .error-icon { font-size: 2.5rem; color: var(--red); }

        .offline-state p {
            font-size: 0.8rem;
            color: var(--text-muted);
            max-width: 300px;
            line-height: 1.6;
        }

        .offline-state code {
            display: inline-block;
            background: var(--charcoal);
            border: 1px solid var(--slate);
            padding: 0.3rem 0.6rem;
            font-size: 0.75rem;
            color: var(--volt);
            font-family: 'Courier New', monospace;
            margin-top: 0.5rem;
            border-radius: 4px;
        }

        /* ── Footer ───────────────────────────────────────────── */
        .card-footer {
            border-top: 1px solid var(--slate);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-refresh {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: transparent;
            border: 1px solid var(--slate);
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            padding: 0.45rem 0.9rem;
            cursor: pointer;
            transition: all 0.15s;
            border-radius: 6px;
        }

        .btn-refresh:hover {
            border-color: var(--volt);
            color: var(--volt);
        }

        .auto-refresh-info {
            font-size: 0.7rem;
            color: var(--slate);
        }

        .auto-refresh-info span {
            color: var(--text-muted);
        }
    </style>
</style>

<div class="wa-status-wrapper">
<div class="wa-card">

    <!-- Header -->
    <div class="card-header">
        <div class="icon"><i class="fa-brands fa-whatsapp"></i></div>
        <h1>
            <span>Tap4Smash Admin</span>
            WhatsApp Gateway
        </h1>
    </div>

    <!-- Body -->
    <div class="card-body">

        <!-- Status Row -->
        <div class="status-row" id="statusRow">
            <div class="status-dot loading" id="statusDot"></div>
            <div class="status-text" id="statusText">Memeriksa koneksi...</div>
            <div class="status-badge" id="statusBadge"></div>
        </div>

        <!-- Phone Info (visible when connected) -->
        <div class="phone-info" id="phoneInfo">
            <span><i class="fa-solid fa-mobile-screen"></i></span>
            <span id="phoneNumber">—</span>
        </div>

        <!-- QR Section (visible when not connected) -->
        <div class="qr-section" id="qrSection">
            <p class="qr-label">Scan QR Code dengan WhatsApp</p>

            <div class="qr-box" id="qrBox">
                <!-- QR image akan diisi JS -->
                <div class="qr-overlay" id="qrOverlay">
                    <div class="spinner"></div>
                    <p style="font-size:.7rem;color:#9CA3AF;margin-top:.25rem">Memuat QR...</p>
                </div>
            </div>

            <div class="qr-timer" id="qrTimer" style="display:none">
                <i class="fa-regular fa-clock"></i> QR refresh dalam <span id="countdown">30</span> detik
            </div>

            <p class="qr-hint">
                Buka WhatsApp di HP → <strong>Setelan</strong> → <strong>Perangkat Tertaut</strong>
                → <strong>Tautkan Perangkat</strong> → Arahkan kamera ke QR di atas.
            </p>
        </div>

        <!-- Connected State -->
        <div class="connected-state" id="connectedState">
            <div class="check-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h2>Terhubung!</h2>
            <p>WhatsApp Gateway aktif dan siap<br>mengirim notifikasi booking.</p>

            <form action="<?= site_url('admin/whatsapp/logout') ?>" method="post" style="margin-top: 1.5rem;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-red" onclick="return confirm('Yakin ingin logout dan hapus sesi WA saat ini? Anda harus melakukan scan QR ulang setelahnya.')">
                    <i class="fa-solid fa-power-off"></i> Logout & Scan Ulang
                </button>
            </form>
        </div>

        <!-- Offline / Service Error State -->
        <div class="offline-state" id="offlineState">
            <div class="error-icon"><i class="fa-solid fa-plug-circle-xmark"></i></div>
            <p>WA Service tidak dapat dijangkau.<br>Pastikan Node.js service sudah berjalan:</p>
            <code>node wa-service/index.js</code>
        </div>

    </div><!-- /card-body -->

    <!-- Footer -->
    <div class="card-footer">
        <button class="btn-refresh" id="btnRefresh" onclick="fetchStatus()">
            <i class="fa-solid fa-rotate-right"></i> Refresh Manual
        </button>
        <div class="auto-refresh-info">
            Auto-refresh: <span id="nextRefresh">3s</span>
        </div>
    </div>

</div><!-- /card -->

<script>
    // ── State ──────────────────────────────────────────────────────
    let qrCountdown      = 30
    let refreshCountdown = 3
    let qrHasImage       = false
    let serviceOffline   = false

    // ── DOM refs ────────────────────────────────────────────────────
    const statusDot      = document.getElementById('statusDot')
    const statusText     = document.getElementById('statusText')
    const statusBadge    = document.getElementById('statusBadge')
    const phoneInfo      = document.getElementById('phoneInfo')
    const phoneNumber    = document.getElementById('phoneNumber')
    const qrSection      = document.getElementById('qrSection')
    const qrBox          = document.getElementById('qrBox')
    const qrOverlay      = document.getElementById('qrOverlay')
    const qrTimer        = document.getElementById('qrTimer')
    const countdown      = document.getElementById('countdown')
    const connectedState = document.getElementById('connectedState')
    const offlineState   = document.getElementById('offlineState')
    const nextRefresh    = document.getElementById('nextRefresh')

    // ── Render: Terhubung ───────────────────────────────────────────
    function renderConnected(phone) {
        statusDot.className   = 'status-dot connected'
        statusText.textContent = 'WhatsApp terhubung & siap kirim notifikasi'
        statusBadge.className  = 'status-badge badge-connected'
        statusBadge.textContent = 'Online'

        if (phone) {
            phoneInfo.style.display = 'flex'
            phoneNumber.textContent  = '+' + phone
        }

        qrSection.style.display      = 'none'
        connectedState.style.display = 'flex'
        offlineState.style.display   = 'none'
    }

    // ── Render: Scanning QR ─────────────────────────────────────────
    function renderScanning() {
        statusDot.className   = 'status-dot scanning'
        statusText.textContent = 'Menunggu scan QR...'
        statusBadge.className  = 'status-badge badge-scanning'
        statusBadge.textContent = 'Scan QR'

        phoneInfo.style.display      = 'none'
        qrSection.style.display      = 'block'
        connectedState.style.display = 'none'
        offlineState.style.display   = 'none'
    }

    // ── Render: Service offline ─────────────────────────────────────
    function renderOffline(msg) {
        statusDot.className   = 'status-dot offline'
        statusText.textContent = msg || 'WA Service tidak berjalan'
        statusBadge.className  = 'status-badge badge-offline'
        statusBadge.textContent = 'Offline'

        phoneInfo.style.display      = 'none'
        qrSection.style.display      = 'none'
        connectedState.style.display = 'none'
        offlineState.style.display   = 'flex'
    }

    // ── Fetch Status (dipanggil tiap 3 detik) ─────────────────────
    async function fetchStatus() {
        try {
            const res  = await fetch('<?= site_url('admin/whatsapp/status') ?>')
            const data = await res.json()

            serviceOffline = false

            if (data.connected) {
                renderConnected(data.phone)
                qrHasImage = false
                return
            }

            renderScanning()

            // Jika QR tersedia dan belum ada gambar / countdown habis
            if (data.hasQR) {
                fetchQR()
            }

        } catch (e) {
            serviceOffline = true
            renderOffline('WA Service tidak dapat dijangkau.')
        }
    }

    // ── Fetch QR Image ─────────────────────────────────────────────
    async function fetchQR() {
        try {
            const res  = await fetch('<?= site_url('admin/whatsapp/qr') ?>')
            const data = await res.json()

            if (data.connected) {
                // Ternyata sudah konek saat fetch QR
                fetchStatus()
                return
            }

            if (data.hasQR && data.qrDataUrl) {
                // Tampilkan gambar QR
                qrOverlay.style.display = 'none'

                // Hapus img lama jika ada
                const oldImg = qrBox.querySelector('img')
                if (oldImg) oldImg.remove()

                const img   = document.createElement('img')
                img.src     = data.qrDataUrl
                img.alt     = 'WhatsApp QR Code'
                qrBox.prepend(img)

                qrHasImage = true

                // Tampilkan timer & reset countdown
                qrTimer.style.display = 'flex'
                qrCountdown = 30
            }
        } catch (e) {
            // Jika gagal fetch QR, biarkan spinner tetap muncul
            console.warn('[WA Dashboard] Gagal fetch QR:', e.message)
        }
    }

    // ── Ticker tiap 1 detik ────────────────────────────────────────
    setInterval(() => {
        // Countdown QR (refresh QR tiap 30 detik)
        if (qrHasImage && !serviceOffline) {
            qrCountdown--
            countdown.textContent = qrCountdown
            if (qrCountdown <= 0) {
                qrCountdown = 30
                fetchQR()
            }
        }

        // Countdown auto-refresh status (tiap 3 detik)
        refreshCountdown--
        nextRefresh.textContent = refreshCountdown + 's'
        if (refreshCountdown <= 0) {
            refreshCountdown = 3
            fetchStatus()
        }
    }, 1000)

    // ── Init ────────────────────────────────────────────────────────
    fetchStatus()
</script>

</div><!-- /wa-status-wrapper -->

<?= $this->endSection() ?>
