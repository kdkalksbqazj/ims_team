<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Modules\Organization\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create(['name' => 'Main Branch', 'location' => 'Downtown']);
        Branch::create(['name' => 'North Branch', 'location' => 'Uptown']);
        Branch::create(['name' => 'South Branch', 'location' => 'Suburbs']);
    }
}
