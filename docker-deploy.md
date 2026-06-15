# Panduan Deployment Tap4Smash dengan Docker

Jika Anda ingin menjalankan Tap4Smash menggunakan **Docker** dan **Docker Compose**, Anda berada di jalur yang tepat! Pendekatan ini jauh lebih bersih dan lebih mudah dipelihara.

Saya telah membuatkan tiga file konfigurasi yang dibutuhkan:
1. `Dockerfile` di folder utama (untuk aplikasi PHP/CodeIgniter 4).
2. `wa-service/Dockerfile` (untuk bot WhatsApp Node.js).
3. `docker-compose.yml` untuk menggabungkan semuanya bersama database MySQL.

---

## Langkah 1: Persiapan Server
Pastikan VPS Ubuntu Anda sudah terinstal **Docker** dan **Docker Compose**. Jika belum, jalankan perintah berikut:

```bash
# Update repository
sudo apt-get update

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose plugin
sudo apt-get install docker-compose-plugin -y
```

## Langkah 2: Upload File Proyek
Upload seluruh folder proyek Anda ke VPS (misalnya ke direktori `/var/www/tap4smash`).
Pastikan struktur foldernya memiliki file `docker-compose.yml` dan `Dockerfile` yang baru saja dibuat.

Masuk ke folder proyek Anda:
```bash
cd /var/www/tap4smash
```

## Langkah 3: Konfigurasi Database di .env
Meskipun Docker Compose akan mengatur banyak hal, Anda tetap harus memastikan kredensial di file `.env` aplikasi Anda merujuk ke kontainer *database*. 

Ubah pengaturan database di file `.env` Anda menjadi seperti ini:
```ini
CI_ENVIRONMENT = production

# --- GANTI SESUAI DOMAIN ATAU IP VPS ANDA ---
app.baseURL = 'http://domain-anda.com/' 

# --- KREDENSIAL DATABASE (Sesuai dengan docker-compose.yml) ---
database.default.hostname = db
database.default.database = tap4smash
database.default.username = tap4smash_user
database.default.password = tap4smash_password
database.default.DBDriver = MySQLi

# --- URL WA SERVICE ---
whatsapp.serviceUrl = http://wa-service:3001
```

> [!WARNING]  
> Jika Anda mengubah _password_ di file `.env`, pastikan Anda juga mengubah baris `MYSQL_PASSWORD` di file `docker-compose.yml` agar keduanya serasi.

## Langkah 4: Build dan Jalankan Kontainer
Jalankan perintah ini untuk membangun image dan menghidupkan seluruh kontainer di background:

```bash
sudo docker compose up -d --build
```
*Tunggu beberapa menit. Docker akan mengunduh image PHP, Node.js, MySQL, serta meng-install dependensinya.*

## Langkah 5: Jalankan Migrasi Database
Karena kita baru saja menyalakan database MySQL yang masih kosong, kita harus menjalankan migrasi dari dalam kontainer PHP:

```bash
# Menjalankan migrasi tabel
sudo docker exec tap4smash_web php spark migrate

# Menambahkan data awal (Seeder)
sudo docker exec tap4smash_web php spark db:seed AdminSeeder
sudo docker exec tap4smash_web php spark db:seed LapanganSeeder
```

## Langkah 6: Scan QR WhatsApp
Kontainer WA Service sudah berjalan di latar belakang. Untuk mengaktifkan bot WhatsApp-nya, Anda harus men-scan QR code.

Buka log dari kontainer WA Service:
```bash
sudo docker logs -f tap4smash_wa
```
QR Code akan muncul di layar terminal Anda. Silakan *scan* melalui WhatsApp di HP Anda (Pilih *Linked Devices*). Jika sudah "Ready", tekan `Ctrl+C` untuk keluar dari layar log.

---
Selesai! Aplikasi **Tap4Smash** sudah berjalan mulus di dalam lingkungan Docker.
Jika Anda perlu memberhentikan sistem, cukup gunakan `sudo docker compose down`.
