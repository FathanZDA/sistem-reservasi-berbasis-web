<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'beranda.hero_judul', 'value' => 'Kopi, Ruang, dan Cerita di KIMJO', 'grup' => 'beranda', 'tipe' => 'text', 'label' => 'Judul hero'],
            ['key' => 'beranda.hero_subjudul', 'value' => 'Singgah untuk secangkir kopi, atau percayakan catering acara Anda kepada kami.', 'grup' => 'beranda', 'tipe' => 'textarea', 'label' => 'Subjudul hero'],
            ['key' => 'beranda.hero_gambar', 'value' => null, 'grup' => 'beranda', 'tipe' => 'image', 'label' => 'Gambar hero'],
            ['key' => 'beranda.tentang_ringkas', 'value' => 'KIMJO adalah coffeeshop yang juga melayani catering untuk acara dan kebutuhan harian.', 'grup' => 'beranda', 'tipe' => 'textarea', 'label' => 'Ringkasan tentang'],
            ['key' => 'beranda.gambar_galeri_1', 'value' => null, 'grup' => 'beranda', 'tipe' => 'image', 'label' => 'Gambar galeri 1'],
            ['key' => 'beranda.gambar_galeri_2', 'value' => null, 'grup' => 'beranda', 'tipe' => 'image', 'label' => 'Gambar galeri 2'],
            ['key' => 'beranda.gambar_galeri_3', 'value' => null, 'grup' => 'beranda', 'tipe' => 'image', 'label' => 'Gambar galeri 3'],
            ['key' => 'tentang.judul', 'value' => 'Tentang KIMJO', 'grup' => 'tentang', 'tipe' => 'text', 'label' => 'Judul halaman tentang'],
            ['key' => 'tentang.isi', 'value' => 'KIMJO berawal dari kecintaan pada kopi dan keinginan menghadirkan ruang yang hangat untuk berkumpul.', 'grup' => 'tentang', 'tipe' => 'textarea', 'label' => 'Isi tentang'],
            ['key' => 'tentang.alamat', 'value' => 'Alamat KIMJO (lengkapi dari pengaturan konten)', 'grup' => 'tentang', 'tipe' => 'textarea', 'label' => 'Alamat'],
            ['key' => 'tentang.jam_buka', 'value' => 'Setiap hari, 10:00–21:00', 'grup' => 'tentang', 'tipe' => 'text', 'label' => 'Jam buka'],
            ['key' => 'tentang.maps_url', 'value' => null, 'grup' => 'tentang', 'tipe' => 'url', 'label' => 'Tautan Google Maps'],
            ['key' => 'kontak.whatsapp', 'value' => null, 'grup' => 'kontak', 'tipe' => 'text', 'label' => 'Nomor WhatsApp'],
            ['key' => 'kontak.email', 'value' => null, 'grup' => 'kontak', 'tipe' => 'text', 'label' => 'Email'],
            ['key' => 'kontak.instagram', 'value' => null, 'grup' => 'kontak', 'tipe' => 'url', 'label' => 'Instagram'],
            ['key' => 'kontak.tiktok', 'value' => null, 'grup' => 'kontak', 'tipe' => 'url', 'label' => 'TikTok'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
