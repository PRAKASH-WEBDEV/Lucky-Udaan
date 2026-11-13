<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /** Default logins — change these passwords immediately after the first sign-in. */
    public const PASSWORD = 'LuckyUdaan@2026';

    public function run(): void
    {
        $super = Role::updateOrCreate(['slug' => 'super-admin'], ['name' => 'Super Admin', 'is_super' => true, 'permissions' => []]);
        $editor = Role::updateOrCreate(['slug' => 'editor'], ['name' => 'Editor', 'is_super' => false, 'permissions' => [
            'content', 'banners', 'programs', 'campaigns', 'posts', 'events', 'gallery', 'team', 'volunteers', 'members', 'enquiries',
        ]]);
        $accountant = Role::updateOrCreate(['slug' => 'accountant'], ['name' => 'Accountant', 'is_super' => false, 'permissions' => [
            'donations', 'donors', 'campaigns', 'analytics',
        ]]);

        foreach ([
            ['Pawan Kumar', 'admin@luckyudaanfoundation.com', $super],
            ['Content Editor', 'editor@luckyudaanfoundation.com', $editor],
            ['Accounts', 'accounts@luckyudaanfoundation.com', $accountant],
        ] as [$name, $email, $role]) {
            Admin::updateOrCreate(['email' => $email], [
                'name' => $name,
                'role_id' => $role->id,
                'password' => self::PASSWORD,
                'is_active' => true,
            ]);
        }
    }
}
