# PRD (Product Requirements Document)

## Sistem Manajemen Kafe — Company Profile + Reservasi + POS + Kitchen/Bar + Dashboard

**Versi:** 2.0
**Bahasa:** Indonesia
**Author:** —
**Status:** Draft Final
**Terakhir Diperbarui:** 2026

---

## 1. Ringkasan Produk

Sistem ini adalah aplikasi web manajemen kafe terintegrasi yang menggabungkan empat kebutuhan utama dalam satu platform:

1. **Guest/Public Mode** — Pengunjung dapat mengakses website tanpa login untuk melihat menu, profil kafe, galeri, dan informasi reservasi.
2. **Reservation System** — Pelanggan dapat melakukan reservasi meja melalui form booking dengan syarat DP Rp20.000, disertai aturan auto-expire jika keterlambatan melebihi 1 jam dari jam booking.
3. **POS System** — Kasir login untuk membuat transaksi, memilih meja, kategori, produk, varian, add-on, voucher, dan metode pembayaran cash/QRIS.
4. **Backoffice Dashboard** — Admin mengelola menu, stok, voucher, laporan penjualan, serta melihat grafik analitik dan export Excel bulanan.

Sistem menghasilkan output otomatis berupa invoice customer, print order ke kitchen, dan print order ke bar.

---

## 2. Latar Belakang Masalah

Banyak kafe skala menengah masih memisahkan proses reservasi, pencatatan pesanan, pengelolaan stok, dan laporan penjualan ke dalam sistem berbeda atau bahkan dikelola secara manual. Kondisi ini menimbulkan berbagai masalah operasional:

- Proses pemesanan lambat karena tidak terintegrasi
- Reservasi meja tidak tertata dan rentan bentrok
- Kesalahan input pesanan oleh kasir
- Laporan penjualan tidak real-time
- Stok tidak terkontrol dengan baik
- Keterlambatan distribusi order ke kitchen dan bar
- Tidak ada data analytics untuk pengambilan keputusan manajerial

Oleh karena itu dibutuhkan satu sistem terintegrasi yang menangani seluruh pengalaman dari sisi pelanggan hingga operasional internal kafe.

---

## 3. Tujuan Produk

### 3.1 Tujuan Utama

- Menyediakan website publik yang informatif dan dapat diakses tanpa login
- Memfasilitasi reservasi meja dengan sistem DP dan batas waktu kedatangan yang jelas
- Mempercepat proses transaksi kasir dan distribusi order ke kitchen/bar
- Menyediakan dashboard analitik real-time untuk admin dan owner
- Menyediakan export laporan penjualan ke Excel per bulan

### 3.2 Tujuan Bisnis

- Meningkatkan efisiensi layanan operasional kafe
- Mengurangi kesalahan pencatatan order
- Memudahkan kontrol reservasi dan manajemen meja
- Mempermudah pengambilan keputusan berbasis data penjualan

---

## 4. Ruang Lingkup Produk

### 4.1 In Scope

- Website publik tanpa login (Home, Menu, About, Gallery, Reservasi)
- Reservasi meja via form dengan DP Rp20.000
- Auto-expire reservasi jika terlambat lebih dari 1 jam
- Login role-based: kasir, admin, kitchen, bartender
- Input pesanan dengan kategori, produk, varian, add-on, voucher
- Pembayaran cash dan QRIS
- Cetak invoice otomatis, print order kitchen, print order bar
- Dashboard admin dengan grafik penjualan, produk terlaris, jam ramai, metode pembayaran
- Export laporan penjualan ke Excel bulanan
- Manajemen master data: menu, stok, voucher, meja, customer, user

### 4.2 Out of Scope

- Aplikasi mobile native (iOS/Android)
- Integrasi langsung payment gateway pihak ketiga secara real-time
- Loyalty point multi-tier yang kompleks
- Multi-cabang dengan konsolidasi lintas outlet kompleks
- Delivery order pihak ketiga (GoFood, GrabFood)
- Fitur live chat support

---

## 5. Target Pengguna / Persona

### 5.1 Guest / Pelanggan Umum

- Tidak perlu login
- Melihat menu, profil kafe, galeri, dan informasi reservasi
- Mengajukan booking meja melalui form
- Datang ke lokasi dan dilayani oleh kasir/waiter

### 5.2 Kasir

- Login ke sistem POS
- Membuat dan mengelola transaksi
- Memilih meja, menu, varian, add-on, dan voucher
- Memproses pembayaran cash dan QRIS
- Mencetak invoice dan order kitchen/bar

### 5.3 Kitchen Staff

- Menerima dan melihat daftar order makanan secara real-time
- Memproses pesanan sesuai urutan masuk
- Melihat nama customer, nomor meja, dan catatan khusus

### 5.4 Bartender / Bar Staff

- Menerima dan melihat daftar order minuman secara real-time
- Memproses pesanan sesuai urutan masuk
- Melihat nama customer, nomor meja, dan catatan khusus

### 5.5 Admin

- Mengelola seluruh master data: produk, kategori, stok, voucher, meja, user
- Mengelola dan memantau data reservasi
- Melihat dashboard grafik analitik
- Mengekspor laporan penjualan bulanan ke Excel

### 5.6 Owner / Manajemen

- Melihat insight penjualan dan performa usaha
- Menganalisis menu terlaris, jam ramai, dan metode pembayaran
- Memantau kondisi stok dan pengeluaran

---

## 6. Arsitektur Teknologi

### 6.1 Pendekatan Arsitektur

Sistem menggunakan **arsitektur Semi-Decoupled Monolith**, yaitu:

- Backend Laravel sebagai REST API server
- Frontend React.js sebagai SPA (Single Page Application) yang terpisah secara layer namun di-deploy dalam satu repository
- Komunikasi menggunakan JSON via HTTP/REST

Pendekatan ini dipilih karena:

- Lebih cepat dikembangkan dibanding full microservice
- Lebih mudah di-maintain untuk skala kafe menengah
- Frontend React.js memberikan pengalaman UI yang responsif dan modern
- Backend Laravel tetap bersih dan scalable sebagai pure API

### 6.2 Stack Teknologi

#### Frontend

| Komponen         | Teknologi                    | Keterangan                          |
| ---------------- | ---------------------------- | ----------------------------------- |
| Framework        | React 18                     | Component-based, fast rendering     |
| Build Tool       | Vite                         | Lebih cepat dari CRA, HMR instan    |
| Styling          | Tailwind CSS                 | Utility-first, konsisten            |
| State Management | Zustand                      | Lebih ringan dari Redux             |
| Data Fetching    | TanStack Query (React Query) | Caching, refetch, optimistic update |
| HTTP Client      | Axios                        | Interceptor untuk token auth        |
| Routing          | React Router v6              | Client-side routing                 |
| Chart            | Recharts                     | Native React, ringan, fleksibel     |
| UI Component     | shadcn/ui                    | Accessible, customizable            |
| Tabel Data       | TanStack Table               | Virtual scroll, sorting, filter     |
| Form             | React Hook Form + Zod        | Performa tinggi, validasi type-safe |
| Notifikasi       | Sonner                       | Toast notification ringan           |
| Icon             | Lucide React                 | Konsisten, tree-shakeable           |

