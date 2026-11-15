# KIMJO — Aturan Kerja Agent

**Status:** final — 15 November 2026
**Peran dokumen:** aturan yang dibaca **setiap sesi coding**. Isinya stack, konvensi, dan larangan. Kebutuhan produk tetap di `PRD.md`; jadwal di `PLAN.md`.

---

## 1. Urutan Membaca Sebelum Mulai

1. `docs/HANDOFF.md` — apa yang sedang dikerjakan dan apa yang gagal.
2. `docs/PRD.md` — kebutuhan & aturan bisnis (acuan utama).
3. `docs/PLAN.md` — milestone yang sedang berjalan.
4. Dokumen teknis sesuai pekerjaan: `DATABASE.md` (migration/model), `FLOW.md` (controller/status), `UI-UX.md` (view/CSS).

Bila dokumen bertentangan: **PRD menang**, lalu dokumen teknis, lalu kode yang sudah ada. Laporkan pertentangannya, jangan diam-diam memilih.

---

## 2. Stack yang Dipakai

| Bagian | Teknologi | Catatan |
|---|---|---|
| Framework | Laravel 13 | Jangan menurunkan versi atau memakai pola Laravel 8/9 |
| Bahasa | PHP 8.3+ | Mesin lokal PHP 8.5.9; jangan memakai sintaks di bawah 8.3 |
| Database | MySQL | Bukan SQLite untuk kerja nyata |
| Front end | Blade + Vite | Tanpa framework JS (tanpa React/Vue/Livewire) |
| CSS | Satu file CSS yang di-bundle Vite | Tanpa Tailwind/Bootstrap kecuali diminta |
| Pembayaran | `midtrans/midtrans-php` (Snap) | Mode sandbox |
| Waktu | Asia/Jakarta | Semua tampilan & perhitungan |

Menambah dependency baru (Composer/npm) harus disebut alasannya di `HANDOFF.md`. Jangan menambah package untuk hal yang bisa diselesaikan dengan fitur bawaan Laravel.

---

## 3. Konvensi Penamaan

| Hal | Aturan | Contoh |
|---|---|---|
| Tabel | snake_case, **jamak** | `bookings`, `booking_items`, `time_slots` |
| Kolom | snake_case, **tunggal** | `tanggal_reservasi`, `jumlah_orang`, `created_at` |
| Foreign key | `<tabel tunggal>_id` | `booking_id`, `menu_id`, `time_slot_id` |
| Model | PascalCase tunggal | `Booking`, `BookingItem`, `TimeSlot` |
| Controller | PascalCase + `Controller` | `ReservasiController`, `Admin\BookingController` |
| Route name | dot-case, Bahasa Indonesia | `reservasi.store`, `admin.reservasi.status` |
| View | folder snake_case, file snake_case | `resources/views/admin/reservasi/index.blade.php` |
| Variabel status | string snake_case sesuai PRD bagian 7 | `menunggu_pembayaran`, `kedaluwarsa` |
| Uang | integer rupiah penuh | `50000` (bukan `50000.00`, bukan float) |
| Tanggal | kolom `date`, waktu `datetime` | `tanggal_reservasi`, `expires_at` |

**Bahasa:** nama tabel/kolom/route memakai Bahasa Indonesia agar cocok dengan PRD; nama kelas dan method framework tetap Bahasa Inggris (`index`, `store`, `update`, `destroy`). Teks yang dilihat pengguna selalu Bahasa Indonesia.

---

## 4. Aturan Migration

- Satu migration = satu perubahan skema. Jangan menggabung banyak tabel dalam satu file kecuali memang satu paket (mis. `bookings`, `booking_items`, `payments`).
- Nama file mengikuti timestamp bawaan `php artisan make:migration`.
- Selalu tulis fungsi `down()` yang benar-benar membalik `up()`.
- **Jangan mengubah migration yang sudah pernah dijalankan** di mesin mana pun; buat migration baru untuk perubahan.
- Semua kolom uang: `unsignedInteger` atau `unsignedBigInteger`, bukan `float`/`double`.
- Foreign key wajib punya `onDelete` yang dipikirkan:
  - `booking_items.booking_id` → `cascadeOnDelete()`
  - `payments.booking_id` → `cascadeOnDelete()`
  - `bookings.menu_id`/`time_slot_id` (bila ada) → `restrictOnDelete()` supaya riwayat tidak hilang
- Status pesanan disimpan sebagai `string` dengan nilai dari PRD bagian 7 (bukan `enum` database) agar mudah ditambah; validasi di level aplikasi.
- Index wajib untuk kolom yang dipakai filter/where: `bookings.kode_booking` (unique), `bookings.status`, `bookings.tanggal_reservasi`, `payments.order_id` (unique), `menus.hari`.
- Tidak ada soft delete di versi 1. Pembatalan = status, bukan hapus baris.

