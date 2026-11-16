<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['jam_mulai' => '10:00:00', 'jam_selesai' => '12:00:00'],
            ['jam_mulai' => '13:00:00', 'jam_selesai' => '15:00:00'],
            ['jam_mulai' => '16:00:00', 'jam_selesai' => '18:00:00'],
            ['jam_mulai' => '19:00:00', 'jam_selesai' => '21:00:00'],
        ];

        foreach ($slots as $slot) {
            TimeSlot::updateOrCreate(
                ['jam_mulai' => $slot['jam_mulai'], 'jam_selesai' => $slot['jam_selesai']],
                ['kapasitas' => 30, 'is_active' => true]
            );
        }
    }
}
