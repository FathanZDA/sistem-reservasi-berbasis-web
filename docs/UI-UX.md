# KIMJO — Panduan UI/UX

**Status:** final — 15 November 2026
**Peran dokumen:** acuan tampilan (design token, komponen, layout per halaman, perilaku responsif). Isi teks final tetap di `settings`/database, bukan di dokumen ini. Kebutuhan halaman merujuk `PRD.md` bagian 8.

Arah rasa: **hangat, gelap, premium-tapi-ramah** — latar hitam pekat, aksen maroon, krem sebagai pemisah. Foto produk besar, teks tenang, banyak ruang kosong.

---

## 1. Design Token

### 1.1 Warna (`resources/css/app.css`, CSS custom properties)

| Token | Hex | Dipakai untuk |
|---|---|---|
| `--hitam` | `#111010` | Latar utama halaman publik |
| `--hitam-lembut` | `#1B1917` | Kartu, panel, navbar transparan |
| `--hitam-garis` | `#2A2624` | Border, divider |
| `--maroon` | `#5E1A22` | Warna brand utama, tombol primer, footer |
| `--maroon-muda` | `#8A2C35` | Hover tombol, aksen garis, badge |
| `--krem` | `#F4EDE4` | Latar section terang, teks di atas maroon |
| `--krem-lembut` | `#E7DCCF` | Border di latar terang |
| `--emas` | `#C9A46A` | Aksen halus: ikon, garis bawah judul, harga |
| `--teks` | `#EDE7E0` | Teks utama di latar gelap |
| `--teks-redup` | `#A69F97` | Teks sekunder, keterangan |
| `--sukses` | `#3F7D5A` | Status lunas/selesai |
| `--peringatan` | `#B98838` | Menunggu pembayaran |
| `--bahaya` | `#A33A3A` | Gagal, batal, kedaluwarsa |

Aturan pemakaian: satu halaman maksimal **dua** warna aksen dominan. Maroon tidak dipakai untuk teks paragraf. Jangan membuat warna baru tanpa menambahkannya ke tabel ini.

### 1.2 Tipografi

| Peran | Font | Ukuran | Berat | Line-height |
|---|---|---|---|---|
| Judul hero | Playfair Display, serif | 40 px (mobile) / 64 px (desktop) | 600 | 1.1 |
| Judul section | Playfair Display | 28 / 40 px | 600 | 1.2 |
| Subjudul kartu | Playfair Display | 20 px | 600 | 1.3 |
| Body | Inter, sans-serif | 16 px | 400 | 1.65 |
| Body kecil | Inter | 14 px | 400 | 1.6 |
| Label form | Inter | 13 px | 500, letter-spacing .04em, uppercase | 1.4 |
| Harga | Inter | 16 px | 600 | 1.4 |
| Tombol | Inter | 14 px | 600, letter-spacing .06em, uppercase | 1 |

Font dimuat dari Google Fonts (`Playfair Display` 600, `Inter` 400/500/600) dengan `preconnect` + `display=swap`. Fallback: `Georgia, serif` dan `system-ui, sans-serif`.
Skala ukuran mengikuti langkah: 12, 14, 16, 18, 20, 24, 28, 32, 40, 48, 64 px. Maksimal lebar paragraf 68 karakter.

### 1.3 Spacing & Grid

- Skala: 4, 8, 12, 16, 24, 32, 40, 56, 72, 96, 128 px (kelipatan 4).
- Container: lebar maks **1200 px**, padding samping **20 px** (mobile) / **40 px** (≥ 768 px) / **64 px** (≥ 1280 px).
- Jarak antar section: **72 px** mobile, **120 px** desktop.
- Radius: `--r-kecil: 6px`, `--r-sedang: 12px`, `--r-besar: 20px`. Foto menu memakai `--r-sedang`, tombol `--r-kecil`.
- Shadow hanya di latar terang: `0 12px 32px rgba(17,16,16,.10)`.

### 1.4 Breakpoint

| Nama | Lebar | Perubahan utama |
|---|---|---|
| `sm` | < 640 px | Navbar jadi menu drawer, grid menu 1 kolom (coffeeshop 2 kolom), hero 1 kolom |
| `md` | 640–1023 px | Grid menu 2–3 kolom, sidebar admin jadi drawer |
| `lg` | 1024–1279 px | Grid menu 3 kolom, layout dua kolom untuk detail |
| `xl` | ≥ 1280 px | Container 1200 px, hero teks + foto berdampingan |

**Target minimum uji: 375 px.** Tidak boleh ada scroll horizontal di lebar mana pun.

---

## 2. Komponen Dasar

