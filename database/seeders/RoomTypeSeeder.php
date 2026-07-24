<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            ['name' => 'Single'],
            ['name' => 'Double'],
            ['name' => 'Suite'],
            ['name' => 'Presidential'],
        ];

        foreach ($roomTypes as $type) {
            RoomType::firstOrCreate($type);
        }
    }
}
