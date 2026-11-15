# KIMJO — Rencana Kerja Proyek (Roadmap)

**Status:** final — 15 November 2026
**Peran dokumen:** mengatur **kapan dan dengan urutan apa** sesuatu dikerjakan. Kebutuhan produk (fitur, aturan bisnis, status, halaman) **tidak** ditulis ulang di sini — rujukannya `PRD.md`. Tech stack dan milestone tetap di dokumen ini.

---

## 1. Peta Dokumen

| Dokumen | Peran | Jadi acuan untuk | Status |
|---|---|---|---|
| `PRD.md` | Kebutuhan produk final | semua dokumen & kode | final |
| `PLAN.md` | Roadmap, tech stack, milestone (dokumen ini) | urutan pengerjaan | final |
| `AGENTS.md` | Aturan kerja agent: stack, konvensi, larangan | setiap sesi coding | final |
| `DATABASE.md` | ERD, tabel, kolom, relasi, index | migration & model | final |
| `FLOW.md` | Alur pengguna & transisi status | controller, middleware, UI state | final |
| `UI-UX.md` | Design token, komponen, layout halaman | view & CSS | final |
| `HANDOFF.md` | Progres terkini & langkah berikutnya | sesi berikutnya | final |
| `DOKUMENTASI-QA.md` | Skenario pengujian | QA & verifikasi | final |

**Aturan anti-duplikasi:** angka tarif, status, dan aturan bisnis hanya boleh hidup di `PRD.md`. Dokumen lain merujuk nomornya (mis. "lihat BR-02"), tidak menyalin.

---

## 2. Ringkasan Produk

Company profile + sistem reservasi KIMJO: pelanggan memesan sendiri (coffeeshop atau catering), membayar **penuh 100%** lewat Midtrans Snap, lalu memantau pesanan dengan kode booking. Admin mengelola menu, konten, dan pesanan dari dashboard.

Detail lengkap: `PRD.md` bagian 5–8.

---

## 3. Tech Stack

- Framework: **Laravel 13**
- Bahasa: **PHP 8.3+** dan JavaScript *(Laravel 13 tidak mendukung PHP 5)*
- Database: **MySQL**
- Front end: **Blade** + CSS/JS lewat **Vite**
- Pembayaran: **Midtrans Snap**, SDK `midtrans/midtrans-php`, mode sandbox (akun sudah tersedia)
- Variabel `.env`: `MIDTRANS_MERCHANT_ID`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_SERVER_KEY`, `MIDTRANS_IS_PRODUCTION=false`
- Timezone aplikasi: **Asia/Jakarta**
- Penyimpanan gambar: `storage/app/public` + `php artisan storage:link`
- Scheduler: `php artisan schedule:work` untuk kedaluwarsa 15 menit

---

## 4. Milestone

Setiap milestone harus selesai dan bisa dicek sebelum lanjut ke berikutnya.

| # | Milestone | Hasil yang bisa dicek | Butuh dokumen |
|---|---|---|---|
| 1 | **Fondasi** | `git init`, MySQL tersambung, migration bawaan jalan, timezone Asia/Jakarta, `storage:link` | — |
| 2 | **Auth & role admin** | Login admin, kolom `role`, seeder admin, middleware `admin`, registrasi publik mati | AGENTS |
| 3 | **Skema data & migration** | 8 tabel + seeder (tarif, 4 slot, menu contoh) | DATABASE |
| 4 | **Company profile (read-only)** | Beranda, tentang, coffeeshop, catering per hari — semua dari DB | UI-UX |
| 5 | **Admin: konten & menu** | CRUD menu coffeeshop, menu catering, slot waktu, paket harga, pengaturan konten | UI-UX |
| 6 | **Reservasi & kalkulasi** | Form, hitung total, validasi slot & minimal order, kode booking, halaman cek pesanan | FLOW |
| 7 | **Pembayaran** | Snap token, popup, webhook + signature, scheduler kedaluwarsa | FLOW |
| 8 | **Admin: reservasi & dashboard** | Daftar/filter/detail, aksi status, grafik pendapatan | UI-UX |
| 9 | **Invoice** | Halaman siap print satu A4 | UI-UX |
| 10 | **Poles UI** | Warna, animasi scroll, carousel, teks berjalan, uji 375 px | UI-UX |

Urutan ketergantungan: 1 → 2 → 3 → 4 → 5 → 6 → 7 → 8 → 9 → 10. Milestone 4 tidak bisa dimulai sebelum 3 selesai karena semua halaman publik membaca tabel.

---

## 5. Urutan Pengisian Dokumen (sudah selesai 15 Nov 2026)

Semua dokumen di bawah sudah terisi final. Tabel ini tetap disimpan sebagai rujukan isi minimum tiap dokumen bila ada revisi. Dokumen diperbarui **sebelum coding milestone terkait**, bukan setelahnya.

| Urut | Dokumen | Isi minimum | Alasan urutan |
|---|---|---|---|
| 1 | `AGENTS.md` | Stack, konvensi nama tabel/kolom/model, aturan migration & seeder, larangan (jangan commit `.env`, jangan ubah migration yang sudah jalan), perintah harian, aturan git | Dibaca setiap sesi; harus ada sebelum file pertama dibuat |
| 2 | `DATABASE.md` | 8 tabel: kolom, tipe, nullable, default, index, FK + onDelete, diagram relasi, catatan (mis. `menus.hari` hanya untuk catering) | Membuka jalan milestone 3 |
| 3 | `FLOW.md` | Diagram alur pelanggan & admin, tabel transisi status (rujuk PRD bagian 7), urutan webhook, aturan pelepasan slot | Butuh nama kolom & status final |
| 4 | `UI-UX.md` | Palet hitam/maroon (hex), tipografi, spacing, komponen (kartu menu, form, popup Snap, kartu status), wireframe teks per halaman, perilaku mobile, daftar animasi | Butuh daftar halaman final |
| 5 | `HANDOFF.md` | Progres per milestone, yang sedang dikerjakan, yang gagal, langkah berikutnya | Baru berguna saat coding berjalan |
| 6 | `DOKUMENTASI-QA.md` | Skenario uji per fitur + kasus batas (slot penuh, 15 menit habis, signature salah, kode salah) | Butuh kode yang bisa diuji |

`PRD.md` dan `PLAN.md` sudah final; jangan dibuat ulang.

---

## 6. Risiko Teknis

- **Webhook dari localhost tidak terjangkau** — perlu tunnel publik (mis. ngrok) sebelum menguji milestone 7.
- **Verifikasi signature** — SHA512 dari `order_id` + `status_code` + `gross_amount` + `server_key`; jangan percaya isi request mentah.
- **Idempotensi webhook** — notifikasi bisa berulang; update status harus aman diulang.
- **Booking bentrok** — jaga kapasitas di level database (constraint/locking), bukan hanya cek di PHP; konsisten dengan pelepasan slot saat `kedaluwarsa`.
- **Spam form publik** — honeypot + rate limit Laravel.
- **Kode booking mudah ditebak** — kode acak 6 karakter, rate limit halaman cek, nomor WhatsApp tersamar.
- **Popup Snap ditutup pelanggan** — pesanan kedaluwarsa dalam 15 menit; bila perlu, tombol bayar ulang ditambahkan di halaman cek pesanan (route `/reservasi/{kode}/bayar` sudah ada).
- **Kunci gateway** — `SERVER_KEY` hanya di `.env`, tidak masuk repo dan tidak dikirim ke browser.
- **Scheduler** — kedaluwarsa harus dicek tiap menit; jangan hanya saat halaman dibuka.
