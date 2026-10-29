<?php

namespace Database\Seeders;

use App\Services\PermissionRegistry;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sync permissions dynamically from your PermissionRegistry service
        PermissionRegistry::sync();

        // 2. Automatically grant ALL permissions to the 'admin' role so you never get locked out
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $allPermissionIds = \App\Models\Permission::pluck('id')->toArray();
            $adminRole->permissions()->sync($allPermissionIds);
        }
    }
}
