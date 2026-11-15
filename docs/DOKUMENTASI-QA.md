# KIMJO — Dokumentasi QA

**Status:** final — 15 November 2026
**Peran dokumen:** skenario pengujian yang bisa dijalankan ulang. Acuan perilaku yang benar adalah `PRD.md`; istilah kolom merujuk `DATABASE.md`; urutan langkah merujuk `FLOW.md`.

**Cara mencatat hasil:** setiap skenario diberi status `Lulus` / `Gagal` / `Belum diuji` + tanggal + catatan singkat. Skenario yang gagal dibuatkan item di `HANDOFF.md` bagian 4, jangan dihapus dari sini.

---

## 1. Lingkungan Uji

| Hal | Nilai yang diharapkan |
|---|---|
| PHP | 8.3+ (lokal 8.5.9) |
| Database | MySQL, data contoh dari seeder |
| Midtrans | Sandbox, `MIDTRANS_IS_PRODUCTION=false` |
| URL webhook | tunnel publik, mis. `https://xxxx.ngrok-free.app/pembayaran/callback` |
| Timezone | `Asia/Jakarta` |
| Lebar uji | 375 px dan 1280 px |

Perintah persiapan:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
php artisan schedule:work     # terminal terpisah
```

Perintah otomatis (bila test sudah ada):

```bash
php artisan test
```

---

## 2. Uji Fondasi (Milestone 1)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-01 | Timezone | `php artisan about` | `timezone` = `Asia/Jakarta` |
| QA-02 | Koneksi DB | `php artisan migrate:status` | semua migration `Ran`, driver `mysql` |
| QA-03 | Storage link | buka `/storage/...` gambar contoh | gambar tampil, bukan 404 |
| QA-04 | `.env` tidak ikut repo | `git status` | `.env` tidak muncul |

---

## 3. Auth & Role Admin (Milestone 2)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-05 | Login valid | `/login`, email+password seeder | masuk ke `/admin` |
| QA-06 | Login salah | password salah | tetap di `/login`, pesan "Email atau password salah." |
| QA-07 | Rate limit login | 6 percobaan gagal beruntun | percobaan ke-6 ditolak (429), pesan menunggu |
| QA-08 | Proteksi admin | buka `/admin` tanpa login (mode incognito) | redirect `/login` |
| QA-09 | Registrasi mati | akses `/register` | 404 |
| QA-10 | Logout | klik logout | sesi berakhir, `/admin` kembali ke login |
| QA-11 | Role bukan admin | ubah `users.role` jadi `staff`, login | `/admin` ditolak 403 |

---

## 4. Skema & Seeder (Milestone 3)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-12 | Seeder idempoten | `php artisan db:seed` dua kali | tidak ada duplikat menu/slot/tier |
| QA-13 | Tier BR-01 | periksa `price_tiers` | 3 baris: 1–5 = 50.000, 6–10 = 45.000, 11–∞ = 40.000 |
| QA-14 | Slot BR-05 | periksa `time_slots` | 4 slot, kapasitas 30, jam sesuai PRD |
| QA-15 | Harga integer | periksa kolom uang | tipe integer, tidak ada nilai desimal |
| QA-16 | FK | hapus booking lewat DB | `booking_items`/`payments` ikut terhapus; `menus`/`time_slots` tidak bisa dihapus bila dipakai |

---

## 5. Company Profile (Milestone 4)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-17 | Beranda dari DB | ubah `settings.beranda.hero_judul` di DB | judul berubah setelah refresh, tanpa edit kode |
| QA-18 | Menu nonaktif | set `menus.is_active = 0` | menu hilang dari `/coffeeshop` |
| QA-19 | Catering per hari | buka `/catering`, klik tab Rabu | hanya menu `hari = rabu` yang tampil |
| QA-20 | Hari kosong | hapus semua menu Kamis | tab Kamis menampilkan pesan kosong, tanpa section rusak |
| QA-21 | Format rupiah | lihat harga menu | tampil `Rp 50.000`, bukan `50000` atau `50,000` |
| QA-22 | Mobile 375 px | buka semua halaman publik di 375 px | tanpa scroll horizontal, navbar jadi drawer |

---

## 6. Admin Konten & Menu (Milestone 5)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-23 | CRUD coffeeshop | tambah menu, simpan | muncul di `/coffeeshop` |
| QA-24 | Menu catering tanpa hari | simpan dengan `hari` kosong | ditolak, pesan pada field hari (FR-A-04) |
| QA-25 | Slot nonaktif | nonaktifkan slot 13:00–15:00 | slot hilang dari form reservasi, booking lama tetap ada |
| QA-26 | Ubah tarif | ubah tier 1–5 jadi 55.000 | booking baru memakai 55.000, booking lama tidak berubah |
| QA-27 | Hapus menu terpakai | hapus menu yang ada di `booking_items` | ditolak, arahkan ke nonaktifkan |
| QA-28 | Upload gambar | unggah JPG 2 MB | tersimpan, pratinjau tampil; file > 2 MB ditolak |

---

## 7. Reservasi & Kalkulasi (Milestone 6)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-29 | Total coffeeshop 4 orang | isi 4 orang | total `Rp 200.000` (4 × 50.000) |
| QA-30 | Total coffeeshop 6 orang | isi 6 orang | `Rp 270.000` (6 × 45.000) — bukan berlapis |
| QA-31 | Total coffeeshop 12 orang | isi 12 orang | `Rp 480.000` (12 × 40.000) |
| QA-32 | Total catering | 2 menu × 3 porsi, harga 25.000 | `Rp 150.000` dan lolos minimal 5 porsi |
| QA-33 | Catering di bawah minimal | total 4 porsi | ditolak, pesan jelas tentang minimal 5 porsi |
| QA-34 | Kode booking | submit 2 pesanan | kode format `KIMJO-XXXXXX`, berbeda, unik di DB |
| QA-35 | Kode unik ketat | cek index `bookings.kode_booking` | unique |
| QA-36 | Tanggal lampau | pilih tanggal kemarin | ditolak di validasi |
| QA-37 | Kapasitas kumulatif | 3 pesanan @10 orang di slot sama | ketiganya diterima, total 30 |
| QA-38 | Slot penuh | pesanan ke-4 @1 orang di slot itu | ditolak, pesan slot penuh |
| QA-39 | Kapasitas melebihi sisa | slot sisa 5, pesan 6 orang | ditolak |
| QA-40 | Submit bersamaan | dua request @20 orang di slot kosong (kirim paralel) | hanya satu berhasil; tidak ada overbooking |
| QA-41 | Total dimanipulasi | ubah `total` di HTML sebelum submit | total tersimpan tetap hasil hitung server |
| QA-42 | Harga menu dimanipulasi | ubah `data-harga` di browser | subtotal tersimpan memakai harga DB |
| QA-43 | Item catering disalin | pesan catering, lalu ubah harga menu | `booking_items.harga_satuan` tidak berubah |
| QA-44 | Menu hari tidak cocok | pilih tanggal Jumat tapi menu Senin via request manual | ditolak validasi |
| QA-45 | Honeypot/rate limit | submit form berkali-kali cepat | dibatasi, tidak membuat data berlebih |

---

## 8. Pembayaran (Milestone 7)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-46 | Snap token | klik Bayar Sekarang | token dibuat, baris `payments` baru `pending` dengan `order_id` unik |
| QA-47 | `gross_amount` | periksa baris payments | sama dengan `bookings.total_harga` |
| QA-48 | Bayar sukses | bayar via simulator sandbox (QRIS/VA) | webhook masuk, booking jadi `dibayar`, `paid_at` terisi |
| QA-49 | Signature salah | kirim `signature_key` palsu | HTTP 403, tidak ada perubahan data |
| QA-50 | `order_id` tak dikenal | kirim notifikasi `order_id` acak | 404, tidak membuat data baru |
| QA-51 | Nominal tidak cocok | kirim `gross_amount` berbeda (signature valid) | 422, status tidak berubah |
| QA-52 | Webhook ganda | kirim notifikasi `settlement` dua kali | 200 dua kali, tidak ada duplikasi, `paid_at` tidak berubah |
| QA-53 | Notifikasi `pending` | kirim `pending` | status tetap `menunggu_pembayaran` |
| QA-54 | `deny`/`cancel` | kirim `deny` | `bookings.status` = `gagal` |
| QA-55 | `expire` gateway | kirim `expire` | `bookings.status` = `kedaluwarsa`, slot terlepas |
| QA-56 | Tidak menurunkan status | booking `dibayar`, kirim notifikasi `expire` lama | status tetap `dibayar`, dibalas 200 |
| QA-57 | Server key aman | cek response/HTML/JS | tidak ada `MIDTRANS_SERVER_KEY` |
| QA-58 | Bayar ulang | popup ditutup, klik Bayar lagi sebelum 15 menit | `order_id` baru, baris `payments` baru, tidak mengubah total |
| QA-59 | Bayar setelah kedaluwarsa | tunggu > 15 menit, klik Bayar | ditolak, status ditampilkan, tidak membuat transaksi |
| QA-60 | Kedaluwarsa otomatis | buat booking, tunggu 15 menit lebih 1 menit | status `kedaluwarsa`, slot bisa dipesan lagi |
| QA-61 | Scheduler tiap menit | `php artisan schedule:work`, amati log | command berjalan tiap menit |
| QA-62 | Scheduler ganda | jalankan `bookings:expire` dua kali | tidak error, tidak mengubah booking lain |

---

## 9. Cek Pesanan & Invoice (Milestone 6 & 9)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-63 | Kode valid | masukkan kode pesanan | redirect ke detail dengan rincian benar |
| QA-64 | Kode salah | masukkan `KIMJO-ZZZZZZ` | pesan "Kode pesanan tidak ditemukan.", tidak membocorkan data |
| QA-65 | Normalisasi | masukkan kode huruf kecil / tanpa `KIMJO-` | tetap ditemukan |
| QA-66 | WA tersamar | lihat detail | `08xx****1234`, bukan nomor penuh |
| QA-67 | Rate limit cek | 11 percobaan/menit | percobaan ke-11 ditolak (429) |
| QA-68 | Hitung mundur | buka detail sebelum bayar | mundur dari 15:00, tombol Bayar hilang saat habis |
| QA-69 | Tanpa tombol batal | lihat detail semua status | tidak ada tombol batal mandiri |
| QA-70 | Invoice print | buka invoice, Ctrl+P | satu halaman A4, tanpa navbar/tombol/warna gelap |
| QA-71 | Invoice kode valid saja | buka invoice kode acak | 404 |

---

## 10. Admin Reservasi & Dashboard (Milestone 8)

| ID | Skenario | Langkah | Hasil yang diharapkan |
|---|---|---|---|
| QA-72 | Filter daftar | filter tanggal + status | hanya baris yang cocok tampil |
| QA-73 | Detail pembayaran | buka detail booking lunas | riwayat payments tampil dengan metode & status |
| QA-74 | Transisi sah | `dibayar` → `diproses` → `selesai` | berhasil, badge berubah |
| QA-75 | Transisi terlarang | paksa `selesai` → `dibayar` via request | ditolak 422/403, status tidak berubah |
| QA-76 | Batal tanpa alasan | kirim form batal kosong | ditolak |
| QA-77 | Batal dengan alasan | isi alasan | status `dibatalkan`, alasan tersimpan, slot terlepas |
| QA-78 | Refund manual | setelah batal | muncul instruksi refund manual; aplikasi tidak memanggil API refund |
| QA-79 | Statistik dashboard | bandingkan angka dengan data DB | reservasi hari ini, menunggu bayar, pendapatan bulan ini sesuai |
| QA-80 | Rumus pendapatan | buat booking `menunggu_pembayaran` dan `dibatalkan` | tidak dihitung pendapatan |
| QA-81 | Grafik 6 bulan | kosongkan data 3 bulan lalu | grafik tetap menampilkan 6 bulan, bulan kosong = 0 |
| QA-82 | Cap waktu 15 menit | bandingkan `expires_at` dan `created_at` | selisih tepat 15 menit |

---

## 11. Uji Non-Fungsional

| ID | Skenario | Hasil yang diharapkan |
|---|---|---|
| QA-83 | Muat halaman publik di 4G | < 2 detik (ukur DevTools throttling) |
| QA-84 | Ukuran gambar | foto menu < 300 KB setelah kompres |
| QA-85 | Kontras | teks utama ≥ 4.5:1 (cek DevTools) |
| QA-86 | Keyboard | semua tombol/input bisa dicapai Tab, fokus terlihat |
| QA-87 | Label form | setiap input punya `<label for>` |
| QA-88 | Reduced motion | animasi mati saat `prefers-reduced-motion: reduce` |
| QA-89 | Bahasa | tidak ada teks Inggris yang terlihat pengguna |
| QA-90 | Judul halaman | setiap halaman punya `<title>` yang berbeda |

---

## 12. Matriks Kasus Batas Wajib

| Kasus | Skenario terkait |
|---|---|
| Slot penuh | QA-38, QA-39, QA-40 |
| 15 menit habis | QA-59, QA-60, QA-68 |
| Signature salah | QA-49 |
| Kode pesanan salah | QA-64 |
| Catering < 5 porsi | QA-33 |
| Webhook ganda | QA-52, QA-56 |
| Transisi status terlarang | QA-75 |
| Nominal dimanipulasi | QA-41, QA-42, QA-51 |
| Kunci rahasia bocor | QA-57 |
| Mobile 375 px | QA-22 |

---

## 13. Kriteria Rilis Versi 1

Semua harus benar:

1. QA-01…QA-82 berstatus **Lulus**.
2. Tidak ada overbooking terdeteksi pada QA-40.
3. Pembayaran sandbox end-to-end berhasil (QA-48) lewat tunnel publik.
4. Invoice QR/print rapi satu halaman (QA-70).
5. Semua halaman publik lolos uji 375 px (QA-22).
6. `HANDOFF.md` bagian 4 kosong atau hanya berisi hal yang disetujui pemilik.
