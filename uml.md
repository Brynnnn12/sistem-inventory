# Manual Aplikasi GudangKu — Dokumentasi UML & Alur Sistem

> **Aplikasi**: GudangKu — Manajemen Stok Gudang
> **Stack**: Laravel 12 + React 19 + Inertia v2 (SPA) + Tailwind v4 + shadcn/ui
> **Tanggal**: 8 Juni 2026

---

## Daftar Isi

1. [Tentang Aplikasi](#1-tentang-aplikasi)
2. [Aktor & Hak Akses](#2-aktor--hak-akses)
3. [Use Case / Fitur Sistem](#3-use-case--fitur-sistem)
4. [Struktur Database (ERD & LRS)](#4-struktur-database-erd--lrs)
5. [Struktur Kode (Class Diagram)](#5-struktur-kode-class-diagram)
6. [Alur Login](#6-alur-login)
7. [Alur Barang Masuk (Inbound)](#7-alur-barang-masuk-inbound)
8. [Alur Barang Keluar (Outbound)](#8-alur-barang-keluar-outbound)
9. [Alur Mutasi Antar Gudang](#9-alur-mutasi-antar-gudang)
10. [Alur Opname Stok](#10-alur-opname-stok)
11. [Alur Laporan & Dashboard](#11-alur-laporan--dashboard)

---

## 1. Tentang Aplikasi

GudangKu adalah aplikasi berbasis web untuk mengelola stok barang di gudang.
Aplikasi ini dipakai oleh 3 jenis pengguna:

- **Super Admin**: Bisa mengakses SEMUA fitur, termasuk mengatur data master (produk, kategori, supplier, dll)
- **Admin**: Bisa transaksi barang masuk/keluar, mutasi, opname, lihat laporan — tapi TIDAK bisa akses data master
- **Viewer**: Hanya bisa melihat stok dan riwayat — TIDAK bisa melakukan perubahan apa pun

Setiap halaman di aplikasi ini menggunakan pola yang sama:
- Ada toolbar untuk mencari dan menyaring data
- Ada tabel untuk menampilkan data
- Ada modal/dialog untuk tambah, edit, lihat, atau hapus data
- Ada navigasi halaman (pagination) kalau data banyak

---

## 2. Aktor & Hak Akses

### 2.1 Super Admin

| Bisa | Tidak Bisa |
|------|------------|
| Login, lihat dashboard, atur profil/password/tema/2FA | - |
| CRUD Kategori, Produk, Supplier, Customer, Gudang, Karyawan | - |
| Buat transaksi Barang Masuk, Barang Keluar, Mutasi, Opname | - |
| Lihat Riwayat Stok, Stok Tersedia, semua Laporan | - |
| Export laporan ke Excel/PDF | - |
| Atur penempatan staf ke gudang | - |

**Role di sistem**: `super-admin`

### 2.2 Admin

| Bisa | Tidak Bisa |
|------|------------|
| Login, lihat dashboard, atur profil/password/tema | CRUD data master (produk, kategori, dll) |
| Buat transaksi Barang Masuk, Barang Keluar, Mutasi, Opname | Kelola karyawan & penempatan staf |
| Lihat Riwayat Stok, Stok Tersedia, semua Laporan | Export laporan |
| Hanya bisa akses gudang yang ditugaskan ke dirinya | - |

**Role di sistem**: `admin`

### 2.3 Viewer

| Bisa | Tidak Bisa |
|------|------------|
| Login, lihat dashboard, atur profil/password/tema | Semua perubahan data |
| Lihat Riwayat Stok | Buat transaksi apa pun |
| Lihat Stok Tersedia | Akses data master |
| Lihat laporan (read-only) | - |

**Role di sistem**: `viewer`

---

## 3. Use Case / Fitur Sistem

Berikut penjelasan setiap fitur yang tersedia:

### 3.1 Fitur Umum (Semua Role)

| Use Case | Penjelasan |
|----------|------------|
| **Login** | Masuk ke sistem pakai email dan password. Ada proteksi rate limiter (3×/menit, 10×/jam). Bisa login via Google. |
| **Dashboard** | Halaman utama setelah login. Menampilkan ringkasan: total produk, gudang, stok rendah, stok habis, grafik bulanan, transaksi terbaru, dan peringatan stok. |
| **Atur Profil** | Ubah nama, email, nomor telepon. |
| **Atur Password** | Ganti password lama dengan yang baru. |
| **Atur Tema** | Ganti tema terang/gelap/sistem. |
| **2FA** | Aktifkan/nonaktifkan autentikasi dua faktor. |

### 3.2 Fitur Operasional (Super Admin & Admin)

| Use Case | Penjelasan |
|----------|------------|
| **Barang Masuk** | Mencatat penerimaan barang dari supplier ke gudang. Otomatis nambah stok dan catat riwayat. |
| **Barang Keluar** | Mencatat pengeluaran barang ke customer. Cek ketersediaan stok dulu, baru kurangi stok. |
| **Mutasi Antar Gudang** | Pindahin barang dari satu gudang ke gudang lain. Status: dikirim → diterima/ditolak. |
| **Opname Stok** | Cocokkan stok sistem dengan stok fisik. Buat draft → approve → otomatis sesuaikan stok. |
| **Riwayat Stok** | Lihat semua perubahan stok yang pernah terjadi, lengkap dengan referensi transaksinya. |
| **Stok Tersedia** | Lihat stok terkini per produk per gudang. |

### 3.3 Fitur Laporan (Semua Role)

| Use Case | Penjelasan |
|----------|------------|
| **Laporan Stok** | Rekap stok semua produk, bisa filter per gudang dan periode. |
| **Laporan Transaksi** | Rekap semua transaksi (inbound, outbound, mutasi) dalam periode tertentu. |
| **Laporan Peringatan** | Produk yang stoknya rendah atau habis. |

### 3.4 Fitur Master Data (Super Admin Only)

| Use Case | Penjelasan |
|----------|------------|
| **Kelola Kategori** | Tambah/edit/hapus kategori produk. Kalau dihapus (soft delete), namanya gak bisa dipakai lagi. |
| **Kelola Produk** | Tambah/edit/hapus produk. Setiap produk punya kode unik, kategori, harga beli (cost), harga jual (price), stok minimal/maksimal. |
| **Kelola Supplier** | Data pemasok barang. Gak bisa dihapus kalau masih punya transaksi inbound. |
| **Kelola Customer** | Data pembeli barang. Gak bisa dihapus kalau masih punya transaksi outbound. |
| **Kelola Gudang** | Data gudang. Gak bisa dihapus kalau masih ada staf yang ditugaskan. |
| **Kelola Karyawan** | Tambah/edit/hapus pengguna (admin/viewer). Pengguna baru bakal dapat email verifikasi. |
| **Penugasan Staf** | Atur staf mana yang bekerja di gudang mana. Bisa tukar penempatan antar staf. |

### Diagram Use Case (Untuk Referensi)

```
Aktor: Super Admin, Admin, Viewer
Setiap aktor punya akses ke fitur tertentu (lihat tabel di atas).
Fitur Login adalah pintu masuk untuk semua aktor.
Setelah login, tampilan sidebar akan menyesuaikan role masing-masing.
```

---

## 4. Struktur Database (ERD & LRS)

### 4.1 Penjelasan Entitas

Aplikasi punya **15 tabel utama** yang saling berelasi. Berikut penjelasan masing-masing:

#### Entitas Master

| Tabel | Fungsi | Soft Delete? |
|-------|--------|-------------|
| **users** | Data pengguna (super admin, admin, viewer). Relasi ke role via Spatie Permission. | ✅ Iya |
| **categories** | Kategori produk, misal: Elektronik, Makanan, Pakaian. Pakai slug otomatis. | ✅ Iya |
| **products** | Data produk: kode, nama, unit, harga, stok minimal/maksimal. Terkait ke kategori. | ✅ Iya |
| **warehouses** | Data gudang: kode, nama, alamat, no telepon. | ✅ Iya |
| **suppliers** | Data pemasok: kode, nama, kontak, NPWP. | ❌ Tidak |
| **customers** | Data pembeli: kode, nama, kontak. | ❌ Tidak |

#### Entitas Relasi

| Tabel | Fungsi | Soft Delete? |
|-------|--------|-------------|
| **warehouse_users** | Relasi many-to-many antara user dan gudang. Satu user bisa ditugaskan ke banyak gudang. | ✅ Iya |
| **stocks** | Stok per produk per gudang. Contoh: Produk A di Gudang Brebes = 35 pcs. | ❌ Tidak |
| **stock_histories** | Riwayat setiap perubahan stok. Catat qty sebelum, sesudah, dan referensi transaksi. | ❌ Tidak |

#### Entitas Transaksi

| Tabel | Fungsi | Soft Delete? |
|-------|--------|-------------|
| **inbound_transactions** | Catat barang masuk dari supplier. Otomatis generate kode BM-YYYYMMDD-XXX. | ❌ Tidak |
| **outbound_transactions** | Catat barang keluar ke customer. Otomatis generate kode BK-YYYYMMDD-XXX. | ❌ Tidak |
| **stock_mutations** | Catat pemindahan barang antar gudang. Status: dikirim → diterima/ditolak/selesai. | ❌ Tidak |
| **opnames** | Catat hasil stock opname. Status: draft → approved. | ❌ Tidak |

#### Entitas Bantuan (Role & Permission)

| Tabel | Fungsi |
|-------|--------|
| **roles** | Daftar role: super-admin, admin, viewer. |
| **permissions** | Izin spesifik per fitur (dari Spatie Permission). |
| **model_has_roles** | Relasi user → role. |
| **model_has_permissions** | Relasi user → permission langsung. |
| **role_has_permissions** | Relasi role → permission. |

### 4.2 Relasi Antar Tabel (Penjelasan)

```
- SATU kategori punya BANYAK produk
- SATU produk cuma punya SATU kategori

- SATU produk bisa ada di BANYAK gudang (via tabel stocks)
- SATU gudang bisa punya BANYAK produk (via tabel stocks)
- stocks adalah tabel relasi dengan kolom tambahan: quantity, available_qty

- SATU user bisa ditugaskan ke BANYAK gudang
- SATU gudang bisa punya BANYAK user
- warehouse_users adalah tabel pivot dengan kolom: assigned_by, is_primary

- SATU transaksi inbound/outbound hanya untuk SATU produk
- SATU transaksi inbound cuma dari SATU supplier
- SATU transaksi outbound cuma ke SATU customer
- TAPI SATU supplier/customer bisa punya BANYAK transaksi

- SATU mutasi punya SATU gudang asal dan SATU gudang tujuan (beda)
- stok digerakkan langsung dari gudang asal ke tujuan

- SATU opname untuk SATU produk di SATU gudang pada SATU tanggal
- Kalau diapprove, stok sistem akan disesuaikan dengan stok fisik

- Setiap perubahan stok akan dicatat di stock_histories
- stock_histories nyambung ke transaksi via reference_type + reference_id
- reference_type bisa: inbound, outbound, mutation_sent, mutation_received,
  mutation_rejected, adjustment, opname
```

### 4.3 Aturan Penting di Database

1. **Stok unik per produk per gudang**: Satu produk cuma bisa punya SATU baris stok di satu gudang
2. **Total price otomatis**: `quantity × unit_price` dihitung otomatis oleh database
3. **Kode transaksi unik**: Format `BM-20260608-001`, `BK-20260608-001`, `MT-20260608-001`
4. **Soft delete**: users, categories, products, warehouses, warehouse_users — kalau dihapus, datanya cuma disembunyikan (deleted_at diisi), bukan beneran dihapus
5. **Restrict on delete**: Kategori gak bisa dihapus kalau masih ada produknya. Supplier gak bisa dihapus kalau masih ada transaksi inbound. Customer gak bisa dihapus kalau masih ada transaksi outbound.

---

## 5. Struktur Kode (Class Diagram)

### 5.1 Arsitektur Umum

Aplikasi ini pakai pola **Controller → Action → Model**:

```
HTTP Request
    ↓
Controller (19 file) — menerima request, cek otorisasi, validasi
    ↓
Action (38 file) — logika bisnis, transaksi database
    ↓
Model (13 file) — representasi tabel database, relasi, query scope
```

### 5.2 Penjelasan Lapisan

#### Controller (app/Http/Controllers)

Tugas controller:
1. Terima request dari browser (via Inertia)
2. Cek otorisasi (apakah user punya izin?)
3. Validasi input (via FormRequest)
4. Panggil Action untuk eksekusi logika bisnis
5. Kembalikan response (redirect atau render halaman)

**Daftar Controller:**

| Controller | Fungsi |
|------------|--------|
| `DashboardController` | Halaman utama dashboard dengan ringkasan data |
| `CategoryController` | CRUD kategori produk |
| `ProductController` | CRUD produk |
| `SupplierController` | CRUD supplier |
| `CustomerController` | CRUD customer |
| `WarehouseController` | CRUD gudang |
| `EmployeeController` | CRUD karyawan (user) |
| `WarehouseUserController` | Atur penempatan staf ke gudang |
| `InboundController` | Barang masuk |
| `OutboundController` | Barang keluar |
| `MutationController` | Mutasi antar gudang |
| `OpnameController` | Stock opname |
| `ReportController` | Laporan stok, transaksi, peringatan |
| `SettingsController` | Atur profil, password (bawaan Laravel/Fortify) |

#### Action (app/Actions)

Tugas Action:
1. Logika bisnis murni (tanpa urusan HTTP)
2. Transaksi database (DB::transaction)
3. Panggil Action lain kalau perlu (misal: CreateInboundAction panggil UpdateStockAction)

**Contoh alur panggil Action:**

```
InboundController@store
  → CreateInboundTransactionAction@execute
      → generates kode transaksi (BM-20260608-001)
      → INSERT ke tabel inbound_transactions
      → panggil UpdateStockAction@execute
          → UPDATE stok (+ quantity)
          → INSERT ke stock_histories
      → return InboundTransaction
  → redirect + flash message
```

#### Model (app/Models)

Tugas Model:
1. Representasi tabel database
2. Definisikan relasi (belongsTo, hasMany, belongsToMany)
3. Definisikan casting tipe data (misal: decimal, boolean)
4. Scope untuk query umum (search, active, byWarehouse)

**Relasi utama antar model:**

```
Category
  → hasMany(Product)

Product
  → belongsTo(Category)
  → hasMany(Stock)        via stocks.product_id
  → hasMany(StockHistory) via stock_histories.product_id

Warehouse
  → belongsToMany(User)   via warehouse_users

User
  → belongsToMany(Warehouse) via warehouse_users
  → hasMany(InboundTransaction, 'created_by')
  → hasMany(OutboundTransaction, 'created_by')

Stock
  → belongsTo(Warehouse)
  → belongsTo(Product)
  → hasMany(StockHistory)

StockHistory
  → belongsTo(Stock)
  → belongsTo(Warehouse)
  → belongsTo(Product)
  → belongsTo(User, 'created_by')

InboundTransaction
  → belongsTo(Supplier)
  → belongsTo(Warehouse)
  → belongsTo(Product)
  → belongsTo(User, 'created_by')
  → hasMany(StockHistory)

OutboundTransaction
  → belongsTo(Customer)
  → belongsTo(Warehouse)
  → belongsTo(Product)
  → belongsTo(User, 'created_by')
  → hasMany(StockHistory)

StockMutation
  → belongsTo(Warehouse, 'from_warehouse')
  → belongsTo(Warehouse, 'to_warehouse')
  → belongsTo(Product)
  → belongsTo(User, 'created_by')
  → belongsTo(User, 'received_by')
  → hasMany(StockHistory)

Opname
  → belongsTo(Warehouse)
  → belongsTo(Product)
  → belongsTo(User, 'created_by')
  → hasMany(StockHistory)
```

### 5.3 Alur Data Frontend ke Backend

```
Browser (React SPA)
  ↓
Inertia v2 (mengirim request seperti fetch biasa, tapi response-nya berupa
komponen React + data)
  ↓
Laravel Router (routes/web.php)
  ↓
Middleware: web, auth, throttle
  ↓
Controller → FormRequest (validasi)
  ↓
Action (logika bisnis)
  ↓
Model → Database (SQLite)
  ↓
Kembali ke Controller → redirect + flash message
  ↓
Inertia merender ulang halaman dengan data baru
```

---

## 6. Alur Login

### 6.1 Cara Kerja

1. User buka halaman `/login`
2. Sistem menampilkan form: Email, Password, Remember Me
3. User isi email & password, klik "Masuk"
4. Sistem validasi:
   - Email wajib diisi → "Email wajib diisi."
   - Password wajib diisi → "Password wajib diisi."
5. Sistem cek kecocokan email & password ke database
6. Kalau cocok → redirect ke dashboard
7. Kalau tidak cocok → "Email atau Password salah."
8. Kalau 3× gagal dalam 1 menit → "Terlalu banyak percobaan login"
9. Kalau 10× gagal dalam 1 jam → "Akun Anda telah dikunci sementara"

### 6.2 Proteksi

- **Rate limiting**: 3 percobaan per menit, 10 per jam
- **CSRF**: Setiap request POST butuh token CSRF
- **Session**: Setelah login, session disimpan di database
- **2FA**: Opsional — bisa diaktifkan di pengaturan

### 6.3 Diagram Alur

```
[Form Login] → Submit
    ↓
[Validasi Input] → Gagal? → Tampilkan error
    ↓ Berhasil
[Cek Kredensial] → Gagal? → Tampilkan error
    ↓ Berhasil
[Regenerasi Session]
    ↓
[Redirect ke Dashboard]
```

---

## 7. Alur Barang Masuk (Inbound)

### 7.1 Cara Kerja

1. User buka halaman `/dashboard/inbound`
2. Sistem tampilkan daftar transaksi inbound yang sudah ada (pagination)
3. User klik "Tambah"
4. Muncul modal form dengan field:
   - **Supplier** (wajib) — pilih dari daftar supplier aktif
   - **Gudang** (wajib) — pilih dari daftar gudang yang bisa diakses
   - **Produk** (wajib) — pilih dari daftar produk aktif; harga beli otomatis terisi
   - **Jumlah** (wajib) — jumlah barang yang diterima
   - **Harga Satuan** (wajib) — otomatis terisi dari harga beli produk, bisa diubah
   - **Tanggal Diterima** (wajib) — tanggal transaksi
   - **Catatan** (opsional)

5. User klik "Simpan"
6. Sistem lakukan:
   a. Validasi input (supplier harus ada, jumlah > 0, dll)
   b. Generate kode transaksi: `BM-YYYYMMDD-XXX`
   c. Simpan ke tabel `inbound_transactions`
   d. **Update stok** di tabel `stocks`: quantity += jumlah
   e. Jika stok belum ada, buat baru
   f. Catat riwayat di `stock_histories`: qty sebelum → qty sesudah (+jumlah)
   g. Redirect ke halaman inbound dengan pesan sukses

7. Kalau gagal validasi, error ditampilkan di masing-masing field

### 7.2 Yang Perlu Diperhatikan

- Stok otomatis **bertambah** sebesar quantity
- Total harga (`total_price`) dihitung otomatis oleh database: `quantity × unit_price`
- Transaksi inbound juga tercatat di Riwayat Stok dengan reference_type = 'inbound'
- Harga satuan otomatis terisi dari field `cost` di tabel produk

### 7.3 Diagram Alur Langkah demi Langkah

```
Step 1: Buka halaman inbound
  → Sistem load daftar inbound (paginated)
  → Load data: warehouses, suppliers, products (untuk form)

Step 2: Klik "Tambah"
  → Modal form muncul
  → User pilih supplier, gudang, produk
  → Harga satuan auto-fill dari cost produk
  → User isi jumlah, tanggal, catatan

Step 3: Klik "Simpan"
  → Validasi server
    → Gagal? Tampilkan error (modal tetap terbuka)
    → Berhasil? Lanjut

Step 4: Eksekusi transaksi database
  → BEGIN TRANSACTION
  → INSERT inbound_transactions
  → SELECT stocks (lock)
    → Kalau belum ada, INSERT stocks baru
    → Kalau sudah ada, UPDATE quantity += qty
  → INSERT stock_histories
  → COMMIT

Step 5: Redirect + flash message
  → Halaman refresh
  → Toast sukses "Inbound transaction BM-20260608-001 berhasil dibuat"
```

---

## 8. Alur Barang Keluar (Outbound)

### 8.1 Cara Kerja

1. User buka halaman `/dashboard/outbound`
2. Sistem tampilkan daftar transaksi outbound (pagination)
3. User klik "Tambah"
4. Muncul modal form dengan field:
   - **Customer** (wajib)
   - **Gudang** (wajib)
   - **Produk** (wajib) — otomatis nampilin stok tersedia
   - **Jumlah** (wajib) — nampilin info "Stok tersedia: X"
   - **Harga Satuan** (wajib) — auto-fill dari harga jual produk
   - **Tanggal Penjualan** (wajib)
   - **Catatan** (opsional)

5. User klik "Simpan"
6. Sistem lakukan:
   a. Validasi input
   b. **Cek ketersediaan stok**: apakah stok cukup?
   c. Kalau stok **tidak cukup** → "Stok tidak cukup. Tersedia: X, diminta: Y"
   d. Kalau stok cukup → generate kode `BK-YYYYMMDD-XXX`
   e. Simpan ke `outbound_transactions`
   f. **Update stok**: quantity -= jumlah
   g. Catat riwayat di `stock_histories`
   h. Redirect dengan pesan sukses

### 8.2 Yang Perlu Diperhatikan

- Stok otomatis **berkurang** sebesar quantity
- Stok **tidak boleh negatif** — dicek sebelum transaksi
- Kalau ada produk yang stoknya 0, otomatis dianggap stok habis
- Harga satuan auto-fill dari field `price` di tabel produk
- Transaksi ini juga trigger pengecekan stok minimal (untuk peringatan di dashboard)

### 8.3 Diagram Alur Langkah demi Langkah

```
Step 1: Buka halaman outbound
  → Load daftar outbound (paginated)
  → Load: warehouses, customers, products, stocks (untuk cek ketersediaan)

Step 2: Klik "Tambah" → pilih gudang, produk, dll
  → Saat pilih produk + gudang, tampilkan "Stok tersedia: X"

Step 3: Klik "Simpan"
  → Cek stok: apakah available_qty >= request_qty?
    → Tidak? Error "Stok tidak cukup"
    → Ya? Lanjut

Step 4: Eksekusi database
  → BEGIN TRANSACTION
  → INSERT outbound_transactions
  → SELECT stocks (lock)
  → UPDATE stocks SET quantity -= qty, available_qty -= qty
  → INSERT stock_histories (change_qty = -qty)
  → COMMIT

Step 5: Redirect + flash message
```

---

## 9. Alur Mutasi Antar Gudang

### 9.1 Cara Kerja

Mutasi adalah proses memindahkan barang dari satu gudang ke gudang lain.
Ada 3 tahap:

#### Tahap 1: Kirim Mutasi

1. User buka halaman `/dashboard/mutations`
2. Klik "Tambah", isi form:
   - **Gudang Asal** (wajib)
   - **Gudang Tujuan** (wajib, harus berbeda dengan asal)
   - **Produk** (wajib)
   - **Jumlah** (wajib)
   - **Catatan** (opsional)
3. Sistem cek: stok di gudang asal cukup?
4. Kalau cukup → stok asal **dikurangi**, transaksi tercatat dengan status **dikirim**

#### Tahap 2: Terima Mutasi (oleh admin gudang tujuan)

1. Di halaman mutasi, admin gudang tujuan lihat mutasi masuk
2. Klik "Terima", isi:
   - **Jumlah Diterima** — berapa barang yang sampai dalam kondisi baik
   - **Jumlah Rusak** — berapa yang rusak selama perjalanan
3. Sistem:
   - Tambah stok gudang tujuan = jumlah diterima
   - Status mutasi berubah jadi **selesai**
   - Jumlah rusak dicatat (tidak ditambahkan ke stok mana pun)
   - Total diterima + rusak **tidak boleh melebihi** quantity kirim

#### Tahap 3 (Alternatif): Tolak Mutasi

1. Admin gudang tujuan klik "Tolak"
2. Stok **dikembalikan** ke gudang asal
3. Status mutasi berubah jadi **ditolak**
4. Catatan (notes) bisa diisi alasan penolakan

### 9.2 Status Mutasi

| Status Database | Status Tampilan | Arti |
|-----------------|-----------------|------|
| dikirim | sent | Barang sudah dikirim dari gudang asal |
| diterima | received | (tidak dipakai langsung, langsung ke selesai) |
| ditolak | rejected | Mutasi ditolak, stok dikembalikan |
| selesai | completed | Barang sudah diterima di gudang tujuan |

### 9.3 Diagram Alur

```
[Kirim Mutasi]
  Gudang Asal → cek stok → kurangi stok asal → status: dikirim
                                                        ↓
                                            ┌───────────┴───────────┐
                                            ↓                       ↓
                                    [Terima Mutasi]          [Tolak Mutasi]
                                          ↓                       ↓
                                    Tambah stok tujuan     Kembalikan stok asal
                                    Catat qty rusak        Catat alasan
                                          ↓                       ↓
                                    Status: selesai        Status: ditolak
```

---

## 10. Alur Opname Stok

### 10.1 Cara Kerja

Opname adalah proses mencocokkan stok yang tercatat di sistem dengan stok fisik
yang ada di gudang.

#### Tahap 1: Buat Opname (draft)

1. User buka halaman `/dashboard/opname`
2. Klik "Tambah", isi form:
   - **Gudang** (wajib)
   - **Produk** (wajib) — otomatis tampilkan stok sistem
   - **Stok Fisik** (wajib) — diisi berdasarkan hitungan fisik
   - **Tanggal Opname** (wajib)
   - **Catatan** (opsional)
3. Sistem hitung:
   - `selisih = stok_fisik - stok_sistem`
   - Tipe: "lebih" (selisih > 0), "kurang" (selisih < 0), "sama" (selisih = 0)
4. Opname tersimpan dengan status **draft**

#### Tahap 2: Approve Opname

1. User klik "Approve" pada opname yang statusnya draft
2. Sistem:
   - Kalau selisihnya "lebih" → stok ditambah
   - Kalau selisihnya "kurang" → stok dikurangi
   - Kalau selisihnya "sama" → tidak ada perubahan stok
   - Catat perubahan di `stock_histories` dengan reference_type = 'adjustment'
   - Status opname berubah jadi **approved**

### 10.2 Yang Perlu Diperhatikan

- Opname cuma bisa dibuat untuk produk yang **punya stok** di gudang tersebut
- Tidak boleh ada opname duplikat (produk + gudang + tanggal yang sama)
- Opname yang sudah diapprove **tidak bisa diapprove lagi**
- Data opname bersifat **immutable** setelah diapprove

### 10.3 Diagram Alur

```
[Draft Opname]
  Pilih gudang & produk
  Sistem tampilkan: stok_sistem = X
  Input stok_fisik = Y
  Sistem hitung: selisih = Y - X
  Simpan draft
       ↓
  [Approve Opname]
       ↓
  ┌────┴────┐
  │         │
selisih   selisih
  > 0       < 0
  │         │
  ↓         ↓
tambah    kurangi
stok       stok
  │         │
  └────┬────┘
       ↓
catat history (adjustment)
       ↓
status: approved
```

---

## 11. Alur Laporan & Dashboard

### 11.1 Dashboard

Halaman dashboard muncul setelah login. Isinya:

1. **Ringkasan Angka**:
   - Total Produk — jumlah semua produk aktif
   - Total Gudang — jumlah gudang yang bisa diakses
   - Stok Rendah — produk dengan stok <= min_stock
   - Stok Habis — produk dengan stok <= 0
   - Total Nilai Stok — jumlah (quantity × cost) semua produk

2. **Grafik Bulanan**:
   - Perbandingan barang masuk dan keluar per bulan (Januari–Desember)
   - Hanya untuk tahun berjalan

3. **Transaksi Terbaru**:
   - 5 transaksi terakhir (gabungan inbound, outbound, mutasi)
   - Diurutkan dari yang terbaru

4. **Peringatan Stok** (maks 5):
   - Produk dengan stok rendah
   - Produk dengan stok habis

5. **Daftar Produk & Karyawan**:
   - 5 produk terbaru
   - 5 karyawan terbaru

### 11.2 Laporan Stok

Halaman `/dashboard/reports/stock` menampilkan:

1. **Filter**: Gudang, Tanggal Mulai, Tanggal Akhir
2. **Tabel**: Daftar stok per produk per gudang dengan kolom:
   - Kode Produk, Nama Produk, Satuan
   - Qty, Tersedia, Stok Min
   - Harga Beli (cost), Nilai (qty × cost)
   - Status: Normal / Stok Rendah / Habis
3. Kalau tidak ada filter gudang, data dikelompokkan per gudang
4. Data **tidak bisa diedit** (read-only)

### 11.3 Laporan Transaksi

Halaman `/dashboard/reports/transactions` menampilkan:

1. **Ringkasan**: Total Inbound, Outbound, Mutasi, Nilai, Pergerakan Neto
2. **Filter**: Tipe (semua/inbound/outbound/mutasi), Gudang, Periode
3. **Tabel detail**: Semua transaksi diurutkan dari terbaru
4. Export ke Excel/PDF (tombol)

### 11.4 Laporan Peringatan

Halaman `/dashboard/reports/alerts` menampilkan:
- Daftar produk stok rendah (card berwarna kuning)
- Daftar produk stok habis (card berwarna merah)

---

## Lampiran: Relasi Lengkap Database

### Daftar Tabel (15 tabel)

```
1. users              → Pengguna sistem
2. categories         → Kategori produk
3. products           → Produk
4. warehouses         → Gudang
5. suppliers          → Supplier/pemasok
6. customers          → Customer/pelanggan
7. warehouse_users    → Penempatan staf di gudang
8. stocks             → Stok produk per gudang
9. stock_histories    → Riwayat perubahan stok
10. inbound_transactions → Transaksi barang masuk
11. outbound_transactions → Transaksi barang keluar
12. stock_mutations   → Mutasi antar gudang
13. opnames           → Stock opname
14. roles             → Role pengguna (Spatie)
15. permissions       → Izin per fitur (Spatie)
```

### Aturan Delete

| Tabel | Kalau Dihapus | Efek |
|-------|---------------|------|
| category | RESTRICT | Gak bisa kalau masih ada produk |
| product | SOFT DELETE | Stok & history tetap ada |
| warehouse | SOFT DELETE | Gak bisa kalau masih ada staf |
| user | SOFT DELETE | Role dihapus, data tetap |
| supplier | HARD DELETE | Gak bisa kalau masih ada transaksi |
| customer | HARD DELETE | Gak bisa kalau masih ada transaksi |
| stock | CASCADE | History ikut terhapus |

### Alur Stok (Yang Paling Penting)

```
BARANG MASUK:
  INSERT inbound_transactions
  → UPDATE stocks SET quantity += qty
  → INSERT stock_histories (change_qty: +qty)

BARANG KELUAR:
  INSERT outbound_transactions
  → UPDATE stocks SET quantity -= qty
  → INSERT stock_histories (change_qty: -qty)

MUTASI (KIRIM):
  INSERT stock_mutations (status: dikirim)
  → UPDATE stocks (gudang asal) SET quantity -= qty
  → INSERT stock_histories (mutation_sent)

MUTASI (TERIMA):
  UPDATE stock_mutations SET status: selesai
  → UPDATE stocks (gudang tujuan) SET quantity += received_qty
  → INSERT stock_histories (mutation_received)

MUTASI (TOLAK):
  UPDATE stock_mutations SET status: ditolak
  → UPDATE stocks (gudang asal) SET quantity += qty
  → INSERT stock_histories (mutation_rejected)

OPNAME (APPROVE):
  UPDATE opnames SET status: approved
  → UPDATE stocks SET quantity += selisih
  → INSERT stock_histories (adjustment)
```

---

> **Dibuat: 8 Juni 2026**
> Dokumen ini bisa diedit dan ditambahkan sesuai kebutuhan.
> Untuk pertanyaan, hubungi tim developer.
