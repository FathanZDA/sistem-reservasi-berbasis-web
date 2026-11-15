# KIMJO — Alur Sistem

**Status:** final — 15 November 2026
**Peran dokumen:** acuan urutan langkah di controller, middleware, job/scheduler, dan state UI. Arti tiap status dan aturan bisnis **tidak** diulang di sini — rujuk `PRD.md` bagian 6–7. Nama tabel/kolom merujuk `DATABASE.md`.

---

## 1. Peta Alur

| # | Alur | Pelaku | Halaman/Route |
|---|---|---|---|
| A | Lihat company profile | Pelanggan | `/`, `/tentang`, `/coffeeshop`, `/catering` |
| B | Reservasi coffeeshop | Pelanggan | `/reservasi` → POST |
| C | Pesan catering | Pelanggan | `/reservasi` → POST |
| D | Bayar (Snap) | Pelanggan + Midtrans | POST `/reservasi/{kode}/bayar` → popup |
| E | Notifikasi pembayaran | Midtrans | POST `/pembayaran/callback` |
| F | Kedaluwarsa otomatis | Scheduler | `bookings:expire` tiap menit |
| G | Cek pesanan | Pelanggan | `/cek-pesanan` → `/cek-pesanan/{kode}` |
| H | Invoice | Pelanggan | `/reservasi/{kode}/invoice` |
| I | Login admin | Admin | `/login` |
| J | Proses & selesaikan pesanan | Admin | `/admin/reservasi/{id}` |
| K | Batalkan pesanan & refund | Admin | `/admin/reservasi/{id}` + dashboard Midtrans |
| L | Kelola konten & menu | Admin | CRUD admin |

---

## 2. Alur A — Company Profile

1. Pelanggan membuka `/` (biasanya dari bio Instagram).
2. `HomeController@index` membaca `settings` grup `beranda`, menu coffeeshop aktif (`jenis = coffeeshop`, `is_active`), dan menu catering aktif.
3. Semua teks dan gambar berasal dari `settings`; tidak ada teks hardcode (FR-P-01).
4. Navigasi utama: Beranda, Tentang, Coffeeshop, Catering, **Reservasi**, Cek Pesanan.
5. `/catering` mengelompokkan menu per `hari` Senin–Jumat; hari tanpa menu tidak dirender (FR-P-04).
6. Menu nonaktif tidak pernah tampil di halaman publik (FR-P-03).

---

## 3. Alur B — Reservasi Coffeeshop

```
┌─ Pelanggan ────────────────────────────────────────────────┐
│ /reservasi, pilih tab "Coffeeshop"                         │
│ isi: tanggal, slot, jumlah orang, nama, WA, catatan        │
│ total terhitung otomatis di layar                          │
│ klik "Pesan Sekarang"                                      │
└──────────────────────────┬─────────────────────────────────┘
                           ▼
┌─ Server: ReservasiController@store ────────────────────────┐
│ 1. validasi input (bagian 8)                               │
│ 2. pilih price_tier aktif sesuai jumlah_orang (BR-01)      │
│ 3. hitung total = jumlah_orang × tarif                     │
│ 4. DB::transaction + lock baris slot                       │
│ 5. cek kapasitas terpakai + jumlah_orang ≤ kapasitas (BR-10)│
│ 6. buat kode_booking, expires_at = now + 15 menit          │
│ 7. simpan bookings (status menunggu_pembayaran)            │
└──────────────────────────┬─────────────────────────────────┘
                           ▼
              redirect ke /cek-pesanan/{kode}?baru=1
              (tampil rincian, kode, hitung mundur, tombol Bayar)
```

Kegagalan yang harus ditangani:

| Kondisi | Respons |
|---|---|
| Slot penuh / sisa < jumlah orang | Kembali ke form, error pada field slot, isian lain dipertahankan |
| Slot nonaktif atau tidak ada | Error validasi |
| Tanggal sudah lewat / hari ini lewat jam slot | Error validasi |
| Tarif tidak ditemukan | Error 500 tercatat di log — berarti seeder/tier belum benar, bukan pesan ke pengguna |
| Dua submit bersamaan | Yang kalah mendapat pesan slot penuh (bukan overbooking) |

Total yang dikirim browser **tidak dipercaya**; server menghitung ulang dari `price_tiers`.

---

## 4. Alur C — Pesan Catering