#### Backend

| Komponen          | Teknologi             | Keterangan                       |
| ----------------- | --------------------- | -------------------------------- |
| Framework         | Laravel 11            | REST API, robust, mature         |
| Auth              | Laravel Sanctum       | Token-based auth untuk SPA       |
| Role & Permission | Spatie Permission     | Role: admin, kasir, kitchen, bar |
| Export Excel      | Maatwebsite Excel     | Export laporan bulanan           |
| Print             | mike42/escpos-php     | Thermal printer ESC/POS          |
| Queue             | Laravel Queue + Redis | Print, export, notifikasi async  |
| Cache             | Redis                 | Dashboard cache, session         |
| Scheduler         | Laravel Scheduler     | Cron job auto-expire reservasi   |

#### Database & Infrastructure

| Komponen     | Teknologi                | Keterangan                      |
| ------------ | ------------------------ | ------------------------------- |
| Database     | MySQL 8                  | Transaksi ACID, relasi kompleks |
| Cache        | Redis                    | Cache query dashboard           |
| Web Server   | Nginx + PHP-FPM          | Production-grade                |
| SSL          | Let's Encrypt            | HTTPS wajib                     |
| CDN/Security | Cloudflare               | Anti-DDoS, CDN, caching         |
| Hosting      | VPS (DigitalOcean/Vultr) | Kontrol penuh                   |

### 6.3 Diagram Arsitektur

```
┌─────────────────────────────────────────┐
│              CLIENT LAYER               │
│                                         │
│   React 18 + Vite + Tailwind CSS        │
│   Zustand + React Query + Axios         │
│   React Router v6                       │
└─────────────────┬───────────────────────┘
                  │ HTTP / JSON (REST API)
                  │ Bearer Token (Sanctum)
┌─────────────────▼───────────────────────┐
│              API LAYER                  │
│                                         │
│   Laravel 11 REST API                   │
│   Sanctum Auth + Spatie Permission      │
│   Laravel Queue + Scheduler             │
└───────────┬─────────────┬───────────────┘
            │             │
┌───────────▼──┐    ┌─────▼──────────────┐
│   MySQL 8    │    │       Redis         │
│   (Utama)    │    │   (Cache + Queue)   │
└──────────────┘    └────────────────────┘
```

---

## 7. Daftar Fitur Utama

### 7.1 Website Publik (Guest Mode)

- Home page dengan hero section, tagline, dan CTA
- Menu page dengan grid produk dan filter kategori
- About page berisi profil kafe, cerita, dan tim
- Gallery page berisi foto-foto kafe
- Reservation page dengan form booking
- Tombol kontak WhatsApp floating
- Responsive untuk mobile dan desktop

### 7.2 Sistem Reservasi

- Form booking: nama, nomor HP, tanggal, jam, jumlah tamu, pilih meja, catatan
- Cek ketersediaan meja secara real-time
- Pembayaran DP Rp20.000 (cash/transfer/QRIS)
- Konfirmasi booking via WhatsApp atau tampilan sukses
- Status booking: pending, confirmed, arrived, expired, cancelled
- Auto-expire otomatis via cron job jika terlambat lebih dari 1 jam
- Admin dapat melihat dan mengelola semua data reservasi

### 7.3 POS Kasir

- Login kasir dengan session aman
- Input nama customer atau pilih dari database customer
- Pilih meja yang tersedia
- Pilih kategori menu (sidebar)
- Pilih produk (card grid)
- Pilih varian produk (modal/drawer)
- Pilih add-on (checkbox list)
- Ubah kuantitas item di cart
- Input kode voucher dan validasi otomatis
- Kalkulasi: subtotal, diskon voucher, pajak, grand total
- Pembayaran cash: input nominal diterima, hitung kembalian
- Pembayaran QRIS: tampilkan QR, konfirmasi setelah bayar
- Print invoice otomatis ke printer thermal
- Print order makanan ke kitchen
- Print order minuman ke bar
- Riwayat transaksi hari ini

### 7.4 Kitchen Display

- Tampilan daftar order masuk secara real-time (polling/SSE)
- Hanya menampilkan item kategori makanan (food)
- Informasi: nomor meja, nama customer, item pesanan, catatan
- Urutan berdasarkan waktu masuk
- Tombol update status: new → preparing → done
- Filter berdasarkan status

### 7.5 Bar Display

- Tampilan daftar order masuk secara real-time (polling/SSE)
- Hanya menampilkan item kategori minuman (drink)
- Informasi: nomor meja, nama customer, item pesanan, catatan
- Urutan berdasarkan waktu masuk
- Tombol update status: new → preparing → done
- Filter berdasarkan status

### 7.6 Admin Dashboard

- Summary card: total revenue hari ini, total transaksi, rata-rata transaksi, produk terjual
- Line chart penjualan harian dalam bulan berjalan
- Bar chart produk terlaris bulan ini
- Pie chart metode pembayaran (cash vs QRIS)
- Bar chart jam ramai (per jam dalam sehari)
- Filter periode: hari ini, minggu ini, bulan ini, custom range
- Export laporan bulanan ke Excel (.xlsx)
- Tabel transaksi terbaru dengan pagination
- Status stok kritis (warning jika stok menipis)

### 7.7 Manajemen Master Data

- **Produk**: tambah, edit, hapus produk; upload foto; atur kategori, harga, tipe (food/drink), status aktif
- **Kategori**: tambah, edit, hapus kategori; tentukan ikon atau warna
- **Varian**: kelola varian per produk (size, level, dll)
- **Variant Options**: opsi per varian beserta harga tambahan
- **Add-on**: produk tambahan opsional dengan harga
- **Stok**: kelola item stok, update kuantitas, lihat riwayat mutasi
- **Relasi Produk-Stok**: atur bahan yang digunakan per produk untuk auto-deduction
- **Voucher**: buat, edit, hapus voucher; atur kode, nominal diskon, minimum pembelian, masa berlaku, kuota
- **Meja**: tambah, edit, hapus meja; atur kapasitas dan status
- **Customer**: lihat dan kelola database customer
- **User & Role**: kelola akun user internal dan assign role

---

## 8. Business Rules

