ALUR SISTEM GUDANG MULTI GUDANG
(Penjelasan Sederhana - Tanpa Kode)
📋 DAFTAR ISI

    KONSEP DASAR & LEVEL USER

    ALUR LOGIN & PILIH GUDANG

    ALUR KERJA DI GUDANG

    ALUR BARANG MASUK

    ALUR BARANG KELUAR

    ALUR MUTASI ANTAR GUDANG

    ALUR LAPORAN

    NOTIFIKASI & ALERT

    CEK LIST PEMAHAMAN

🎯 KONSEP DASAR {#konsep-dasar}
📌 3 LEVEL USER & HAK AKSESNYA
Level	Siapa?	Bisa Akses?	Bisa Input?	Bisa Lihat Laporan?
🟢 Super Admin	Pemilik/Pimpinan	Semua gudang	✅ Bisa	Semua gudang
🔵 Admin	Kepala gudang	Hanya gudang sendiri	✅ Bisa	Gudang sendiri
🟡 Viewer	Owner/Direktur	Semua gudang	❌ TIDAK BISA	Semua gudang (read only)
📋 ALUR LENGKAP DARI AWAL SAMPAI AKHIR {#alur-login}
🟢 SKENARIO 1: USER = SUPER ADMIN
text

1. Login dengan email & password
2. Sistem cek role → "SUPER ADMIN"
   ↓
3. ARAHKAN ke HALAMAN PILIH GUDANG
   ↓
4. MUNCUL DAFTAR SEMUA GUDANG:
   ☑ Gudang Pusat (Jakarta)
   ☑ Gudang Cabang (Bandung)
   ☑ Gudang Cabang (Surabaya)
   ☑ Gudang Cabang (Semarang)
   ↓
5. Super Admin PILIH "Gudang Pusat"
   ↓
6. Sistem CATAT: 
   "Super Admin aktif di Gudang Pusat"
   ↓
7. MASUK ke DASHBOARD Gudang Pusat

🔄 GANTI GUDANG:

    Klik menu "Ganti Gudang" → Pilih gudang lain → Sistem pindah konteks

🔵 SKENARIO 2: USER = ADMIN GUDANG (Contoh: Budi)
text

1. Budi login dengan email & password
   ↓
2. Sistem cek role → "ADMIN GUDANG"
   ↓
3. Sistem CEK DATABASE:
   "Budi ditempatkan di gudang mana?"
   ↓
4. Database menjawab: "Budi di Gudang Bandung"
   ↓
5. OTOMATIS!
   Budi LANGSUNG masuk ke DASHBOARD Gudang Bandung
   ↓
6. Menu "Ganti Gudang" ❌ TIDAK MUNCUL
   Budi TIDAK BISA pilih gudang lain

🔄 KALAU DIPINDAHKAN TUGAS:
text

1. Super Admin pindahkan Budi ke Gudang Surabaya
2. Besoknya Budi login
3. Sistem cek database → "Budi di Gudang Surabaya"
4. Otomatis masuk ke DASHBOARD Gudang Surabaya

🟡 SKENARIO 3: USER = VIEWER (Owner/Direktur)
text

1. Pak Direktur login
   ↓
2. Sistem cek role → "VIEWER"
   ↓
3. ARAHKAN ke HALAMAN DASHBOARD MONITORING
   ↓
4. Yang BISA DILIHAT:
   ✅ Stok semua gudang
   ✅ Laporan transaksi semua gudang
   ✅ Grafik dan statistik
   ↓
5. Yang ❌ TIDAK BISA DILAKUKAN:
   ❌ Input barang masuk
   ❌ Input barang keluar
   ❌ Buat mutasi
   ❌ Edit data apapun
   ❌ Hapus transaksi
   
   👉 HANYA MELIHAT!

🏭 ALUR KERJA DI GUDANG {#alur-kerja}
A. CEK STOK BARANG 📦

👥 BISA DILIHAT:

    ✅ Super Admin

    ✅ Admin Gudang

    ✅ Viewer

📌 PROSES:
text

1. User buka menu "STOK BARANG"
   ↓
2. Sistem CEK:
   "User ini sedang aktif di gudang mana?"
   ↓
3. Sistem FILTER data:
   "Tampilkan produk dengan stok > 0 
    di gudang [nama gudang]"
   ↓
4. MUNCUL TABEL:

CONTOH TAMPILAN:

┌─────────────────────────────────────────────────┐
│ 🏭 GUDANG BANDUNG - STOK SAAT INI │
├───────────┬──────────┬──────────┬───────────────┤
│ Produk │ Stok │ Minimal │ Status │
├───────────┼──────────┼──────────┼───────────────┤
│ Beras │ 150 kg │ 50 kg │ ✅ Normal │
│ Minyak │ 5 liter │ 30 liter │ ⚠️ MENIPIS! │
│ Gula │ 0 kg │ 40 kg │ ❌ HABIS │
│ Telur │ 25 kg │ 20 kg │ ⚠️ MENIPIS! │
└───────────┴──────────┴──────────┴───────────────┘

🟢 FITUR SUPER ADMIN:

    Bisa klik "Lihat Semua Gudang"
    → Muncul stok GABUNGAN semua cabang

B. FILTER & PENCARIAN 🔍

SEMUA USER BISA:
text

📌 FILTER PRODUK:
   - Cari nama produk
   - Filter kategori
   - Filter status (Normal/Menipis/Habis)

📌 SORTIR:
   - Stok terendah
   - Stok tertinggi
   - Nama A-Z

📌 EXPORT DATA:
   - Cetak PDF
   - Download Excel

📦 ALUR BARANG MASUK (DARI SUPPLIER) {#barang-masuk}
👤 PIHAK YANG TERLIBAT
Role	Bisa Input?	Keterangan
Super Admin	✅ Ya	Setelah pilih gudang
Admin Gudang	✅ Ya	Di gudangnya sendiri
Viewer	❌ Tidak	Read only
📋 PROSES LENGKAP
text

CONTOH:
Budi (Admin Gudang Bandung) menerima kiriman beras 100 kg dari PT ABC

1. Budi buka menu "BARANG MASUK"
   ↓
2. Sistem OTOMATIS mengisi:
   ┌────────────────────────┐
   │ Gudang    : Bandung    │ ← dari session login
   │ Tanggal   : 24/11/2024 │ ← system date
   │ Petugas   : Budi       │ ← dari user login
   └────────────────────────┘
   ↓
3. Budi pilih: SUPPLIER = "PT ABC"
   ↓
4. Budi pilih: PRODUK = "Beras"
   ↓
5. Budi isi: JUMLAH = 100
   ↓
6. Budi isi: KETERANGAN = "PO-2024-001" (opsional)
   ↓
7. Budi klik "SIMPAN"

⚙️ YANG TERJADI DI BELAKANG
text

┌─────────────────────────────────────────────────┐
│               PROSES SISTEM                     │
├─────────────────────────────────────────────────┤
│                                                  │
│  1. ✅ TRANSAKSI MASUK TERCATAT                 │
│     ID: BM-20241124-001                        │
│     Gudang: Bandung                            │
│     Produk: Beras                              │
│     Jumlah: 100 kg                            │
│     Petugas: Budi                             │
│     Waktu: 24/11/2024 09:30                   │
│                                                  │
│  2. ✅ STOK DIPERBARUI                         │
│     Stok awal Beras: 50 kg                    │
│     Barang masuk: +100 kg                     │
│     ──────────────────────────────────        │
│     Stok akhir: 150 kg ✅                     │
│                                                  │
│  3. ✅ NOTIFIKASI UPDATE                       │
│     Status "MENIPIS" HILANG                   │
│     (karena stok 150 kg > minimal 50 kg)      │
│                                                  │
└─────────────────────────────────────────────────┘

❗ KALAU SALAH INPUT
Kondisi	Tindakan
Belum disimpan	Bisa diedit langsung
Sudah disimpan	❌ TIDAK BISA HAPUS!
Buat transaksi PEMBETULAN
Salah jumlah	Input barang masuk NEGATIF atau
Buat transaksi keluar untuk koreksi

📌 AUDIT TRAIL:

    Semua perubahan TERCATAT:

        Siapa yang input

        Kapan diinput

        Nilai sebelum dan sesudah

        Tidak bisa dihapus permanen

🚚 ALUR BARANG KELUAR (KE CUSTOMER) {#barang-keluar}
👤 PIHAK YANG TERLIBAT
Role	Bisa Input?	Syarat
Admin Gudang	✅ Ya	Stok harus cukup
Super Admin	✅ Ya	Stok harus cukup
Viewer	❌ Tidak	-
📋 PROSES LENGKAP
text

CONTOH:
Customer beli beras 20 kg dari Gudang Bandung

1. Staff buka menu "BARANG KELUAR"
   ↓
2. Sistem OTOMATIS mengisi:
   ┌────────────────────────┐
   │ Gudang    : Bandung    │
   │ Tanggal   : 24/11/2024 │
   │ Petugas   : [user]     │
   └────────────────────────┘
   ↓
3. Isi: PENERIMA = "Toko Sumber Rejeki"
   ↓
4. Pilih: PRODUK = "Beras"
   ↓
5. Isi: JUMLAH = 20
   ↓
6. Sistem CEK STOK:
   "Stok Beras di Gudang Bandung = 150 kg"
   "Apakah 150 ≥ 20? ✅ YA"
   ↓
7. Klik "SIMPAN"

⚙️ PROSES DI BELAKANG
text

┌─────────────────────────────────────────────────┐
│          VALIDASI & EKSEKUSI                   │
├─────────────────────────────────────────────────┤
│                                                  │
│  ✅ VALIDASI BERHASIL                          │
│  ─────────────────                             │
│  Stok tersedia : 150 kg                       │
│  Diminta       : 20 kg                        │
│  Status        : ✔️ CUKUP                     │
│                                                  │
│  ✅ EKSEKUSI:                                  │
│  1. Catat transaksi keluar                    │
│  2. Kurangi stok: 150 - 20 = 130 kg          │
│  3. Update stok di database                   │
│  4. Tampilkan pesan sukses                    │
│                                                  │
└─────────────────────────────────────────────────┘

❌ KALAU STOK TIDAK CUKUP
text

CONTOH:
Customer pesan gula 50 kg, stok hanya 10 kg

1. User input: Produk = Gula, Jumlah = 50
2. Sistem CEK STOK: 
   "Stok Gula = 10 kg"
   "Apakah 10 ≥ 50? ❌ TIDAK"
   ↓
3. ❌ TRANSAKSI GAGAL DISIMPAN
   ↓
4. MUNCUL PESAN:
   ┌──────────────────────────────────┐
   │  ⚠️  STOK TIDAK MENCUKUPI        │
   │                                  │
   │  Produk : Gula                  │
   │  Stok tersedia : 10 kg          │
   │  Jumlah diminta : 50 kg         │
   │  Kekurangan : 40 kg            │
   │                                  │
   │  [✔️ OK]                        │
   └──────────────────────────────────┘

🔄 ALUR MUTASI (TRANSFER ANTAR GUDANG) {#mutasi}
📌 KONSEP DASAR

Mutasi = Memindahkan barang dari satu gudang ke gudang lain

👤 YANG BISA INPUT:

    ✅ Admin Gudang (dari gudang asalnya)

    ✅ Super Admin (dari gudang manapun)

    ❌ Viewer (tidak bisa)

📋 PROSES MUTASI LENGKAP
text

SKENARIO:
Gudang Pusat kelebihan beras (500 kg)
Gudang Bandung kekurangan beras (10 kg)

TAHAP 1: ADMIN GUDANG PUSAT KIRIM
──────────────────────────────────

1. Admin Gudang Pusat buka menu "MUTASI BARANG"
   ↓
2. Sistem OTOMATIS isi:
   - Gudang Asal: Pusat
   - Tanggal: hari ini
   - Status: Dikirim
   ↓
3. Admin pilih: GUDANG TUJUAN = "Bandung"
   ↓
4. Admin pilih: PRODUK = "Beras"
   ↓
5. Admin isi: JUMLAH = 100 kg
   ↓
6. Admin isi: KETERANGAN = "Kirim ke Bandung"
   ↓
7. Sistem CEK STOK: 
   "Stok Pusat 500 kg ≥ 100 kg? ✅ CUKUP"
   ↓
8. Klik "KIRIM"

⚙️ YANG TERJADI TAHAP 1
text

┌─────────────────────────────────────────────────┐
│          GUDANG ASAL (PUSAT)                   │
├─────────────────────────────────────────────────┤
│                                                  │
│  ✅ TRANSAKSI MUTASI TERCATAT                  │
│     ID: MT-20241124-001                       │
│     Dari: Gudang Pusat                        │
│     Ke: Gudang Bandung                        │
│     Produk: Beras                             │
│     Jumlah: 100 kg                           │
│     Status: 🟡 DIKIRIM                        │
│                                                  │
│  ✅ STOK GUDANG PUSAT BERKURANG               │
│     Stok awal: 500 kg                        │
│     Dikirim: -100 kg                         │
│     ─────────────────────                    │
│     Stok akhir: 400 kg ✅                    │
│                                                  │
└─────────────────────────────────────────────────┘

📋 TAHAP 2: ADMIN GUDANG BANDUNG TERIMA
text

1. Admin Gudang Bandung login
   ↓
2. Buka menu "MUTASI MASUK"
   ↓
3. Lihat daftar mutasi yang menunggu konfirmasi:
   
   ┌────────────────────────────────────┐
   │ MUTASI DARI GUDANG PUSAT          │
   │ No: MT-20241124-001              │
   │ Produk: Beras 100 kg             │
   │ Dikirim: 24/11/2024             │
   │                                  │
   │ [✔️ TERIMA]  [❌ TOLAK]          │
   └────────────────────────────────────┘
   ↓
4. Klik "TERIMA"
   ↓
5. Cek fisik barang (opsional: input jika ada selisih/rusak)
   ↓
6. Konfirmasi

⚙️ YANG TERJADI TAHAP 2
text

┌─────────────────────────────────────────────────┐
│          GUDANG TUJUAN (BANDUNG)               │
├─────────────────────────────────────────────────┤
│                                                  │
│  ✅ STATUS MUTASI UPDATE                       │
│     ID: MT-20241124-001                       │
│     Status: 🟢 DITERIMA                       │
│     Diterima: 24/11/2024                     │
│     Penerima: Admin Bandung                  │
│                                                  │
│  ✅ STOK GUDANG BANDUNG BERTAMBAH             │
│     Stok awal: 10 kg                         │
│     Diterima: +100 kg                        │
│     ─────────────────────                    │
│     Stok akhir: 110 kg ✅                    │
│                                                  │
└─────────────────────────────────────────────────┘

📦 KALAU BARANG RUSAK/HILANG DI JALAN
text

SKENARIO:
Dikirim 100 kg, sampai 95 kg (5 kg rusak)

PROSES KONFIRMASI:
1. Admin Bandung klik "TERIMA"
2. Muncul form:
   ┌─────────────────────────┐
   │ Jumlah diterima: 95 kg │
   │ Barang rusak: 5 kg     │
   │ Keterangan: "Bocor"    │
   └─────────────────────────┘
3. Klik "KONFIRMASI"

HASIL AKHIR:
✅ Stok Pusat: 500 - 100 = 400 kg
✅ Stok Bandung: 10 + 95 = 105 kg
📝 Catatan selisih: 5 kg (rusak)

📊 ALUR LAPORAN {#laporan}
👤 HAK AKSES LAPORAN PER ROLE
Jenis Laporan	Super Admin	Admin Gudang	Viewer
Stok per gudang	✅ Semua	✅ Gudang sendiri	✅ Semua
Stok semua gudang	✅ Ya	❌ Tidak	✅ Ya
Barang Masuk	✅ Semua	✅ Gudang sendiri	✅ Semua
Barang Keluar	✅ Semua	✅ Gudang sendiri	✅ Semua
Mutasi	✅ Semua	✅ Gudang sendiri	✅ Semua
History harga	✅ Ya	❌ Tidak	❌ Tidak
A. LAPORAN STOK 📋

🟢 SUPER ADMIN / 🟡 VIEWER:
text

Bisa pilih tampilan:
☑ Laporan Stok SEMUA GUDANG
☑ Laporan Stok PER GUDANG
☑ Laporan Stok PER PRODUK
☑ Laporan Stok KRITIS (stok ≤ minimal)
☑ Laporan Stok HABIS

🔵 ADMIN GUDANG:
text

☑ Laporan Stok GUDANGNYA SAJA
☑ Laporan Stok PER PRODUK (di gudangnya)
☑ Laporan Stok KRITIS (di gudangnya)

📋 CONTOH LAPORAN STOK SUPER ADMIN:
text

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   LAPORAN STOK GABUNGAN - 24 NOVEMBER 2024
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🏭 GUDANG PUSAT:
┌──────────┬──────────┬──────────┬─────────┬─────────┐
│ Produk   │ Stok     │ Minimal  │ Status  │ Nilai   │
├──────────┼──────────┼──────────┼─────────┼─────────┤
│ Beras    │ 400 kg   │ 100 kg   │ ✅ Aman │ Rp 4jt  │
│ Minyak   │ 50 liter │ 50 liter │ ⚠️ Min  │ Rp 750k │
│ Gula     │ 200 kg   │ 50 kg    │ ✅ Aman │ Rp 2.4jt│
└──────────┴──────────┴──────────┴─────────┴─────────┘

🏭 GUDANG BANDUNG:
┌──────────┬──────────┬──────────┬─────────┬─────────┐
│ Produk   │ Stok     │ Minimal  │ Status  │ Nilai   │
├──────────┼──────────┼──────────┼─────────┼─────────┤
│ Beras    │ 110 kg   │ 50 kg    │ ✅ Aman │ Rp 1.1jt│
│ Minyak   │ 5 liter  │ 30 liter │ 🔴 KRITIS│ Rp 75k  │
│ Gula     │ 0 kg     │ 40 kg    │ ❌ HABIS│ Rp 0    │
└──────────┴──────────┴──────────┴─────────┴─────────┘

🏭 GUDANG SURABAYA:
┌──────────┬──────────┬──────────┬─────────┬─────────┐
│ Produk   │ Stok     │ Minimal  │ Status  │ Nilai   │
├──────────┼──────────┼──────────┼─────────┼─────────┤
│ Beras    │ 75 kg    │ 50 kg    │ ⚠️ Menipis│ Rp 750k │
│ Minyak   │ 40 liter │ 30 liter │ ✅ Aman │ Rp 600k │
└──────────┴──────────┴──────────┴─────────┴─────────┘

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
TOTAL NILAI STOK: Rp 9.675.000
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

B. LAPORAN TRANSAKSI 📄

📅 LAPORAN BARANG MASUK (HARI INI):
text

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
No │ Kode    │ Gudang │ Supplier   │ Produk │ Jumlah
───┼─────────┼────────┼────────────┼────────┼───────
1  │ BM-001  │ Pusat  │ PT ABC     │ Beras  │ 500 kg
2  │ BM-002  │ Pusat  │ PT Indo    │ Minyak │ 100 L
3  │ BM-003  │ Bandung│ PT XYZ     │ Gula   │ 200 kg
4  │ BM-004  │ Sby    │ PT ABC     │ Beras  │ 300 kg
───┴─────────┴────────┴────────────┴────────┴───────
Total Masuk Hari Ini: 4 transaksi
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📅 LAPORAN BARANG KELUAR (HARI INI):
text

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
No │ Kode    │ Gudang │ Penerima    │ Produk │ Jumlah
───┼─────────┼────────┼─────────────┼────────┼───────
1  │ BK-001  │ Pusat  │ Toko A      │ Beras  │ 50 kg
2  │ BK-002  │ Pusat  │ Toko B      │ Minyak │ 20 L
3  │ BK-003  │ Bandung│ Toko Sumber │ Gula   │ 30 kg
4  │ BK-004  │ Sby    │ Toko Makmur │ Beras  │ 40 kg
───┴─────────┴────────┴─────────────┴────────┴───────
Total Keluar Hari Ini: 4 transaksi
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

C. LAPORAN MUTASI 🔄
text

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   LAPORAN MUTASI - 24 NOVEMBER 2024
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
No │ ID Mutasi │ Asal → Tujuan │ Produk │ Qty │ Status
───┼───────────┼───────────────┼────────┼─────┼───────
1  │ MT-001    │ Pusat→Bandung │ Beras  │ 100 │ 🟢 Terima
2  │ MT-002    │ Sby→Pusat     │ Gula   │ 50  │ 🟡 Dikirim
3  │ MT-003    │ Bandung→Sby   │ Telur  │ 30  │ 🟢 Terima
───┴───────────┴───────────────┴────────┴─────┴───────

🔴 STATUS:
🟢 Diterima = Barang sudah sampai di tujuan
🟡 Dikirim  = Barang dalam perjalanan
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🔔 NOTIFIKASI & ALERT {#notifikasi}
📌 SISTEM OTOMATIS CEK SETIAP:
text

⏰ Setiap transaksi masuk/keluar
⏰ Setiap jam 08:00 (pagi)
⏰ Setiap stok diupdate

1. NOTIFIKASI STOK MENIPIS ⚠️

KONDISI:

    Stok ≤ Stok Minimal

CONTOH:
text

Minyak Goreng di Gudang Bandung
- Stok: 5 liter
- Minimal: 30 liter
- Status: MENIPIS

YANG TERJADI:
┌─────────────────────────────────────┐
│ 🔔 NOTIFIKASI DASHBOARD            │
├─────────────────────────────────────┤
│ ⚠️ Stok Menipis (2)                │
│                                    │
│ • Minyak Goreng - Bandung         │
│   Tersisa 5 dari minimal 30 L     │
│                                    │
│ • Telur - Surabaya               │
│   Tersisa 10 dari minimal 25 kg  │
└─────────────────────────────────────┘

📧 EMAIL KE ADMIN GUDANG:
Subject: [PENTING] Stok Menipis di Gudang Bandung
Isi: Minyak Goreng tersisa 5 liter.
      Segera lakukan pemesanan!

2. NOTIFIKASI STOK HABIS ❌

KONDISI:

    Stok = 0

CONTOH:
text

Gula di Gudang Bandung
- Stok: 0 kg
- Status: HABIS

YANG TERJADI:
┌─────────────────────────────────────┐
│ 🔔 NOTIFIKASI DASHBOARD            │
├─────────────────────────────────────┤
│ ❌ Stok Habis (1)                  │
│                                    │
│ • Gula - Bandung                  │
│   Stok 0 kg - TIDAK BISA DIJUAL   │
└─────────────────────────────────────┘

⛔ SISTEM CEK:
Jika ada transaksi jual Gula:
"Transaksi ditolak - Stok habis"

3. NOTIFIKASI MUTASI MASUK 📦

KONDISI:

    Ada barang dikirim ke gudang tujuan

CONTOH:
text

Dari Gudang Pusat mengirim Beras 100 kg
ke Gudang Bandung

YANG TERJADI:
┌─────────────────────────────────────┐
│ 🔔 NOTIFIKASI DASHBOARD            │
├─────────────────────────────────────┤
│ 📦 Mutasi Masuk (1)               │
│                                    │
│ • Dari: Gudang Pusat              │
│   Produk: Beras 100 kg           │
│   [✔️ TERIMA]  [❌ TOLAK]         │
└─────────────────────────────────────┘

4. NOTIFIKASI UNTUK SUPER ADMIN 👑

KONDISI KHUSUS:
text

📌 GUDANG TIDAK AKTIF:
Jika gudang tidak ada transaksi > 3 hari
→ Notifikasi ke Super Admin

📌 ADMIN BARU DITAMBAHKAN:
"User Budi telah ditambahkan sebagai 
 Admin Gudang Bandung"

📌 MUTASI TERTUNDA:
"Ada 2 mutasi belum diterima > 2 hari"

📊 RINGKASAN ALUR PER USER {#ringkasan}
🟢 SUPER ADMIN
text

┌─────────────────────────────────────────────┐
│              SUPER ADMIN                   │
│            (Punya akses penuh)             │
└─────────────────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │     LOGIN             │
        └────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │   PILIH GUDANG        │◄────┐
        │   (HALAMAN AWAL)      │     │
        └────────────────────────┘     │
                     │                 │
                     ▼                 │
        ┌────────────────────────┐     │
        │   DASHBOARD           │     │
        │   (GUDANG TERPILIH)   │     │
        └────────────────────────┘     │
                     │                 │
         ┌───────────┴───────────┐     │
         │                       │     │
         ▼                       ▼     │
   ┌─────────────┐        ┌─────────────┐
   │ KERJA DI    │        │ GANTI      │────┘
   │ GUDANG INI  │        │ GUDANG     │
   └─────────────┘        └─────────────┘

✅ BISA:
• Lihat stok semua gudang
• Input transaksi di gudang aktif
• Pindah gudang kapan saja
• Tambah user baru
• Assign admin ke gudang
• Pindahin admin antar gudang
• Lihat semua laporan

🔵 ADMIN GUDANG
text

┌─────────────────────────────────────────────┐
│              ADMIN GUDANG                  │
│           (Kepala Gudang)                 │
└─────────────────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │        LOGIN          │
        └────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │   OTOMATIS MASUK       │
        │   GUDANG TEMPAT TUGAS  │
        └────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │      DASHBOARD         │
        │   GUDANG BANDUNG      │
        └────────────────────────┘
                     │
         ┌───────────┴───────────┐
         │                       │
         ▼                       ▼
   ┌─────────────┐        ┌─────────────┐
   │ BARANG      │        │ BARANG      │
   │ MASUK       │        │ KELUAR      │
   └─────────────┘        └─────────────┘
         │                       │
         ▼                       ▼
   ┌─────────────┐        ┌─────────────┐
   │ MUTASI      │        │ LAPORAN     │
   │ KELUAR      │        │ GUDANG SENDIRI│
   └─────────────┘        └─────────────┘

❌ TIDAK BISA:
• Pilih gudang lain
• Lihat stok gudang lain
• Tambah user
• Pindah gudang sendiri

🟡 VIEWER (Owner/Direktur)
text

┌─────────────────────────────────────────────┐
│               VIEWER                       │
│           (Owner/Direktur)                │
└─────────────────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │        LOGIN          │
        └────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │   DASHBOARD           │
        │   MONITORING         │
        └────────────────────────┘
                     │
         ┌───────────┴───────────┐
         │           │           │
         ▼           ▼           ▼
   ┌─────────┐ ┌─────────┐ ┌─────────┐
   │ STOK    │ │LAPORAN  │ │ GRAFIK  │
   │ ALL     │ │ALL      │ │TREN     │
   └─────────┘ └─────────┘ └─────────┘
         │           │           │
         └───────────┴───────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │   HANYA MELIHAT!       │
        │   TIDAK BISA INPUT    │
        └────────────────────────┘

✅ BISA:
• Lihat stok semua gudang
• Lihat semua laporan
• Lihat grafik dan statistik
• Export data ke Excel/PDF

❌ TIDAK BISA:
• Input barang masuk
• Input barang keluar
• Buat mutasi
• Edit data
• Hapus transaksi
• Tambah user

✅ CEK LIST PEMAHAMAN {#cek-list}
📌 APAKAH ANDA SUDAH PAHAM?
🟢 SUPER ADMIN vs ADMIN GUDANG
[✔️] Saya paham	Perbedaan
✅	Super Admin bisa pilih gudang, Admin Gudang tidak bisa
✅	Super Admin lihat semua gudang, Admin Gudang lihat gudang sendiri
✅	Super Admin bisa pindahin admin, Admin Gudang tidak bisa pindah sendiri
🔵 ADMIN GUDANG & GANTI GUDANG
[✔️] Saya paham	Penjelasan
✅	Admin Gudang login → otomatis masuk ke gudang tugasnya
✅	Admin Gudang tidak bisa ganti gudang karena sudah ditetapkan
✅	Kalau dipindah tugas → Super Admin yang atur di database
📦 BARANG MASUK
[✔️] Saya paham	Penjelasan
✅	Admin Gudang input barang masuk → stok bertambah
✅	Sistem otomatis isi gudang, tanggal, petugas
✅	Tidak bisa hapus transaksi yang sudah disimpan
🚚 BARANG KELUAR
[✔️] Saya paham	Penjelasan
✅	Admin Gudang input barang keluar → stok berkurang
✅	Sistem cek stok dulu sebelum simpan
✅	Kalau stok kurang → transaksi gagal
🔄 MUTASI ANTAR GUDANG
[✔️] Saya paham	Penjelasan
✅	Mutasi = pindah barang antar gudang
✅	2 status: Dikirim (stok asal turun) & Diterima (stok tujuan naik)
✅	Bisa catat barang rusak/hilang di jalan
📊 LAPORAN PER ROLE
[✔️] Saya paham	Super Admin	Admin Gudang	Viewer
✅	Lihat SEMUA gudang	Lihat gudang SENDIRI	Lihat SEMUA gudang
✅	✅ Bisa input	✅ Bisa input	❌ TIDAK bisa input
✅	✅ Bisa export	✅ Bisa export	✅ Bisa export
👤 ROLE STAFF (YANG DIHAPUS)
[✔️] Saya paham	Penjelasan
✅	Staff dihapus dari alur
✅	Hanya ada 3 role: Super Admin, Admin Gudang, Viewer
✅	Semua operasional dipegang Admin Gudang
🎯 KESIMPULAN AKHIR
text

┌─────────────────────────────────────────────────────┐
│                                                     │
│   SISTEM GUDANG MULTI GUDANG SIAP DIGUNAKAN        │
│                                                     │
│   ✅ 3 LEVEL USER JELAS PERANNYA                   │
│   ✅ ALUR BARANG MASUK/KELUAR JELAS                │
│   ✅ MUTASI ANTAR GUDANG JELAS                     │
│   ✅ LAPORAN SESUAI HAK AKSES                      │
│   ✅ NOTIFIKASI OTOMATIS AKTIF                     │
│                                                     │
│   🚀 SIAP DIIMPLEMENTASIKAN!                       │
│                                                     │
└─────────────────────────────────────────────────────┘

📌 CATATAN PENTING:

    Super Admin = Bisa segalanya, bebas pindah gudang

    Admin Gudang = Operasional harian di gudang tetap

    Viewer = Hanya lihat, tidak bisa input

    Stok = Jantung sistem, selalu update real-time

    Mutasi = 2 step proses (kirim + terima)

    Laporan = Selalu filter berdasarkan hak akses

Dokumen Alur Sistem Gudang Multi Gudang v1.0
Penjelasan Sederhana - Tanpa Kode
erdnya gimana ini
📐 DESIGN DATABASE (ERD)
SISTEM GUDANG MULTI GUDANG
🎯 ENTITAS UTAMA & RELASI
text

┌─────────────────────────────────────────────────────────────────────────────────────┐
│                                                                                     │
│                             DIAGRAM RELASI ANTAR TABEL                              │
│                                                                                     │
└─────────────────────────────────────────────────────────────────────────────────────┘

                                ┌─────────────────┐
                                │     users       │
                                ├─────────────────┤
                          ┌────>│ id              │
                          │     │ name            │
                          │     │ email           │
                          │     │ password        │
                          │     │ role            │◄──────────┐
                          │     │ created_at      │          │
                          │     └─────────────────┘          │
                          │            │                      │
                          │            │ 1                   │
                          │            ▼                     │
                          │     ┌─────────────────┐         │
                          │     │   warehouses    │         │
                          │     ├─────────────────┤         │
                          └─────│ id              │         │
                                │ code            │         │
                          ┌─────│ name            │         │
                          │     │ address         │         │
                          │     │ phone           │         │
                          │     │ is_active       │         │
                          │     └─────────────────┘         │
                          │             1                   │
                          │             │                   │
                          │             │                   │
                          │             ▼                   │
                          │      ┌──────────────┐          │
                          │      │ user_warehouse│          │
                          │      ├──────────────┤          │
                          │      │ user_id      │◄─────────┘
                          └──────│ warehouse_id │
                                 └──────────────┘
                                          M
                                          │
                                          │
              ┌───────────────────────────┴───────────────────────────┐
              │                           │                           │
              ▼                           ▼                           ▼
    ┌─────────────────┐        ┌─────────────────┐        ┌─────────────────┐
    │   products      │        │   categories    │        │    suppliers    │
    ├─────────────────┤        ├─────────────────┤        ├─────────────────┤
    │ id             │        │ id              │        │ id              │
    │ code           │        │ name            │        │ code            │
    │ name           │        │ description     │        │ name            │
    │ category_id    │───────>│ parent_id       │        │ contact_person  │
    │ unit           │        │ is_active       │        │ phone           │
    │ min_stock      │        └─────────────────┘        │ email           │
    │ is_active      │                                    │ address         │
    └─────────────────┘                                    └─────────────────┘
            1                                                       1
            │                                                       │
            │                                                       │
            ▼                                                       ▼
    ┌─────────────────────────────────────────────────────────────────┐
    │                         stock                                   │
    │  ┌───────────────────────────────────────────────────────────┐  │
    │  │ id                                                       │  │
    │  │ warehouse_id                (FK → warehouses.id)         │  │
    │  │ product_id                 (FK → products.id)           │  │
    │  │ quantity                   (decimal)                    │  │
    │  │ last_updated              (timestamp)                  │  │
    │  └───────────────────────────────────────────────────────────┘  │
    └─────────────────────────────────────────────────────────────────┘
                              ▲
                              │
          ┌───────────────────┴───────────────────┐
          │                                       │
          ▼                                       ▼
┌─────────────────┐                     ┌─────────────────┐
│ stock_mutations │                     │ stock_history   │
├─────────────────┤                     ├─────────────────┤
│ id             │                     │ id             │
│ code           │                     │ stock_id       │
│ from_warehouse │                     │ previous_qty   │
│ to_warehouse   │                     │ new_qty        │
│ product_id     │                     │ change_qty     │
│ quantity       │                     │ reference_type │
│ status         │                     │ reference_id   │
│ created_by     │                     │ created_at     │
│ received_by    │                     └─────────────────┘
│ sent_at        │
│ received_at    │
│ notes          │
└─────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ inbound_transactions│  │ outbound_transactions│ │   customers    │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id             │    │ id             │    │ id             │
│ code           │    │ code           │    │ code           │
│ warehouse_id   │    │ warehouse_id   │    │ name           │
│ supplier_id    │───┐│ product_id     │    │ phone          │
│ product_id     │    ││ quantity       │    │ address        │
│ quantity       │    ││ customer_id    │────┘               │
│ received_date  │    ││ received_date  │    └─────────────────┘
│ created_by     │    ││ created_by     │
│ notes          │    ││ notes          │
└─────────────────┘    └─────────────────┘

📋 DETAIL STRUKTUR TABEL
1. TABEL: users

Menyimpan data semua user (Super Admin, Admin Gudang, Viewer)
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
name	VARCHAR(100)	Nama lengkap user
email	VARCHAR(100)	Username untuk login, unique
password	VARCHAR(255)	Hash password
role	ENUM	super_admin, admin_gudang, viewer
is_active	BOOLEAN	Default: true
created_at	DATETIME	Waktu dibuat
updated_at	DATETIME	Waktu diupdate
last_login	DATETIME	Terakhir login

🔑 Primary Key: id
🔍 Index: email, role
2. TABEL: warehouses

Menyimpan data gudang
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(20)	Kode unik gudang (WH01, WH02)
name	VARCHAR(100)	Nama gudang
address	TEXT	Alamat lengkap
phone	VARCHAR(20)	No telepon
is_active	BOOLEAN	Default: true
created_at	DATETIME	
updated_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, is_active
3. TABEL: user_warehouse

Menentukan Admin Gudang ditempatkan di gudang mana (Many-to-Many)
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
user_id	INT (FK)	→ users.id
warehouse_id	INT (FK)	→ warehouses.id
assigned_by	INT (FK)	→ users.id (Super Admin yang assign)
assigned_at	DATETIME	Waktu assign
is_primary	BOOLEAN	Default: true (1 user 1 gudang)

🔑 Primary Key: id
🔍 Index: user_id, warehouse_id
🔗 Foreign Key:

    user_id → users(id) ON DELETE CASCADE

    warehouse_id → warehouses(id) ON DELETE CASCADE

✏️ Catatan:

    Super Admin & Viewer TIDAK masuk tabel ini

    Hanya Admin Gudang yang direkam penempatannya

4. TABEL: categories

Kategori produk (bisa bertingkat/parent-child)
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
name	VARCHAR(100)	Nama kategori
slug	VARCHAR(100)	URL friendly
description	TEXT	
parent_id	INT (FK)	→ categories.id (null jika parent)
is_active	BOOLEAN	Default: true
created_at	DATETIME	

🔑 Primary Key: id
🔍 Index: parent_id, is_active
🔗 Foreign Key:

    parent_id → categories(id) ON DELETE SET NULL

5. TABEL: suppliers

Data supplier/vendor
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(20)	Kode supplier (SPL001)
name	VARCHAR(100)	Nama perusahaan
contact_person	VARCHAR(100)	Nama kontak
phone	VARCHAR(20)	
email	VARCHAR(100)	
address	TEXT	
tax_id	VARCHAR(50)	NPWP
is_active	BOOLEAN	Default: true
created_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, is_active
6. TABEL: customers

Data pembeli (untuk barang keluar)
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(20)	Kode customer (CST001)
name	VARCHAR(100)	Nama customer/toko
contact_person	VARCHAR(100)	
phone	VARCHAR(20)	
email	VARCHAR(100)	
address	TEXT	
is_active	BOOLEAN	Default: true
created_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, is_active
7. TABEL: products

Master produk/barang
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(50)	SKU/Kode produk (BRG001)
barcode	VARCHAR(50)	Barcode (opsional)
name	VARCHAR(200)	Nama produk
category_id	INT (FK)	→ categories.id
unit	VARCHAR(20)	Satuan (kg, liter, pcs, box)
min_stock	DECIMAL(10,2)	Stok minimal untuk notifikasi
max_stock	DECIMAL(10,2)	Stok maksimal (opsional)
price	DECIMAL(12,2)	Harga jual
cost	DECIMAL(12,2)	Harga beli/modal
description	TEXT	
image_url	VARCHAR(255)	Foto produk
is_active	BOOLEAN	Default: true
created_at	DATETIME	
updated_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, barcode, name, category_id, is_active
🔗 Foreign Key:

    category_id → categories(id) ON DELETE RESTRICT

8. TABEL: stock

Stok real-time per gudang per produk
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
warehouse_id	INT (FK)	→ warehouses.id
product_id	INT (FK)	→ products.id
quantity	DECIMAL(10,2)	Jumlah stok saat ini
reserved_qty	DECIMAL(10,2)	Stok yang dipesan (pending)
available_qty	DECIMAL(10,2)	quantity - reserved_qty (virtual)
last_updated	DATETIME	
updated_by	INT (FK)	→ users.id

🔑 Primary Key: id
🔍 Index: warehouse_id, product_id (UNIQUE)
🔗 Foreign Key:

    warehouse_id → warehouses(id) ON DELETE CASCADE

    product_id → products(id) ON DELETE CASCADE

    updated_by → users(id) ON DELETE SET NULL

✏️ Catatan:

    Kombinasi warehouse_id + product_id harus UNIQUE

    Satu produk di satu gudang hanya punya 1 record stok

9. TABEL: stock_history

History/audit trail perubahan stok
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
stock_id	INT (FK)	→ stock.id
warehouse_id	INT (FK)	→ warehouses.id
product_id	INT (FK)	→ products.id
previous_qty	DECIMAL(10,2)	Stok sebelumnya
new_qty	DECIMAL(10,2)	Stok setelah perubahan
change_qty	DECIMAL(10,2)	Selisih (+/-)
reference_type	ENUM	Sumber transaksi: inbound, outbound, mutation_sent, mutation_received, adjustment, opname
reference_id	INT	ID dari tabel sumber
reference_code	VARCHAR(50)	Nomor transaksi (BM001, BK001, dll)
notes	TEXT	Keterangan
created_by	INT (FK)	→ users.id
created_at	DATETIME	

🔑 Primary Key: id
🔍 Index: stock_id, warehouse_id, product_id, reference_type, created_at
🔗 Foreign Key:

    stock_id → stock(id) ON DELETE CASCADE

    warehouse_id → warehouses(id)

    product_id → products(id)

    created_by → users(id)

✏️ Catatan:

    Tabel ini hanya INSERT, tidak ada UPDATE/DELETE

    Menyimpan semua jejak perubahan stok

10. TABEL: inbound_transactions

Barang masuk dari supplier
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(50)	Nomor transaksi (BM-YYYYMMDD-001)
warehouse_id	INT (FK)	→ warehouses.id
supplier_id	INT (FK)	→ suppliers.id
product_id	INT (FK)	→ products.id
quantity	DECIMAL(10,2)	Jumlah barang masuk
unit_price	DECIMAL(12,2)	Harga beli saat itu
total_price	DECIMAL(12,2)	quantity * unit_price
received_date	DATE	Tanggal terima
created_by	INT (FK)	→ users.id
notes	TEXT	Keterangan
attachment	VARCHAR(255)	File surat jalan, invoice
created_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, warehouse_id, supplier_id, product_id, received_date, created_by
🔗 Foreign Key:

    warehouse_id → warehouses(id)

    supplier_id → suppliers(id)

    product_id → products(id)

    created_by → users(id)

11. TABEL: outbound_transactions

Barang keluar ke customer
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(50)	Nomor transaksi (BK-YYYYMMDD-001)
warehouse_id	INT (FK)	→ warehouses.id
customer_id	INT (FK)	→ customers.id
product_id	INT (FK)	→ products.id
quantity	DECIMAL(10,2)	Jumlah barang keluar
unit_price	DECIMAL(12,2)	Harga jual saat itu
total_price	DECIMAL(12,2)	quantity * unit_price
received_date	DATE	Tanggal keluar
created_by	INT (FK)	→ users.id
notes	TEXT	Keterangan
attachment	VARCHAR(255)	File surat jalan
created_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, warehouse_id, customer_id, product_id, received_date, created_by
🔗 Foreign Key:

    warehouse_id → warehouses(id)

    customer_id → customers(id)

    product_id → products(id)

    created_by → users(id)

12. TABEL: stock_mutations

Mutasi/transfer antar gudang
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(50)	Nomor mutasi (MT-YYYYMMDD-001)
from_warehouse	INT (FK)	→ warehouses.id (gudang asal)
to_warehouse	INT (FK)	→ warehouses.id (gudang tujuan)
product_id	INT (FK)	→ products.id
quantity	DECIMAL(10,2)	Jumlah dikirim
received_qty	DECIMAL(10,2)	Jumlah diterima (default = quantity)
damaged_qty	DECIMAL(10,2)	Jumlah rusak (default = 0)
status	ENUM	dikirim, diterima, ditolak, selesai
sent_at	DATETIME	Waktu kirim
received_at	DATETIME	Waktu terima
created_by	INT (FK)	→ users.id (yg kirim)
received_by	INT (FK)	→ users.id (yg terima)
notes	TEXT	Keterangan
created_at	DATETIME	
updated_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, from_warehouse, to_warehouse, product_id, status, sent_at
🔗 Foreign Key:

    from_warehouse → warehouses(id)

    to_warehouse → warehouses(id)

    product_id → products(id)

    created_by → users(id)

    received_by → users(id)

13. TABEL: opname (Opsional)

Stock opname / stok fisik
Kolom	Tipe Data	Keterangan
id	INT (PK)	Auto increment
code	VARCHAR(50)	Nomor opname
warehouse_id	INT (FK)	→ warehouses.id
product_id	INT (FK)	→ products.id
system_qty	DECIMAL(10,2)	Stok sistem
physical_qty	DECIMAL(10,2)	Stok fisik
difference	DECIMAL(10,2)	selisih (fisik - sistem)
notes	TEXT	Keterangan selisih
opname_date	DATE	
created_by	INT (FK)	→ users.id
approved_by	INT (FK)	→ users.id
status	ENUM	draft, selesai, disetujui
created_at	DATETIME	

🔑 Primary Key: id
🔍 Index: code, warehouse_id, opname_date
🔗 RINGKASAN RELASI ANTAR TABEL
text

┌───────────┐       ┌───────────────┐       ┌───────────┐
│  users    │───────│user_warehouse │───────│warehouses │
└───────────┘       └───────────────┘       └───────────┘
     │                                              │
     │                                              │
     ▼                                              ▼
┌───────────┐      ┌───────────┐            ┌───────────┐
│inbound/   │      │  stock    │◄───────────│ products  │
│outbound/  │─────>│ history   │            └───────────┘
│mutation   │      └───────────┘                   ▲
└───────────┘            │                         │
     ▲                  │                         │
     │                  ▼                         │
     │           ┌───────────┐            ┌───────────┐
     └───────────│  stock    │            │categories │
                 └───────────┘            └───────────┘
                       │
                       ▼
                 ┌───────────┐
                 │mutation   │
                 └───────────┘

RELASI LENGKAP:

1. users → user_warehouse → warehouses  (M:M)
2. warehouses → stock → products        (1:M)
3. products → categories                (M:1)
4. inbound_transactions → suppliers     (M:1)
5. inbound_transactions → warehouses    (M:1)
6. inbound_transactions → products      (M:1)
7. inbound_transactions → users         (M:1)
8. outbound_transactions → customers    (M:1)
9. outbound_transactions → warehouses   (M:1)
10. outbound_transactions → products     (M:1)
11. outbound_transactions → users        (M:1)
12. stock_mutations → from_warehouse    (M:1)
13. stock_mutations → to_warehouse      (M:1)
14. stock_mutations → products          (M:1)
15. stock_mutations → created_by        (M:1)
16. stock_mutations → received_by       (M:1)
17. stock_history → stock              (M:1)
18. stock_history → warehouses         (M:1)
19. stock_history → products           (M:1)
20. stock_history → users             (M:1)

📊 CONTOH DATA & RELASI
CONTOH 1: User Admin Gudang

Tabel users:
id	name	email	role
2	Budi	budi@email.com	admin_gudang

Tabel user_warehouse:
id	user_id	warehouse_id	assigned_by
1	2	2	1

Tabel warehouses:
id	code	name
2	WH02	Gudang Bandung

➡️ Hasil: Budi adalah Admin di Gudang Bandung
CONTOH 2: Stok Produk di Gudang

Tabel products:
id	code	name	unit	min_stock
1	BRG001	Beras	kg	50

Tabel warehouses:
id	code	name
1	WH01	Gudang Pusat
2	WH02	Gudang Bandung

Tabel stock:
id	warehouse_id	product_id	quantity
1	1	1	400.00
2	2	1	110.00

➡️ Hasil:

    Beras di Gudang Pusat = 400 kg

    Beras di Gudang Bandung = 110 kg

CONTOH 3: Transaksi & History

Tabel inbound_transactions:
id	code	warehouse_id	product_id	quantity	created_by
1	BM-20241124-001	2	1	100.00	2

Tabel stock_history:
id	stock_id	previous_qty	new_qty	change_qty	reference_type	reference_id
1	2	10.00	110.00	+100.00	inbound	1

Tabel stock (setelah update):
id	warehouse_id	product_id	quantity
2	2	1	110.00

➡️ Hasil:

    Barang masuk 100 kg

    Stok naik dari 10 kg → 110 kg

    Semua tercatat di history

✅ CEK LIST INTEGRITAS DATABASE
No	Aturan	Status
1	Satu user (admin gudang) hanya punya 1 gudang aktif	✅ UNIQUE user_id di user_warehouse
2	Super Admin & Viewer tidak masuk user_warehouse	✅ Cek role di aplikasi
3	Kombinasi warehouse_id + product_id di tabel stock harus UNIQUE	✅ UNIQUE KEY
4	Stok tidak boleh minus	✅ Cek di aplikasi
5	Setiap perubahan stok WAJIB tercatat di stock_history	✅ TRIGGER/APPLICATION
6	Foreign key harus terjaga (ON DELETE CASCADE/RESTRICT)	✅ Sesuai kebutuhan
7	Nomor transaksi harus unik	✅ Index UNIQUE
🎯 KESIMPULAN ERD
text

┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│   ✅ 13 TABEL UTAMA                                            │
│   ✅ RELASI JELAS (1:M, M:M)                                   │
│   ✅ AUDIT TRAIL (stock_history)                               │
│   ✅ MULTI WAREHOUSE SIAP                                      │
│   ✅ 3 LEVEL USER TERDEFINISI                                  │
│   ✅ INTEGRITAS DATA TERJAGA                                   │
│                                                                 │
│   🚀 SIAP DIIMPLEMENTASIKAN KE DATABASE!                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘

Catatan Implementasi:

    Gunakan InnoDB untuk foreign key

    Aktifkan strict mode

    Pertimbangkan partitioning untuk stock_history jika data besar

    Backup rutin untuk audit trail

ini saya pakai role spatie ya terus inisaya pakai laravel12 inertia react auth pakai fortify nanti kan buat controller, action, model ,factory, seeder, migrasi , policy ,request ,test ,routes, view,type data ts nya gimana strukturnya dan mana dulu yang harus di kerjakan biar rapi dan mudah di maintenance danberikan perintah artisanya
🚀 STRUKTUR & ALUR KERJA LARAVEL 12 + INERTIA REACT + SPATIE ROLE
📦 STACK YANG DIGUNAKAN

    Laravel 12

    Inertia.js + React + TypeScript

    Laravel Fortify (Auth)

    Spatie Laravel Permission (Roles)

    Laravel Data (Type safe DTO) - opsional

    Pest/PHPUnit (Testing)

🗺️ STRUKTUR FOLDER KOMPLIT
text

📁 project-root/
├── 📁 app/
│   ├── 📁 Actions/              # Single Action Classes
│   │   ├── 📁 Warehouse/
│   │   │   ├── SelectWarehouseAction.php
│   │   │   ├── SwitchWarehouseAction.php
│   │   │   └── GetUserWarehouseAction.php
│   │   ├── 📁 Stock/
│   │   │   ├── UpdateStockAction.php
│   │   │   ├── CheckStockAvailabilityAction.php
│   │   │   └── CalculateStockStatusAction.php
│   │   ├── 📁 Transaction/
│   │   │   ├── CreateInboundTransactionAction.php
│   │   │   ├── CreateOutboundTransactionAction.php
│   │   │   └── CreateMutationTransactionAction.php
│   │   └── 📁 User/
│   │       ├── AssignWarehouseToUserAction.php
│   │       └── SyncUserPermissionAction.php
│   │
│   ├── 📁 Models/
│   │   ├── User.php
│   │   ├── Warehouse.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Supplier.php
│   │   ├── Customer.php
│   │   ├── Stock.php
│   │   ├── StockHistory.php
│   │   ├── InboundTransaction.php
│   │   ├── OutboundTransaction.php
│   │   ├── StockMutation.php
│   │   └── UserWarehouse.php
│   │
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/
│   │   │   ├── 📁 Api/              # API Controllers (opsional)
│   │   │   ├── 📁 Web/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── WarehouseController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── StockController.php
│   │   │   │   ├── InboundController.php
│   │   │   │   ├── OutboundController.php
│   │   │   │   ├── MutationController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── UserController.php
│   │   │   └── Controller.php
│   │   │
│   │   ├── 📁 Middleware/
│   │   │   ├── SetWarehouseContext.php   # Middleware penting!
│   │   │   └── CheckWarehouseAccess.php
│   │   │
│   │   ├── 📁 Requests/
│   │   │   ├── 📁 Warehouse/
│   │   │   ├── 📁 Product/
│   │   │   ├── 📁 Transaction/
│   │   │   └── 📁 User/
│   │   │
│   │   └── 📁 Resources/          # Laravel Resources (API)
│   │
│   ├── 📁 Services/
│   │   ├── WarehouseContextService.php  # Session manager
│   │   ├── StockService.php
│   │   ├── NotificationService.php
│   │   └── ReportService.php
│   │
│   └── 📁 Policies/
│       ├── WarehousePolicy.php
│       ├── ProductPolicy.php
│       ├── StockPolicy.php
│       ├── TransactionPolicy.php
│       └── UserPolicy.php
│
├── 📁 database/
│   ├── 📁 migrations/
│   ├── 📁 factories/
│   └── 📁 seeders/
│       ├── DatabaseSeeder.php
│       ├── RolePermissionSeeder.php    # #1 PRIORITAS!
│       ├── UserSeeder.php
│       ├── WarehouseSeeder.php
│       └── ProductSeeder.php
│
├── 📁 resources/
│   └── 📁 js/
│       ├── 📁 Pages/              # Inertia Pages (React TS)
│       │   ├── 📁 Auth/
│       │   ├── 📁 Dashboard/
│       │   ├── 📁 Warehouse/
│       │   ├── 📁 Stock/
│       │   ├── 📁 Transaction/
│       │   └── 📁 Report/
│       │
│       ├── 📁 Components/        # Reusable React Components
│       │   ├── 📁 UI/
│       │   ├── 📁 Layouts/
│       │   └── 📁 Forms/
│       │
│       ├── 📁 Hooks/            # Custom React Hooks
│       │   ├── useWarehouse.ts
│       │   ├── usePermission.ts
│       │   └── useNotification.ts
│       │
│       ├── 📁 Types/            # TypeScript Deklarasi
│       │   ├── models.ts
│       │   ├── requests.ts
│       │   └── responses.ts
│       │
│       └── 📁 Utils/
│
├── 📁 routes/
│   ├── web.php
│   ├── api.php
│   └── inertia.php              # Inertia route grouping
│
├── 📁 tests/
│   ├── 📁 Feature/
│   └── 📁 Unit/
│
└── 📁 storage/

🎯 PRIORITAS PENGERJAAN (URUTAN WAJIB!)
🔴 TAHAP 1: SETUP & KONFIGURASI DASAR (HARI 1)
bash

# 1. Install Laravel 12
composer create-project laravel/laravel warehouse-system "^12.0"

# 2. Install Fortify
composer require laravel/fortify
php artisan fortify:install

# 3. Install Spatie Permission
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 4. Install Inertia React TS
composer require inertiajs/inertia-laravel
npm install @inertiajs/react react react-dom typescript @types/react @types/react-dom
npm install -D @vitejs/plugin-react

# 5. Install Laravel Data (Opsional - untuk type safety)
composer require spatie/laravel-data

# 6. Setup database .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse_db
DB_USERNAME=root
DB_PASSWORD=

# 7. Jalankan migrasi awal
php artisan migrate

🔴 TAHAP 2: MODEL & MIGRASI PERTAMA (HARI 1-2)
bash

# 1. ROLE & PERMISSION (WAJIB PERTAMA!)
php artisan make:migration create_permission_tables
# Sudah otomatis dari Spatie

# 2. USER (Extend dengan Spatie)
php artisan make:model User --migration --factory
# Edit User.php tambahkan: use Spatie\Permission\Traits\HasRoles;

# 3. WAREHOUSE
php artisan make:model Warehouse --migration --factory --seed
php artisan make:model UserWarehouse --migration --factory

# 4. CATEGORY & PRODUCT
php artisan make:model Category --migration --factory --seed
php artisan make:model Product --migration --factory --seed
php artisan make:model Supplier --migration --factory --seed
php artisan make:model Customer --migration --factory --seed

# 5. STOCK CORE
php artisan make:model Stock --migration --factory
php artisan make:model StockHistory --migration

# 6. TRANSACTIONS
php artisan make:model InboundTransaction --migration --factory
php artisan make:model OutboundTransaction --migration --factory
php artisan make:model StockMutation --migration --factory

# 7. JALANKAN MIGRASI
php artisan migrate

📄 isi migration file yang WAJIB diperhatikan:
php

// database/migrations/xxxx_create_user_warehouse_table.php
public function up()
{
    Schema::create('user_warehouse', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
        $table->foreignId('assigned_by')->nullable()->constrained('users');
        $table->timestamp('assigned_at')->useCurrent();
        $table->boolean('is_primary')->default(true);
        $table->unique(['user_id', 'warehouse_id']); // UNIQUE!
        $table->index(['user_id', 'is_primary']);
    });
}

// database/migrations/xxxx_create_stock_table.php
public function up()
{
    Schema::create('stock', function (Blueprint $table) {
        $table->id();
        $table->foreignId('warehouse_id')->constrained();
        $table->foreignId('product_id')->constrained();
        $table->decimal('quantity', 12, 2)->default(0);
        $table->decimal('reserved_qty', 12, 2)->default(0);
        $table->timestamp('last_updated')->useCurrent();
        $table->foreignId('updated_by')->nullable()->constrained('users');
        $table->unique(['warehouse_id', 'product_id']); // UNIQUE!
        $table->index(['warehouse_id', 'product_id', 'quantity']);
    });
}

🔴 TAHAP 3: SEEDER & FACTORY (HARI 2)

URUTAN SEEDER WAJIB:
bash

# 1. Buat seeders
php artisan make:seeder RolePermissionSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder WarehouseSeeder
php artisan make:seeder ProductSeeder
php artisan make:seeder TransactionSeeder

# 2. Edit DatabaseSeeder.php

📄 database/seeders/RolePermissionSeeder.php - #1 PRIORITAS!
php

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============ PERMISSIONS ============
        $permissions = [
            // Warehouse
            'view any warehouses',
            'view warehouse',
            'create warehouse',
            'update warehouse',
            'delete warehouse',
            
            // Product
            'view any products',
            'view product',
            'create product',
            'update product',
            'delete product',
            
            // Stock
            'view any stocks',
            'view stock',
            'update stock',
            
            // Transaction
            'view any inbound',
            'view inbound',
            'create inbound',
            'update inbound',
            
            'view any outbound',
            'view outbound',
            'create outbound',
            'update outbound',
            
            // Mutation
            'view any mutations',
            'view mutation',
            'create mutation',
            'receive mutation',
            'update mutation',
            
            // Report
            'view any reports',
            'view report',
            'export report',
            
            // User Management
            'view any users',
            'view user',
            'create user',
            'update user',
            'delete user',
            'assign warehouse',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ============ ROLES ============
        
        // 1. SUPER ADMIN
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());
        
        // 2. ADMIN GUDANG
        $adminGudang = Role::create(['name' => 'admin_gudang']);
        $adminGudang->givePermissionTo([
            'view warehouse',
            'view product',
            'view stock',
            'update stock',
            'view inbound',
            'create inbound',
            'view outbound',
            'create outbound',
            'view mutation',
            'create mutation',
            'receive mutation',
            'view report',
        ]);
        
        // 3. VIEWER
        $viewer = Role::create(['name' => 'viewer']);
        $viewer->givePermissionTo([
            'view any warehouses',
            'view any products',
            'view any stocks',
            'view any inbound',
            'view any outbound',
            'view any mutations',
            'view any reports',
            'export report',
        ]);

        // ============ CREATE DEFAULT USERS ============
        
        // Super Admin
        $super = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $super->assignRole('super_admin');

        // Admin Gudang (nanti diassign ke warehouse)
        $admin = User::create([
            'name' => 'Admin Bandung',
            'email' => 'admin@bandung.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin_gudang');

        // Viewer
        $viewer = User::create([
            'name' => 'Direktur',
            'email' => 'direktur@company.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $viewer->assignRole('viewer');
    }
}

📄 database/seeders/DatabaseSeeder.php:
php

public function run()
{
    $this->call([
        RolePermissionSeeder::class,  // #1 HARUS PERTAMA!
        WarehouseSeeder::class,       // #2
        UserSeeder::class,           // #3 (tapi RolePermission sudah bikin user)
        ProductSeeder::class,        // #4
        TransactionSeeder::class,    // #5
    ]);
}

JALANKAN:
bash

php artisan db:seed --class=RolePermissionSeeder
# Atau langsung
php artisan db:seed

🔴 TAHAP 4: MODEL RELATIONS & TRAITS (HARI 2-3)

📄 app/Models/User.php:
php

<?php

namespace App\Models;

use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticatable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ============ RELATIONS ============
    
    // Admin Gudang -> Warehouse (M:M via user_warehouse)
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'user_warehouse')
                    ->withPivot('assigned_by', 'assigned_at', 'is_primary')
                    ->withTimestamps();
    }

    // Warehouse aktif yang sedang dipilih (untuk Super Admin)
    public function activeWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'active_warehouse_id');
    }

    // Scope untuk filter Admin Gudang
    public function scopeAdmins($query)
    {
        return $query->role('admin_gudang');
    }

    // Helper methods
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdminGudang(): bool
    {
        return $this->hasRole('admin_gudang');
    }

    public function isViewer(): bool
    {
        return $this->hasRole('viewer');
    }

    public function getAssignedWarehouseAttribute()
    {
        if ($this->isAdminGudang()) {
            return $this->warehouses()->wherePivot('is_primary', true)->first();
        }
        return null;
    }
}

📄 app/Models/Warehouse.php:
php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    protected $fillable = [
        'code', 'name', 'address', 'phone', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ============ RELATIONS ============
    
    // Admin yang ditempatkan di gudang ini
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_warehouse')
                    ->withPivot('assigned_by', 'assigned_at')
                    ->withTimestamps();
    }

    // Stok di gudang ini
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    // Transaksi masuk
    public function inboundTransactions(): HasMany
    {
        return $this->hasMany(InboundTransaction::class);
    }

    // Transaksi keluar
    public function outboundTransactions(): HasMany
    {
        return $this->hasMany(OutboundTransaction::class);
    }

    // Mutasi sebagai asal
    public function mutationsFrom(): HasMany
    {
        return $this->hasMany(StockMutation::class, 'from_warehouse');
    }

    // Mutasi sebagai tujuan
    public function mutationsTo(): HasMany
    {
        return $this->hasMany(StockMutation::class, 'to_warehouse');
    }

    // ============ SCOPES ============
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

📄 app/Models/Stock.php:
php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'warehouse_id', 'product_id', 'quantity', 
        'reserved_qty', 'last_updated', 'updated_by'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_qty' => 'decimal:2',
        'last_updated' => 'datetime',
    ];

    // ============ RELATIONS ============
    
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories()
    {
        return $this->hasMany(StockHistory::class);
    }

    // ============ ATTRIBUTES ============
    
    public function getAvailableQtyAttribute(): float
    {
        return $this->quantity - $this->reserved_qty;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->product->min_stock;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->quantity <= 0;
    }

    public function getStatusAttribute(): string
    {
        if ($this->quantity <= 0) return 'Habis';
        if ($this->quantity <= $this->product->min_stock) return 'Menipis';
        return 'Normal';
    }
}

🔴 TAHAP 5: MIDDLEWARE & SERVICE (HARI 3) - KRUSIAL!
bash

php artisan make:middleware SetWarehouseContext
php artisan make:middleware CheckWarehouseAccess

📄 app/Http/Middleware/SetWarehouseContext.php:
php

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\WarehouseContextService;

class SetWarehouseContext
{
    public function __construct(
        protected WarehouseContextService $warehouseContextService
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user) {
            // Admin Gudang: OTOMATIS ke gudangnya
            if ($user->isAdminGudang()) {
                $warehouse = $user->assignedWarehouse;
                if ($warehouse) {
                    $this->warehouseContextService->setWarehouse($warehouse->id);
                    session(['active_warehouse_id' => $warehouse->id]);
                    session(['active_warehouse_name' => $warehouse->name]);
                }
            }
            
            // Super Admin: Pake session atau default ke null
            if ($user->isSuperAdmin()) {
                if (!session('active_warehouse_id') && $request->has('warehouse_id')) {
                    $this->warehouseContextService->setWarehouse($request->warehouse_id);
                }
            }
            
            // Viewer: Tidak perlu context gudang aktif
        }

        // Share ke Inertia
        if ($user) {
            \Inertia\Inertia::share([
                'auth' => [
                    'user' => $user,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                    'active_warehouse' => $this->warehouseContextService->getActiveWarehouse(),
                ]
            ]);
        }

        return $next($request);
    }
}

📄 app/Services/WarehouseContextService.php:
php

<?php

namespace App\Services;

use App\Models\Warehouse;

class WarehouseContextService
{
    public function setWarehouse(int $warehouseId): void
    {
        session(['active_warehouse_id' => $warehouseId]);
    }

    public function getWarehouseId(): ?int
    {
        return session('active_warehouse_id');
    }

    public function getActiveWarehouse(): ?Warehouse
    {
        $warehouseId = $this->getWarehouseId();
        
        if ($warehouseId) {
            return Warehouse::find($warehouseId);
        }
        
        return null;
    }

    public function clearWarehouse(): void
    {
        session()->forget('active_warehouse_id');
        session()->forget('active_warehouse_name');
    }

    public function canAccessWarehouse(int $warehouseId, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        
        if (!$user) return false;
        
        // Super Admin bisa akses semua
        if ($user->isSuperAdmin()) return true;
        
        // Viewer bisa akses semua (read only)
        if ($user->isViewer()) return true;
        
        // Admin Gudang cek di user_warehouse
        if ($user->isAdminGudang()) {
            return $user->warehouses()->where('warehouse_id', $warehouseId)->exists();
        }
        
        return false;
    }
}

📄 Register Middleware di Kernel:
php

// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \App\Http\Middleware\SetWarehouseContext::class,
    ],
];

