<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Role;

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
        $roles = Role::orderByRaw('FIELD(name, "manager", "internal_coach", "external_coach", "member", "user")');
        if ($this->isSuperLevel($user)) {
            $roles->whereIn('name', ['manager', 'internal_coach', 'external_coach', 'member', 'user']);
        } else {
            $roles->where('name', $user->role->name);
        }

        // always return JSON type for calendar.
        return $roles->paginate();
    }

}
