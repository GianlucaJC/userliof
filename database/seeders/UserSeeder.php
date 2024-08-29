<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = [

                'name'=>'admin',
                'email'=>'stefanoleonzi@liofilchem.net',
                'password'=> bcrypt('unplugged1992'),

            ];
        User::create($user);
    }
}
