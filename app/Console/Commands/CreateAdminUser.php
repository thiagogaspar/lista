<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:create-admin-user')]
#[Description('Create admin user if not exists')]
class CreateAdminUser extends Command
{
    public function handle()
    {
        $email = 'admin@lista.site';
        $user = \App\Models\User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => bcrypt('1234'),
                'role' => 'admin',
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->info('Admin user created successfully.');
        } else {
            $this->info('Admin user already exists.');
        }
    }
}
