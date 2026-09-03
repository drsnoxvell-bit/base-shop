<?php

namespace Database\Seeders;

use App\Support\ShopPermissions;
use Illuminate\Database\Seeder;
use Orchid\Platform\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ShopPermissions::ROLE_ADMINISTRATOR => [
                'name' => 'Администратор',
                'permissions' => ShopPermissions::administrator(),
            ],
            ShopPermissions::ROLE_EDITOR => [
                'name' => 'Редактор',
                'permissions' => ShopPermissions::editor(),
            ],
            ShopPermissions::ROLE_USER => [
                'name' => 'Пользователь',
                'permissions' => ShopPermissions::user(),
            ],
        ];

        foreach ($roles as $slug => $data) {
            Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'permissions' => $data['permissions'],
                ]
            );
        }
    }
}