1. Form `/reservasi` tab "Catering": tanggal, pilih menu per hari + jumlah porsi, nama, WA, catatan.
2. Menu yang boleh dipilih hanya `menus` dengan `jenis = catering`, `is_active`, dan **`hari` sesuai hari dari tanggal yang dipilih**. Bila pelanggan mengganti tanggal, daftar menu dimuat ulang.
3. Tombol `+` / `−` mengubah porsi; keranjang kecil menampilkan subtotal dan total berjalan.
4. Validasi: total porsi **≥ 5** (BR-02), setiap menu minimal 1 porsi, menu harus aktif dan harinya cocok.
5. Server menghitung ulang total dari `menus.harga` (harga browser diabaikan).
6. Transaksi DB: simpan `bookings` (`jenis = catering`, `time_slot_id` dan `jumlah_orang` `NULL`) + baris `booking_items` (nama & harga disalin).
7. Catering **tidak** memakai cek kapasitas dan tidak dibatasi jumlah pesanan per hari (BR-07).
8. Setelah tersimpan → halaman cek pesanan dengan tombol Bayar, sama seperti coffeeshop.

---

## 5. Alur D — Pembayaran Snap

```
Pelanggan klik "Bayar Sekarang"
        ▼
POST /reservasi/{kode}/bayar   (throttle)
        ▼
Cek booking: ada? kode valid?
        ▼
status masih menunggu_pembayaran dan now < expires_at?
   ├─ tidak → tampilkan status terkini, tidak buat transaksi baru
   └─ ya
        ▼
Tutup transaksi sebelumnya bila masih pending (opsional, via API Midtrans)
        ▼
order_id = KIMJO-XXXXXX-<uniqid/timestamp>
snap = Midtrans Snap::getSnapToken([
    transaction_details: order_id, gross_amount = bookings.total_harga,
    customer_details: nama, no WA, item_details: rincian
])
        ▼
Simpan payments (status pending, snap_token, gross_amount)
        ▼
Response JSON { snap_token } → snap.pay(token) di browser
        ▼
Pelanggan memilih metode, membayar
        ▼
Midtrans kirim webhook (alur E)
```

Aturan:

- `gross_amount` **wajib** sama dengan `bookings.total_harga`; jangan mengirim total dari browser.
- Satu booking boleh punya beberapa baris `payments` (bayar ulang), tapi hanya satu yang boleh `settlement`/`capture`.
- Popup ditutup tanpa bayar → tidak ada perubahan status; pesanan menjadi `kedaluwarsa` lewat alur F. Tombol Bayar di halaman cek pesanan tetap tersedia selama `now < expires_at`.
- Halaman cek pesanan menampilkan hitung mundur dari `expires_at`; saat mencapai nol, tombol Bayar disembunyikan dan status dimuat ulang.
- Bila Snap gagal membuat token, tampilkan pesan "pembayaran belum bisa diproses, coba lagi" dan catat error di log.

---

## 6. Alur E — Webhook Midtrans

Endpoint `POST /pembayaran/callback`, **tanpa** CSRF (`VerifyCsrfToken` dikecualikan), tanpa auth.

Urutan **wajib**:

1. Terima body JSON.
2. Hitung `signature_key` = SHA512(`order_id` + `status_code` + `gross_amount` + `SERVER_KEY`).
3. Bandingkan dengan `signature_key` dari body memakai perbandingan waktu-konstan. **Tidak cocok → `403`, berhenti.** Jangan memperbarui apa pun.
4. Cari `payments.order_id`. Tidak ditemukan → `404` (jangan membuat data baru dari webhook).
5. Cek `gross_amount` notifikasi = `payments.gross_amount` = `bookings.total_harga`. Beda → `422`, catat log.
6. Simpan `payload` notifikasi dan status transaksi ke `payments`.
7. Petakan `transaction_status` → aksi:

| `transaction_status` | `fraud_status` | Aksi pada `payments.status` | Aksi pada `bookings.status` |
|---|---|---|---|
| `capture` | `accept` | `capture` | `dibayar` |
| `settlement` | — | `settlement` | `dibayar` |
| `pending` | — | `pending` | tetap `menunggu_pembayaran` |
| `deny` | — | `deny` | `gagal` |
| `cancel` | — | `cancel` | `gagal` |
| `expire` | — | `expire` | `kedaluwarsa` |
| `failure` | — | `failure` | `gagal` |

8. Balas `200 OK` untuk semua notifikasi valid, termasuk yang tidak mengubah status.

Aturan idempotensi & keamanan:

