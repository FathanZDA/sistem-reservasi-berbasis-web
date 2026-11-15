# KIMJO — Product Requirements Document (PRD)

**Status:** final — 15 November 2026
**Peran dokumen:** acuan utama kebutuhan produk. Semua dokumen lain (`PLAN.md`, `DATABASE.md`, `FLOW.md`, `UI-UX.md`) diturunkan dari sini. Bila ada perbedaan, PRD yang menang.

---

## 1. Ringkasan Produk

KIMJO adalah coffeeshop yang juga melayani catering. Website ini menggabungkan dua hal:

1. **Company profile** — menampilkan menu coffeeshop, menu catering, profil, dan kontak.
2. **Sistem reservasi** — pelanggan memesan sendiri, membayar penuh lewat Midtrans Snap, lalu memantau pesanannya dengan kode booking.

---

## 2. Masalah & Tujuan

| Masalah | Tujuan | Ukuran berhasil |
|---|---|---|
| Booking lewat DM Instagram tidak tercatat, mudah terlewat | Semua pesanan masuk database dengan kode booking | Tidak ada lagi pesanan yang hanya tercatat di DM |
| Pelanggan sulit mengecek status pesanannya | Halaman Cek Pesanan dengan kode booking | Pelanggan bisa cek status tanpa menghubungi admin |
| Layanan catering kurang terlihat | Halaman catering dengan menu per hari | Catering punya halaman sendiri di navigasi utama |
| Admin mencatat pesanan manual | Dashboard + kelola reservasi | Admin cukup memproses status, bukan mencatat ulang |

Target angka bisnis (jumlah booking/bulan, pendapatan) **belum ditetapkan** pemilik.

---

## 3. Aktor & Hak Akses

| Aktor | Bisa melakukan | Batasan |
|---|---|---|
| Pelanggan | Lihat halaman publik, isi form reservasi/catering, bayar, cek pesanan lewat kode | Tanpa akun, tanpa login, tanpa riwayat pesanan |
| Admin | Login, kelola menu & konten, kelola reservasi, lihat dashboard | Satu tingkat role saja (bukan multi-role) |
| Midtrans | Mengirim notifikasi pembayaran ke webhook | Bukan pengguna; diverifikasi lewat signature |

Registrasi publik **tidak ada**. Akun admin dibuat lewat seeder.

---

## 4. Ruang Lingkup

**Termasuk versi 1:** company profile, katalog menu, form reservasi coffeeshop, form catering, pembayaran Midtrans Snap, cek pesanan, dashboard admin, kelola menu/konten/reservasi, invoice siap print.

**Tidak termasuk:** lihat bagian 11.

---

## 5. Kebutuhan Fungsional

Format: **ID — nama.** Penjelasan. *Selesai bila:* kriteria terima.

### 5.1 Pelanggan

**FR-P-01 — Beranda.** Hero, highlight coffeeshop & catering, galeri, testimoni (opsional).
*Selesai bila:* seluruh teks & gambar berasal dari tabel `settings`, bukan hardcode, dan halaman tampil rapi di lebar 375 px.

**FR-P-02 — Tentang.** Profil KIMJO, alamat, jam buka, kontak, sosial media.
*Selesai bila:* data dapat diubah admin tanpa mengedit kode.

**FR-P-03 — Menu Coffeeshop.** Daftar produk aktif: foto, nama, deskripsi, harga, kategori (kopi / non-kopi / makanan ringan).
*Selesai bila:* produk nonaktif tidak muncul, dan harga tampil dalam format Rupiah.

**FR-P-04 — Catering.** Menu catering dikelompokkan **per hari Senin–Jumat**, masing-masing dengan foto, nama, deskripsi, dan **harga per menu**.
*Selesai bila:* setiap menu hanya muncul di hari yang ditetapkan, dan hari tanpa menu tidak menampilkan bagian kosong.

**FR-P-05 — Form Reservasi / Catering.** Satu form, pilih jenis layanan:

| Jenis | Isian khusus |
|---|---|
| Reservasi coffeeshop | tanggal, slot waktu, jumlah orang |
| Catering | tanggal, pilihan menu + jumlah porsi |

Keduanya: nama, nomor WhatsApp, catatan. Total dihitung otomatis dan ditampilkan sebelum submit.
*Selesai bila:* total sesuai aturan BR-01/BR-02, slot penuh tidak bisa dipilih, dan catering di bawah 5 item ditolak dengan pesan jelas.

