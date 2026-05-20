<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['name' => 'Damascus', 'name_ar' => 'دمشق', 'code' => 'DM'],
            ['name' => 'Aleppo', 'name_ar' => 'حلب', 'code' => 'AL'],
            ['name' => 'Homs', 'name_ar' => 'حمص', 'code' => 'HM'],
            ['name' => 'Hama', 'name_ar' => 'حماة', 'code' => 'HA'],
            ['name' => 'Lattakia', 'name_ar' => 'اللاذقية', 'code' => 'LA'],
            ['name' => 'Tartous', 'name_ar' => 'طرطوس', 'code' => 'TA'],
            ['name' => 'Idlib', 'name_ar' => 'إدلب', 'code' => 'ID'],
            ['name' => 'Daraa', 'name_ar' => 'درعا', 'code' => 'DA'],
            ['name' => 'Sweida', 'name_ar' => 'السويداء', 'code' => 'SW'],
            ['name' => 'Quneitra', 'name_ar' => 'القنيطرة', 'code' => 'QU'],
            ['name' => 'Deir ez-Zor', 'name_ar' => 'دير الزور', 'code' => 'DE'],
            ['name' => 'Raqqa', 'name_ar' => 'الرقة', 'code' => 'RA'],
            ['name' => 'Hasakah', 'name_ar' => 'الحسكة', 'code' => 'HS'],
            ['name' => 'Damascus Countryside', 'name_ar' => 'ريف دمشق', 'code' => 'RD'],
        ];

        foreach ($governorates as $governorate) {
            \App\Models\Governorate::create($governorate);
        }
    }
}