1. Pengunjung dapat mengakses website dan seluruh halaman publik tanpa login.
2. Reservasi meja mewajibkan pembayaran DP sebesar Rp20.000.
3. Reservasi dianggap hangus (expired) apabila customer datang lebih dari 1 jam dari jam booking yang ditentukan.
4. Pengecekan auto-expire dilakukan oleh cron job yang berjalan setiap menit.
5. Kasir hanya dapat mengakses fitur POS setelah login dengan role yang sesuai.
6. Setiap transaksi harus memiliki nomor invoice unik yang di-generate otomatis.
7. Order dapat terdiri dari satu atau lebih item, di mana setiap item memiliki produk, varian opsional, add-on opsional, dan kuantitas.
8. Voucher hanya dapat digunakan apabila memenuhi syarat: kode valid, belum expired, kuota masih tersedia, dan total pembelian memenuhi minimum.
9. Pembayaran cash: kasir memasukkan jumlah uang diterima, sistem menghitung kembalian. Transaksi selesai setelah kasir mengkonfirmasi.
10. Pembayaran QRIS: transaksi dianggap selesai setelah kasir mengkonfirmasi penerimaan pembayaran.
11. Setelah transaksi selesai, sistem mencetak: invoice customer, order makanan ke kitchen, dan order minuman ke bar.
12. Item dengan tipe `food` dikirim ke kitchen, item dengan tipe `drink` dikirim ke bar.
13. Stok bahan baku otomatis berkurang saat transaksi berstatus `completed` (jika relasi produk-stok telah dikonfigurasi).
14. Data dashboard dan laporan Excel hanya diambil dari transaksi dengan status `completed`.
15. Admin dapat mengekspor laporan penjualan untuk periode bulan tertentu kapan saja.

---

## 9. Workflow Sistem (End-to-End)

### 9.1 Workflow Guest / Customer

```
Customer membuka website
↓
Melihat halaman Home, Menu, About, Gallery
↓
Customer memutuskan: reservasi atau langsung datang?
↓
[Jika Reservasi]
Customer mengisi form booking
(nama, HP, tanggal, jam, jumlah tamu, meja, catatan)
↓
Sistem cek ketersediaan meja pada slot waktu tersebut
↓
Customer membayar DP Rp20.000
↓
Sistem simpan reservasi dengan status: pending/confirmed
Sistem set expired_at = reserved_at + 60 menit
↓
Cron job berjalan setiap menit:
Jika NOW() > expired_at dan status = confirmed → status = expired
↓
[Customer Datang]
Kasir konfirmasi kedatangan, status = arrived
Kasir lanjut proses transaksi POS
```

### 9.2 Workflow Kasir (POS)

```
Kasir login ke sistem
↓
Kasir input nama customer (baru atau pilih existing)
↓
Kasir pilih meja yang tersedia
↓
Kasir pilih kategori → produk → varian → add-on
↓
Kasir atur kuantitas item
↓
Kasir input voucher (opsional)
↓
Sistem kalkulasi:
- Subtotal per item
- Total diskon voucher
- Pajak (jika ada)
- Grand total
↓
Kasir pilih metode pembayaran
↓
[Cash]                          [QRIS]
Kasir input nominal diterima    Tampilkan QR code
Sistem hitung kembalian         Kasir konfirmasi pembayaran diterima
↓                               ↓
           Transaksi disimpan (status: completed)
           ↓
           Invoice dicetak ke printer thermal
           ↓
           Order makanan dikirim ke kitchen printer
           ↓
           Order minuman dikirim ke bar printer
           ↓
           Stok bahan otomatis berkurang
```

### 9.3 Workflow Kitchen / Bar

```
Order masuk dari kasir (transaksi completed)
↓
Sistem memisahkan item berdasarkan tipe produk:
- food → Kitchen
- drink → Bar
↓
Kitchen/Bar melihat daftar pesanan baru
↓
Staff ubah status: new → preparing → done
↓
Pesanan disajikan ke customer
```

### 9.4 Workflow Admin Dashboard

```
Admin login ke dashboard
↓
Dashboard tampil dengan data summary (revenue, transaksi, dll)
↓
Admin melihat grafik: penjualan, produk terlaris, metode bayar, jam ramai
↓
Admin filter periode (hari ini / minggu / bulan / custom)
↓
Admin pilih bulan → klik Export Excel
↓
Sistem generate file .xlsx
↓
File terunduh otomatis ke browser admin
```

---

## 10. Functional Requirements

### 10.1 Guest / Public

| Kode  | Requirement                                                                    |
| ----- | ------------------------------------------------------------------------------ |
| FR-01 | Sistem dapat diakses tanpa login untuk semua halaman publik                    |
| FR-02 | Sistem menampilkan daftar menu produk dengan gambar, nama, harga, dan kategori |
| FR-03 | Sistem menampilkan profil kafe dan galeri foto                                 |
| FR-04 | Sistem menyediakan form reservasi yang dapat diisi oleh customer               |
| FR-05 | Sistem menyediakan tombol WhatsApp untuk kontak langsung                       |

### 10.2 Reservasi

| Kode  | Requirement                                                                                    |
| ----- | ---------------------------------------------------------------------------------------------- |
| FR-06 | Sistem menyimpan data reservasi dengan semua field yang diperlukan                             |
| FR-07 | Sistem memvalidasi ketersediaan meja pada slot waktu yang diminta                              |
| FR-08 | Sistem mencatat DP Rp20.000 per reservasi                                                      |
| FR-09 | Sistem menyimpan `expired_at` = `reserved_at` + 60 menit                                       |
| FR-10 | Cron job otomatis mengubah status reservasi menjadi `expired` jika waktu melewati `expired_at` |
| FR-11 | Admin dapat melihat, memfilter, dan mengelola semua data reservasi                             |

### 10.3 POS Kasir

| Kode  | Requirement                                                                           |
| ----- | ------------------------------------------------------------------------------------- |
| FR-12 | Kasir harus login untuk mengakses sistem POS                                          |
| FR-13 | Kasir dapat memilih meja yang tersedia                                                |
| FR-14 | Kasir dapat memilih kategori, produk, varian, dan add-on                              |
| FR-15 | Kasir dapat mengubah kuantitas setiap item dalam cart                                 |
| FR-16 | Kasir dapat memasukkan kode voucher dan sistem memvalidasi otomatis                   |
| FR-17 | Sistem menghitung subtotal, diskon, pajak, grand total, dan kembalian secara otomatis |
| FR-18 | Sistem mendukung pembayaran cash dan QRIS                                             |
| FR-19 | Sistem mencetak invoice ke printer thermal setelah transaksi selesai                  |
| FR-20 | Sistem mengirim order makanan ke kitchen printer dan order minuman ke bar printer     |

### 10.4 Kitchen / Bar

| Kode  | Requirement                                                                          |
| ----- | ------------------------------------------------------------------------------------ |
| FR-21 | Sistem memisahkan order berdasarkan tipe produk: food ke kitchen, drink ke bar       |
| FR-22 | Kitchen/Bar display menampilkan nomor meja, nama customer, item pesanan, dan catatan |
| FR-23 | Staff dapat memperbarui status order: new → preparing → done                         |
| FR-24 | Display diperbarui secara real-time tanpa refresh manual                             |

### 10.5 Admin Dashboard

