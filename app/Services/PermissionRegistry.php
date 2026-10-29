<?php

namespace App\Services;

use App\Models\Permission;

class PermissionRegistry
{
    /**
     * Define system permissions mapped precisely from your web routes and modules.
     */
    public static function getDefinedPermissions(): array
    {
        return [
            'User Management' => [
                ['name' => 'users.index', 'display_name' => 'View Users List'],
                ['name' => 'users.create', 'display_name' => 'Create User Form'],
                ['name' => 'users.store', 'display_name' => 'Store New User'],
                ['name' => 'users.show', 'display_name' => 'View User Details'],
                ['name' => 'users.edit', 'display_name' => 'Edit User Form'],
                ['name' => 'users.update', 'display_name' => 'Update User Details'],
                ['name' => 'users.destroy', 'display_name' => 'Archive User'],
                ['name' => 'users.archives', 'display_name' => 'View Archived Users'],
                ['name' => 'users.restore', 'display_name' => 'Restore Archived User'],
                ['name' => 'users.force-delete', 'display_name' => 'Permanently Delete User'],
                ['name' => 'users.reset-password', 'display_name' => 'Send Temporary Password'],
            ],
            'Access Control' => [
                ['name' => 'roles.index', 'display_name' => 'View Roles List'],
                ['name' => 'roles.create', 'display_name' => 'Create Role Form'],
                ['name' => 'roles.store', 'display_name' => 'Store New Role'],
                ['name' => 'roles.edit', 'display_name' => 'Edit Role Form'],
                ['name' => 'roles.update', 'display_name' => 'Update Role Details'],
                ['name' => 'roles.destroy', 'display_name' => 'Delete Role'],
            ],
            'Quotations' => [
                ['name' => 'quotations.dashboard', 'display_name' => 'View Quotations Dashboard'],
                ['name' => 'quotations.index', 'display_name' => 'View Quotations List'],
                ['name' => 'quotations.create', 'display_name' => 'Create Quotation Form'],
                ['name' => 'quotations.store', 'display_name' => 'Store New Quotation'],
                ['name' => 'quotations.show', 'display_name' => 'View Quotation Details'],
                ['name' => 'quotations.edit', 'display_name' => 'Edit Quotation Form'],
                ['name' => 'quotations.update', 'display_name' => 'Update Quotation Details'],
                ['name' => 'quotations.destroy', 'display_name' => 'Archive/Delete Quotation'],
                ['name' => 'quotations.archives', 'display_name' => 'View Archived Quotations'],
                ['name' => 'quotations.restore', 'display_name' => 'Restore Quotation'],
                ['name' => 'quotations.force-delete', 'display_name' => 'Permanently Delete Quotation'],
                ['name' => 'quotations.pdf', 'display_name' => 'Generate Quotation PDF'],
                ['name' => 'quotations.send', 'display_name' => 'Send Quotation Email'],
            ],
            'Profile' => [
                ['name' => 'profile.edit', 'display_name' => 'Edit Own Profile'],
                ['name' => 'profile.update', 'display_name' => 'Update Own Profile'],
                ['name' => 'profile.password', 'display_name' => 'Update Own Password'],
            ],
        ];
    }

    /**
     * Syncs defined permissions into the database automatically.
     */
    public static function sync(): void
    {
        foreach (self::getDefinedPermissions() as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission['name']],
                    [
                        'display_name' => $permission['display_name'],
                        'group' => $group,
                    ]
                );
            }
        }
    }
}
