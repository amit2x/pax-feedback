<?php

namespace App\Support;

class PermissionGroups
{
    /**
     * Master list of all permissions grouped by domain.
     * Used by:
     *   - RolesAndPermissionsSeeder (to create them)
     *   - Role create/edit forms (to render checkbox grid)
     *   - RoleController (to validate submitted permissions)
     *
     * @return array<string, array<string>> group label => permission names
     */
    public static function all(): array
    {
        return [
            'Dashboard' => [
                'dashboard.view',
            ],

            'Feedback' => [
                'feedback.view',
                'feedback.update',
                'feedback.assign',
                'feedback.delete',
                'feedback.export',
                'feedback.attachments.download',
                'feedback.contact.view',
            ],

            'Categories' => [
                'categories.view',
                'categories.manage',
            ],

            'Locations' => [
                'locations.view',
                'locations.manage',
            ],

            'QR Codes' => [
                'qr.view',
                'qr.manage',
                'qr.download',
            ],

            'Departments' => [
                'departments.view',
                'departments.manage',
            ],

            'Reports' => [
                'reports.view',
                'reports.export',
            ],

            'Audit Logs' => [
                'audit.view',
            ],

            'Users' => [
                'users.view',
                'users.manage',
            ],

            'Roles' => [
                'roles.view',
                'roles.manage',
            ],

            'Settings' => [
                'settings.view',
                'settings.manage',
            ],
        ];
    }

    /**
     * @return array<string> Flat list of every permission name.
     */
    public static function flat(): array
    {
        return array_merge(...array_values(self::all()));
    }

    /**
     * Given a permission name, return the group it belongs to.
     */
    public static function groupFor(string $permission): ?string
    {
        foreach (self::all() as $group => $permissions) {
            if (in_array($permission, $permissions, true)) {
                return $group;
            }
        }

        return null;
    }
}
