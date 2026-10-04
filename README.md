# 🐟 POS Kulu Asri — Resto Management & QR Self-Order System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/Chart.js-4.x-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white" alt="Chart.js">
  <img src="https://img.shields.io/badge/Status-Production%20Ready-047857?style=for-the-badge" alt="Status">
</p>

<p align="center">
  <strong>Sistem Kasir Modern (POS), Pemesanan Mandiri Meja (QR Self-Order), dan Dashboard Analitik Finansial untuk Rumah Makan Kulu Asri.</strong><br>
  <em>"Jagonya Ikan Bakar!"</em>
</p>

---

## 📖 Tentang Proyek

**POS Kulu Asri** adalah platform manajemen restoran terintegrasi yang dirancang untuk mempercepat perputaran pesanan, mencegah kecurangan kasir, dan memudahkan pemilik/manajemen memantau performa bisnis secara *real-time* langsung dari smartphone atau tablet.

Sistem ini mengusung arsitektur **Dual-Channel Order**:
1. **Terminal Kasir POS Langsung**: Digunakan oleh staf kasir di meja depan untuk pesanan langsung (*dine-in/takeaway*).
2. **QR Meja Self-Order**: Digunakan oleh pelanggan yang duduk di meja untuk memindai kode QR unik, memilih menu favorit, dan mengirim pesanan tanpa perlu menunggu pelayan.

Kedua saluran pesanan ini disinkronkan secara otomatis ke dalam satu sistem dapur dan laporan keuangan terpusat.

---

## 🌟 Keunggulan Utama

### 1. 📱 Ramah Pemantauan Mobile & Tablet (Owner & Manajemen)
- **Akses Di Mana Saja**: Dashboard manajemen didesain khusus agar dapat diakses secara mulus lewat layar handphone (smartphone) maupun tablet/iPad.
- **Grid Metrik 2x2 Cerdas**: 4 indikator utama (*Omset*, *Total Transaksi*, *Audit Void*, dan *Peringatan Stok*) tersusun rapi di bagian atas layar HP sehingga manajemen bisa melihat kondisi terkini dalam 3 detik pertama tanpa perlu *scroll* jauh.
- **Drawer Navigasi Offcanvas**: Menu navigasi samping geser (*slide-in*) yang tidak memakan ruang layar, dilengkapi tombol cepat pintasan ke Terminal POS.
- **Grafik Berbasis Sentuhan**: Grafik tren penjualan harian dan bulanan dapat digeser secara horizontal menggunakan sentuhan jari tanpa merusak tata letak halaman.

### 2. ⚡ Sinkronisasi Pesanan Dua Jalur (Dual-Channel POS)
- Kasir dapat melihat pesanan yang masuk dari meja pelanggan secara *real-time*.
- Pelanggan dapat memantau status pesanan (Menunggu Pembayaran, Diproses Dapur, Selesai) langsung dari browser smartphone tanpa instalasi aplikasi tambahan.

### 3. 📊 Analitik Finansial & Intelligence Restoran
- **Kalkulasi Laba Bersih Otomatis**: Menghitung estimasi *Net Profit* berdasarkan selisih Omset dikurangi HPP (*Modal Pokok / Cost of Goods Sold*).
- **Komparasi DoD & MoM**: Membandingkan performa penjualan hari ini vs kemarin (*Day-over-Day*) dan bulan ini vs bulan lalu (*Month-over-Month*) secara otomatis dengan indikator persentase surplus/defisit.
- **Top 5 Menu Terlaris**: Identifikasi menu paling laku untuk membantu strategi pengadaan bahan baku.

### 4. 🛡️ Audit Void & Pencegahan Fraud Kasir
- Setiap pembatalan pesanan atau *void item* dicatat secara otomatis dalam log audit (*Void Audit Logs*) lengkap dengan nama petugas, alasan pembatalan, waktu, serta nominal untuk mencegah kebocoran kasir.

### 5. 📦 Manajemen Inventori & Peringatan Stok Otomatis
- Peringatan instan (*Low Stock Alert*) pada dashboard saat jumlah stok menu menyentuh batas minimum yang telah dikonfigurasi.

### 6. 🖨️ Cetak Struk Multi-Format
- Mendukung cetak struk kasir termal ukuran 58mm dan 80mm via browser standar serta integrasi printer thermal Bluetooth/USB (aplikasi RawBT).

### 7. 🎨 Identitas Visual & Design System Kulu Asri
- Antarmuka mengadopsi palet warna kuliner Nusantara:
  - **Botanical Emerald (`#047857`, `#064e3b`)**: Melambangkan kesegaran rempah dan alam Asri.
  - **Honey Amber (`#d97706`, `#f59e0b`)**: Melambangkan kehangatan aroma bumbu bakar madu.
  - **Flame Accent (`#ea580c`)**: Aksentuasi api panggangan khas ikan bakar.

---

## 🛠️ Bahasa & Teknologi (Tech Stack)