| Komponen | Aturan |
|---|---|
| **Tombol primer** | Latar `--maroon`, teks `--krem`, padding 14/24, uppercase 14 px, radius 6, hover `--maroon-muda` + naik 2 px, aktif turun 1 px, disabled opacity .5 tanpa hover |
| **Tombol sekunder** | Transparan, border 1 px `--emas`/`--krem-lembut`, teks sesuai latar |
| **Tombol teks** | Tanpa border, garis bawah saat hover |
| **Input** | Latar `--hitam-lembut` (atau putih di section krem), border 1 px `--hitam-garis`, radius 6, padding 12/14, fokus: border `--emas` + `outline` 2 px offset 2 |
| **Label** | Di atas input, selalu ada (bukan placeholder saja) |
| **Pesan error** | Teks 13 px `--bahaya` di bawah input, ikon kecil opsional |
| **Badge status** | Pil radius penuh, padding 4/10, teks 12 px 600; warna sesuai peta bagian 5 |
| **Kartu menu** | Foto rasio 4:3 `object-fit: cover`, isi padding 20, nama + deskripsi 2 baris (`line-clamp`), harga `--emas` |
| **Divider** | 1 px `--hitam-garis` atau `--krem-lembut` |
| **Toast** | Kanan bawah, latar `--hitam-lembut`, border kiri 3 px warna status, hilang 4 detik |

## 3. Navigasi & Footer

**Navbar publik:** tinggi 72 px (mobile 64 px), sticky, latar `--hitam` dengan `backdrop-filter: blur(8px)` dan opacity .92 setelah scroll > 40 px. Kiri logo teks "KIMJO" (Playfair, letter-spacing .12em). Kanan: Beranda · Tentang · Coffeeshop · Catering · Cek Pesanan, lalu tombol **Reservasi** (primer, paling kanan). Di < 640 px semua tautan masuk drawer dari kanan, tombol Reservasi tetap di dalam drawer.

**Footer:** latar `--maroon`, teks `--krem`. Tiga kolom (bertumpuk di mobile): alamat & jam buka · tautan cepat · sosial media + WhatsApp. Baris bawah: `© 2026 KIMJO` dan tautan kebijakan pembatalan (teks dari `settings`).

**Sidebar admin:** lebar 240 px, latar `--hitam-lembut`, item aktif diberi garis kiri 3 px `--emas`. Di < 1024 px menjadi drawer dengan tombol hamburger di topbar. Topbar admin tinggi 64 px berisi judul halaman, nama admin, tombol logout.

---

## 4. Wireframe per Halaman

### 4.1 Beranda `/`

```
┌ Navbar ────────────────────────────────────────────┐
│ HERO (min-height 88vh, foto gelap + overlay .45)   │
│   "Kopi yang menemani cerita"      [Reservasi]     │
│   subjudul 1–2 baris               [Lihat Menu]    │
└────────────────────────────────────────────────────┘
  Section "Tentang singkat"  — 2 kolom: foto | teks + [Selengkapnya]
  Section "Menu Coffeeshop"  — judul + 3 kartu unggulan + [Lihat semua]
  Section "Catering"         — banner maroon, teks + [Pesan Catering]
  Section "Galeri"           — carousel 3–6 foto, geser
  Section "Testimoni"        (opsional, tampil bila ada isi)
  Teks berjalan (marquee)    — satu baris pengulangan nama menu
  CTA penutup                — "Siap reservasi?" + tombol
└ Footer ────────────────────────────────────────────┘
```

Hanya section yang datanya ada yang dirender; section kosong hilang tanpa meninggalkan jarak aneh.

### 4.2 Tentang `/tentang`

Hero pendek 40vh dengan judul "Tentang KIMJO". Dua kolom: cerita (teks panjang, 68 karakter/baris) dan kartu info (alamat, jam buka, WhatsApp, Instagram, tombol buka Maps). Di bawah: strip foto 2–3 gambar. Mobile: kartu info pindah ke atas foto.

### 4.3 Coffeeshop `/coffeeshop`

Judul + subjudul. Baris filter kategori: **Semua · Kopi · Non-Kopi · Makanan Ringan** (pil; filter di sisi klien tanpa reload, tetap berfungsi bila JS mati dengan fallback menampilkan semua). Grid kartu menu: 2 kolom ≥ 400 px, 3 kolom ≥ 1024 px, 1 kolom < 400 px. Bila kosong: pesan "Menu sedang diperbarui."

### 4.4 Catering `/catering`

Hero pendek. Penjelasan singkat + pengingat "minimal 5 porsi per pesanan" (teks diambil dari aturan; jangan menulis angka baru). Tab hari: **Senin Selasa Rabu Kamis Jumat** (horizontal scroll di mobile), default hari ini bila termasuk Senin–Jumat, selain itu Senin. Isi: grid kartu menu hari terpilih. Hari tanpa menu: tampilkan satu baris "Belum ada menu untuk hari ini." dan tab tetap bisa diklik. Tombol tetap "Pesan Catering" → `/reservasi?jenis=catering`.

