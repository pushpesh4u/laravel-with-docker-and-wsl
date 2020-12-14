<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $providers = [
            [
                'name' => 'Google',
                'status' => 1
            ],[
                'name' => 'Snapchat',
                'status' => 1
            ]
        ];
        \DB::table('providers')->insert($providers);
    }
}
