<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Lapangan — Tap4Smash</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,700;0,900;1,900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url('css/user.css') ?>">

    <style>
    .section { max-width: 1100px; }

    /* Centered Date Strip */
    .date-strip-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-bottom: 1.5rem;
    }
    .date-strip {
        display: flex;
        gap: .75rem;
        overflow-x: auto;
        padding-bottom: .25rem;
        scrollbar-width: none; /* hide scrollbar for cleaner look if centered */
    }
    .date-strip::-webkit-scrollbar { display: none; }
    
    .date-pill {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .2rem;
        padding: .75rem 1rem;
        border: 2px solid var(--border);
        border-radius: 12px;
        background: var(--charcoal);
        cursor: pointer;
        transition: all .2s;
        min-width: 70px;
        user-select: none;
    }
    .date-pill:hover:not(.active) { border-color: var(--volt-border); }
    .date-pill.active {
        background: var(--volt);
        border-color: var(--volt);
        transform: scale(1.05);
    }
    .date-pill .day-name {
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--text-muted);
    }
    .date-pill.active .day-name { color: rgba(0,0,0,.65); }
    .date-pill .day-num {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: 1.5rem;
        line-height: 1;
        color: var(--text);
    }
    .date-pill.active .day-num { color: #000; }
    /* TODAY label: putih biasa, baru volt saat active */
    .date-pill.today-pill .day-name { color: var(--text-muted); }
    .date-pill.today-pill.active .day-name { color: rgba(0,0,0,.65); }

    /* Kotak Pilih Bulan Ini */
    .month-picker-pill {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        padding: .75rem 1rem;
        border: 2px dashed var(--volt-border);
        border-radius: 12px;
        background: var(--volt-dim, rgba(204,255,0,.07));
        cursor: pointer;
        transition: all .2s;
        min-width: 70px;
        user-select: none;
        color: var(--volt);
    }
    .month-picker-pill:hover {
        background: rgba(204,255,0,.15);
        border-color: var(--volt);
        transform: scale(1.05);
    }
    .month-picker-pill .mp-icon { font-size: 1.3rem; }
    .month-picker-pill .mp-label { font-size: .6rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }

    /* Modal Kalender */
    .cal-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.75);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        animation: fadeIn .2s;
    }
    .cal-modal-overlay.open { display: flex; }
    .cal-modal {
        background: var(--surface2, #1a1f2e);
        border: 1px solid var(--border, #2a2f3e);
        border-radius: 16px;
        padding: 1.5rem;
        width: min(420px, 94vw);
        box-shadow: 0 20px 60px rgba(0,0,0,.6);
        animation: slideUp .25s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .cal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }
    .cal-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 900;
        font-size: 1.1rem;
        text-transform: uppercase;
        color: var(--volt);
    }
    .cal-close {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 1.2rem;
        cursor: pointer;
        transition: color .2s;
        padding: .3rem;
    }
    .cal-close:hover { color: var(--text); }
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: .35rem;
    }
    .cal-day-label {
        text-align: center;
        font-size: .65rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .08em;
        padding-bottom: .4rem;
    }
    .cal-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-weight: 700;
        font-size: .85rem;
        cursor: pointer;
        transition: all .15s;
        border: 1px solid transparent;
        color: var(--text);
    }
    .cal-day:hover:not(.cal-day-past):not(.cal-day-empty) {
        background: rgba(204,255,0,.15);
        border-color: var(--volt-border);
        color: var(--volt);
    }
    .cal-day.cal-day-today {
        border-color: var(--volt-border);
        color: var(--volt);
    }
    .cal-day.cal-day-selected {
        background: var(--volt);
        color: #000;
        border-color: var(--volt);
    }
    .cal-day.cal-day-past {
        opacity: .25;
        cursor: not-allowed;
    }
    .cal-day.cal-day-empty { cursor: default; }

    /* 2-column Court Grid */
    .court-card-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    @media (max-width: 500px) {
        .court-card-grid { grid-template-columns: 1fr; }
    }
    
    .court-card {
        background: var(--surface2);
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all .2s;
        text-align: center;
    }
    .court-card:hover {
        border-color: var(--volt-border);
        background: var(--volt-dim);
    }
    .court-card.active {
        background: var(--volt);
        border-color: var(--volt);
        color: #000;
        box-shadow: 0 0 20px rgba(204,255,0,.3);
    }
    .court-card.active .court-type,
    .court-card.active .court-price { color: rgba(0,0,0,0.7); }
    .court-name {
        font-weight: 900;
        font-family: 'Montserrat', sans-serif;
        font-size: 1.2rem;
        margin-bottom: .25rem;
    }
    .court-type {
        font-size: .8rem;
        color: var(--text-muted);
        margin-bottom: .5rem;
    }
    .court-price {
        font-size: 1rem;
        font-weight: 700;
        color: var(--volt);
    }
    .court-card.active .court-price { color: #000; }

    /* Slot Card Grid */
    .slot-card-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .75rem;
    }
    @media (max-width: 768px) { .slot-card-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 480px) { .slot-card-grid { grid-template-columns: repeat(2, 1fr); } }

    .slot-card {
        background: var(--surface2);
        border: 2px solid var(--border);
        border-radius: 10px;
        padding: .8rem;
        cursor: pointer;
        transition: all .2s;
        position: relative;
        overflow: hidden;
        user-select: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .slot-card:hover:not(.slot-taken) {
        border-color: var(--volt-border);
        background: var(--volt-dim);
    }
    .slot-card.slot-taken {
        opacity: .4;
        cursor: not-allowed;
        background: rgba(0,0,0,.3);
    }
    .slot-card.slot-taken .slot-time { text-decoration: line-through; }
    .slot-card.slot-taken::after {
        content: 'RESERVED';
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Montserrat', sans-serif;
        font-size: .6rem;
        font-weight: 900;
        letter-spacing: .15em;
        color: var(--text-muted);
        background: rgba(0,0,0,.6);
    }
    
    /* Checkbox selected style */
    .slot-card.slot-selected {
        background: var(--volt);
        border-color: var(--volt);
        color: #000;
        box-shadow: 0 0 15px rgba(204,255,0,.3);
    }
    .slot-card.slot-selected .slot-time { color: #000; }
    .slot-card.slot-selected .slot-type-label { color: rgba(0,0,0,.7); }

    .slot-time {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800;
        font-size: .82rem;
        color: var(--text);
        margin-bottom: .2rem;
        white-space: nowrap;
    }
    .slot-type-label {
        font-size: .65rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* Wizard Steps */
    .wizard-steps {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        background: var(--surface2);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--border);
    }
    .wizard-step {
        display: flex;
        align-items: center;
        gap: .5rem;
        color: var(--text-muted);
        transition: all 0.3s;
        opacity: 0.5;
    }
    .wizard-step.active {
        color: var(--volt);
        opacity: 1;
    }
    .wizard-step .step-num {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: var(--charcoal);
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: .85rem;
    }
    .wizard-step.active .step-num {
        background: var(--volt);
        color: #000;
        border-color: var(--volt);
    }
    .wizard-step .step-text { font-size: .85rem; font-weight: 700; }
    .wizard-line {
        flex: 1; height: 2px;
        background: var(--border);
        margin: 0 1rem;
    }
    @media (max-width: 600px) {
        .wizard-steps { padding: .75rem; }
        .wizard-step .step-text { display: none; }
        .wizard-line { margin: 0 .5rem; }
    }

    .wizard-content { display: none; animation: fadeIn 0.3s ease-in-out; }
    .wizard-content.active { display: block; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Buttons */
    .btn-step {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .85rem 1.75rem;
        border-radius: 8px;
        font-weight: 800;
        font-size: .95rem;
        cursor: pointer;
        transition: all .2s;
        border: none;
    }
    .btn-next { background: var(--volt); color: #000; }
    .btn-next:hover:not(:disabled) { background: #b3e600; transform: translateY(-2px); }
    .btn-next:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
    .btn-prev { background: var(--charcoal); color: var(--text); border: 1px solid var(--border); }
    .btn-prev:hover { background: var(--surface2); border-color: var(--text-muted); }

    /* Review Card styling */
    .review-wrapper {
        max-width: 600px;
        margin: 0 auto;
    }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <span class="brand-icon"><i class="fa-solid fa-table-tennis-paddle-ball"></i></span>
        <h1><a href="<?= site_url('/') ?>" style="color:inherit;">Tap4Smash <span>GOR Sport Center</span></a></h1>
    </div>
</nav>

<div class="section" style="padding-top:2rem;">
    <div style="margin-bottom:2rem; text-align: center;">
        <h2 style="font-family:'Montserrat',sans-serif;font-weight:900;font-size:1.8rem;text-transform:uppercase; color:var(--volt);">
            Booking Lapangan
        </h2>
        <p style="color:var(--text-muted);font-size:.9rem;margin-top:.5rem;">
            Ikuti langkah di bawah untuk mengamankan jadwal mainmu.
        </p>
    </div>

    <?php if (session()->getFlashdata('errors') || ! empty($errors)): ?>
    <?php $errs = session()->getFlashdata('errors') ?? $errors; ?>
    <div class="alert alert-error" style="margin-bottom:1.5rem; max-width:800px; margin-left:auto; margin-right:auto;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <strong>Terdapat kesalahan:</strong>
            <ul style="margin:.3rem 0 0 1rem;font-size:.85rem;">
                <?php foreach ((array)$errs as $e): ?>
                <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('booking/proses') ?>" id="bookingForm" style="max-width: 900px; margin: 0 auto;">
        <?= csrf_field() ?>

        <div class="wizard-steps">
            <div class="wizard-step active" id="indicator-step1">
                <div class="step-num">1</div>
                <div class="step-text">Jadwal & Lapangan</div>
            </div>
            <div class="wizard-line"></div>
            <div class="wizard-step" id="indicator-step2">
                <div class="step-num">2</div>
                <div class="step-text">Jam Main</div>
            </div>
            <div class="wizard-line"></div>
            <div class="wizard-step" id="indicator-step3">
                <div class="step-num">3</div>
                <div class="step-text">Detail & Bayar</div>
            </div>
            <div class="wizard-line"></div>
            <div class="wizard-step" id="indicator-step4">
                <div class="step-num">4</div>
                <div class="step-text">Review</div>
            </div>
        </div>

        <!-- ═════ STEP 1 ═════ -->
        <div class="wizard-content active" id="step1">
            <div class="form-card" style="margin-bottom:1.5rem;">
                <div class="form-card-header" style="justify-content:center; text-align:center;">
                    <h2>Pilih Tanggal Main</h2>
                </div>
                <div class="form-body">
                    <div class="date-strip-wrapper">
                        <div class="date-strip" id="dateStrip">
                            <?php
                            $days = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
                            $today = date('Y-m-d');
                            for ($i = 0; $i < 7; $i++):
                                $ts    = strtotime("+{$i} days");
                                $ymd   = date('Y-m-d', $ts);
                                $dayNm = $days[date('w', $ts)];
                                $dayN  = date('d', $ts);
                                $isToday = ($ymd === $today);
                            ?>
                            <div class="date-pill <?= $isToday ? 'today-pill' : '' ?>"
                                 data-date="<?= $ymd ?>"
                                 id="date-<?= $ymd ?>"
                                 onclick="selectDate('<?= $ymd ?>')">
                                <span class="day-name"><?= $isToday ? 'TODAY' : $dayNm ?></span>
                                <span class="day-num"><?= $dayN ?></span>
                            </div>
                            <?php endfor; ?>
                            <!-- Kotak Pilih Bulan Ini -->
                            <div class="month-picker-pill" onclick="openCalModal()" title="Pilih tanggal lain">
                                <span class="mp-icon"><i class="fa-regular fa-calendar-days"></i></span>
                                <span class="mp-label">Tanggal Lain</span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="tanggal_main" id="tanggal_main" value="<?= old('tanggal_main') ?>">
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header" style="justify-content:center; text-align:center;">
                    <h2>Pilih Lapangan</h2>
                </div>
                <div class="form-body">
                    <input type="hidden" name="lapangan_id" id="lapangan_id" value="<?= old('lapangan_id', $lapangan_id ?? '') ?>">
                    <div class="court-card-grid">
                        <?php foreach ($lapangans as $l): ?>
                        <div class="court-card <?= (old('lapangan_id', $lapangan_id ?? '') == $l['id']) ? 'active' : '' ?>"
                             id="court-card-<?= $l['id'] ?>"
                             data-id="<?= $l['id'] ?>"
                             data-harga="<?= $l['harga_per_jam'] ?>"
                             data-nama="<?= esc($l['nama_lapangan']) ?>"
                             onclick="selectCourt(this)">
                            <div class="court-name"><?= esc($l['nama_lapangan']) ?></div>
                            <div class="court-type"><?= esc($l['jenis_lapangan'] ?? 'Reguler') ?></div>
                            <div class="court-price">Rp <?= number_format($l['harga_per_jam'], 0, ',', '.') ?>/jam</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-top:2rem; text-align:center;">
                        <button type="button" class="btn-step btn-next" onclick="goToStep(2)" id="btnNext1" disabled>
                            Lanjut Pilih Jam <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════ STEP 2 ═════ -->
        <div class="wizard-content" id="step2">
            <div class="form-card">
                <div class="form-card-header" style="justify-content:center;">
                    <h2 id="step2-title">Pilih Jam Main (—)</h2>
                </div>
                <div class="form-body">
                    <div style="text-align:center; margin-bottom:1.5rem; color:var(--text-muted); font-size:.9rem;">
                        Anda dapat memilih lebih dari satu jam permainan.
                    </div>

                    <div class="slot-card-grid" id="slotGrid">
                        <div class="slot-empty" id="slotEmptyMsg" style="grid-column: 1/-1;">
                            <i class="fa-solid fa-spinner fa-spin"></i> Memuat slot...
                        </div>
                    </div>

                    <input type="hidden" name="jam_main" id="jam_main" value="<?= old('jam_main') ?>">
                    
                    <div style="margin-top:2rem; display:flex; justify-content:space-between;">
                        <button type="button" class="btn-step btn-prev" onclick="goToStep(1)"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                        <button type="button" class="btn-step btn-next" onclick="goToStep(3)" id="btnNext2" disabled>Lanjut Detail <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════ STEP 3 ═════ -->
        <div class="wizard-content" id="step3">
            <div class="form-card" style="margin-bottom:1.5rem;">
                <div class="form-card-header">
                    <h2>Data Pemesan</h2>
                </div>
                <div class="form-body">
                    <div class="form-group">
                        <label for="nama_pemesan">Nama Lengkap <span style="color:var(--red)">*</span></label>
                        <input type="text" id="nama_pemesan" name="nama_pemesan" value="<?= old('nama_pemesan') ?>" placeholder="Masukkan nama lengkap kamu" required>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="nomor_wa">Nomor WhatsApp <span style="color:var(--red)">*</span></label>
                        <input type="tel" id="nomor_wa" name="nomor_wa" value="<?= old('nomor_wa') ?>" placeholder="08123456789" required>
                        <div class="hint">Nomor ini akan digunakan untuk pengiriman e-tiket via WhatsApp.</div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header">
                    <h2>Skema Pembayaran</h2>
                </div>
                <div class="form-body">
                    <div class="skema-options">
                        <div class="skema-opt">
                            <input type="radio" name="skema_pembayaran" id="skema_dp" value="dp" <?= (old('skema_pembayaran', 'dp') === 'dp') ? 'checked' : '' ?>>
                            <label for="skema_dp">
                                <strong><i class="fa-solid fa-percent"></i> DP 50%</strong>
                                Bayar setengah dulu<br>
                                <span style="font-size:.75rem;opacity:.7;">Sisa bayar di kasir GOR</span>
                            </label>
                        </div>
                        <div class="skema-opt">
                            <input type="radio" name="skema_pembayaran" id="skema_full" value="full" <?= (old('skema_pembayaran') === 'full') ? 'checked' : '' ?>>
                            <label for="skema_full">
                                <strong><i class="fa-solid fa-check-double"></i> Full Payment</strong>
                                Bayar lunas sekarang<br>
                                <span style="font-size:.75rem;opacity:.7;">Langsung dikonfirmasi</span>
                            </label>
                        </div>
                    </div>
                    
                    <div style="margin-top:2rem; display:flex; justify-content:space-between;">
                        <button type="button" class="btn-step btn-prev" onclick="goToStep(2)"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                        <button type="button" class="btn-step btn-next" onclick="goToStep(4)" id="btnNext3" disabled>Lanjut Review <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════ STEP 4 ═════ -->
        <div class="wizard-content" id="step4">
            <div class="review-wrapper">
                <div class="summary-card" style="position:static; margin-bottom:1.5rem;">
                    <div class="summary-header" style="justify-content:center; font-size:1.1rem;">
                        <i class="fa-solid fa-receipt" style="color:var(--volt);"></i> Ringkasan Pesanan
                    </div>
                    <div class="summary-body" style="padding: 1.5rem;">
                        <div class="summary-row">
                            <span class="lbl">Lapangan</span>
                            <span class="val" id="sum-lapangan" style="font-weight:800;">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="lbl">Tanggal</span>
                            <span class="val" id="sum-tanggal">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="lbl">Jam Main</span>
                            <span class="val" id="sum-jam" style="color:var(--volt); font-weight:700;">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="lbl">Durasi</span>
                            <span class="val" id="sum-durasi">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="lbl">Harga / Jam</span>
                            <span class="val" id="sum-harga-jam">—</span>
                        </div>
                        <hr class="summary-divider">
                        <div class="summary-row" style="margin-bottom:.5rem;">
                            <span class="lbl">Total Harga</span>
                            <span class="val" id="sum-total" style="font-size:1.1rem; font-weight:800;">—</span>
                        </div>
                        <div class="summary-row" id="sum-sisa-row" style="font-size:.85rem;margin-bottom:0;display:none;">
                            <span class="lbl">Sisa di Kasir (Nanti)</span>
                            <span class="val" id="sum-sisa" style="color:var(--yellow); font-weight:700;">—</span>
                        </div>
                        <hr class="summary-divider">
                        <div class="summary-total" style="margin-top:1rem; padding-top:1rem; border-top: 2px dashed var(--border);">
                            <span class="lbl" style="font-size:1rem;">Bayar Sekarang</span>
                            <span class="val" id="sum-bayar" style="font-size:1.5rem;">Rp 0</span>
                        </div>
                        <div style="margin-top:1.5rem;padding:1rem;background:var(--charcoal);border-radius:8px;font-size:.8rem;color:var(--text-muted);text-align:center;">
                            <i class="fa-solid fa-bolt" style="color:var(--yellow);margin-right:.3rem;"></i>
                            Segera selesaikan pembayaran. Slot akan dikunci selama <strong style="color:var(--yellow);">15 menit</strong>.
                        </div>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <button type="button" class="btn-step btn-prev" onclick="goToStep(3)"><i class="fa-solid fa-arrow-left"></i> Edit Data</button>
                    <button type="submit" class="btn-step btn-next" style="font-size:1.1rem; padding: 1rem 2rem; background:var(--volt); color:#000;">
                        <i class="fa-solid fa-qrcode"></i> Lanjut Pembayaran
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- ═══ MODAL KALENDER BULAN INI ═══ -->
<div class="cal-modal-overlay" id="calModal" onclick="handleModalOverlayClick(event)">
    <div class="cal-modal" id="calModalBox">
        <div class="cal-header">
            <span class="cal-title" id="calModalTitle">Pilih Tanggal</span>
            <button class="cal-close" onclick="closeCalModal()" title="Tutup"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="cal-grid" id="calGrid"></div>
    </div>
</div>

<footer class="footer">
    <p><strong style="color:var(--volt);">Tap4Smash</strong> GOR Sport Center &copy; <?= date('Y') ?></p>
</footer>

<script>
const SLOTS_API  = '<?= site_url('api/slots') ?>';
const SLOT_HOURS = [6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21];

let currentStep   = 1;
let selectedDate  = '';
let lapanganNama  = '';
let hargaPerJam   = 0;
let takenSlots    = [];
let selectedSlots = []; // Array of integers

function pad(n) { return String(n).padStart(2, '0'); }
function formatRp(n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); }
function formatDateLong(ymd) {
    if (!ymd) return '—';
    return new Date(ymd + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
}

function formatJamMain(slotsArr) {
    if (!slotsArr || slotsArr.length === 0) return '—';
    let arr = [...slotsArr].sort((a,b) => a-b);
    let ranges = [];
    let start = arr[0];
    let prev = start;

    for (let i = 1; i < arr.length; i++) {
        if (arr[i] === prev + 1) {
            prev = arr[i];
        } else {
            ranges.push(`${pad(start)}:00 - ${pad(prev + 1)}:00`);
            start = arr[i];
            prev = start;
        }
    }
    ranges.push(`${pad(start)}:00 - ${pad(prev + 1)}:00`);
    return ranges.join(', ');
}

// ── WIZARD ──
function goToStep(step) {
    document.querySelectorAll('.wizard-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.wizard-step').forEach(el => {
        el.classList.remove('active');
        el.style.opacity = '0.5';
    });
    
    document.getElementById('step' + step).classList.add('active');
    
    for (let i = 1; i <= step; i++) {
        let ind = document.getElementById('indicator-step' + i);
        ind.classList.add('active');
        ind.style.opacity = '1';
    }
    
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    updateUI();
}

// ── SELECTION ──
function selectDate(ymd) {
    selectedDate = ymd;
    document.getElementById('tanggal_main').value = ymd;
    document.querySelectorAll('.date-pill').forEach(p => p.classList.remove('active'));
    document.getElementById('date-' + ymd).classList.add('active');
    
    // Clear slots
    selectedSlots = [];
    document.getElementById('jam_main').value = '';
    
    fetchSlots();
    updateUI();
}

function selectCourt(cardEl) {
    document.querySelectorAll('.court-card').forEach(c => c.classList.remove('active'));
    cardEl.classList.add('active');
    
    document.getElementById('lapangan_id').value = cardEl.dataset.id;
    hargaPerJam  = parseFloat(cardEl.dataset.harga) || 0;
    lapanganNama = cardEl.dataset.nama || '';
    
    selectedSlots = [];
    document.getElementById('jam_main').value = '';
    
    if (selectedDate) fetchSlots();
    updateUI();
}

async function fetchSlots() {
    const lapId = document.getElementById('lapangan_id').value;
    if (!lapId || !selectedDate) return;

    const grid = document.getElementById('slotGrid');
    grid.innerHTML = `<div class="slot-empty" style="grid-column:1/-1;"><i class="fa-solid fa-spinner fa-spin"></i> Memuat slot...</div>`;

    try {
        const res  = await fetch(`${SLOTS_API}?lapangan_id=${lapId}&tanggal=${selectedDate}`);
        const data = await res.json();
        takenSlots = data.slots || [];
    } catch (e) { takenSlots = []; }

    renderSlotGrid();
}

function renderSlotGrid() {
    const grid = document.getElementById('slotGrid');
    grid.innerHTML = '';
    
    document.getElementById('step2-title').textContent = `Pilih Jam Main (${formatDateLong(selectedDate)})`;

    SLOT_HOURS.forEach(h => {
        const taken = takenSlots.includes(h);
        const sel   = selectedSlots.includes(h);

        let classes = 'slot-card';
        if (taken) classes += ' slot-taken';
        if (sel)   classes += ' slot-selected';

        const time = `${pad(h)}:00 - ${pad(h + 1)}:00`;

        const card = document.createElement('div');
        card.className = classes;
        card.innerHTML = `
            <span class="slot-time">${time}</span>
            <div class="slot-type-label">Standard</div>
        `;

        if (!taken) {
            card.addEventListener('click', () => toggleSlot(h));
        }

        grid.appendChild(card);
    });
}

function toggleSlot(h) {
    if (takenSlots.includes(h)) return;
    
    const idx = selectedSlots.indexOf(h);
    if (idx > -1) {
        selectedSlots.splice(idx, 1);
    } else {
        selectedSlots.push(h);
    }
    
    selectedSlots.sort((a,b) => a-b);
    document.getElementById('jam_main').value = selectedSlots.join(',');
    
    renderSlotGrid();
    updateUI();
}

// ── UPDATE SUMMARY & BUTTONS ──
function updateUI() {
    const lapId = document.getElementById('lapangan_id').value;
    
    // Step 1 btn
    document.getElementById('btnNext1').disabled = !(selectedDate && lapId);
    
    // Step 2 btn
    document.getElementById('btnNext2').disabled = (selectedSlots.length === 0);
    
    // Step 3 btn
    const nama = document.getElementById('nama_pemesan').value.trim();
    const wa   = document.getElementById('nomor_wa').value.trim();
    document.getElementById('btnNext3').disabled = !(nama.length > 2 && wa.length > 7);

    // Update Summary in Step 4
    if (lapId) {
        if (!lapanganNama) {
            const activeCard = document.querySelector('.court-card.active');
            if (activeCard) {
                lapanganNama = activeCard.dataset.nama;
                hargaPerJam = parseFloat(activeCard.dataset.harga) || 0;
            }
        }
    }
    
    document.getElementById('sum-lapangan').textContent = lapanganNama || '—';
    document.getElementById('sum-tanggal').textContent  = selectedDate ? formatDateLong(selectedDate) : '—';
    document.getElementById('sum-harga-jam').textContent = formatRp(hargaPerJam) + ' / jam';
    
    if (selectedSlots.length > 0) {
        document.getElementById('sum-jam').textContent    = formatJamMain(selectedSlots);
        document.getElementById('sum-durasi').textContent = selectedSlots.length + ' Jam';
        
        const total = hargaPerJam * selectedSlots.length;
        const skema = document.querySelector('input[name="skema_pembayaran"]:checked')?.value || 'dp';
        const bayar = skema === 'dp' ? total * 0.5 : total;
        const sisa  = total - bayar;
        
        document.getElementById('sum-total').textContent = formatRp(total);
        document.getElementById('sum-bayar').textContent = formatRp(bayar);
        
        if (skema === 'dp') {
            document.getElementById('sum-sisa-row').style.display = 'flex';
            document.getElementById('sum-sisa').textContent       = formatRp(sisa);
        } else {
            document.getElementById('sum-sisa-row').style.display = 'none';
        }
    } else {
        document.getElementById('sum-jam').textContent    = '—';
        document.getElementById('sum-durasi').textContent = '—';
        document.getElementById('sum-total').textContent  = '—';
        document.getElementById('sum-bayar').textContent  = 'Rp 0';
        document.getElementById('sum-sisa-row').style.display = 'none';
    }
}

document.getElementById('nama_pemesan').addEventListener('input', updateUI);
document.getElementById('nomor_wa').addEventListener('input', updateUI);
document.querySelectorAll('input[name="skema_pembayaran"]').forEach(el => el.addEventListener('change', updateUI));

window.addEventListener('DOMContentLoaded', () => {
    const today = '<?= date('Y-m-d') ?>';
    
    // Restore
    const oldDate = document.getElementById('tanggal_main').value;
    if (oldDate) selectDate(oldDate); else selectDate(today);

    const oldLap = document.getElementById('lapangan_id').value;
    if (oldLap) {
        const card = document.getElementById('court-card-' + oldLap);
        if (card) selectCourt(card);
    }
    
    const oldJam = document.getElementById('jam_main').value;
    if (oldJam) {
        selectedSlots = oldJam.split(',').map(s => parseInt(s.trim())).filter(s => !isNaN(s));
        selectedSlots.sort((a,b) => a-b);
        if (document.querySelector('.alert-error')) {
            goToStep(3); // or 4
        } else {
            goToStep(1);
        }
    } else {
        goToStep(1);
    }
});

// ── MODAL KALENDER BULAN INI ────────────────────────────────────────────────
function openCalModal() {
    const now   = new Date();
    const year  = now.getFullYear();
    const month = now.getMonth(); // 0-indexed
    renderCalModal(year, month);
    document.getElementById('calModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeCalModal() {
    document.getElementById('calModal').classList.remove('open');
    document.body.style.overflow = '';
}

function handleModalOverlayClick(e) {
    if (e.target === document.getElementById('calModal')) closeCalModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCalModal(); });

function renderCalModal(year, month) {
    const DAYS   = ['MIN','SEN','SEL','RAB','KAM','JUM','SAB'];
    const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    document.getElementById('calModalTitle').textContent = `${MONTHS[month]} ${year}`;

    const grid  = document.getElementById('calGrid');
    grid.innerHTML = '';

    // Header hari
    DAYS.forEach(d => {
        const el = document.createElement('div');
        el.className = 'cal-day-label';
        el.textContent = d;
        grid.appendChild(el);
    });

    const todayYmd = new Date().toISOString().slice(0,10);
    const firstDay = new Date(year, month, 1).getDay(); // 0=Sun
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Sel kosong awal
    for (let i = 0; i < firstDay; i++) {
        const el = document.createElement('div');
        el.className = 'cal-day cal-day-empty';
        grid.appendChild(el);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const mm  = String(month + 1).padStart(2,'0');
        const dd  = String(d).padStart(2,'0');
        const ymd = `${year}-${mm}-${dd}`;

        const isToday = (ymd === todayYmd);
        const isPast  = (ymd < todayYmd);
        const isSel   = (ymd === selectedDate);

        const el = document.createElement('div');
        el.className = 'cal-day';
        if (isPast)   el.classList.add('cal-day-past');
        if (isToday)  el.classList.add('cal-day-today');
        if (isSel)    el.classList.add('cal-day-selected');
        el.textContent = d;

        if (!isPast) {
            el.addEventListener('click', () => {
                selectDate(ymd);
                closeCalModal();
            });
        }
        grid.appendChild(el);
    }
}
</script>

</body>
</html>
