<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Role;

class TrainerController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        //
        $trainers = User::orderBy('id', 'desc')
            ->whereIn('role_id', function($query){   // ref: https://stackoverflow.com/questions/16815551/how-to-do-this-in-laravel-subquery-where-in
                $query->select('id')
                    ->from(with(new Role)->getTable())
                    ->whereIn('name', ['manager', 'internal_coach', 'external_coach']);// who will be trainers.
            });

        // trainer himself can see self only.
        if (!$this->isSuperLevel($user)) {
            $trainers->where('id', $user->id);
        }

        if ($request->expectsJson()) {
            return $trainers->with('role')->with('teammates')->paginate(request()->all());
        }
        return view("trainers.list", $trainers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }
}