| Komponen | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Bahasa Utama** | **PHP 8.2+** & **JavaScript (ES6+)** | Bahasa backend server-side dan logika interaktif frontend |
| **Framework Backend** | **Laravel 12** | Arsitektur MVC, Eloquent ORM, Routing, Middleware, dan Auth |
| **Frontend Template** | **Blade Templating Engine** | Render tampilan server-side yang cepat dan aman |
| **Styling & UI** | **Vanilla CSS + Bootstrap 5.3** | Tata letak responsif, flexbox/grid, dan Bootstrap Icons |
| **Design Tokens** | **3-Layer CSS Variables** | Token warna, tipografi, dan radius terstandarisasi |
| **Visualisasi Grafik** | **Chart.js** | Grafik garis interaktif dengan AJAX fetch perbandingan waktu |
| **Database** | **MySQL / SQLite** | Penyimpanan transaksi, menu, meja, dan riwayat audit |
| **Tipografi** | **Plus Jakarta Sans & Playfair Display** | Tipografi modern yang bersih dipadukan dengan aksen elegan |

---

## 👥 Hak Akses & Peran Pengguna (Roles)

1. **Administrator / Pemilik (Owner)**
   - Mengakses Dashboard analitik penjualan dan estimasi laba.
   - Mengelola katalog produk, harga, stok, dan batas minimum stok.
   - Mengelola kategori makanan dan minuman.
   - Mengelola tata letak meja dan men-generate QR Code pelanggan.
   - Memantau laporan rekap transaksi dan log audit *void*.
   - Mengelola akun pengguna kasir dan staf.

2. **Kasir**
   - Mengoperasikan Terminal POS Kasir untuk melayani pelanggan langsung.
   - Memverifikasi pembayaran pesanan masuk dari Meja QR (Cash / QRIS).
   - Melakukan cetak struk pembayaran.

3. **Pelanggan (Customer / Tamu Meja)**
   - Memindai QR Meja menggunakan kamera smartphone.
   - Memilih menu makanan & minuman dan memasukkan ke keranjang.
   - Memilih opsi pembayaran (Kasir / QRIS).
   - Melihat nomor antrean dan status pemrosesan hidangan.

---

## 🚀 Panduan Instalasi (Local Development)

### Prasyarat
- PHP >= 8.2
- Composer
- Database MySQL atau SQLite
- Node.js & NPM (opsional, jika ingin compile aset tambahan)

### Langkah Instalasi

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/ipankexe/pos-kulu-asri.git
   cd pos-kulu-asri
   ```

2. **Instal Dependensi Composer:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database di `.env`:**
   Sesuaikan parameter database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pos_kulu_asri
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Atau gunakan `DB_CONNECTION=sqlite` untuk pengujian lokal cepat)*

5. **Jalankan Migrasi & Seeder Awal:**
   ```bash
   php artisan migrate --seed
   ```

6. **Hubungkan Storage:**
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://127.0.0.1:8000` (atau IP LAN Anda untuk pengujian lewat smartphone).

---

## 🔑 Akun Bawaan (Default Credentials)

Setelah menjalankan `php artisan db:seed`, Anda dapat masuk menggunakan akun berikut:

| Peran (Role) | Email | Password | Akses URL |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@kuluasri.com` | `password` | `/dashboard` |
| **Kasir** | `kasir@kuluasri.com` | `password` | `/pos` |

---

## 📁 Struktur Direktori Penting

```text
pos-kulu-asri/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php        # Dashboard, analitik, dan perbandingan DoD/MoM
│   │   ├── PosController.php          # Logika kasir, kalkulasi struk, dan checkout
│   │   ├── CustomerOrderController.php# Logika pemesanan mandiri via QR Meja
│   │   ├── ProductController.php      # Manajemen katalog menu dan stok
│   │   └── TableController.php        # Manajemen meja dan QR code generator
│   └── Models/                        # Model Eloquent (Transaction, Product, VoidLog, dll.)
├── assets/
│   ├── design-tokens.json             # Master token desain 3-layer Kulu Asri
│   └── design-tokens.css              # Variabel CSS desain Kulu Asri
├── docs/
│   └── brand-guidelines.md            # Panduan identitas brand, warna, dan tipografi
├── public/
│   ├── css/
│   │   └── design-tokens.css          # Token CSS yang dimuat pada browser
│   └── brand-presentation.html        # Presentasi visual brand Kulu Asri
└── resources/views/
    ├── admin/
    │   ├── dashboard.blade.php        # Dashboard responsive mobile & tablet
    │   └── sidebar.blade.php          # Drawer offcanvas dan topbar mobile
    ├── pos/
    │   └── index.blade.php            # Antarmuka terminal kasir POS
    ├── customer/
    │   └── menu.blade.php             # Antarmuka pemesanan QR pelanggan
    └── auth/
        └── login.blade.php            # Halaman login berdesain kuliner
```

---

## 📄 Lisensi & Hak Cipta

Proyek ini dikembangkan khusus untuk **Rumah Makan Kulu Asri**. Seluruh hak cipta dan merek dagang dilindungi.
Perangkat lunak dasar berbasis [Laravel Framework](https://laravel.com) berlisensi [MIT License](https://opensource.org/licenses/MIT).
