<?php

namespace App\Facade;

use App\Models\User;

class PermissionService
{
    public static function isSuperLevel($user) {
        $super_levels = [User::$ADMIN, User::$MANAGER];
        return in_array($user->role->name, $super_levels);
    }

    public static function isInternalCoachLevel($user) {
        $super_levels = [User::$INTERNAL_STAFF, User::$ADMIN, User::$MANAGER];
        return in_array($user->role->name, $super_levels);
    }

    public static function isExternalCoachLevel($user) {
        $super_levels = [User::$EXTERNAL_STAFF, User::$INTERNAL_STAFF, User::$ADMIN, User::$MANAGER];
        return in_array($user->role->name, $super_levels);
    }
}