protected $routeMiddleware = [
    // ... other middleware
    'warehouse.access' => \App\Http\Middleware\CheckWarehouseAccess::class,
];

🔴 TAHAP 6: ACTIONS (HARI 3-4)
bash

# Buat folder Actions
mkdir -p app/Actions/Warehouse
mkdir -p app/Actions/Stock
mkdir -p app/Actions/Transaction
mkdir -p app/Actions/User

📄 app/Actions/Stock/UpdateStockAction.php:
php

<?php

namespace App\Actions\Stock;

use App\Models\Stock;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

class UpdateStockAction
{
    public function execute(
        int $warehouseId,
        int $productId,
        float $quantity,
        string $type,
        int $referenceId,
        string $referenceCode,
        ?string $notes = null
    ): Stock {
        return DB::transaction(function () use (
            $warehouseId, $productId, $quantity, 
            $type, $referenceId, $referenceCode, $notes
        ) {
            // Get or create stock
            $stock = Stock::firstOrCreate(
                [
                    'warehouse_id' => $warehouseId,
                    'product_id' => $productId
                ],
                [
                    'quantity' => 0,
                    'reserved_qty' => 0,
                    'last_updated' => now(),
                    'updated_by' => auth()->id()
                ]
            );

            // Save previous quantity
            $previousQty = $stock->quantity;
            
            // Update stock
            $stock->quantity += $quantity;
            $stock->last_updated = now();
            $stock->updated_by = auth()->id();
            $stock->save();

            // Create history
            StockHistory::create([
                'stock_id' => $stock->id,
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'previous_qty' => $previousQty,
                'new_qty' => $stock->quantity,
                'change_qty' => $quantity,
                'reference_type' => $type,
                'reference_id' => $referenceId,
                'reference_code' => $referenceCode,
                'notes' => $notes,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return $stock;
        });
    }
}

📄 app/Actions/Transaction/CreateInboundTransactionAction.php:
php

<?php

namespace App\Actions\Transaction;

use App\Models\InboundTransaction;
use App\Actions\Stock\UpdateStockAction;
use Illuminate\Support\Facades\DB;

class CreateInboundTransactionAction
{
    public function __construct(
        protected UpdateStockAction $updateStockAction
    ) {}

    public function execute(array $data): InboundTransaction
    {
        return DB::transaction(function () use ($data) {
            // Generate code
            $code = 'BM-' . date('Ymd') . '-' . 
                    str_pad(InboundTransaction::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
            
            // Create transaction
            $transaction = InboundTransaction::create([
                'code' => $code,
                'warehouse_id' => $data['warehouse_id'],
                'supplier_id' => $data['supplier_id'],
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'unit_price' => $data['unit_price'] ?? 0,
                'total_price' => ($data['unit_price'] ?? 0) * $data['quantity'],
                'received_date' => $data['received_date'] ?? now(),
                'created_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            // Update stock (+ quantity)
            $this->updateStockAction->execute(
                warehouseId: $data['warehouse_id'],
                productId: $data['product_id'],
                quantity: $data['quantity'],
                type: 'inbound',
                referenceId: $transaction->id,
                referenceCode: $code,
                notes: $data['notes'] ?? null
            );

            return $transaction;
        });
    }
}

🔴 TAHAP 7: CONTROLLERS & REQUESTS (HARI 4-5)
bash

# Controllers
php artisan make:controller Web/DashboardController
php artisan make:controller Web/WarehouseController --resource
php artisan make:controller Web/StockController
php artisan make:controller Web/InboundController --resource
php artisan make:controller Web/OutboundController --resource
php artisan make:controller Web/MutationController --resource
php artisan make:controller Web/ReportController

# Requests
php artisan make:request Warehouse/StoreWarehouseRequest
php artisan make:request Warehouse/UpdateWarehouseRequest
php artisan make:request Transaction/StoreInboundRequest
php artisan make:request Transaction/StoreOutboundRequest
php artisan make:request Transaction/StoreMutationRequest

📄 app/Http/Controllers/Web/InboundController.php:
php

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StoreInboundRequest;
use App\Actions\Transaction\CreateInboundTransactionAction;
use App\Models\InboundTransaction;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;

class InboundController extends Controller
{
    public function __construct(
        protected CreateInboundTransactionAction $createInboundAction
    ) {}

    public function index(Request $request)
    {
        $this->authorize('view any inbound');

        $query = InboundTransaction::with(['warehouse', 'supplier', 'product', 'creator']);
        
        // Filter by warehouse context
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isViewer()) {
            $query->where('warehouse_id', session('active_warehouse_id'));
        }

        // Filter by date
        if ($request->filled('start_date')) {
            $query->whereDate('received_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('received_date', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('received_date', 'desc')
                             ->paginate(15)
                             ->withQueryString();

        return Inertia::render('Transaction/Inbound/Index', [
            'transactions' => $transactions,
            'filters' => $request->only(['start_date', 'end_date'])
        ]);
    }

    public function create()
    {
        $this->authorize('create inbound');

        // Get warehouses based on role
        $warehouses = auth()->user()->isSuperAdmin() 
            ? Warehouse::active()->get()
            : collect([auth()->user()->assignedWarehouse]);
            
        return Inertia::render('Transaction/Inbound/Create', [
            'warehouses' => $warehouses,
            'suppliers' => Supplier::active()->get(),
            'products' => Product::active()->get(),
            'active_warehouse' => session('active_warehouse_id')
        ]);
    }

    public function store(StoreInboundRequest $request)
    {
        $this->authorize('create inbound');

        $transaction = $this->createInboundAction->execute($request->validated());

        return redirect()->route('inbound.index')
            ->with('success', 'Barang masuk berhasil dicatat. #' . $transaction->code);
    }

    public function show(InboundTransaction $inbound)
    {
        $this->authorize('view inbound');

        $inbound->load(['warehouse', 'supplier', 'product', 'creator']);

        return Inertia::render('Transaction/Inbound/Show', [
            'transaction' => $inbound
        ]);
    }
}

🔴 TAHAP 8: POLICIES (HARI 5)
bash

php artisan make:policy WarehousePolicy --model=Warehouse
php artisan make:policy ProductPolicy --model=Product
php artisan make:policy StockPolicy --model=Stock
php artisan make:policy InboundTransactionPolicy --model=InboundTransaction
php artisan make:policy OutboundTransactionPolicy --model=OutboundTransaction
php artisan make:policy UserPolicy --model=User

📄 app/Policies/InboundTransactionPolicy.php:
php

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InboundTransaction;

class InboundTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view any inbound');
    }

    public function view(User $user, InboundTransaction $inbound): bool
    {
        // Super Admin & Viewer bisa view semua
        if ($user->isSuperAdmin() || $user->isViewer()) {
            return $user->can('view any inbound');
        }
        
        // Admin Gudang hanya bisa view gudangnya
        if ($user->isAdminGudang()) {
            return $user->can('view inbound') && 
                   $inbound->warehouse_id === $user->assignedWarehouse?->id;
        }
        
        return false;
    }

    public function create(User $user): bool
    {
        // Viewer TIDAK BISA create
        if ($user->isViewer()) {
            return false;
        }
        
        return $user->can('create inbound');
    }

    public function update(User $user, InboundTransaction $inbound): bool
    {
        // Tidak bisa update setelah disimpan
        return false;
    }

    public function delete(User $user, InboundTransaction $inbound): bool
    {
        // Tidak bisa delete (audit trail)
        return false;
    }
}

🔴 TAHAP 9: ROUTES (HARI 5-6)

📄 routes/web.php:
php

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web;

// Public routes (no auth)
Route::get('/', function () {
    return redirect('/login');
});

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [Web\DashboardController::class, 'index'])
         ->name('dashboard');
    
    // Warehouse Selection (khusus Super Admin)
    Route::get('/select-warehouse', [Web\WarehouseController::class, 'select'])
         ->name('warehouse.select')
         ->middleware('can:view any warehouses');
    Route::post('/warehouse/switch', [Web\WarehouseController::class, 'switch'])
         ->name('warehouse.switch');
    
    // Routes yang butuh context warehouse
    Route::middleware(['warehouse.access'])->group(function () {
        
        // Warehouse Management
        Route::resource('warehouses', Web\WarehouseController::class)
              ->except(['show']);
        
        // Products
        Route::resource('products', Web\ProductController::class);
        Route::get('products/export', [Web\ProductController::class, 'export'])
             ->name('products.export');
        
        // Stock
        Route::get('/stock', [Web\StockController::class, 'index'])
             ->name('stock.index');
        Route::get('/stock/all', [Web\StockController::class, 'all'])
             ->name('stock.all')
             ->middleware('can:view any stocks');
        
        // Transactions
        Route::resource('inbound', Web\InboundController::class)
              ->except(['edit', 'update', 'destroy']);
        Route::resource('outbound', Web\OutboundController::class)
              ->except(['edit', 'update', 'destroy']);
        Route::resource('mutations', Web\MutationController::class);
        Route::post('/mutations/{mutation}/receive', 
                    [Web\MutationController::class, 'receive'])
             ->name('mutations.receive');
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/stock', [Web\ReportController::class, 'stock'])
                 ->name('stock');
            Route::get('/inbound', [Web\ReportController::class, 'inbound'])
                 ->name('inbound');
            Route::get('/outbound', [Web\ReportController::class, 'outbound'])
                 ->name('outbound');
            Route::get('/mutation', [Web\ReportController::class, 'mutation'])
                 ->name('mutation');
        });
    });
    
