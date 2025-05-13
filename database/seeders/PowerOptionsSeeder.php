<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PowerOption;

class PowerOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $powerOptions = [
            ['power_value' => '450', 'description' => '450 VA'],
            ['power_value' => '900', 'description' => '900 VA'],
            ['power_value' => '1300', 'description' => '1300 VA'],
            ['power_value' => '2200', 'description' => '2200 VA'],
            ['power_value' => '3500', 'description' => '3500 VA'],
            ['power_value' => '4400', 'description' => '4400 VA'],
            ['power_value' => '5500', 'description' => '5500 VA'],
        ];

        foreach ($powerOptions as $option) {
            PowerOption::create($option);
        }
    }
}