# KIMJO — Handoff Progres

**Terakhir diperbarui:** 16 November 2026
**Peran dokumen:** catatan kerja antar sesi. Ditulis ulang setiap kali sesi berakhir — **status terkini**, bukan riwayat panjang. Aturan kerja di `AGENTS.md`; kebutuhan di `PRD.md`.

---

## 1. Status Ringkas

| Hal | Kondisi |
|---|---|
| Tahap saat ini | **Milestone 1 (Fondasi) selesai — siap Milestone 2** |
| Milestone terakhir yang selesai | Milestone 1 — konfigurasi, skema database, model, seeder |
| Milestone berikutnya | **Milestone 2 — Auth & layout publik** |
| Repo git | sudah di-`init` & commit pertama oleh pemilik; commit fitur berikutnya dilakukan pemilik |
| Database | MySQL (MariaDB XAMPP 10.4.32), nama `sistem-reservasi-berbasis-web`, sudah terisi data awal |

---

## 2. Yang Sudah Selesai

### Dokumentasi
`PRD.md`, `PLAN.md`, `AGENTS.md`, `DATABASE.md`, `FLOW.md`, `UI-UX.md`, dan `DOKUMENTASI-QA.md` — semuanya final 15 Nov 2026.

### Kode (Milestone 1)
| Item | Keterangan |
|---|---|
| Konfigurasi waktu & bahasa | `config/app.php`: `timezone` dari `APP_TIMEZONE` (default `Asia/Jakarta`), `locale` `id`, `faker_locale` `id_ID` |
| `.env` | `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` (nilai tidak ditulis di repo) |
| `.env.example` | `DB_CONNECTION=mysql`, nama database, timezone & locale ikut diperbarui |
| Dependency | `midtrans/midtrans-php ^2.6` terpasang |
| Storage | `php artisan storage:link` sudah dijalankan |
| Migration | 8 tabel sesuai `DATABASE.md` + kolom `users.role` |
| Model | `Menu`, `PriceTier` (dengan `untukJumlahOrang()`), `TimeSlot`, `Booking` (konstanta status + `scopeMenahanSlot`), `BookingItem`, `Payment`, `Setting`, `User` |
| Seeder | `AdminSeeder`, `PriceTierSeeder`, `TimeSlotSeeder`, `MenuSeeder`, `SettingSeeder` — semuanya idempoten (`updateOrCreate`) |
| Data awal | 1 admin, 3 tier (50.000/45.000/40.000), 4 slot @30 orang, 13 menu (8 coffeeshop + 5 catering), 16 baris `settings` |

---

## 3. Yang Sedang Dikerjakan

Tidak ada. Milestone 1 sudah diverifikasi; pekerjaan berikutnya Milestone 2.

---

## 4. Yang Gagal / Terbuka

| Hal | Status | Catatan |
|---|---|---|
| Harga menu contoh | **placeholder** | Harga di `MenuSeeder` (18.000–35.000) belum dari pemilik. Ganti dengan harga asli sebelum rilis. |
| Teks & kontak di `settings` | placeholder | `tentang.alamat`, `kontak.whatsapp`, `kontak.email`, sosial media, dan gambar masih kosong/contoh — diisi lewat admin nanti. |
| Aset gambar | belum ada | `design/menu` kosong; `beranda.hero_gambar` dan galeri masih `NULL`. |
| Akun admin | dibuat dari `.env` | Email `admin@kimjo`, password ada di `.env` (`ADMIN_PASSWORD`). Ganti sebelum dipakai sungguhan. |
| MariaDB XAMPP sempat hang | sudah pulih | 16 Nov 2026 InnoDB deadlock (`DICT_SYS` mutex) setelah `KILL` sesi; server dihentikan lalu dijalankan ulang lewat `mysql/bin/mysqld --defaults-file=mysql/bin/my.ini --standalone`. Data utuh. Jangan `KILL` sesi MySQL saat ada query berjalan. |
| Verifikasi webhook dari localhost | belum diuji | Butuh tunnel publik saat Milestone 7 |
| Fitur admin "Kelola Slot Waktu" | ditambahkan atas inisiatif | Kalau tidak dipakai, hapus dari PRD FR-A-05 dan route `/admin/slot-waktu` |
| Angka target bisnis | belum ditetapkan pemilik | Jangan mengarang |

---

## 5. Langkah Berikutnya (Milestone 2 — Auth & layout publik)

Urut:

1. Layout Blade dasar + token CSS dari `UI-UX.md` (header, footer, tombol, kartu, badge status).
2. Halaman publik baca-saja: beranda, tentang, coffeeshop, catering — data dari `menus` dan `settings`.
3. Format Rupiah & tanggal Indonesia (helper/Blade directive), samakan dengan `UI-UX.md`.
4. Login admin (`/login`, `/logout`) + middleware `auth`; belum perlu middleware `admin` karena role tunggal.
5. Layout admin minimal sebagai tempat dashboard Milestone 3.

Verifikasi yang sudah dijalankan untuk Milestone 1: `php artisan migrate:fresh --seed` (12 migration OK), `php artisan db:seed` dua kali (jumlah baris tidak berubah), cek 4 foreign key (`RESTRICT`/`CASCADE` sesuai dokumen), `./vendor/bin/pint`, `php artisan test` (2 lulus).

---

## 6. Cara Melanjutkan Sesi

1. Baca dokumen ini lebih dulu.
2. Cek milestone berjalan di `PLAN.md` bagian 4.
3. Kerjakan hanya lingkup milestone itu; jangan melompat.
4. Sebelum menutup sesi: perbarui bagian 1–5 dokumen ini dan sebutkan perintah verifikasi yang dijalankan beserta hasilnya.
