<?php

namespace App\Support;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Role-based access control: the single list of permissions and default roles.
 *
 * Permission names are "<area>.<ability>", e.g. "packages.update".
 * Run `php artisan access:sync` (or the seeder) after changing this file.
 */
class Access
{
    public const SUPER_ADMIN = 'super-admin';

    public const CUSTOMER = 'customer';

    /** Roles that cannot be renamed or deleted. */
    public const PROTECTED_ROLES = [self::SUPER_ADMIN, self::CUSTOMER];

    /** area => [label, abilities] */
    public const AREAS = [
        'dashboard' => ['Dashboard booking figures', ['view']],
        'bookings' => ['Bookings', ['view', 'update', 'delete']],
        'inquiries' => ['Enquiries', ['view', 'update', 'delete']],
        'packages' => ['Tour packages', ['view', 'create', 'update', 'delete']],
        'tour_categories' => ['Tour types & menu', ['view', 'create', 'update', 'delete']],
        'destinations' => ['Destinations', ['view', 'create', 'update', 'delete']],
        'pages' => ['Pages', ['view', 'create', 'update', 'delete']],
        'testimonials' => ['Testimonials', ['view', 'create', 'update', 'delete']],
        'gallery' => ['Gallery', ['view', 'create', 'update', 'delete']],
        'page_content' => ['Page content (texts on website pages)', ['update']],
        'settings' => ['Site settings', ['update']],
        'users' => ['Users', ['view', 'create', 'update', 'delete']],
        'roles' => ['Roles & permissions', ['view', 'create', 'update', 'delete']],
    ];

    public const ABILITY_LABELS = [
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Edit',
        'delete' => 'Delete',
    ];

    /** Starting roles. Super admin is granted everything implicitly. */
    public const DEFAULT_ROLES = [
        'manager' => [
            'dashboard.*', 'bookings.*', 'inquiries.*', 'packages.*', 'tour_categories.*', 'destinations.*',
            'pages.*', 'testimonials.*', 'gallery.*', 'page_content.*', 'settings.*', 'users.view',
        ],
        'booking-staff' => [
            'bookings.view', 'bookings.update', 'inquiries.view', 'inquiries.update', 'packages.view', 'destinations.view',
        ],
        'content-editor' => [
            'packages.*', 'tour_categories.*', 'destinations.*', 'pages.*', 'testimonials.*', 'gallery.*', 'page_content.*',
        ],
    ];

    /** @return list<string> every permission name */
    public static function permissions(): array
    {
        $names = [];
        foreach (self::AREAS as $area => [, $abilities]) {
            foreach ($abilities as $ability) {
                $names[] = "{$area}.{$ability}";
            }
        }

        return $names;
    }

    /** Expand "orders.*" style patterns into concrete permission names. */
    public static function expand(array $patterns): array
    {
        return collect(self::permissions())
            ->filter(fn ($name) => collect($patterns)->contains(fn ($p) => $p === $name || (str_ends_with($p, '.*') && str_starts_with($name, substr($p, 0, -1)))))
            ->values()
            ->all();
    }

    /**
     * Create missing permissions and roles. Existing roles keep the permissions
     * an admin has given them; default permissions are only applied when a role
     * is first created.
     */
    public static function sync(): void
    {
        // Permissions that did not exist before this run (e.g. added by an update).
        $existing = Permission::where('guard_name', 'web')->pluck('name')->all();
        $new = array_values(array_diff(self::permissions(), $existing));

        foreach (self::permissions() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        Role::findOrCreate(self::SUPER_ADMIN, 'web');
        Role::findOrCreate(self::CUSTOMER, 'web');

        foreach (self::DEFAULT_ROLES as $name => $patterns) {
            $role = Role::where('name', $name)->where('guard_name', 'web')->first();

            if (! $role) {
                Role::create(['name' => $name, 'guard_name' => 'web'])->syncPermissions(self::expand($patterns));

                continue;
            }

            // An existing default role keeps whatever an admin has set, and only
            // gains brand-new permissions that its defaults include.
            if ($existing !== [] && $add = array_values(array_intersect($new, self::expand($patterns)))) {
                $role->givePermissionTo($add);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public static function roleLabel(string $name): string
    {
        return str($name)->replace('-', ' ')->title()->toString();
    }
}
