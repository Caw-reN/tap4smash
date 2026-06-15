# 🏸 Tap4Smash — WA Service

Service Node.js yang menghubungkan Tap4Smash (CI4) ke WhatsApp menggunakan library **Baileys** (open-source, gratis, tanpa API berbayar).

---

## 📁 Struktur File

```
wa-service/
├── index.js          ← Service utama
├── package.json      ← Dependensi Node.js
├── .env.example      ← Template konfigurasi
├── .env              ← Konfigurasi aktif (tidak di-commit)
├── .gitignore
└── auth_info/        ← Session WA (dibuat otomatis, tidak di-commit)
```

---

## 🚀 Cara Setup (Pertama Kali)

### 1. Install dependensi
```bash
cd wa-service
npm install
```

### 2. Buat file `.env`
```bash
copy .env.example .env
```
Edit `.env` sesuai kebutuhan (API key harus sama dengan di `.env` CI4).

### 3. Jalankan service
```bash
node index.js
```

### 4. Scan QR Code
Saat pertama kali dijalankan, **QR code akan muncul di terminal**. Scan dengan HP kamu:
- Buka WhatsApp → Setelan → Perangkat Tertaut → Tautkan Perangkat

> ⚠️ Gunakan **nomor WA khusus/sekunder** untuk Tap4Smash, bukan nomor pribadi utama.

### 5. Session tersimpan otomatis
Setelah scan, session disimpan di folder `auth_info/`. Kamu **tidak perlu scan ulang** selama folder ini ada.

---

## 📡 Endpoint HTTP

### `GET /status`
Cek apakah WA service sudah terhubung.

**Header:** `X-Api-Key: <API_KEY>`

**Response:**
```json
{ "success": true, "connected": true, "message": "✅ WhatsApp terhubung..." }
```

---

### `POST /send-message`
Kirim pesan WA. Dipanggil otomatis oleh CI4 setelah payment callback.

**Header:** `X-Api-Key: <API_KEY>`, `Content-Type: application/json`

**Body:**
```json
{
  "phone": "628123456789",
  "message": "Halo! Booking kamu berhasil..."
}
```

**Response sukses:**
```json
{ "success": true, "message": "Pesan berhasil dikirim." }
```

---

## 🔧 Menjalankan Bersamaan dengan CI4

Kamu perlu **2 terminal** yang berjalan bersamaan:

| Terminal | Perintah | Keterangan |
|---|---|---|
| Terminal 1 | `cd tap4smash && php spark serve` | CI4 di port 8000 |
| Terminal 2 | `cd tap4smash/wa-service && node index.js` | WA Service di port 3001 |

---

## ❓ Troubleshooting

| Masalah | Solusi |
|---|---|
| QR muncul terus / tidak bisa scan | Hapus folder `auth_info/` dan restart |
| "Logged out!" di terminal | Hapus `auth_info/` dan scan ulang |
| CI4 error "Gagal konek ke WA service" | Pastikan `node index.js` sudah berjalan |
| Pesan tidak terkirim | Cek format nomor: harus `628xxxxxxxxxx` |
