<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Consultorio;

class ConsultorioSeeder extends Seeder
{
    public function run()
    {
        Consultorio::create(['numCons' => '101']);
        Consultorio::create(['numCons' => '102']);
        Consultorio::create(['numCons' => '103']);
        Consultorio::create(['numCons' => '104']);
        Consultorio::create(['numCons' => '105']);
        Consultorio::create(['numCons' => '201']);
        Consultorio::create(['numCons' => '202']);
        Consultorio::create(['numCons' => '203']);
        Consultorio::create(['numCons' => '301']);
        Consultorio::create(['numCons' => 'Emergencia']);
    }
}   