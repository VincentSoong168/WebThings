<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'			=>	'user',
            'email'			=>	'vincent.soong168@gmail.com',
            'password'		=>	Hash::make('123456'),
            'created_at'	=>	date("Y-m-d H:i:s"),
            'updated_at'	=>	date("Y-m-d H:i:s"),
        ]);
    }
}