### 4.5 Form Reservasi `/reservasi`

```
┌ Judul "Reservasi" + kalimat pengantar ─────────────┐
│ Tab: [ Coffeeshop ] [ Catering ]                   │
├ Kolom kiri (form) ─────────┬ Ringkasan (sticky) ───┤
│ Coffeeshop:                │ Jenis layanan          │
│  Tanggal [date]            │ Tanggal / slot         │
│  Slot (4 kartu radio)      │ Jumlah orang           │
│  Jumlah orang [number]     │ ---------------------- │
│ Catering:                  │ Total   Rp 0           │
│  Tanggal [date]            │ [Pesan Sekarang]       │
│  Daftar menu hari itu      │ Catatan kecil 15 menit │
│   kartu + [−] n [+]       │                        │
│  Keranjang total porsi     │                        │
│ Nama, WhatsApp, Catatan    │                        │
└────────────────────────────┴────────────────────────┘
```

- Di mobile ringkasan menjadi **bar bawah tetap** (sticky bottom) berisi total + tombol, agar tombol selalu terlihat.
- Kartu slot menampilkan jam besar + "sisa X orang"; slot penuh/nonaktif tampil redup, tidak bisa diklik, label "Penuh".
- Ringkasan dihitung ulang setiap perubahan, tanpa reload. Bila JS mati, tombol submit tetap mengirim dan server menampilkan total di halaman hasil.
- Tombol submit punya state loading ("Memproses…") dan dinonaktifkan setelah diklik.

### 4.6 Cek Pesanan `/cek-pesanan`

Kotak terpusat maks 520 px: judul, satu input kode besar (letter-spacing .1em, uppercase otomatis), tombol "Cek Pesanan". Di bawah: teks bantuan "Kode ada di halaman setelah kamu memesan, format KIMJO-XXXXXX."

### 4.7 Detail Pesanan `/cek-pesanan/{kode}`

```
Kartu utama (maks 720 px):
  KIMJO-ABC123            [badge status]
  Nama · jenis layanan
  Tanggal · slot · jumlah orang   (atau daftar menu + porsi)
  ------------------------------
  Total                 Rp xxx.xxx
  Status pembayaran     Lunas / Belum dibayar
  [Bayar Sekarang]  (hanya bila menunggu & belum habis)
  Hitung mundur  14:32
  [Lihat Invoice]
  Catatan pembatalan: hubungi admin (H-1)
```

Hitung mundur: teks monospace 20 px, berubah merah bila < 5 menit. Saat habis, tombol Bayar hilang dan badge berubah `kedaluwarsa` setelah reload otomatis satu kali.

### 4.8 Invoice `/reservasi/{kode}/invoice`

Latar putih, teks hitam, maks 800 px. Kop: logo teks KIMJO + alamat + kontak. Kanan atas: "INVOICE", kode booking, tanggal cetak. Tabel rincian (menu/jumlah/harga/subtotal atau baris reservasi). Total tebal. Badge status. Catatan kaki: "Pembayaran penuh 100% melalui Midtrans." Tombol "Cetak" (hidden saat print).

### 4.9 Login `/login`

Layar penuh latar `--hitam` dengan foto/blur maroon. Kartu 400 px di tengah: logo, judul "Masuk Admin", email, password, tombol primer lebar penuh, pesan error di atas tombol. Tanpa tautan registrasi/lupa password.

### 4.10 Dashboard `/admin`

Baris 3 kartu statistik: **Reservasi hari ini**, **Menunggu pembayaran**, **Pendapatan bulan ini**. Di bawah: grafik batang pendapatan 6 bulan terakhir (CSS bar, label bulan di bawah, nilai di hover/tooltip judul) dan tabel 5 pesanan terbaru dengan badge status. Angka 0 tetap tampil (bukan kosong).

### 4.11 Daftar Reservasi `/admin/reservasi`

Filter baris atas: rentang tanggal (dua input date) + dropdown status + tombol "Terapkan" + "Reset". Tabel: Kode · Nama · Jenis · Tanggal · Slot/Item · Total · Status · Aksi (Lihat). Di mobile tabel berubah menjadi daftar kartu (bukan scroll horizontal). Pagination 20 baris. Empty state: "Belum ada reservasi pada filter ini."

### 4.12 Detail Reservasi `/admin/reservasi/{id}`