    // User Management (hanya Super Admin)
    Route::resource('users', Web\UserController::class)
         ->middleware('can:view any users');
    Route::post('/users/{user}/assign-warehouse', 
                [Web\UserController::class, 'assignWarehouse'])
         ->name('users.assign-warehouse')
         ->middleware('can:assign warehouse');
});

🔴 TAHAP 10: TYPESCRIPT & INERTIA REACT (HARI 6-7)

📄 resources/js/types/models.ts:
typescript

export interface User {
    id: number;
    name: string;
    email: string;
    role: string[];
    permissions: string[];
    assigned_warehouse?: Warehouse | null;
    active_warehouse?: Warehouse | null;
    created_at: string;
}

export interface Warehouse {
    id: number;
    code: string;
    name: string;
    address: string;
    phone: string;
    is_active: boolean;
    created_at: string;
    pivot?: {
        is_primary: boolean;
        assigned_at: string;
    }
}

export interface Product {
    id: number;
    code: string;
    name: string;
    category_id: number;
    category?: Category;
    unit: string;
    min_stock: number;
    price: number;
    is_active: boolean;
}

export interface Stock {
    id: number;
    warehouse_id: number;
    warehouse?: Warehouse;
    product_id: number;
    product?: Product;
    quantity: number;
    reserved_qty: number;
    available_qty: number;
    status: 'Normal' | 'Menipis' | 'Habis';
    last_updated: string;
}

