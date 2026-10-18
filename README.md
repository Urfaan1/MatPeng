# MatPeng — Aplikasi Manajemen Keuangan Pribadi

**MatPeng** adalah aplikasi pencatatan dan pengelolaan keuangan pribadi berbasis website yang responsif dan dapat diinstal pada smartphone menggunakan teknologi **Progressive Web App (PWA)**.

---

## 🚀 Fitur Utama

- **Dashboard Finansial Komprehensif**: Saldo total akumulatif, total pemasukan & pengeluaran bulanan, serta arus kas bersih (net cash flow).
- **Pencatatan Transaksi**: Manajemen pemasukan dan pengeluaran dengan nominal Rupiah presisi, pemilihan kategori reaktif, tanggal, dan catatan.
- **Kategori Terorganisir**: Kategori bawaan dan kustom untuk pemasukan & pengeluaran dengan proteksi integritas data transaksi.
- **Visualisasi & Laporan**: Grafik tren pemasukan vs pengeluaran dan grafik distribusi kategori berbasis Chart.js.
- **Mobile First & PWA**: Dukungan penuh instalasi di smartphone/desktop, offline static shell caching, dan desain modern ramah sentuhan.

---

## 🛠️ Teknologi & Stack

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Frontend** | Laravel Blade |
| **Styling** | Tailwind CSS v4 & Lucide Icons |
| **JavaScript** | Alpine.js |
| **Visualisasi Grafik** | Chart.js |
| **Database** | MySQL 8.0 |
| **PWA** | Web App Manifest & Service Worker |
| **Mata Uang & Zona Waktu** | Rupiah (IDR) & Asia/Jakarta |

---

## 📦 Cara Instalasi & Menjalankan

### 1. Klon Repositori
```bash
git clone https://github.com/Urfaan1/MatPeng.git
cd MatPeng
```

### 2. Pasang Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi database MySQL di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=matpeng_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi Database
```bash
php artisan migrate
```

### 5. Kompilasi Aset Frontend
```bash
npm run build
# Atau untuk mode development:
npm run dev
```

### 6. Jalankan Server
```bash
php artisan serve
```
Buka browser di: **`http://localhost:8000`**

---

## 🧪 Menjalankan Pengujian

```bash
php artisan test
```

---

## 📄 Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).