| Kode  | Requirement                                                                             |
| ----- | --------------------------------------------------------------------------------------- |
| FR-25 | Dashboard menampilkan summary card: revenue, total transaksi, rata-rata, produk terjual |
| FR-26 | Sistem menampilkan grafik penjualan harian/bulanan                                      |
| FR-27 | Sistem menampilkan grafik produk terlaris                                               |
| FR-28 | Sistem menampilkan grafik metode pembayaran                                             |
| FR-29 | Sistem menampilkan grafik jam ramai                                                     |
| FR-30 | Sistem mendukung filter periode: hari ini, minggu ini, bulan ini, custom range          |
| FR-31 | Sistem dapat mengekspor laporan bulanan ke file Excel (.xlsx)                           |

### 10.6 Master Data

| Kode  | Requirement                                                                                    |
| ----- | ---------------------------------------------------------------------------------------------- |
| FR-32 | Admin dapat melakukan CRUD produk (beserta foto, kategori, tipe, harga)                        |
| FR-33 | Admin dapat melakukan CRUD kategori, varian, variant option, dan add-on                        |
| FR-34 | Admin dapat mengelola stok item dan melihat riwayat mutasi stok                                |
| FR-35 | Admin dapat mengatur relasi produk-stok untuk auto-deduction                                   |
| FR-36 | Admin dapat melakukan CRUD voucher dengan validasi kode, expired, kuota, dan minimum pembelian |
| FR-37 | Admin dapat melakukan CRUD meja beserta status dan kapasitasnya                                |
| FR-38 | Admin dapat mengelola akun user internal dan assign role                                       |

---

## 11. Non-Functional Requirements

### 11.1 Performance

- Halaman publik harus selesai loading dalam kurang dari 2 detik pada koneksi normal
- Dashboard harus memuat data ringkasan dalam kurang dari 3 detik
- Operasi transaksi POS harus selesai dalam kurang dari 1 detik
- Sistem harus menggunakan caching Redis untuk query dashboard yang berat
- Semua query database yang berat harus menggunakan index yang sesuai
- Pengambilan data menggunakan eager loading untuk mencegah N+1 problem
- List data yang panjang wajib menggunakan pagination (default 10–25 per halaman)

### 11.2 Security

- Password user disimpan menggunakan bcrypt hashing
- Autentikasi menggunakan token Sanctum dengan mekanisme expiry
- Setiap endpoint API dilindungi middleware auth dan permission sesuai role
- Rate limiting pada endpoint login: maksimal 5 percobaan per menit
- Seluruh input divalidasi di sisi server sebelum diproses
- Konfigurasi sensitif disimpan di `.env` dan tidak di-expose
- Debug mode dimatikan di environment production
- Seluruh komunikasi menggunakan HTTPS (SSL)
- Cloudflare digunakan sebagai lapisan keamanan tambahan (DDoS protection, CDN)

### 11.3 Usability

- UI harus responsif untuk desktop, tablet, dan mobile
- Halaman POS harus dapat dioperasikan dengan cepat dan minim klik
- Tampilan kitchen/bar harus terbaca jelas dari jarak pandang normal
- Notifikasi error dan sukses harus informatif dan mudah dimengerti
- Form yang panjang harus dibagi menjadi step yang logis

### 11.4 Reliability

- Transaksi yang sudah terkonfirmasi tidak boleh hilang
- Sistem harus mencegah double-booking pada meja yang sama di slot waktu yang sama
- Proses print harus dicatat di `order_print_logs` untuk keperluan audit
- Queue digunakan untuk proses async agar tidak memblokir respons utama

### 11.5 Maintainability

- Struktur database harus modular dan terdokumentasi
- Kode backend mengikuti prinsip Repository Pattern dan Service Layer
- Komponen React dibuat reusable dan terdokumentasi dengan PropTypes atau TypeScript
- Setiap fitur utama memiliki minimal satu unit test atau feature test

### 11.6 Scalability

- Struktur API dirancang RESTful dan stateless sehingga mudah di-scale horizontal
- Redis digunakan untuk session dan cache agar tidak bergantung pada file system
- Export Excel dijalankan via queue agar tidak membebani server utama

---

## 12. Desain Database

### 12.1 Entitas Utama

#### `users`

| Kolom      | Tipe         | Keterangan |
| ---------- | ------------ | ---------- |
| id         | CHAR(36)     | UUID, PK   |
| email      | VARCHAR(255) | Unique     |
| password   | VARCHAR(255) | Bcrypt     |
| created_at | TIMESTAMP    |            |
| updated_at | TIMESTAMP    |            |

#### `profiles`

| Kolom     | Tipe         | Keterangan                        |
| --------- | ------------ | --------------------------------- |
| id        | INT AI PK    |                                   |
| user_id   | CHAR(36)     | FK → users                        |
| name      | VARCHAR(255) |                                   |
| phone     | VARCHAR(20)  |                                   |
| role      | ENUM         | admin, kasir, kitchen, bar, owner |
| is_active | TINYINT(1)   | Default 1                         |

#### `customers`

| Kolom      | Tipe         | Keterangan |
| ---------- | ------------ | ---------- |
| id         | INT AI PK    |            |
| name       | VARCHAR(255) |            |
| phone      | VARCHAR(20)  |            |
| created_at | TIMESTAMP    |            |

#### `tables`

| Kolom    | Tipe        | Keterangan                    |
| -------- | ----------- | ----------------------------- |
| id       | INT AI PK   |                               |
| number   | VARCHAR(10) | Unique                        |
| capacity | INT         |                               |
| status   | ENUM        | available, occupied, reserved |

#### `categories`

| Kolom      | Tipe         | Keterangan |
| ---------- | ------------ | ---------- |
| id         | INT AI PK    |            |
| name       | VARCHAR(255) |            |
| icon       | VARCHAR(255) | Nullable   |
| sort_order | INT          | Default 0  |
| is_active  | TINYINT(1)   | Default 1  |

#### `products`

| Kolom       | Tipe         | Keterangan      |
| ----------- | ------------ | --------------- |
| id          | INT AI PK    |                 |
| category_id | INT          | FK → categories |
| name        | VARCHAR(255) |                 |
| description | TEXT         | Nullable        |
| price       | BIGINT       | Dalam Rupiah    |
| type        | ENUM         | food, drink     |
| image       | VARCHAR(255) | Nullable        |
| is_active   | TINYINT(1)   | Default 1       |

#### `product_variants`

| Kolom       | Tipe         | Keterangan               |
| ----------- | ------------ | ------------------------ |
| id          | INT AI PK    |                          |
| product_id  | INT          | FK → products            |
| name        | VARCHAR(255) | Contoh: Ukuran, Level Es |
| is_required | TINYINT(1)   | Default 0                |

#### `variant_options`

| Kolom            | Tipe         | Keterangan                   |
| ---------------- | ------------ | ---------------------------- |
| id               | INT AI PK    |                              |
| variant_id       | INT          | FK → product_variants        |
| name             | VARCHAR(255) | Contoh: Small, Medium, Large |
| price_adjustment | BIGINT       | Default 0                    |

#### `product_addons`

| Kolom      | Tipe         | Keterangan    |
| ---------- | ------------ | ------------- |
| id         | INT AI PK    |               |
| product_id | INT          | FK → products |
| name       | VARCHAR(255) |               |
| price      | BIGINT       |               |
| is_active  | TINYINT(1)   | Default 1     |

