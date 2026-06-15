System Design Documentation - Tap4Smash (system_design.md)
Dokumen ini menjelaskan perancangan sistem, struktur modul MVC, integrasi pihak ketiga, serta implementasi gaya visual sporty pada aplikasi Tap4Smash.

1. Arsitektur Komponen & Pola MVC
Aplikasi ini menggunakan arsitektur Monolitik Tradisional di mana seluruh alur logika, validasi, dan rendering UI terjadi di dalam server lokal (Server-Side Rendering).

   [ Web Browser (User) ]
      │             ▲
(HTTP Request)  (Rendered HTML + Tailwind)
      ▼             │
 ┌────────────────────────────────────────┐
 │ CodeIgniter 4 Engine                   │
 │                                        │
 │  ┌────────────┐      ┌──────────────┐  │
 │  │ Controller │ ───► │ View (.php)  │  │
 │  └────────────┘      └──────────────┘  │
 │        │                    ▲          │
 │   (Data Array)         (Data Array)    │
 │        ▼                    │          │
 │  ┌────────────┐                    │  │
 │  │   Model    │ ───────────────────┘  │
 │  └────────────┘                        │
 └───────┬────────────────────────────────┘
         ▼
 ┌──────────────┐      ┌─────────────┐      ┌─────────────────┐
 │ MySQL Database│      │  PaymentKu  │      │ WhatsApp Gateway│
 └──────────────┘      └─────────────┘      └─────────────────┘
A. Komponen Backend (CI4)
Core Routing: Mengamankan rute admin /admin/* menggunakan AdminFilter berbasis native PHP session.

Data Layer (Models): BookingModel menangani transaksi, pencatatan timestamp, dan kalkulasi sisa tagihan (DP 50%).

Integration Layer: Mengelola jabat tangan (handshake) aman dengan Webhook PaymentKu dan pengiriman payload teks ke API WhatsApp Gateway.

B. Komponen Frontend (Tailwind CSS)
Utility-First Compilation: Menghasilkan berkas public/css/style.css yang telah diminifikasi melalui pemindaian berkas View PHP secara berkala.

2. Implementasi Desain Visual "Sporty" & UI Engine
Untuk menghidupkan atmosfer olahraga (badminton) yang agresif, cepat, dan dinamis, aturan CSS dan Tailwind berikut harus diterapkan secara konsisten pada elemen HTML:

A. Tipografi Dinamis (Sleek & Aggressive)
Kesan Kecepatan (Speed & Motion): Gunakan utilitas italic pada heading besar untuk memberikan impresi gerakan/pukulan smash.

Kekuatan (Power): Gunakan font font-heading (Montserrat) dengan ketebalan ekstrem font-black (900) dan paksa menjadi huruf besar (uppercase).

B. Pola Komponen Visual Sporty
Sudut Kaku (Sharp Edges): Hindari lingkaran membulat lembut ala aplikasi media sosial (seperti rounded-xl atau rounded-2xl). Gunakan rounded-sm (2px) atau rounded-none pada tombol dan kartu untuk memberikan kesan kaku, kokoh, dan profesional khas arena olahraga.

Kontras Tinggi & Efek Cahaya (High-Contrast Glow): Di atas latar belakang gelap bg-charcoal, gunakan efek glow neon pada elemen aktif atau tombol CTA untuk mensimulasikan lampu sorot lapangan indoor.

3. Spesifikasi Komponen UI Utama (View Level)
A. Komponen Hero Section (Landing Page)
Komponen pembuka yang langsung membakar semangat pengguna saat membuka Tap4Smash.

PHP
<section class="relative min-h-[80vh] bg-charcoal flex items-center overflow-hidden">
    <div class="absolute inset-0 bg-[url('/img/court-bg.jpg')] bg-cover bg-center opacity-30 skew-y-3 scale-110"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-charcoal via-charcoal/80 to-transparent"></div>

    <div class="relative max-w-5xl mx-auto px-6 z-10">
        <span class="inline-block bg-volt text-charcoal font-accent text-sm font-bold px-3 py-1 uppercase tracking-widest mb-4 skew-x-12">
            Arena Badminton Premium
        </span>
        <h1 class="text-5xl md:text-7xl font-heading font-black uppercase tracking-tight italic text-white leading-none mb-4">
            TAP, BOOK, <br><span class="text-volt">AND SMASH!</span>
        </h1>
        <p class="font-body text-textMuted max-w-md text-sm md:text-base mb-8">
            Amankan slot lapangan pilihanmu dalam hitungan detik. Tanpa ribet, otomatis terverifikasi dengan WhatsApp e-tiket.
        </p>
        <a href="/booking" class="inline-block bg-volt text-charcoal font-accent text-xl font-bold tracking-wider uppercase px-8 py-3 rounded-sm transform transition-all hover:scale-105 hover:shadow-neon duration-150">
            Pesan Lapangan Sekarang
        </a>
    </div>
</section>
B. Komponen Pilihan DP / Lunas (Checkout Page)
Komponen kartu interaktif yang memanfaatkan utilitas border Tailwind untuk menandakan pilihan finansial pengguna.

PHP
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <label class="group relative bg-slateDark border-2 border-gray-800 p-5 rounded-sm cursor-pointer transition-all hover:border-volt/50 has-[:checked]:border-volt has-[:checked]:shadow-neon">
        <input type="radio" name="skema_pembayaran" value="dp" class="sr-only">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-heading font-bold text-lg text-white uppercase italic group-hover:text-volt transition-colors">Uang Muka (DP 50%)</h3>
                <p class="font-body text-xs text-textMuted mt-1">Bayar setengah sekarang melalui PaymentKu, lunasi sisanya secara tunai di lokasi.</p>
            </div>
            <span class="font-accent text-2xl font-bold text-volt">50%</span>
        </div>
    </label>

    <label class="group relative bg-slateDark border-2 border-gray-800 p-5 rounded-sm cursor-pointer transition-all hover:border-volt/50 has-[:checked]:border-volt has-[:checked]:shadow-neon">
        <input type="radio" name="skema_pembayaran" value="full" checked class="sr-only">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-heading font-bold text-lg text-white uppercase italic group-hover:text-volt transition-colors">Bayar Lunas (100%)</h3>
                <p class="font-body text-xs text-textMuted mt-1">Selesaikan seluruh pembayaran hari ini. Datang ke GOR langsung main tanpa antre kasir.</p>
            </div>
            <span class="font-accent text-2xl font-bold text-white">FULL</span>
        </div>
    </label>
</div>
4. Alur Integrasi Gateway (Webhook & Notification Architecture)
Inisiasi Transaksi: Pengguna menekan tombol bayar ➔ Controller meminta token session ke API PaymentKu dengan nominal sesuai skema (DP/Full).

Sinyal Webhook: Ketika pengguna menyelesaikan pembayaran di aplikasi e-wallet/bank mereka, server PaymentKu mengirimkan HTTP POST request (Webhook) ke URL Tap4Smash /payment/callback.

Eksekusi Handler: Controller menerima callback ➔ Memvalidasi Signature MD5/SHA256 ➔ Mengubah status tabel bookings menjadi success ➔ Memanggil fungsi internal WhatsAppHelper::sendTicket() untuk menembakkan payload teks e-tiket ke nomor pelanggan secara instan.

Buat implementasi kode Controller untuk menangani callback webhook dari PaymentKu di CI4

Buat skrip migrasi database CodeIgniter 4 untuk tabel bookings sesuai PRD terbaru

Buat contoh file seeder database untuk data lapangan awal Tap4Smash