## 5. Aturan Seeder

- Seeder harus **idempoten** (`updateOrCreate`, bukan `insert` mentah) supaya aman dijalankan ulang.
- Seeder wajib mengisi: 1 akun admin, 3 `price_tiers` (BR-01), 4 `time_slots` (BR-05), beberapa menu coffeeshop contoh, menu catering contoh untuk Senin–Jumat, dan baris `settings` default.
- Kredensial admin diambil dari `.env` (`ADMIN_EMAIL`, `ADMIN_PASSWORD`); jangan hardcode email/ password asli di repo.
- Seeder tidak boleh membuat data booking palsu di database produksi.

---

## 6. Aturan Bisnis yang Tidak Boleh Dikarang

Angka dan aturan berikut **hanya** berasal dari `PRD.md`. Jangan menambah, membulatkan, atau "memperbaiki" tanpa keputusan pemilik:

- Tarif per orang (BR-01), harga catering & minimal 5 porsi (BR-02)
- Pembayaran penuh 100% (BR-03), batas 15 menit (BR-04)
- Daftar slot & kapasitas 30 orang (BR-05)
- Refund 100% manual, pengajuan H-1 lewat admin (BR-08, BR-09)
- Daftar status & transisi (PRD bagian 7)

Kalau implementasi butuh angka yang belum ada di PRD, **berhenti dan tanya**. Jangan mengisi dengan asumsi diam-diam.

---

## 7. Larangan

- Jangan commit `.env`, kunci Midtrans, atau dump database.
- Jangan menaruh `SERVER_KEY` di Blade, JavaScript, atau response JSON.
- Jangan membuat halaman registrasi publik atau fitur login pelanggan.
- Jangan menambah fitur di luar `PRD.md` bagian 11 (notifikasi otomatis, refund otomatis, multi-cabang, stok, voucher, ongkir, ekspor laporan).
- Jangan menghapus atau melemahkan validasi kapasitas slot hanya agar form lolos.
- Jangan mengubah `PRD.md`/`PLAN.md` tanpa persetujuan pemilik.
- Jangan memakai `float` untuk uang, `date()` PHP langsung tanpa timezone aplikasi, atau query SQL mentah tanpa binding.
- Jangan meninggalkan `dd()`/`dump()`/`console.log` di kode yang di-commit.
- Jangan menamai ulang tabel/kolom yang sudah ada di `DATABASE.md` tanpa memperbarui dokumennya.

---

## 8. Perintah Harian

```bash
php artisan serve                 # server lokal
npm run dev                       # Vite
php artisan migrate               # jalankan migration
php artisan migrate:fresh --seed  # reset + isi data contoh (hanya lokal)
php artisan db:seed               # seeder saja
php artisan storage:link          # sekali per mesin
php artisan schedule:work         # pengecekan kedaluwarsa 15 menit
./vendor/bin/pint                 # format PHP
php artisan test                  # test
```

Webhook Midtrans dari localhost butuh tunnel publik (mis. `ngrok http 8000`); URL notifikasi diatur di dashboard Midtrans sandbox.

---

## 9. Aturan Git

- Branch: `main` untuk kondisi stabil, `feat/<ringkas>` atau `fix/<ringkas>` untuk kerja harian.
- Satu commit = satu perubahan yang bisa dijelaskan dalam satu kalimat.
- Pindah branch, commit, dan push akan saya lakukan sendiri (kamu cukup memberikan arahan)
- Format pesan: `feat: ...`, `fix: ...`, `docs: ...`, `chore: ...` (imperatif, Bahasa Indonesia atau Inggris, konsisten).
- Sebelum commit: `./vendor/bin/pint` dan pastikan `php artisan test` lulus untuk bagian yang disentuh.
- Jangan `push --force`, jangan menulis ulang history, jangan commit file `storage/` hasil upload.

---

## 10. Checklist Sebelum Menutup Sesi

1. Perbarui `docs/HANDOFF.md`: apa yang selesai, apa yang belum, apa yang gagal, langkah berikutnya.
2. Bila skema berubah, perbarui `docs/DATABASE.md`.
3. Bila alur/status berubah, perbarui `docs/FLOW.md` (dan `PRD.md` hanya bila pemilik menyetujui).
4. Bila menambah halaman/komponen visual, catat di `docs/UI-UX.md`.
5. Sebutkan perintah yang dijalankan untuk verifikasi, dan hasilnya apa adanya.
