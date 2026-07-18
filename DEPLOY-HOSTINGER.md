# Panduan Deploy ke Hostinger (hPanel + SSH)

Aplikasi: **InPULS KEMENKES** — Laravel 12 + Inertia + React.
Metode: upload paket build, lalu setup via SSH. Karena Anda punya **akses SSH**, `vendor/` **tidak** perlu diupload (dijalankan `composer install` di server).

> Prasyarat: plan Hostinger yang mendukung **SSH** (Premium/Business), **PHP 8.2+**, dan **MySQL**.

---

## 0. Yang sudah disiapkan
- Aset frontend sudah di-build (`public/build/`) dan **ikut** di dalam arsip.
- Arsip deploy: **`labkesmas-deploy.tar.gz`** (di Desktop Anda) — sudah tanpa `node_modules`, `vendor`, `.git`, `.env`, dan file SQLite lokal.
- Template env: **`.env.production.example`** (ikut di arsip).

---

## 1. Buat Database MySQL (hPanel)
1. hPanel → **Databases → MySQL Databases**.
2. Buat **database baru** + **user baru**, beri user itu **All Privileges** ke database tersebut.
3. Catat: nama database, username, password, dan host (biasanya `127.0.0.1` atau `localhost`).

## 2. Set versi PHP
hPanel → **Advanced → PHP Configuration** → pilih **PHP 8.2** (atau lebih baru).
Pastikan ekstensi aktif: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`.

## 3. Upload & ekstrak kode
Pilih salah satu:

**A. Via SSH (disarankan)** — upload `labkesmas-deploy.tar.gz` ke home (`~`) lewat File Manager, lalu:
```bash
cd ~
mkdir -p labkesmas && tar -xzf labkesmas-deploy.tar.gz -C labkesmas
cd labkesmas
```

**B. Via File Manager** — buat folder `labkesmas` di luar `public_html`, upload arsip ke situ, klik kanan → **Extract**.

> Rekomendasi: taruh aplikasi di folder **di luar** `public_html` (mis. `~/labkesmas`), lalu arahkan document root ke `public/`-nya (langkah 5). Ini praktik aman Laravel — folder selain `public/` tidak bisa diakses publik.

## 4. Setup aplikasi via SSH
```bash
cd ~/labkesmas

# Dependency PHP (tanpa dev, autoloader dioptimasi)
composer install --no-dev --optimize-autoloader

# Siapkan .env produksi
cp .env.production.example .env
nano .env          # isi APP_URL, DB_DATABASE, DB_USERNAME, DB_PASSWORD (dan DB_HOST bila 'localhost')

# Generate APP_KEY
php artisan key:generate

# Buat tabel
php artisan migrate --force

# Buat akun admin (WAJIB — ganti kredensial di bawah):
php artisan tinker --execute="\App\Models\User::create(['username'=>'admin','password'=>\Illuminate\Support\Facades\Hash::make('GANTI_PASSWORD_KUAT'),'role'=>'super_admin']);"

# Cache konfigurasi/route/view untuk performa produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Izin tulis
chmod -R 775 storage bootstrap/cache
```

> Catatan: JANGAN jalankan `php artisan db:seed` untuk produksi — seeder itu berisi data contoh (Sumatera/Medan/dsb.) untuk pengembangan. Gunakan perintah `tinker` di atas untuk membuat **hanya** akun admin, lalu isi data wilayah/labkesmas Anda lewat panel admin.

## 5. Arahkan domain ke folder `public/`
hPanel → **Website → [domain Anda] → Ubah Document Root** menjadi:
```
/home/uXXXXXXXX/labkesmas/public
```
(Sesuaikan `uXXXXXXXX` dan path folder Anda. Cek path absolut dengan `pwd` di dalam `~/labkesmas`.)

Jika hosting tidak mengizinkan ubah document root: taruh isi folder `public/` ke `public_html/`, taruh sisa aplikasi satu level di atasnya, lalu edit `public_html/index.php` agar `require __DIR__.'/../labkesmas/vendor/autoload.php'` dan `bootstrap/app.php` menunjuk ke lokasi aplikasi. (Opsi document root jauh lebih rapi — pakai itu bila bisa.)

## 6. Verifikasi
- Buka `https://domain-anda.com` → muncul **landing InPULS KEMENKES** (animasi fade).
- Klik **Pemeriksaan** → dashboard (masih kosong sampai Anda input data).
- Buka `https://domain-anda.com/admin` → login dengan akun admin yang dibuat di langkah 4 → **segera ganti password bila masih default**.

---

## Troubleshooting cepat
| Gejala | Penyebab & solusi |
|---|---|
| **500 Server Error** | Cek `storage/logs/laravel.log`. Umumnya: `APP_KEY` kosong (jalankan `php artisan key:generate`), kredensial DB salah, atau `storage/` tak writable (`chmod -R 775 storage bootstrap/cache`). |
| **Halaman blank / 403** | Document root belum menunjuk ke `public/`. Ulangi langkah 5. |
| **CSS/JS tidak muncul** | `public/build/` tidak terupload, atau document root salah. Pastikan `public/build/manifest.json` ada di server. |
| **419 saat login** | Cookie/session. Pastikan `APP_URL` = domain HTTPS yang benar dan `SESSION_SECURE_COOKIE=true`. |
| **Error setelah update kode** | Bersihkan cache: `php artisan optimize:clear` lalu cache ulang (`config:cache`, `route:cache`, `view:cache`). |

## Update ke depan
1. Di lokal: `npm run build`, lalu buat arsip baru (lihat cara di bawah).
2. Upload & ekstrak menimpa folder aplikasi (jangan timpa `.env`).
3. Di server: `composer install --no-dev --optimize-autoloader` (jika dependency berubah), `php artisan migrate --force`, lalu `php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache`.

### Membuat ulang arsip deploy (di lokal, Git Bash)
```bash
cd /c/Users/insan/OneDrive/Desktop/dashboard
npm run build
tar -czf ../labkesmas-deploy.tar.gz \
  --exclude='./node_modules' --exclude='./vendor' --exclude='./.git' \
  --exclude='./.env' --exclude='./database/database.sqlite' \
  --exclude='./storage/logs/*' --exclude='./storage/framework/cache/data/*' \
  --exclude='./storage/framework/sessions/*' --exclude='./storage/framework/views/*' \
  --exclude='./bootstrap/cache/*.php' .
```

---

## Catatan tentang data lokal Anda
Data yang Anda input selama ini ("Labkesda Kab. Banjar", "Regional 7", dst.) tersimpan di **SQLite lokal** (`database/database.sqlite`) dan **tidak** ikut ke produksi (produksi memakai MySQL kosong). Bila ingin memindahkannya, beri tahu saya — bisa dibuatkan skrip ekspor/impor. Jika belum banyak, lebih mudah diinput ulang lewat panel admin di produksi.