**FR-P-06 — Pembayaran.** Pelanggan klik "Bayar Sekarang", muncul popup **Snap** (QRIS, virtual account, e-wallet), membayar **penuh 100%**.
*Selesai bila:* setelah pembayaran berhasil, status berubah menjadi `dibayar` tanpa intervensi admin.

**FR-P-07 — Kode Booking.** Setiap pesanan mendapat kode unik format `KIMJO-XXXXXX` (6 karakter acak).
*Selesai bila:* kode tidak pernah sama antar pesanan dan ditampilkan setelah booking dibuat.

**FR-P-08 — Cek Pesanan.** Halaman publik berisi kolom kode booking. Setelah kode valid, tampil **rincian pesanan** (nama, tanggal, slot/jumlah orang atau daftar menu catering) dan **status pembayaran**.
*Selesai bila:* kode salah menampilkan pesan "kode tidak ditemukan" tanpa membocorkan data, dan nomor WhatsApp tampil tersamar (`08xx****1234`).

**FR-P-09 — Invoice.** Halaman siap print berisi rincian pesanan dan status pembayaran.
*Selesai bila:* hasil print muat satu halaman A4 dan tanpa elemen navigasi.

### 5.2 Admin

**FR-A-01 — Login & Logout.** Login email + password, tanpa registrasi.
*Selesai bila:* halaman admin tidak bisa diakses tanpa login, dan percobaan login dibatasi (rate limit).

**FR-A-02 — Dashboard.** Menampilkan: reservasi hari ini, reservasi menunggu pembayaran, total pendapatan bulan ini, dan **grafik pendapatan per bulan**.
*Selesai bila:* angka dihitung dari pesanan berstatus `dibayar`/`diproses`/`selesai`, dan grafik menampilkan minimal 6 bulan terakhir.

**FR-A-03 — Kelola Menu Coffeeshop.** CRUD menu: foto, nama, deskripsi, harga, kategori, status aktif.
*Selesai bila:* perubahan langsung terlihat di halaman publik.

**FR-A-04 — Kelola Menu Catering.** CRUD menu catering termasuk penempatan **hari** (Senin–Jumat), harga, foto.
*Selesai bila:* menu tanpa hari tidak bisa disimpan.

**FR-A-05 — Kelola Slot Waktu.** CRUD slot: jam mulai, jam selesai, kapasitas, status aktif.
*Selesai bila:* slot nonaktif tidak muncul di form reservasi.

**FR-A-06 — Kelola Paket Harga.** CRUD tingkatan tarif per jumlah orang (BR-01).
*Selesai bila:* perubahan tarif langsung memengaruhi perhitungan total reservasi baru, tanpa mengubah pesanan lama.

**FR-A-07 — Kelola Reservasi.** Daftar dengan filter tanggal & status, halaman detail pesanan, status pembayaran terbaca otomatis dari gateway, aksi admin: **proses / selesai / batalkan** (batal wajib mengisi alasan).
*Selesai bila:* admin tidak bisa mengubah status ke kombinasi yang dilarang bagian 7, dan alasan pembatalan tersimpan.

**FR-A-08 — Pengaturan Konten.** CRUD teks & gambar halaman beranda, tentang, kontak, jam buka, sosial media.
*Selesai bila:* semua konten halaman publik bisa diubah dari sini.

### 5.3 Pembayaran & Sistem

**FR-S-01 — Transaksi Midtrans.** Saat booking dibuat, sistem membuat transaksi Snap dan mengembalikan token untuk popup.
*Selesai bila:* `order_id` unik per percobaan pembayaran dan tersimpan di tabel `payments`.

**FR-S-02 — Webhook.** Endpoint menerima notifikasi Midtrans, memverifikasi `signature_key`, lalu memperbarui status pesanan.
*Selesai bila:* notifikasi dengan signature salah ditolak, dan notifikasi yang dikirim berulang tidak menggandakan data.

**FR-S-03 — Kedaluwarsa Otomatis.** Pesanan yang belum dibayar dalam **15 menit** berubah menjadi `kedaluwarsa` dan slotnya dilepas.
*Selesai bila:* scheduler berjalan tiap menit dan slot bisa dipesan orang lain setelahnya.

---

## 6. Aturan Bisnis

**BR-01 — Tarif reservasi coffeeshop per orang.**

| Jumlah orang | Tarif per orang |
|---|---|
| 1–5 | Rp 50.000 |
| 6–10 | Rp 45.000 |
| 11 atau lebih | Rp 40.000 |

Total = jumlah orang × tarif sesuai tingkatan (bukan tarif berlapis).