Dua kolom (mobile bertumpuk): kiri rincian pesanan + item catering + riwayat pembayaran (order_id, metode, status, waktu); kanan panel aksi berisi status sekarang, tombol transisi yang sah, dan tombol "Batalkan" (bahaya, memunculkan modal alasan wajib).

### 4.13 Halaman CRUD Admin

Pola seragam: judul + tombol "Tambah" di kanan → tabel/daftar kartu → form tambah/edit di halaman terpisah dengan tombol **Simpan** (primer) dan **Batal** (sekunder). Hapus memakai modal konfirmasi. Upload gambar: pratinjau kotak 4:3 + teks "JPG/PNG, maks 2 MB". Pesan sukses muncul sebagai toast di atas tabel.

### 4.14 Pengaturan `/admin/pengaturan`

Form panjang dikelompokkan per kartu (Beranda, Tentang, Kontak). Satu tombol "Simpan Semua" menempel di bawah. Field gambar punya pratinjau dan tombol "Ganti".

---

## 5. Peta Badge Status

| Status | Teks tampil | Warna |
|---|---|---|
| `menunggu_pembayaran` | Menunggu Pembayaran | `--peringatan` |
| `dibayar` | Dibayar | `--sukses` |
| `diproses` | Diproses | `--emas` (teks gelap) |
| `selesai` | Selesai | `--sukses` (lebih redup) |
| `dibatalkan` | Dibatalkan | `--bahaya` |
| `kedaluwarsa` | Kedaluwarsa | `--teks-redup` |
| `gagal` | Gagal | `--bahaya` |

Semua tanggal tampil `15 November 2026`; jam `10:00–12:00`. Nominal `Rp 50.000` (titik ribuan, tanpa desimal).

---

## 6. Animasi & Interaksi

| Nama | Perilaku | Durasi/easing |
|---|---|---|
| Scroll reveal | Section muncul (opacity 0→1, translateY 16→0) saat masuk viewport, sekali saja | 500 ms, ease-out |
| Hover kartu | Naik 4 px + shadow lembut | 200 ms |
| Navbar shrink | Padding berkurang + blur setelah scroll | 250 ms |
| Carousel galeri | Geser horizontal, tombol ‹ ›, swipe di mobile, autoplay 5 detik berhenti saat hover | 400 ms |
| Marquee | Satu baris teks berulang, 30 detik/loop, berhenti saat hover | linear |
| Filter menu | Kartu fade 150 ms saat kategori berganti | 150 ms |
| Countdown | Update tiap detik, tanpa animasi | — |
| Modal | Fade latar .4 + skala kartu .96→1 | 180 ms |

Semua animasi **wajib** dimatikan di `@media (prefers-reduced-motion: reduce)`. Tidak ada animasi yang menahan interaksi pengguna.

---

## 7. Perilaku Mobile

- Sasaran sentuh minimal **44×44 px**.
- Input `type` dan `inputmode` tepat: tanggal → date picker, WhatsApp → `tel`, jumlah orang → `numeric`.
- Tombol utama di form reservasi menempel bawah layar, tinggi 56 px, dengan safe-area padding.
- Drawer navigasi menutup saat tautan diklik dan saat tombol Esc ditekan.
- Tabel admin menjadi kartu; tidak ada scroll horizontal.
- Gambar memakai `loading="lazy"` kecuali hero; `width/height` selalu diisi agar tidak ada layout shift.
- Popup Snap mengikuti bawaan Midtrans; halaman tetap bisa di-scroll setelah popup ditutup.

## 8. Aksesibilitas

- Kontras teks utama di latar hitam ≥ 4.5:1 (memakai `--teks`, bukan abu tua).
- Fokus terlihat jelas di semua elemen interaktif (outline `--emas`).
- Setiap input punya `<label for>`; error dihubungkan via `aria-describedby`.
- Badge status tidak mengandalkan warna saja — selalu ada teks.
- Tombol ikon punya `aria-label`.
- Urutan heading logis (satu `h1` per halaman).

## 9. Aturan Cetak (Invoice)

```css
@media print {
  /* sembunyikan navbar, footer, tombol, sidebar */
  /* latar putih, teks hitam, font 12pt */
  /* @page { size: A4; margin: 18mm } */
  /* hindari pemotongan tabel: tr { break-inside: avoid } */
}
```

Hasil print maksimal satu halaman A4; tanpa warna latar gelap dan tanpa URL/tautan.

## 10. Aset

- Logo: teks "KIMJO" (Playfair, letter-spacing .12em) agar tidak bergantung file; favicon huruf "K".
- Foto: dari admin, disimpan di `storage/app/public`, rasio 4:3, sisi terpanjang maks 1600 px, dikompres (target < 300 KB).
- Placeholder bila foto belum ada: kotak `--hitam-lembut` dengan huruf awal nama menu di tengah.