- Notifikasi bisa datang berulang atau tidak berurutan. **Jangan pernah menurunkan status** yang sudah `dibayar`/`diproses`/`selesai` menjadi `gagal`/`kedaluwarsa` karena notifikasi lama.
- Transisi `bookings.status` hanya boleh mengikuti tabel pada PRD bagian 7. Bila notifikasi meminta transisi terlarang, catat log dan balas `200` tanpa mengubah data.
- `paid_at` diisi hanya sekali (saat pertama kali menjadi `dibayar`).
- Satu transaksi DB untuk memperbarui `payments` + `bookings`.
- `SERVER_KEY` hanya dibaca dari config; jangan pernah ditulis ke response atau log.

---

## 7. Alur F — Kedaluwarsa Otomatis

Command terjadwal `bookings:expire`, dijalankan **setiap menit** (`routes/console.php` atau `Kernel`), timezone Asia/Jakarta.

```
Ambil booking: status = menunggu_pembayaran AND expires_at <= now()
Untuk tiap booking (chunk):
    - set status = kedaluwarsa
    - slot otomatis terlepas (tidak ada baris yang dihapus)
    - catat log jumlah yang kedaluwarsa
```

- Jangan memanggil Midtrans untuk setiap baris; pembatalan transaksi di sisi gateway ditangani lewat `expire` dari Midtrans sendiri.
- Booking berstatus lain tidak disentuh.
- Harus aman dijalankan dua kali bersamaan (pakai `where status = ...` di update, atau lock baris).
- Untuk lokal: `php artisan schedule:work`.

---

## 8. Alur G — Cek Pesanan

```
/cek-pesanan  → form satu kolom kode booking
        ▼
POST /cek-pesanan  (throttle 10/menit/IP)
        ▼
Normalisasi kode: trim, uppercase, tambahkan "KIMJO-" bila belum ada
        ▼
Cari bookings.kode_booking
   ├─ tidak ada → kembali ke form, pesan "Kode pesanan tidak ditemukan."
   └─ ada → GET /cek-pesanan/{kode}
```

Halaman detail menampilkan:

- kode booking, nama pelanggan (nama lengkap), jenis layanan
- coffeeshop: tanggal, slot, jumlah orang, total
- catering: tanggal, daftar menu + porsi, total
- status pesanan (label Bahasa Indonesia) + status pembayaran
- nomor WhatsApp **tersamar** (`08xx****1234`), bukan nomor penuh (FR-P-08)
- bila `menunggu_pembayaran` dan belum kedaluwarsa: hitung mundur + tombol Bayar
- link invoice
- tidak ada tombol batal mandiri (BR-09); teks pengajuan pembatalan mengarah ke admin/WhatsApp

Kode pada URL bukan pengganti autentikasi; tetap kena rate limit, dan halaman ini tidak boleh menampilkan data pelanggan lain.

---

## 9. Alur H — Invoice

1. `GET /reservasi/{kode}/invoice` — tanpa login, hanya butuh kode valid.
2. Layout cetak terpisah: tanpa navbar, footer, tombol Bayar; hanya kop, rincian, status, total, kode booking.
3. Muat satu halaman A4 saat `Ctrl+P` (lihat `UI-UX.md` bagian print).
4. Boleh dibuka untuk semua status; status ditampilkan apa adanya.

---

## 10. Alur I — Login Admin

1. `GET /login` → form email + password.
2. `POST /login` dengan rate limit (5 percobaan/menit per email+IP).
3. Berhasil → redirect `/admin`. Gagal → pesan umum "Email atau password salah."
4. Middleware `auth` + `admin` menjaga seluruh `/admin/*`; pengguna tanpa `role = admin` ditolak `403`.
5. `POST /logout` → sesi dihapus → `/login`.
6. Tidak ada route registrasi, lupa password, atau verifikasi email (PRD bagian 3).

---

## 11. Alur J — Admin Memproses Pesanan

1. `/admin/reservasi` menampilkan daftar dengan filter tanggal (rentang) dan status, urut terbaru.
2. Klik baris → `/admin/reservasi/{id}`: rincian, riwayat `payments`, tombol aksi.
3. Tombol aksi hanya muncul untuk transisi yang sah (lihat tabel efek samping bagian 12).
4. `PATCH /admin/reservasi/{id}/status` memvalidasi transisi di server, bukan hanya menyembunyikan tombol.
5. Setiap perubahan dicatat `updated_at`; tidak ada tabel audit terpisah di versi 1.
6. Aksi `diproses` → `selesai` tidak mengubah kapasitas (slot sudah tidak relevan setelah tanggal lewat).

---

## 12. Alur K — Pembatalan & Refund

Transisi dan arti status: PRD bagian 7. Efek samping tiap transisi:

| Dari → Ke | Efek di sistem | Efek di gateway | Dicatat |
|---|---|---|---|
| `menunggu_pembayaran` → `dibayar` | slot tetap tertahan | — | `payments.paid_at`, payload |
| `menunggu_pembayaran` → `kedaluwarsa` | slot dilepas | — | log scheduler |
| `menunggu_pembayaran` → `gagal` | slot dilepas | — | payload |
| `dibayar` → `diproses` | — | — | — |
| `diproses` → `selesai` | — | — | — |
| `dibayar`/`diproses` → `dibatalkan` | slot dilepas (bila tanggal belum lewat) | **refund manual** oleh admin di dashboard Midtrans | `alasan_pembatalan` wajib |

Langkah admin membatalkan:

1. Buka detail reservasi, klik "Batalkan".
2. Isi alasan (wajib, minimal 5 karakter) → konfirmasi.
3. Sistem set `dibatalkan` + simpan alasan.
4. Sistem menampilkan instruksi: proses refund **100% manual** di dashboard Midtrans (BR-08).
5. Aplikasi **tidak** memanggil API refund. Tidak ada refund otomatis.

Pengajuan pembatalan oleh pelanggan: hanya lewat admin (WhatsApp), maksimal H-1 (BR-09). Bila pengajuan masuk setelah H-1, admin menolak dan **tidak** membatalkan pesanan.

---

## 13. Alur L — Admin Kelola Konten & Menu

- Menu coffeeshop: CRUD di `/admin/menu-coffeeshop`; simpan → langsung terlihat di publik (FR-A-03).
- Menu catering: CRUD di `/admin/menu-catering`; `hari` wajib dipilih, menyimpan tanpa hari ditolak (FR-A-04).
- Slot waktu: CRUD di `/admin/slot-waktu`; menonaktifkan slot menyembunyikannya dari form, tidak menghapus booking lama (FR-A-05).
- Paket harga: CRUD di `/admin/paket-harga`; perubahan hanya memengaruhi perhitungan baru (FR-A-06).
- Pengaturan konten: satu halaman `/admin/pengaturan` berisi grup beranda/tentang/kontak, disimpan sekaligus (FR-A-08).
- Hapus menu yang sudah dipakai booking → ditolak; arahkan ke nonaktifkan.

---

## 14. Urutan Validasi Form Reservasi

Dijalankan berurutan; berhenti pada kegagalan pertama per field.

1. `jenis` ∈ {`coffeeshop`, `catering`}
2. `nama_pelanggan` wajib, 3–100 karakter
3. `no_whatsapp` wajib, 9–15 digit, hanya angka/`+`/`-`/spasi
4. `tanggal_reservasi` wajib, format tanggal valid, ≥ hari ini
5. coffeeshop: `time_slot_id` wajib & aktif; tanggal+jam slot belum lewat; `jumlah_orang` 1–kapasitas
6. coffeeshop: kapasitas tersisa ≥ `jumlah_orang` (di dalam transaksi)
7. catering: minimal 1 menu; semua menu aktif, `jenis = catering`, `hari` cocok dengan tanggal; total porsi ≥ 5
8. `catatan` opsional, maks 500 karakter

Error ditampilkan di dekat field terkait, Bahasa Indonesia, isian lain tidak hilang.

---

## 15. Pengecualian & Kasus Batas

| Kasus | Perilaku |
|---|---|
| Pelanggan refresh halaman setelah submit | Tidak membuat booking baru; halaman cek memakai kode di URL |
| Pelanggan membuka `/cek-pesanan/{kode}` dengan kode orang lain | Tetap tampil, tapi nomor WA tersamar dan tidak ada aksi; kode acak 6 karakter + rate limit sebagai pengaman |
| Webhook datang setelah booking `kedaluwarsa` | Bila `settlement` valid, status naik ke `dibayar` **hanya** bila admin menandai manual; default: catat log, balas `200`, tidak mengubah status. Admin menyelesaikan lewat rekonsiliasi manual |
| Webhook ganda `settlement` | Idempoten: tidak menggandakan payment, `paid_at` tidak berubah |
| Slot dinonaktifkan setelah ada booking | Booking lama tetap sah; slot hilang dari form |
| Tarif diubah setelah booking | Booking lama tidak berubah karena total sudah tersimpan |
| Tanggal reservasi lewat tapi status masih `menunggu_pembayaran` | Scheduler menandai `kedaluwarsa` sesuai `expires_at` (15 menit setelah dibuat), bukan menunggu tanggal |
| Popup Snap ditutup | Booking tetap `menunggu_pembayaran` sampai kedaluwarsa; pelanggan bisa klik Bayar lagi sebelum batas |
