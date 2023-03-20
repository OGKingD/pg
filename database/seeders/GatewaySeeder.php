<?php

namespace Database\Seeders;

use App\Models\Gateway;
use Illuminate\Database\Seeder;

class GatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Gateway::insert([
            ['name' => "Card", 'status' => 0],
            ['name' => "Bank Transfer", 'status' => 0],
            ['name' => "Remita", 'status' => 0],
            ['name' => "Googlepay", 'status' => 0],
            ['name' => "Applepay", 'status' => 0],
            ['name' => "CashAtBank", 'status' => 0],
        ]);
        //
    }
}