#### `reservations`

| Kolom          | Tipe         | Keterangan                                      |
| -------------- | ------------ | ----------------------------------------------- |
| id             | INT AI PK    |                                                 |
| customer_name  | VARCHAR(255) |                                                 |
| phone          | VARCHAR(20)  |                                                 |
| table_id       | INT          | FK → tables                                     |
| guest_count    | INT          |                                                 |
| reserved_at    | DATETIME     | Waktu booking                                   |
| expired_at     | DATETIME     | reserved_at + 60 menit                          |
| notes          | TEXT         | Nullable                                        |
| dp_amount      | BIGINT       | Default 20000                                   |
| dp_status      | ENUM         | unpaid, paid, refunded                          |
| payment_method | ENUM         | cash, qris, transfer; Nullable                  |
| status         | ENUM         | pending, confirmed, arrived, expired, cancelled |
| created_at     | TIMESTAMP    |                                                 |

#### `vouchers`

| Kolom          | Tipe        | Keterangan                     |
| -------------- | ----------- | ------------------------------ |
| id             | INT AI PK   |                                |
| code           | VARCHAR(50) | Unique                         |
| type           | ENUM        | fixed, percent                 |
| discount_value | BIGINT      | Nominal atau persentase        |
| min_purchase   | BIGINT      | Default 0                      |
| max_discount   | BIGINT      | Nullable (untuk tipe percent)  |
| quota          | INT         | Nullable (unlimited jika null) |
| used_count     | INT         | Default 0                      |
| expired_at     | DATETIME    |                                |
| is_active      | TINYINT(1)  | Default 1                      |

#### `transactions`

| Kolom             | Tipe         | Keterangan                                      |
| ----------------- | ------------ | ----------------------------------------------- |
| id                | INT AI PK    |                                                 |
| invoice           | VARCHAR(50)  | Unique, auto-generate                           |
| customer_id       | INT          | FK → customers; Nullable                        |
| customer_name     | VARCHAR(255) | Snapshot                                        |
| table_id          | INT          | FK → tables; Nullable                           |
| table_number      | VARCHAR(10)  | Snapshot                                        |
| cashier_id        | CHAR(36)     | FK → users                                      |
| voucher_id        | INT          | FK → vouchers; Nullable                         |
| subtotal          | BIGINT       | Sebelum diskon                                  |
| discount_amount   | BIGINT       | Default 0                                       |
| tax_amount        | BIGINT       | Default 0                                       |
| grand_total       | BIGINT       | Final                                           |
| payment_method    | ENUM         | cash, qris                                      |
| payment_amount    | BIGINT       | Nominal diterima (cash)                         |
| change_amount     | BIGINT       | Kembalian (cash)                                |
| payment_reference | VARCHAR(255) | QRIS ref; Nullable                              |
| payment_status    | ENUM         | unpaid, paid, failed                            |
| status            | ENUM         | pending, processing, paid, completed, cancelled |
| notes             | TEXT         | Nullable                                        |
| created_at        | TIMESTAMP    |                                                 |
| updated_at        | TIMESTAMP    |                                                 |

#### `transaction_items`

| Kolom          | Tipe         | Keterangan                   |
| -------------- | ------------ | ---------------------------- |
| id             | INT AI PK    |                              |
| transaction_id | INT          | FK → transactions            |
| product_id     | INT          | FK → products; Nullable      |
| product_name   | VARCHAR(255) | Snapshot                     |
| product_type   | ENUM         | food, drink (snapshot)       |
| variant_info   | JSON         | Snapshot varian yang dipilih |
| addon_info     | JSON         | Snapshot add-on yang dipilih |
| qty            | INT          |                              |
| unit_price     | BIGINT       | Harga satuan (snapshot)      |
| subtotal       | BIGINT       | unit_price × qty             |
| notes          | TEXT         | Catatan item; Nullable       |

#### `stock_items`

| Kolom        | Tipe          | Keterangan                |
| ------------ | ------------- | ------------------------- |
| id           | INT AI PK     |                           |
| name         | VARCHAR(255)  |                           |
| unit         | VARCHAR(20)   | gram, ml, pcs, dll        |
| quantity     | DECIMAL(10,2) | Stok saat ini             |
| min_quantity | DECIMAL(10,2) | Batas warning stok kritis |
| created_at   | TIMESTAMP     |                           |

#### `stock_movements`

| Kolom         | Tipe          | Keterangan                    |
| ------------- | ------------- | ----------------------------- |
| id            | INT AI PK     |                               |
| stock_item_id | INT           | FK → stock_items              |
| type          | ENUM          | in, out, adjustment           |
| quantity      | DECIMAL(10,2) |                               |
| reference     | VARCHAR(255)  | Nomor invoice atau keterangan |
| notes         | TEXT          | Nullable                      |
| created_at    | TIMESTAMP     |                               |

#### `product_ingredients`

| Kolom         | Tipe          | Keterangan                |
| ------------- | ------------- | ------------------------- |
| id            | INT AI PK     |                           |
| product_id    | INT           | FK → products             |
| stock_item_id | INT           | FK → stock_items          |
| quantity      | DECIMAL(10,2) | Jumlah bahan per 1 produk |

### 12.2 Tabel Pendukung

#### `order_print_logs`

| Kolom          | Tipe      | Keterangan            |
| -------------- | --------- | --------------------- |
| id             | INT AI PK |                       |
| transaction_id | INT       | FK → transactions     |
| print_type     | ENUM      | invoice, kitchen, bar |
| printed_at     | TIMESTAMP |                       |

#### `daily_sales_summary`

| Kolom              | Tipe      | Keterangan |
| ------------------ | --------- | ---------- |
| id                 | INT AI PK |            |
| date               | DATE      | Unique     |
| total_transactions | INT       |            |
| total_revenue      | BIGINT    |            |
| total_tax          | BIGINT    |            |
| total_discount     | BIGINT    |            |
| created_at         | TIMESTAMP |            |

#### `export_logs`

| Kolom        | Tipe         | Keterangan                    |
| ------------ | ------------ | ----------------------------- |
| id           | INT AI PK    |                               |
| user_id      | CHAR(36)     | FK → users                    |
| type         | ENUM         | sales, transactions, products |
| period_month | INT          |                               |
| period_year  | INT          |                               |
| file_path    | VARCHAR(255) |                               |
| created_at   | TIMESTAMP    |                               |

#### `notifications`

| Kolom      | Tipe         | Keterangan                                 |
| ---------- | ------------ | ------------------------------------------ |
| id         | INT AI PK    |                                            |
| user_id    | CHAR(36)     | FK → users                                 |
| type       | ENUM         | order, stock, reservation, system, payment |
| title      | VARCHAR(255) |                                            |
| message    | TEXT         |                                            |
| is_read    | TINYINT(1)   | Default 0                                  |
| created_at | TIMESTAMP    |                                            |

### 12.3 Index Database

