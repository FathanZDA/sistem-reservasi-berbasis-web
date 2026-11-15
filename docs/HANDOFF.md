# KIMJO — Handoff Progres

**Terakhir diperbarui:** 15 November 2026
**Peran dokumen:** catatan kerja antar sesi. Ditulis ulang setiap kali sesi berakhir — **status terkini**, bukan riwayat panjang. Aturan kerja di `AGENTS.md`; kebutuhan di `PRD.md`.

---

## 1. Status Ringkas

| Hal | Kondisi |
|---|---|
| Tahap saat ini | **Dokumentasi selesai, coding belum dimulai** |
| Milestone terakhir yang selesai | — (belum ada) |
| Milestone berikutnya | **Milestone 1 — Fondasi** |
| Repo git | belum di-`init` |
| Database | belum dibuat; `config/database.php` masih default **SQLite** |

---

## 2. Yang Sudah Selesai

| Item | Keterangan |
|---|---|
| `docs/PRD.md` | Final 15 Nov 2026 — kebutuhan, aturan bisnis, status, halaman, non-fungsional |
| `docs/PLAN.md` | Final — tech stack, milestone 1–10, urutan pengisian dokumen |
| `docs/AGENTS.md` | Final — konvensi, larangan, perintah harian, aturan git |
| `docs/DATABASE.md` | Final — 8 tabel, kolom, index, FK, relasi, rumus kapasitas slot |
| `docs/FLOW.md` | Final — alur A–L, urutan webhook, validasi, kasus batas |
| `docs/UI-UX.md` | Final — token warna/tipografi, komponen, wireframe tiap halaman, print |
| `docs/DOKUMENTASI-QA.md` | Final — skenario uji per milestone |

Belum ada perubahan kode aplikasi. Isi repo masih kerangka Laravel 13 bawaan: hanya route `/` → `welcome`, migration bawaan `users`/`cache`/`jobs`, tanpa kolom `role`.

---

## 3. Yang Sedang Dikerjakan

Tidak ada. Menunggu mulai Milestone 1.

## 4. Yang Gagal / Terbuka

| Hal | Status | Catatan |
|---|---|---|
| Verifikasi webhook dari localhost | belum diuji | Butuh tunnel publik (ngrok/Cloudflare Tunnel) saat Milestone 7 |
| Integrasi Midtrans | belum ada kode | Akun sandbox sudah tersedia; SDK belum di-`require` |
| Konsekuensi popup Snap ditutup | sudah diputuskan | Pesanan kedaluwarsa 15 menit; tombol Bayar tetap tersedia sebelum batas (lihat `FLOW.md` bagian 5) |
| Fitur admin "Kelola Slot Waktu" | ditambahkan atas inisiatif | Kalau tidak dipakai, hapus dari PRD FR-A-05 dan route `/admin/slot-waktu` |
| Angka target bisnis | belum ditetapkan pemilik | Jangan mengarang di dokumen/kode |

---

## 5. Langkah Berikutnya (Milestone 1 — Fondasi)

Urut, semuanya harus bisa dicek:

1. `git init` + `.gitignore` memastikan `.env` tidak ikut.
2. Buat database MySQL (mis. `kimjo`), ubah `.env` (`DB_CONNECTION=mysql`, host/port/user/pass/nama).
3. `php artisan migrate` untuk migration bawaan.
4. Set `APP_TIMEZONE=Asia/Jakarta` dan `config/app.php` `timezone`.
5. `php artisan storage:link`.
6. `composer require midtrans/midtrans-php` dan tambahkan kunci Midtrans ke `.env` (kosongkan di `.env.example`).
7. Cek: `php artisan serve` bisa dibuka, `php artisan about` menunjukkan timezone dan driver yang benar.

Setelah Milestone 1 lulus, lanjut **Milestone 2 (Auth & role admin)** — rujuk `AGENTS.md` bagian 4–5 dan `DATABASE.md` bagian 3.

---

## 6. Cara Melanjutkan Sesi

1. Baca dokumen ini lebih dulu.
2. Cek milestone berjalan di `PLAN.md` bagian 4.
3. Kerjakan hanya lingkup milestone itu; jangan melompat.
4. Sebelum menutup sesi: perbarui bagian 1–5 dokumen ini (status, yang selesai, yang gagal, langkah berikutnya) dan sebutkan perintah verifikasi yang dijalankan beserta hasilnya.