**BR-02 — Harga catering per menu.** Total = Σ(harga menu × jumlah porsi). **Minimal 5 item/porsi** per pesanan, boleh dari satu jenis menu.

**BR-03 — Pembayaran penuh 100% di muka.** Tidak ada DP, tidak ada pelunasan di kasir.

**BR-04 — Batas waktu bayar 15 menit.** Lewat dari itu pesanan `kedaluwarsa` dan slot dilepas.

**BR-05 — Slot waktu coffeeshop** (kapasitas kumulatif per slot):

| Slot | Kapasitas |
|---|---|
| 10:00–12:00 | 30 orang |
| 13:00–15:00 | 30 orang |
| 16:00–18:00 | 30 orang |
| 19:00–21:00 | 30 orang |

Contoh: slot 10:00–12:00 boleh diisi 3 reservasi @10 orang sampai total 30 orang.

**BR-06 — Tidak ada tanggal yang diblokir.** Tanpa hari libur khusus.

**BR-07 — Catering tidak dibatasi** jumlah pesanan per hari.

**BR-08 — Refund 100% saat pembatalan.** Diproses **manual** oleh admin lewat dashboard Midtrans, bukan otomatis oleh aplikasi.

**BR-09 — Batas pengajuan pembatalan H-1** sebelum tanggal reservasi, diajukan lewat admin (WhatsApp). Setelah H-1 tidak ada refund. Tidak ada tombol batal mandiri.

**BR-10 — Satu pesanan tidak boleh melebihi kapasitas slot.** Ditolak saat submit, termasuk bila dua pelanggan submit bersamaan.

---

## 7. Status Pesanan

| Status | Arti |
|---|---|
| `menunggu_pembayaran` | Pesanan dibuat, uang belum masuk |
| `dibayar` | Pembayaran lunas (webhook diterima), pesanan sah |
| `diproses` | Admin sedang menyiapkan |
| `selesai` | Pelanggan sudah datang / pesanan dijalankan |
| `dibatalkan` | Dibatalkan admin, dengan alasan |
| `kedaluwarsa` | Lewat 15 menit tanpa pembayaran |
| `gagal` | Transaksi ditolak/deny/expired oleh gateway |

**Transisi yang diizinkan:**

| Dari | Ke | Pemicu | Pelaku |
|---|---|---|---|
| `menunggu_pembayaran` | `dibayar` | webhook sukses | sistem |
| `menunggu_pembayaran` | `kedaluwarsa` | lewat 15 menit | scheduler |
| `menunggu_pembayaran` | `gagal` | webhook gagal | sistem |
| `dibayar` | `diproses` | aksi admin | admin |
| `diproses` | `selesai` | aksi admin | admin |
| `dibayar` / `diproses` | `dibatalkan` | aksi admin + alasan | admin |

Slot terisi selama status `menunggu_pembayaran`, `dibayar`, atau `diproses`. Slot dilepas saat `kedaluwarsa`, `gagal`, atau `dibatalkan`.

---

## 8. Halaman & Route

### Publik

| Halaman | Method | Route | Keterangan |
|---|---|---|---|
| Beranda | GET | `/` | Company profile |
| Tentang | GET | `/tentang` | Profil, alamat, jam buka |
| Menu Coffeeshop | GET | `/coffeeshop` | Daftar produk |
| Catering | GET | `/catering` | Menu per hari |
| Form Reservasi | GET | `/reservasi` | Pilih jenis layanan |
| Simpan Reservasi | POST | `/reservasi` | Hitung total, buat kode booking |
| Cek Pesanan (form) | GET | `/cek-pesanan` | Kolom kode booking |
| Cek Pesanan (proses) | POST | `/cek-pesanan` | Cari kode, redirect ke detail |
| Detail Pesanan | GET | `/cek-pesanan/{kode}` | Rincian + status pembayaran |
| Buat Pembayaran | POST | `/reservasi/{kode}/bayar` | Buat transaksi & Snap token |
| Invoice | GET | `/reservasi/{kode}/invoice` | Siap print |
| Webhook | POST | `/pembayaran/callback` | Notifikasi Midtrans |

### Admin (`/admin`, middleware `auth` + `admin`)

| Halaman | Method | Route |
|---|---|---|
| Login | GET/POST | `/login` |
| Logout | POST | `/logout` |
| Dashboard | GET | `/admin` |
| Menu Coffeeshop | CRUD | `/admin/menu-coffeeshop` |
| Menu Catering | CRUD | `/admin/menu-catering` |
| Slot Waktu | CRUD | `/admin/slot-waktu` |
| Paket Harga | CRUD | `/admin/paket-harga` |
| Reservasi | GET | `/admin/reservasi` |
| Detail Reservasi | GET | `/admin/reservasi/{booking}` |
| Ubah Status | PATCH | `/admin/reservasi/{booking}/status` |
| Pengaturan Konten | GET/PUT | `/admin/pengaturan` |