```sql
-- Transaksi
CREATE INDEX idx_transactions_created_at ON transactions(created_at);
CREATE INDEX idx_transactions_status ON transactions(status);
CREATE INDEX idx_transactions_payment_method ON transactions(payment_method);

-- Transaction items
CREATE INDEX idx_transaction_items_transaction ON transaction_items(transaction_id);
CREATE INDEX idx_transaction_items_product ON transaction_items(product_id);

-- Reservasi
CREATE INDEX idx_reservations_reserved_at ON reservations(reserved_at);
CREATE INDEX idx_reservations_status ON reservations(status);
CREATE INDEX idx_reservations_table ON reservations(table_id);

-- Stok
CREATE INDEX idx_stock_movements_item ON stock_movements(stock_item_id);
CREATE INDEX idx_stock_movements_created ON stock_movements(created_at);
```

---

## 13. Desain API (REST)

### 13.1 Auth

| Method | Endpoint         | Deskripsi       | Auth |
| ------ | ---------------- | --------------- | ---- |
| POST   | /api/auth/login  | Login user      | No   |
| POST   | /api/auth/logout | Logout user     | Yes  |
| GET    | /api/auth/me     | Data user aktif | Yes  |

### 13.2 Public (Guest)

| Method | Endpoint                 | Deskripsi            | Auth |
| ------ | ------------------------ | -------------------- | ---- |
| GET    | /api/public/categories   | Daftar kategori      | No   |
| GET    | /api/public/products     | Daftar produk publik | No   |
| GET    | /api/public/store        | Info profil kafe     | No   |
| POST   | /api/public/reservations | Buat reservasi baru  | No   |

### 13.3 POS

| Method | Endpoint                         | Deskripsi                              | Auth  |
| ------ | -------------------------------- | -------------------------------------- | ----- |
| GET    | /api/pos/tables                  | Daftar meja beserta status             | kasir |
| GET    | /api/pos/categories              | Kategori menu aktif                    | kasir |
| GET    | /api/pos/products                | Produk aktif beserta varian dan add-on | kasir |
| GET    | /api/pos/vouchers/check          | Validasi kode voucher                  | kasir |
| POST   | /api/pos/transactions            | Buat transaksi baru                    | kasir |
| GET    | /api/pos/transactions            | Riwayat transaksi hari ini             | kasir |
| POST   | /api/pos/transactions/{id}/print | Trigger print ulang                    | kasir |

### 13.4 Kitchen / Bar

| Method | Endpoint                        | Deskripsi                   | Auth    |
| ------ | ------------------------------- | --------------------------- | ------- |
| GET    | /api/kitchen/orders             | Daftar order makanan        | kitchen |
| PATCH  | /api/kitchen/orders/{id}/status | Update status order makanan | kitchen |
| GET    | /api/bar/orders                 | Daftar order minuman        | bar     |
| PATCH  | /api/bar/orders/{id}/status     | Update status order minuman | bar     |

### 13.5 Admin

| Method | Endpoint                              | Deskripsi                | Auth  |
| ------ | ------------------------------------- | ------------------------ | ----- |
| GET    | /api/admin/dashboard/summary          | Data ringkasan dashboard | admin |
| GET    | /api/admin/dashboard/chart/sales      | Data grafik penjualan    | admin |
| GET    | /api/admin/dashboard/chart/products   | Data produk terlaris     | admin |
| GET    | /api/admin/dashboard/chart/payment    | Data metode pembayaran   | admin |
| GET    | /api/admin/dashboard/chart/peak-hours | Data jam ramai           | admin |
| GET    | /api/admin/reports/export             | Export Excel bulanan     | admin |
| GET    | /api/admin/products                   | CRUD Produk              | admin |
| POST   | /api/admin/products                   |                          | admin |
| PUT    | /api/admin/products/{id}              |                          | admin |
| DELETE | /api/admin/products/{id}              |                          | admin |
| GET    | /api/admin/categories                 | CRUD Kategori            | admin |
| GET    | /api/admin/vouchers                   | CRUD Voucher             | admin |
| GET    | /api/admin/tables                     | CRUD Meja                | admin |
| GET    | /api/admin/reservations               | Kelola Reservasi         | admin |
| GET    | /api/admin/stock                      | Kelola Stok              | admin |
| GET    | /api/admin/users                      | Kelola User              | admin |

---

## 14. Desain UI / UX

### 14.1 Design System

- **Color Palette**: Warna warm (krem, coklat, hijau tua) untuk nuansa kafe yang hangat
- **Typography**: Font Inter (body) + Playfair Display (heading) untuk kesan premium
- **Komponen**: shadcn/ui sebagai base, dikustomisasi dengan Tailwind CSS
- **Dark Mode**: Didukung via Tailwind dark variant (opsional, prioritas menengah)
- **Responsive Breakpoint**: Mobile (< 768px), Tablet (768px – 1024px), Desktop (> 1024px)

### 14.2 Halaman Guest

- **Home**: Hero section full-screen dengan foto kafe, tagline, dan tombol CTA (Lihat Menu / Reservasi)
- **Menu**: Grid produk dengan sidebar filter kategori, kartu produk dengan foto dan harga
- **About**: Cerita kafe, foto tim, dan nilai-nilai bisnis
- **Gallery**: Masonry grid foto kafe
- **Reservasi**: Form dua langkah: (1) pilih tanggal, jam, jumlah tamu, meja; (2) isi data diri dan catatan
- **Floating Button**: Tombol WhatsApp di pojok kanan bawah

### 14.3 Halaman POS (Kasir)

```
┌─────────────────────────────────────────────────────────┐
│  HEADER: Logo | Nama Kasir | Jam | Meja Aktif           │
├──────────────┬──────────────────────────┬───────────────┤
│  SIDEBAR     │  PRODUCT GRID            │  CART         │
│  Kategori    │  [Foto] Nama Produk      │  Customer     │
│  (Icon list) │  Rp harga                │  Meja         │
│              │  [Foto] Nama Produk      │  ─────────    │
│              │  Rp harga                │  Item 1       │
│              │  [Foto] Nama Produk      │  Item 2       │
│              │  Rp harga                │  ─────────    │
│              │                          │  Subtotal     │
│              │                          │  Diskon       │
│              │                          │  Pajak        │
│              │                          │  Total        │
│              │                          │  [BAYAR]      │
└──────────────┴──────────────────────────┴───────────────┘
```

### 14.4 Halaman Kitchen / Bar Display

```
┌─────────────────────────────────────────────────────────┐
│  HEADER: Kitchen Orders | [Filter: All / New / Done]    │
├──────────────┬──────────────┬──────────────┬────────────┤
│  ORDER CARD  │  ORDER CARD  │  ORDER CARD  │  ...       │
│  Meja: A1    │  Meja: B3    │  Meja: C2    │            │
│  John        │  Sarah       │  Mike        │            │
│  ─────────── │  ─────────── │  ─────────── │            │
│  • Nasi Goreng│  • Ayam Bakar│  • Mie Goreng│            │
│    1x        │    2x        │    1x        │            │
│  • Gado-Gado │  ─────────── │  ─────────── │            │
│    1x        │  [PREPARING] │  [NEW →]     │            │
│  ─────────── │              │  Preparing   │            │
│  [NEW →]     │              │              │            │
│  Preparing   │              │              │            │
└──────────────┴──────────────┴──────────────┴────────────┘
```