export interface InboundTransaction {
    id: number;
    code: string;
    warehouse_id: number;
    warehouse?: Warehouse;
    supplier_id: number;
    supplier?: Supplier;
    product_id: number;
    product?: Product;
    quantity: number;
    unit_price: number;
    total_price: number;
    received_date: string;
    created_by: number;
    creator?: User;
    notes?: string;
}

export interface StockMutation {
    id: number;
    code: string;
    from_warehouse: number;
    from_warehouse_detail?: Warehouse;
    to_warehouse: number;
    to_warehouse_detail?: Warehouse;
    product_id: number;
    product?: Product;
    quantity: number;
    received_qty: number;
    damaged_qty: number;
    status: 'dikirim' | 'diterima' | 'ditolak';
    sent_at: string;
    received_at?: string;
    created_by: number;
    creator?: User;
    received_by?: number;
    receiver?: User;
    notes?: string;
}

// ============ REQUEST TYPES ============

export interface InboundStoreRequest {
    warehouse_id: number;
    supplier_id: number;
    product_id: number;
    quantity: number;
    unit_price?: number;
    received_date?: string;
    notes?: string;
}

export interface OutboundStoreRequest {
    warehouse_id: number;
    customer_id: number;
    product_id: number;
    quantity: number;
    notes?: string;
}

