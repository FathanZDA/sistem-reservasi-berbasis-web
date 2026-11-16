<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * Menu contoh. Harga di bawah adalah placeholder — ganti dengan harga asli KIMJO
 * lewat halaman admin atau dengan mengubah data seeder ini.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $coffeeshop = [
            ['nama' => 'Espresso', 'kategori' => 'kopi', 'harga' => 18000, 'deskripsi' => 'Satu shot espresso dari biji pilihan KIMJO.'],
            ['nama' => 'Americano', 'kategori' => 'kopi', 'harga' => 22000, 'deskripsi' => 'Espresso dengan air panas, disajikan dingin atau panas.'],
            ['nama' => 'Cappuccino', 'kategori' => 'kopi', 'harga' => 25000, 'deskripsi' => 'Espresso, susu steamed, dan foam tebal.'],
            ['nama' => 'Kopi Susu KIMJO', 'kategori' => 'kopi', 'harga' => 24000, 'deskripsi' => 'Signature: espresso, susu segar, dan gula aren.'],
            ['nama' => 'Matcha Latte', 'kategori' => 'non-kopi', 'harga' => 26000, 'deskripsi' => 'Matcha Jepang dengan susu segar.'],
            ['nama' => 'Chocolate', 'kategori' => 'non-kopi', 'harga' => 23000, 'deskripsi' => 'Cokelat pekat dengan susu.'],
            ['nama' => 'Croissant Butter', 'kategori' => 'makanan-ringan', 'harga' => 20000, 'deskripsi' => 'Croissant mentega, dipanggang setiap pagi.'],
            ['nama' => 'French Fries', 'kategori' => 'makanan-ringan', 'harga' => 22000, 'deskripsi' => 'Kentang goreng dengan saus rumahan.'],
        ];

        foreach ($coffeeshop as $index => $menu) {
            Menu::updateOrCreate(
                ['jenis' => Menu::JENIS_COFFEESHOP, 'nama' => $menu['nama']],
                $menu + ['urutan' => $index + 1, 'is_active' => true]
            );
        }

        $catering = [
            'senin' => ['Nasi Goreng Kampung', 28000],
            'selasa' => ['Ayam Geprek Sambal Matah', 30000],
            'rabu' => ['Nasi Ayam Kecap', 28000],
            'kamis' => ['Mie Goreng Jawa', 26000],
            'jumat' => ['Nasi Ikan Bakar', 35000],
        ];

        foreach ($catering as $hari => [$nama, $harga]) {
            Menu::updateOrCreate(
                ['jenis' => Menu::JENIS_CATERING, 'nama' => $nama],
                [
                    'hari' => $hari,
                    'harga' => $harga,
                    'deskripsi' => 'Paket catering '.$hari.' KIMJO, per porsi.',
                    'kategori' => null,
                    'urutan' => 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
