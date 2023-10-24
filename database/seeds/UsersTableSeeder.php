<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
            'name' => 'Kizuner',
            'email' => 'admin@admin.com',
            'phone' => '01999999892',
            'admin' => true,
            'password' => Hash::make('admin123'),
        ]);

//        App\User::create([
//            'name' => 'Dang',
//            'email' => 'tung.dang@inapps.net',
//            'phone' => '01999999893',
//            'password' => Hash::make('admin123'),
//        ]);
//
//        App\User::create([
//            'name' => 'Bui',
//            'email' => 'khanh.bui@inapps.net',
//            'phone' => '01999999894',
//            'password' => Hash::make('admin123'),
//        ]);
//
//        App\User::create([
//            'name' => 'Tran',
//            'email' => 'thang.tran@inapps.net',
//            'phone' => '01999999895',
//            'password' => Hash::make('admin123'),
//        ]);
    }
}