### 14.5 Halaman Admin Dashboard

```
┌─────────────────────────────────────────────────────────┐
│  SIDEBAR: Logo | Menu Nav                               │
├─────────────────────────────────────────────────────────┤
│  HEADER: Dashboard | Filter Periode | Export Excel      │
├──────────┬──────────┬──────────┬──────────┐            │
│ Revenue  │ Transaksi│ Rata-rata│ Produk   │            │
│ Hari ini │ Hari ini │ / Trx    │ Terjual  │            │
├──────────┴──────────┴──────────┴──────────┘            │
│  [Line Chart: Penjualan 30 Hari Terakhir]               │
├─────────────────────────┬───────────────────────────────┤
│  [Bar Chart: Top 10     │  [Pie Chart: Metode Bayar]    │
│   Produk Terlaris]      │                               │
├─────────────────────────┴───────────────────────────────┤
│  [Bar Chart: Jam Ramai] │  [Tabel: Transaksi Terbaru]   │
└─────────────────────────┴───────────────────────────────┘
```

---

## 15. Cron Job & Background Job

### 15.1 Cron Job Laravel Scheduler

```php
// Auto-expire reservasi (setiap menit)
$schedule->command('reservations:expire')->everyMinute();

// Update daily_sales_summary (setiap tengah malam)
$schedule->command('sales:summarize')->dailyAt('00:05');

// Bersihkan export log lama (setiap minggu)
$schedule->command('exports:clean')->weekly();
```

### 15.2 Query Auto-Expire Reservasi

```sql
UPDATE reservations
SET status = 'expired'
WHERE status = 'confirmed'
AND NOW() > expired_at;
```

### 15.3 Queue Jobs

| Job                 | Trigger             | Keterangan                       |
| ------------------- | ------------------- | -------------------------------- |
| PrintInvoiceJob     | Transaksi completed | Print invoice ke thermal printer |
| PrintKitchenJob     | Transaksi completed | Print order makanan              |
| PrintBarJob         | Transaksi completed | Print order minuman              |
| ExportSalesJob      | Admin klik export   | Generate file Excel dan simpan   |
| DeductStockJob      | Transaksi completed | Kurangi stok bahan baku          |
| SendNotificationJob | Berbagai trigger    | Kirim notifikasi ke user         |

---

## 16. Security Implementation

### 16.1 Authentication Flow

```
1. User POST /api/auth/login dengan email + password
2. Laravel validasi kredensial
3. Jika valid → generate Sanctum token
4. Token dikembalikan ke React
5. React simpan token di memory (Zustand store)
   atau httpOnly cookie (lebih aman)
6. Setiap request selanjutnya sertakan:
   Authorization: Bearer {token}
7. Middleware auth:sanctum memvalidasi token
8. Middleware permission cek role untuk endpoint tertentu
```

### 16.2 Role & Permission Matrix

| Fitur           | Guest | Kasir | Kitchen | Bar | Admin | Owner |
| --------------- | ----- | ----- | ------- | --- | ----- | ----- |
| Halaman publik  | ✅    | ✅    | ✅      | ✅  | ✅    | ✅    |
| Form reservasi  | ✅    | ✅    | —       | —   | ✅    | ✅    |
| POS transaksi   | —     | ✅    | —       | —   | ✅    | —     |
| Kitchen display | —     | —     | ✅      | —   | ✅    | —     |
| Bar display     | —     | —     | —       | ✅  | ✅    | —     |
| Dashboard       | —     | —     | —       | —   | ✅    | ✅    |
| Master data     | —     | —     | —       | —   | ✅    | —     |
| Export Excel    | —     | —     | —       | —   | ✅    | ✅    |

### 16.3 Input Validation

Semua endpoint API memvalidasi input di backend menggunakan Laravel Form Request:

```php
// Contoh validasi transaksi
'customer_name' => 'required|string|max:255',
'table_id'      => 'nullable|exists:tables,id',
'items'         => 'required|array|min:1',
'items.*.product_id' => 'required|exists:products,id',
'items.*.qty'   => 'required|integer|min:1',
'payment_method' => 'required|in:cash,qris',
```

---

## 17. Testing Plan

### 17.1 Backend (Laravel)

| Test                     | Tipe    | Keterangan     |
| ------------------------ | ------- | -------------- |
| Login berhasil dan gagal | Feature | Auth flow      |
| Buat transaksi valid     | Feature | POS core       |
| Validasi voucher         | Unit    | Voucher logic  |
| Auto-expire reservasi    | Unit    | Cron logic     |
| Kalkulasi grand total    | Unit    | Pricing logic  |
| Permission per role      | Feature | Access control |

### 17.2 Frontend (React)

| Test                    | Tipe        | Keterangan            |
| ----------------------- | ----------- | --------------------- |
| Render komponen cart    | Unit        | React Testing Library |
| Kalkulasi total di UI   | Unit        | Zustand store         |
| Form validasi reservasi | Integration |                       |
| Flow pembayaran cash    | E2E         | Playwright (opsional) |

---

## 18. Deployment & Infrastruktur

### 18.1 Environment

| Environment | Keterangan                                 |
| ----------- | ------------------------------------------ |
| Development | Lokal dengan Docker atau Laravel Herd      |
| Staging     | VPS kecil untuk testing sebelum production |
| Production  | VPS dengan Nginx, PHP-FPM, MySQL, Redis    |

### 18.2 Setup Production

```
OS          : Ubuntu 22.04 LTS
Web Server  : Nginx 1.24
PHP         : PHP 8.2 + FPM
Database    : MySQL 8.0
Cache/Queue : Redis 7
SSL         : Let's Encrypt (via Certbot)
CDN/Security: Cloudflare (Free plan)
```

### 18.3 Struktur Folder

```
/
├── backend/          (Laravel 11)
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   └── Jobs/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/api.php
│
└── frontend/         (React + Vite)
    ├── src/
    │   ├── components/
    │   │   ├── ui/         (shadcn/ui)
    │   │   ├── common/     (reusable)
    │   │   └── layout/
    │   ├── pages/
    │   │   ├── public/
    │   │   ├── pos/
    │   │   ├── kitchen/
    │   │   ├── bar/
    │   │   └── admin/
    │   ├── stores/         (Zustand)
    │   ├── hooks/          (custom hooks)
    │   ├── services/       (Axios API calls)
    │   └── utils/
    └── vite.config.js
```

---

## 19. Prioritas Pengembangan

### Sprint 1 — Core Foundation (Minggu 1–2)

- Setup project Laravel + React + Vite
- Konfigurasi database dan migrasi semua tabel
- Sistem autentikasi dan role permission
- CRUD master data: kategori, produk, meja, user