export interface MutationStoreRequest {
    from_warehouse: number;
    to_warehouse: number;
    product_id: number;
    quantity: number;
    notes?: string;
}

export interface MutationReceiveRequest {
    received_qty: number;
    damaged_qty?: number;
    notes?: string;
}

📄 resources/js/hooks/useWarehouse.ts:
typescript

import { usePage, router } from '@inertiajs/react';
import { useCallback } from 'react';

export function useWarehouse() {
    const { auth } = usePage().props as any;
    
    const activeWarehouse = auth.active_warehouse;
    const isSuperAdmin = auth.roles?.includes('super_admin');
    const isAdminGudang = auth.roles?.includes('admin_gudang');
    const isViewer = auth.roles?.includes('viewer');
    
    const switchWarehouse = useCallback((warehouseId: number) => {
        router.post('/warehouse/switch', {
            warehouse_id: warehouseId
        }, {
            preserveState: false,
            preserveScroll: true,
        });
    }, []);
    
    const canAccessWarehouse = useCallback((warehouseId: number) => {
        if (isSuperAdmin || isViewer) return true;
        if (isAdminGudang) {
            return auth.user?.assigned_warehouse?.id === warehouseId;
        }
        return false;
    }, [isSuperAdmin, isViewer, isAdminGudang, auth.user]);
    
    return {
        activeWarehouse,
        isSuperAdmin,
        isAdminGudang,
        isViewer,
        switchWarehouse,
        canAccessWarehouse,
    };
}

