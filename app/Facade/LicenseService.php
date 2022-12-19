<?php

namespace App\Facade;

use App\Models\User;
use TCG\Voyager\Models\Role;

class LicenseService
{

    private function getRole($roleId) {
        return Role::find($roleId);
    }

    public function checkLicense($roleId, $bypassUserId = 0) {
        $role = $this->getRole($roleId);
        $name = $role->name;
        $roles = [];
        $role_name = '';
        if ($name == 'internal_coach' || $name == 'external_coach') {
            $roles = ['internal_coach', 'external_coach'];
            $role_name = 'tutor';
        } else if ($name == 'member' || $name == 'user') {
            $roles = ['member', 'user'];
            $role_name = 'student';
        } else if ($name == 'manager') {
            $roles = ['manager'];
            $role_name = 'manager';
        } else if ($name == 'admin') {
        }
        $user = User::whereIn('role_id', function($query) use ($roles) {   // ref: https://stackoverflow.com/questions/16815551/how-to-do-this-in-laravel-subquery-where-in
            $query->select('id')
                ->from(with(new Role)->getTable())
                ->whereIn('name', $roles);
        });
        if ($bypassUserId > 0) {
            $user->where('id', '<>', $bypassUserId);
        }
        $counter = $user->count();
        $success = false;
        $max_license = config('app.jws.license.' . $role_name);
        if ($max_license == -1)     // -1 means unlimited license.
            $success = true;
        else if ($max_license > $counter) {
            $success = true;
        }
        if (!$success) {
            return [
                'success' => false,
                'role' => $role_name,
                'error' => 'Reached maximum license counter.',
                'params' => [
                    'max' => $max_license,
                    'used' => $counter,
                ]
            ];
        }
        return [
            'success' => true,
            'role' => $role_name
        ];
        //compact($success, $max_license, $counter, $role_name);
    }
}
