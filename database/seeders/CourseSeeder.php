<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourseSeeder extends Seeder
{

    public function run(): void
    {
        Course::create([
            'name' => 'Calculo',
            'hour' => '80',
        ]);
        Course::create([
            'name' => 'Ingles',
            'hour' => '50',
        ]);
        Course::create([
            'name' => 'Quimica',
            'hour' => '30',
        ]);
    }
}