---

## 9. Data yang Disimpan (ringkas)

`users`, `menus`, `price_tiers`, `time_slots`, `bookings`, `booking_items`, `payments`, `settings`.

Detail kolom, tipe, relasi, dan index ada di **`DATABASE.md`**.

---

## 10. Kebutuhan Non-Fungsional

- **Mobile-first** — mayoritas pelanggan membuka dari HP lewat Instagram; nyaman di 375 px.
- **Bahasa** — seluruh antarmuka Bahasa Indonesia; format Rupiah `Rp 50.000`, tanggal `15 November 2026`.
- **Zona waktu** — Asia/Jakarta untuk semua tampilan & perhitungan.
- **Keamanan** — halaman cek pesanan kena rate limit (10 percobaan/menit/IP); `SERVER_KEY` hanya di `.env`; webhook diverifikasi signature.
- **Performa** — halaman publik < 2 detik pada koneksi 4G; gambar menu dikompres.
- **Aksesibilitas dasar** — kontras teks memadai, form punya label, tombol bisa dioperasikan keyboard.

---

## 11. Di Luar Scope Versi 1

- Notifikasi WhatsApp/email otomatis
- Refund otomatis di aplikasi (refund 100% tetap ada, tapi manual di dashboard Midtrans)
- Akun & riwayat pesanan pelanggan
- Pembatalan mandiri oleh pelanggan
- Multi-cabang
- Laporan keuangan & ekspor data (selain grafik dashboard)
- Manajemen stok bahan baku
- Promo/voucher/loyalitas
- Ongkir catering

---

## 12. Risiko Produk

| Risiko | Dampak | Rencana |
|---|---|---|
| Dua pelanggan memesan slot terakhir bersamaan | Overbooking | Validasi kapasitas di level database/locking |
| Pelanggan menutup popup Snap | Pesanan kedaluwarsa, pelanggan mengulang | Batas 15 menit + halaman cek pesanan sebagai rujukan; tombol bayar ulang bisa ditambah nanti |
| Kode booking ditebak orang lain | Data pesanan terbaca | Kode 6 karakter acak, rate limit, nomor WA tersamar |
| Webhook tidak sampai (localhost) | Status tidak berubah | Tunnel publik saat development; rekonsiliasi status manual oleh admin |
| Refund manual terlewat | Komplain pelanggan | Admin menandai pembatalan + alasan; refund dicek di dashboard Midtrans |
| Form publik di-spam | Data sampah | Honeypot + rate limit |

---

## 13. Riwayat Keputusan

| Tanggal | Keputusan |
|---|---|
| 15 Nov 2026 | Mengganti DP 50% menjadi pembayaran penuh 100% di muka |
| 15 Nov 2026 | Payment gateway: Midtrans Snap (sandbox, akun sudah tersedia) |
| 15 Nov 2026 | Batas bayar 15 menit |
| 15 Nov 2026 | Catering: menu per hari Senin–Jumat, minimal 5 item/porsi per pesanan |
| 15 Nov 2026 | Reservasi memakai slot waktu + kapasitas (4 slot, 30 orang/slot) |
| 15 Nov 2026 | Pembatalan refund 100%, diajukan H-1, diproses manual admin |
| 15 Nov 2026 | Cek pesanan: satu halaman, cukup kode booking, tanpa batal mandiri |
| 15 Nov 2026 | Tambah kelola slot waktu di admin (sebelumnya hanya disebut "bisa diubah admin") |

---

## 14. Dokumen Terkait

| Dokumen | Isi | Status |
|---|---|---|
| `PRD.md` | Kebutuhan produk (dokumen ini) | final |
| `PLAN.md` | Roadmap, tech stack, milestone, urutan pengisian dokumen | final |
| `DATABASE.md` | ERD, tabel, kolom, relasi | final |
| `FLOW.md` | Alur pengguna & transisi status | final |
| `UI-UX.md` | Design token, komponen, layout per halaman | final |
| `AGENTS.md` | Aturan kerja agent (stack, konvensi, larangan) | final |
| `HANDOFF.md` | Progres pengerjaan terkini | final |
| `DOKUMENTASI-QA.md` | Skenario pengujian | final |
