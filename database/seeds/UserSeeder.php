<?php

use App\User;
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
        $dataUserFakers =  factory(App\User::class, 1)->make()->toArray();

        $dataUserModels = array_map(function ($user) {
            $user['password'] = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
            $user['created_at'] = now();
            $user['updated_at'] = now();
            $user['email_verified_at'] = now();
            
            return $user;
        }, $dataUserFakers);

        User::insert($dataUserModels);
    }
}
