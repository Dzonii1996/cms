<?php


namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    public function run()
    {


        $user = new User();
       $user ->name = 'Nikola Vuckovic';
        $user->email = 'nikola@gmail.com';
        $user->password = bcrypt('secret');
        $user->save();





    }
}
