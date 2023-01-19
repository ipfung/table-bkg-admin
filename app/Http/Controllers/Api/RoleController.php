<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Role;

/**
 * The 'name' field of roles table.
 * 1. user - Make appointments, View appointments, Cancel appointments.
 * 2. member - All 'user' can do.
 * 3. external_coach - All 'member' can do. Plus can see SELF with students appointments only, can create appointment with desire "room". can set SELF working hours.
 * 4. internal_coach - All 'external_coach' can do. Plus can see external coach with student's appointments,
 * 5. manager - ALL permission
 * :: Below users have "no Voyage access" ::
 * 6. admin - ALL permission including Voyager.
 */
class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        DB::enableQueryLog(); // Enable query log
        $roles = Role::orderByRaw('FIELD(name, "user", "member", "external_coach", "internal_coach", "manager")');
        if ($this->isSuperLevel($user)) {
            $roles->whereIn('name', [User::$MANAGER, User::$INTERNAL_STAFF, User::$EXTERNAL_STAFF, User::$MEMBER, User::$USER]);
        } else {
            $roles->where('name', $user->role->name);
        }

        // always return JSON type for calendar.
        return $roles->paginate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRoles(Request $request)
    {
        DB::enableQueryLog(); // Enable query log
        $roles = Role::orderByRaw('FIELD(name, "user", "member", "external_coach", "internal_coach", "manager")');
        if ($request->type == 'coach')
            $roles->whereIn('name', [User::$MANAGER, User::$INTERNAL_STAFF, User::$EXTERNAL_STAFF]);
        else {
            $roles->whereIn('name', [User::$MEMBER, User::$USER]);
        }

        // always return JSON type for calendar.
        return $roles->paginate();
    }
}