### Sprint 2 — Guest & Reservasi (Minggu 3–4)

- Halaman publik: Home, Menu, About, Gallery
- Form reservasi dan logika validasi meja
- Cron job auto-expire reservasi
- Admin panel: kelola reservasi

### Sprint 3 — POS Core (Minggu 5–6)

- Tampilan POS kasir (sidebar + grid + cart)
- Flow order: kategori → produk → varian → add-on → cart
- Validasi dan penggunaan voucher
- Kalkulasi harga (subtotal, diskon, pajak, total, kembalian)

### Sprint 4 — Pembayaran & Print (Minggu 7–8)

- Flow pembayaran cash dan QRIS
- Integrasi thermal printer (ESC/POS)
- Print invoice, kitchen order, bar order
- Pencatatan print log

### Sprint 5 — Kitchen & Bar Display (Minggu 9)

- Tampilan kitchen display (real-time polling)
- Tampilan bar display (real-time polling)
- Update status order per item

### Sprint 6 — Dashboard & Laporan (Minggu 10–11)

- Summary card dashboard
- Seluruh grafik: penjualan, produk terlaris, metode bayar, jam ramai
- Filter periode dashboard
- Export Excel bulanan

### Sprint 7 — Polish & Testing (Minggu 12)

- Manajemen stok dan auto-deduction
- Notifikasi stok kritis
- Testing menyeluruh
- Optimasi performa dan security hardening
- Deployment ke VPS production

---

## 20. Risiko dan Mitigasi

| No  | Risiko                                | Dampak | Mitigasi                                                    |
| --- | ------------------------------------- | ------ | ----------------------------------------------------------- |
| 1   | Reservasi bentrok pada meja yang sama | Tinggi | Validasi ketersediaan meja dengan query lock sebelum simpan |
| 2   | Order terlambat diproses kitchen/bar  | Sedang | Real-time polling + notifikasi audio saat order baru masuk  |
| 3   | Kesalahan input kasir                 | Sedang | Validasi form sebelum submit + konfirmasi pembayaran        |
| 4   | Laporan tidak akurat                  | Tinggi | Dashboard dan export hanya dari transaksi `completed`       |
| 5   | Print gagal                           | Sedang | Pencatatan print log + tombol print ulang manual            |
| 6   | Stok minus karena deduction error     | Sedang | Gunakan database transaction + rollback jika gagal          |
| 7   | N+1 query membebani server            | Sedang | Wajib eager loading + caching Redis untuk query berat       |
| 8   | Token auth bocor                      | Tinggi | Gunakan httpOnly cookie + HTTPS + Cloudflare                |

---

## 21. KPI Keberhasilan Sistem

| KPI                       | Target                          |
| ------------------------- | ------------------------------- |
| Waktu input order kasir   | < 60 detik per transaksi        |
| Akurasi laporan penjualan | 100% sesuai transaksi completed |
| Reservasi tidak bentrok   | 0 double-booking                |
| Waktu load dashboard      | < 3 detik                       |
| Uptime sistem             | > 99%                           |
| Order kitchen/bar tepat   | 0 salah distribusi tipe produk  |

---

## 22. Acceptance Criteria

### Reservasi

- ✅ Customer dapat mengisi form dan menyimpan reservasi dengan DP
- ✅ Sistem mencegah double-booking pada meja dan slot waktu yang sama
- ✅ Status reservasi otomatis berubah jadi `expired` setelah 1 jam dari jam booking
- ✅ Admin dapat melihat dan mengelola semua data reservasi

### POS Kasir

- ✅ Kasir dapat membuat transaksi lengkap dari input item hingga cetak
- ✅ Voucher tervalidasi sesuai aturan (kode, expired, kuota, minimum)
- ✅ Cash dan QRIS berfungsi sesuai alur masing-masing
- ✅ Invoice, order kitchen, dan order bar tercetak otomatis

### Kitchen / Bar

- ✅ Item makanan hanya muncul di kitchen, item minuman hanya di bar
- ✅ Nomor meja dan nama customer tampil di setiap order
- ✅ Staff dapat memperbarui status order
- ✅ Tampilan diperbarui tanpa refresh manual

### Admin Dashboard

- ✅ Seluruh grafik menampilkan data yang akurat
- ✅ Filter periode berfungsi untuk semua grafik
- ✅ Export Excel berhasil mengunduh file laporan bulanan
- ✅ Seluruh master data dapat dikelola (CRUD)

---

## 23. Ringkasan Workflow Final

```
Guest membuka website
↓
Melihat Menu / About / Gallery
↓
Booking meja (form + DP) atau langsung datang
       ↓
   [Cron Job berjalan setiap menit]
   Jika lewat 1 jam → status: expired
↓
Kasir login → buka sesi POS
↓
Input customer + pilih meja
↓
Pilih kategori → produk → varian → add-on → kuantitas
↓
Input voucher (opsional) → sistem hitung total
↓
Pilih metode bayar:
  [Cash] → input nominal → hitung kembalian
  [QRIS] → konfirmasi pembayaran
↓
Transaksi disimpan (status: completed)
↓
[Background Jobs berjalan via Queue]
  → Print invoice ke printer thermal
  → Print order food ke kitchen printer
  → Print order drink ke bar printer
  → Deduct stok bahan baku
↓
Kitchen/Bar melihat order real-time
↓
Staff update status: new → preparing → done
↓
Admin login → buka dashboard
↓
Melihat grafik penjualan, produk terlaris, jam ramai
↓
Filter periode → Export Excel bulanan
```

---

## 24. Catatan Implementasi

Dokumen ini dapat dijadikan dasar untuk:

- Analisis kebutuhan sistem
- Activity diagram dan use case diagram
- Sequence diagram per fitur
- Desain dan implementasi database MySQL
- Implementasi backend Laravel 11 (REST API)
- Implementasi frontend React 18 + Vite + Tailwind CSS
- Penulisan Bab II (Landasan Teori) dan Bab III (Analisis dan Perancangan Sistem) skripsi
- Pembuatan proposal dan presentasi sistem

## 25. olor

tampilan tetap menggunakan sistem desain Vibrant Citric Professional dengan dominasi warna kuning-orange yang energik

## 26. Kesimpulan

Sistem Manajemen Kafe ini merupakan solusi terintegrasi yang menggabungkan company profile digital, sistem reservasi berbasis DP dengan auto-expire, POS kasir modern, kitchen dan bar display real-time, serta dashboard analitik lengkap dengan fitur export laporan.

Sistem dibangun dengan arsitektur Semi-Decoupled Monolith menggunakan **React 18 + Vite** sebagai frontend modern yang responsif dan **Laravel 11** sebagai backend REST API yang kokoh, dengan database MySQL, caching Redis, dan keamanan berlapis melalui Cloudflare dan Sanctum authentication.

Dengan kombinasi ini, sistem setara dengan aplikasi POS industri skala menengah, dapat dioperasikan secara real-time, aman, dan mudah dikembangkan lebih lanjut.
