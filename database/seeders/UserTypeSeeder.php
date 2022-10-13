<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_types')->insert([
            ['name' => "SuperAdmin", "created_at" =>now(), "updated_at" => now() ],
            ['name' => "Admin", "created_at" =>now(), "updated_at" => now() ],
            ['name' => "Finance", "created_at" =>now(), "updated_at" => now() ],
            ['name' => "Support", "created_at" =>now(), "updated_at" => now() ],
            ['name' => "Merchant", "created_at" =>now(), "updated_at" => now() ],
        ]);
        //
        /** @var User $admin */
        $admin = User::create([
            "type" => 1,
            "first_name" => "David",
            "last_name" => "OG",
            "email" => "dogunejimite@saanapay.ng",
            "password" => bcrypt('1234')
        ]);
        /** @var User $company */
        $company = User::create([
            "type" => 1,
            "first_name" => "Saanapay",
            "last_name" => "",
            "email" => "business@saanapay.ng",
            "password" => bcrypt('1234')
        ]);
        $company->addWallet();

        event(new Registered($admin));
        event(new Registered($company));


    }
}