📄 resources/js/Pages/Warehouse/Select.tsx:
typescript

import React from 'react';
import { Head, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Warehouse } from '@/types/models';

interface Props {
    warehouses: Warehouse[];
}

export default function SelectWarehouse({ warehouses }: Props) {
    const handleSelect = (warehouseId: number) => {
        router.post('/warehouse/switch', {
            warehouse_id: warehouseId
        }, {
            onSuccess: () => {
                router.visit('/dashboard');
            }
        });
    };
    
    return (
        <AuthenticatedLayout>
            <Head title="Pilih Gudang" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h2 className="text-2xl font-bold mb-6">
                                Pilih Gudang
                            </h2>
                            
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {warehouses.map((warehouse) => (
                                    <div
                                        key={warehouse.id}
                                        className="border rounded-lg p-6 cursor-pointer hover:shadow-lg transition"
                                        onClick={() => handleSelect(warehouse.id)}
                                    >
                                        <h3 className="text-lg font-semibold">
                                            {warehouse.name}
                                        </h3>
                                        <p className="text-gray-600 mt-2">
                                            {warehouse.address}
                                        </p>
                                        <div className="mt-4 text-blue-600">
                                            Klik untuk memilih →
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

🔴 TAHAP 11: TESTING (HARI 7-8)
bash

# Install Pest
composer require pestphp/pest --dev
php artisan pest:install

# Buat test files
php artisan make:test Feature/InboundTransactionTest --pest
php artisan make:test Feature/StockMutationTest --pest
php artisan make:test Feature/WarehouseContextTest --pest

📄 tests/Feature/WarehouseContextTest.php:
php

<?php

use App\Models\User;
use App\Models\Warehouse;
use App\Models\UserWarehouse;

test('admin gudang langsung masuk ke gudangnya', function () {
    // Arrange
    $warehouse = Warehouse::factory()->create(['name' => 'Bandung']);
    $admin = User::factory()->create();
    $admin->assignRole('admin_gudang');
    
    UserWarehouse::create([
        'user_id' => $admin->id,
        'warehouse_id' => $warehouse->id,
        'assigned_by' => 1,
    ]);
    
    // Act
    $response = $this->actingAs($admin)->get('/dashboard');
    
    // Assert
    $response->assertStatus(200);
    $this->assertEquals(session('active_warehouse_id'), $warehouse->id);
});

test('super admin bisa pilih gudang', function () {
    // Arrange
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super_admin');
    $warehouse = Warehouse::factory()->create();
    
    // Act
    $response = $this->actingAs($superAdmin)
                     ->post('/warehouse/switch', [
                         'warehouse_id' => $warehouse->id
                     ]);
    
    // Assert
    $response->assertSessionHas('active_warehouse_id', $warehouse->id);
});

test('viewer tidak bisa input barang masuk', function () {
    // Arrange
    $viewer = User::factory()->create();
    $viewer->assignRole('viewer');
    $warehouse = Warehouse::factory()->create();
    
    // Act
    $response = $this->actingAs($viewer)
                     ->post('/inbound', [
                         'warehouse_id' => $warehouse->id,
                         'supplier_id' => 1,
                         'product_id' => 1,
                         'quantity' => 100,
                     ]);
    
    // Assert
    $response->assertStatus(403);
});

📋 CHECKLIST PERINTAH ARTISAN LENGKAP
🔴 TAHAP 1: SETUP (HARI 1)
bash

# 1.1 Install Laravel 12
composer create-project laravel/laravel warehouse-system "^12.0"

# 1.2 Install Packages
composer require laravel/fortify
composer require spatie/laravel-permission
composer require inertiajs/inertia-laravel
composer require spatie/laravel-data  # opsional

npm install @inertiajs/react react react-dom typescript @types/react @types/react-dom
npm install -D @vitejs/plugin-react

# 1.3 Publish Configs
php artisan fortify:install
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\LaravelData\LaravelDataServiceProvider" --tag="config"

🔴 TAHAP 2: MODELS & MIGRATIONS (HARI 1-2)
bash

# 2.1 Models & Migrations
php artisan make:model Warehouse --migration --factory
php artisan make:model UserWarehouse --migration --factory
php artisan make:model Category --migration --factory --seed
php artisan make:model Product --migration --factory --seed
php artisan make:model Supplier --migration --factory --seed
php artisan make:model Customer --migration --factory --seed
php artisan make:model Stock --migration --factory
php artisan make:model StockHistory --migration
php artisan make:model InboundTransaction --migration --factory
php artisan make:model OutboundTransaction --migration --factory
php artisan make:model StockMutation --migration --factory

# 2.2 Run Migration
php artisan migrate

🔴 TAHAP 3: SEEDERS (HARI 2)
bash

# 3.1 Create Seeders
php artisan make:seeder RolePermissionSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder WarehouseSeeder
php artisan make:seeder ProductSeeder
php artisan make:seeder TransactionSeeder

# 3.2 Run Seeders
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed  # or run all

🔴 TAHAP 4: MIDDLEWARE & SERVICES (HARI 3)
bash

# 4.1 Create Middleware
php artisan make:middleware SetWarehouseContext
php artisan make:middleware CheckWarehouseAccess

# 4.2 Create Services
mkdir -p app/Services
# Manual create files

🔴 TAHAP 5: ACTIONS (HARI 3-4)
bash

# 5.1 Create Action Folders
mkdir -p app/Actions/Warehouse
mkdir -p app/Actions/Stock
mkdir -p app/Actions/Transaction
mkdir -p app/Actions/User

# 5.2 Create Action Files
# Manual create files (no artisan for actions)

🔴 TAHAP 6: CONTROLLERS & REQUESTS (HARI 4-5)
bash

# 6.1 Controllers
php artisan make:controller Web/DashboardController
php artisan make:controller Web/WarehouseController --resource
php artisan make:controller Web/ProductController --resource
php artisan make:controller Web/StockController
php artisan make:controller Web/InboundController --resource
php artisan make:controller Web/OutboundController --resource
php artisan make:controller Web/MutationController --resource
php artisan make:controller Web/ReportController
php artisan make:controller Web/UserController --resource

# 6.2 Form Requests
php artisan make:request Warehouse/StoreWarehouseRequest
php artisan make:request Warehouse/UpdateWarehouseRequest
php artisan make:request Product/StoreProductRequest
php artisan make:request Product/UpdateProductRequest
php artisan make:request Transaction/StoreInboundRequest
php artisan make:request Transaction/StoreOutboundRequest
php artisan make:request Transaction/StoreMutationRequest
php artisan make:request Transaction/ReceiveMutationRequest

🔴 TAHAP 7: POLICIES (HARI 5)
bash

# 7.1 Create Policies
php artisan make:policy WarehousePolicy --model=Warehouse
php artisan make:policy ProductPolicy --model=Product
php artisan make:policy StockPolicy --model=Stock
php artisan make:policy InboundTransactionPolicy --model=InboundTransaction
php artisan make:policy OutboundTransactionPolicy --model=OutboundTransaction
php artisan make:policy StockMutationPolicy --model=StockMutation
php artisan make:policy UserPolicy --model=User

# 7.2 Register Policies in AuthServiceProvider
# Manual edit app/Providers/AuthServiceProvider.php

🔴 TAHAP 8: TESTS (HARI 7-8)
bash

# 8.1 Install Pest
composer require pestphp/pest --dev
php artisan pest:install

# 8.2 Create Tests
php artisan make:test Feature/Auth/LoginTest --pest
php artisan make:test Feature/Warehouse/WarehouseSelectionTest --pest
php artisan make:test Feature/Stock/InboundTransactionTest --pest
php artisan make:test Feature/Stock/OutboundTransactionTest --pest
php artisan make:test Feature/Stock/MutationTest --pest
php artisan make:test Feature/Authorization/RolePermissionTest --pest

# 8.3 Run Tests
php artisan test
# or
./vendor/bin/pest

🔴 TAHAP 9: FRONTEND SETUP (HARI 6-7)
bash

# 9.1 TypeScript Setup
touch tsconfig.json
# Edit manually

# 9.2 Create React + Inertia structure
mkdir -p resources/js/{Pages,Components,Hooks,Types,Layouts}
touch resources/js/app.tsx
touch resources/js/ssr.tsx

# 9.3 Build Assets
npm run build

🎯 URUTAN PRIORITAS PENGERJAAN
text

HARI 1:  SETUP & KONFIGURASI
        ├── Install Laravel + Packages
        ├── Setup Database
        ├── Konfigurasi Fortify
        └── Konfigurasi Inertia React TS

HARI 2:  DATABASE LAYER
        ├── Models & Migrations
        ├── Relations
        ├── RolePermissionSeeder (#1 PRIORITAS!)
        └── Database Seeders

HARI 3:  CORE LOGIC
        ├── Middleware (WarehouseContext)
        ├── Service (WarehouseContextService)
        ├── Actions (UpdateStock, CreateTransaction)
        └── Testing Middleware

HARI 4:  BUSINESS LOGIC
        ├── Actions (Lanjutan)
        ├── Form Requests
        └── Controllers (Basic CRUD)

HARI 5:  AUTHORIZATION
        ├── Policies
        ├── Routes dengan Middleware
        └── Testing Authorization

HARI 6:  FRONTEND TYPESCRIPT
        ├── Type Definitions
        ├── Hooks (useWarehouse, usePermission)
        └── Layout Components

HARI 7:  FRONTEND PAGES
        ├── Authentication Pages
        ├── Dashboard
        ├── Warehouse Selection
        └── Stock Pages

HARI 8:  TRANSACTION PAGES
        ├── Inbound Pages
        ├── Outbound Pages
        ├── Mutation Pages
        └── Reports

HARI 9:  ADVANCED FEATURES
        ├── Export/Import
        ├── Notifications
        └── Dashboard Widgets

HARI 10: TESTING & DEPLOY
        ├── Feature Tests
        ├── Optimization
        └── Deployment Preparation

✅ FINAL CHECKLIST
text

[✔️] Laravel 12 installed
[✔️] Fortify configured
[✔️] Spatie Permission installed
[✔️] Inertia React TS configured
[✔️] Database migrations created
[✔️] Models with relationships
[✔️] RolePermissionSeeder executed
[✔️] WarehouseContext middleware
[✔️] Actions created
[✔️] Controllers created
[✔️] Policies created
[✔️] Routes defined
[✔️] Form Requests created
[✔️] TypeScript types defined
[✔️] React hooks created
[✔️] Inertia pages created
[✔️] Tests written
[✔️] All tests passing

🚀 PERINTAH ARTISAN LENGKAP (COPY PASTE READY)
bash

# ============ 1. SETUP PROJECT ============
composer create-project laravel/laravel warehouse-system "^12.0"
cd warehouse-system

composer require laravel/fortify
composer require spatie/laravel-permission
composer require inertiajs/inertia-laravel
composer require spatie/laravel-data

npm install @inertiajs/react react react-dom typescript @types/react @types/react-dom
npm install -D @vitejs/plugin-react

php artisan fortify:install
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\LaravelData\LaravelDataServiceProvider"

# ============ 2. MODELS & MIGRATIONS ============
php artisan make:model Warehouse --migration --factory
php artisan make:model UserWarehouse --migration --factory
php artisan make:model Category --migration --factory --seed
php artisan make:model Product --migration --factory --seed
php artisan make:model Supplier --migration --factory --seed
php artisan make:model Customer --migration --factory --seed
php artisan make:model Stock --migration --factory
php artisan make:model StockHistory --migration
php artisan make:model InboundTransaction --migration --factory
php artisan make:model OutboundTransaction --migration --factory
php artisan make:model StockMutation --migration --factory

php artisan migrate

# ============ 3. SEEDERS ============
php artisan make:seeder RolePermissionSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder WarehouseSeeder
php artisan make:seeder ProductSeeder
php artisan make:seeder TransactionSeeder

php artisan db:seed --class=RolePermissionSeeder

# ============ 4. MIDDLEWARE ============
php artisan make:middleware SetWarehouseContext
php artisan make:middleware CheckWarehouseAccess

# ============ 5. CONTROLLERS ============
php artisan make:controller Web/DashboardController
php artisan make:controller Web/WarehouseController --resource
php artisan make:controller Web/ProductController --resource
php artisan make:controller Web/StockController
php artisan make:controller Web/InboundController --resource
php artisan make:controller Web/OutboundController --resource
php artisan make:controller Web/MutationController --resource
php artisan make:controller Web/ReportController
php artisan make:controller Web/UserController --resource

# ============ 6. REQUESTS ============
php artisan make:request Warehouse/StoreWarehouseRequest
php artisan make:request Warehouse/UpdateWarehouseRequest
php artisan make:request Product/StoreProductRequest
php artisan make:request Product/UpdateProductRequest
php artisan make:request Transaction/StoreInboundRequest
php artisan make:request Transaction/StoreOutboundRequest
php artisan make:request Transaction/StoreMutationRequest
php artisan make:request Transaction/ReceiveMutationRequest

# ============ 7. POLICIES ============
php artisan make:policy WarehousePolicy --model=Warehouse
php artisan make:policy ProductPolicy --model=Product
php artisan make:policy StockPolicy --model=Stock
php artisan make:policy InboundTransactionPolicy --model=InboundTransaction
php artisan make:policy OutboundTransactionPolicy --model=OutboundTransaction
php artisan make:policy StockMutationPolicy --model=StockMutation
php artisan make:policy UserPolicy --model=User

# ============ 8. TESTS ============
composer require pestphp/pest --dev
php artisan pest:install

php artisan make:test Feature/Auth/LoginTest --pest
php artisan make:test Feature/Warehouse/WarehouseSelectionTest --pest
php artisan make:test Feature/Stock/InboundTransactionTest --pest
php artisan make:test Feature/Stock/OutboundTransactionTest --pest
php artisan make:test Feature/Stock/MutationTest --pest
php artisan make:test Feature/Authorization/RolePermissionTest --pest

# ============ 9. CACHE & OPTIMIZE ============
php artisan optimize
php artisan route:cache
php artisan config:cache
php artisan view:cache

# ============ 10. RUN ============
php artisan serve
npm run dev

🎯 KESIMPULAN

✅ KEUNGGULAN STRUKTUR INI:

    Separation of Concerns - Action Pattern untuk business logic

    Type Safety - TypeScript + Laravel Data

    Authorization - Spatie Roles + Policies

    Context Aware - WarehouseContext middleware

    Audit Trail - StockHistory untuk semua perubahan

    Testable - Unit & Feature tests siap

    Maintainable - Struktur folder jelas

    Scalable - Modular dan terpisah

🚀 MULAI DARI:

    RolePermissionSeeder - #1 PRIORITAS WAJIB!

    Models & Migrations - Struktur database

    Middleware - Warehouse context

    Actions - Business logic

    Sisanya - Bertahap

📌 INGAT:

    Jangan lupa php artisan optimize setiap selesai

    Test setiap fitur yang selesai

    Commit per fitur ke git

    Dokumentasikan API endpoints

Siap coding! 🚀🔥
