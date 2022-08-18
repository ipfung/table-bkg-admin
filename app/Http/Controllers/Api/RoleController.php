<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $roles = Role::orderBy('name', 'asc');
        if ($this->isSuperLevel($user)) {
            $roles->where('name', '<>', 'admin');
        } else {
            $roles->where('name', $user->role->name);
        }

        // always return JSON type for calendar.
        return $roles->paginate();
    }

}
