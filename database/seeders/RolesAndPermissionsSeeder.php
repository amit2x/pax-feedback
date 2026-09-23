<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'feedback.view',
            'feedback.update',
            'feedback.assign',
            'feedback.delete',
            'feedback.export',
            'feedback.attachments.download',
            'feedback.contact.view',

            'categories.view',
            'categories.manage',

            'subcategories.view',
            'subcategories.manage',

            'questions.view',
            'questions.manage',

            'locations.view',
            'locations.manage',

            'qr.view',
            'qr.manage',
            'qr.download',

            'departments.view',
            'departments.manage',

            'reports.view',
            'reports.export',

            'settings.view',
            'settings.manage',

            'audit.view',

            'users.view',
            'users.manage',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'Super Admin' => $permissions,

            'Feedback Administrator' => [
                'dashboard.view',
                'feedback.view', 'feedback.update', 'feedback.assign',
                'feedback.export', 'feedback.attachments.download', 'feedback.contact.view',
                'categories.view', 'categories.manage',
                'subcategories.view', 'subcategories.manage',
                'questions.view', 'questions.manage',
                'locations.view', 'locations.manage',
                'qr.view', 'qr.manage', 'qr.download',
                'departments.view', 'departments.manage',
                'reports.view', 'reports.export',
                'audit.view',
            ],

            'Airport Operations' => [
                'dashboard.view',
                'feedback.view', 'feedback.update', 'feedback.assign',
                'feedback.attachments.download', 'feedback.contact.view',
                'categories.view', 'subcategories.view',
                'locations.view',
                'qr.view',
                'departments.view',
                'reports.view',
            ],

            'Department Manager' => [
                'dashboard.view',
                'feedback.view', 'feedback.update',
                'feedback.attachments.download', 'feedback.contact.view',
                'categories.view', 'subcategories.view',
                'locations.view',
                'reports.view',
            ],

            'Viewer' => [
                'dashboard.view',
                'feedback.view',
                'categories.view', 'subcategories.view',
                'locations.view', 'qr.view',
                'departments.view',
                'reports.view',
            ],

            'Report Analyst' => [
                'dashboard.view',
                'feedback.view',
                'feedback.export',
                'reports.view', 'reports.export',
            ],
        ];

        foreach ($roles as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePerms);
        }
    }
}
