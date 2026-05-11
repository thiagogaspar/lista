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
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@lista.site'],
            [
                'name' => 'Admin',
                'password' => '1234',
                'role' => \App\Models\User::ROLE_ADMIN,
            ]
        );

        if ($user->wasRecentlyCreated) {
            $this->info('Admin user created successfully.');
        } elseif ($user->role !== \App\Models\User::ROLE_ADMIN) {
            $user->update(['role' => \App\Models\User::ROLE_ADMIN]);
            $this->info('Admin user updated with admin role.');
        } else {
            $this->info('Admin user already exists with admin role.');
        }
    }
}
