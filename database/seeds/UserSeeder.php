<?php

use Vanguard\Role;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
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
        $user = User::create([
            'first_name' => 'ApplicationAdmin',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => 'admin123',
            'vendor_id'=>'0',
            'avatar' => null,
            'country_id' => null,
            'status' => UserStatus::ACTIVE
        ]);

        $admin = Role::where('name', 'Admin')->first();

        $user->attachRole($admin);
        $user->socialNetworks()->create([]);
    }
}
