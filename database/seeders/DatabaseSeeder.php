<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin      = Role::firstOrCreate(['name' => 'admin']);
        $sales      = Role::firstOrCreate(['name' => 'sales']);
        $content    = Role::firstOrCreate(['name' => 'content']);
        $products   = Role::firstOrCreate(['name' => 'products']);

        // Permissions
        $permissions = [
            // products
            'products.view', 'products.create', 'products.edit', 'products.delete', 'products.import',
            // quotes
            'quotes.view', 'quotes.edit', 'quotes.delete', 'quotes.respond', 'quotes.assign',
            // contact
            'contact.view', 'contact.reply', 'contact.delete',
            // content
            'content.blog', 'content.pages', 'content.menus', 'content.sections', 'content.banners',
            // settings
            'settings.view', 'settings.edit',
            // users
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // media
            'media.view', 'media.upload', 'media.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Assign permissions to roles
        $admin->syncPermissions(array_filter($permissions, fn($p) => !str_starts_with($p, 'users.')));

        $sales->syncPermissions([
            'quotes.view', 'quotes.edit', 'quotes.respond', 'quotes.assign',
            'contact.view', 'contact.reply',
            'products.view',
        ]);

        $content->syncPermissions([
            'content.blog', 'content.pages', 'content.menus', 'content.sections', 'content.banners',
            'media.view', 'media.upload', 'media.delete',
            'products.view',
        ]);

        $products->syncPermissions([
            'products.view', 'products.create', 'products.edit', 'products.delete', 'products.import',
            'media.view', 'media.upload', 'media.delete',
        ]);

        // Super Admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@pcfy.in'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('Admin@pcfy2026'),
            ]
        );
        $user->assignRole($superAdmin);

        $this->command->info('✓ Roles, permissions and Super Admin seeded.');
        $this->command->info('  Email: admin@pcfy.in');
        $this->command->info('  Password: Admin@pcfy2026');

        $this->call(Phase5ContentSeeder::class);
        $this->call(Phase6SettingsSeeder::class);
    }
}
