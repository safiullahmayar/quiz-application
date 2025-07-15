<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $admin=[
            'name'=>'Admin',
            'email'=>'admin@gmail.com',
            'password'=>bcrypt('password'),
            'is_admin'=>1, // Assuming this is to indicate that this user is an admin
        ];

        Admin::create($admin);
    }
}
