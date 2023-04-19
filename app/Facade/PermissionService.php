<?php

namespace App\Facade;

use App\Models\User;

class PermissionService
{
    public function isSuperLevel($user) {
        $super_levels = [User::$ADMIN, User::$MANAGER];
        return in_array($user->role->name, $super_levels);
    }

    public function isInternalCoachLevel($user) {
        $super_levels = [User::$INTERNAL_STAFF, User::$ADMIN, User::$MANAGER];
        return in_array($user->role->name, $super_levels);
    }

    public function isExternalCoachLevel($user) {
        $super_levels = [User::$EXTERNAL_STAFF, User::$INTERNAL_STAFF, User::$ADMIN, User::$MANAGER];
        return in_array($user->role->name, $super_levels);
    }
}
