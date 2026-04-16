<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        if (\App\Models\Student::count() === 0) {
            \App\Models\Student::factory(50)->create();
        }
    }
}
