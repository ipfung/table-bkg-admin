<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserTeammate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Store both new or update of student list of trainer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'user_id' => 'required|integer',
            'counter' => 'required|integer',
        ]);
        $user_id = $request->user_id;
        $teammates = $request->teammates;
        if ($request->counter == count($teammates)) {
            // delete non-existence teammate
            UserTeammate::where('user_id', $user_id)
                ->whereNotIn('teammate_id', $teammates)
                ->delete();
            // bulk insert
            $data = [];
            foreach ($teammates as &$value) {
                $data[] = ["user_id" => $user_id, "teammate_id" => $value, "created_by" => $user->id];
            }
            DB::table('user_teammates')->insertOrIgnore($data);

            //
            $result = ["success" => true];
        } else {
            $result = ["success" => false, "error" => "User teammate counter not match."];
        }
        return $result;
    }

    /**
     * Display the trainer resource with his/her student list.
     *
     * @param  int  $id the trainer id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $trainer = User::find($id);
        return $trainer->with('teammates');
    }


    /**
     * Display available student list.
     *
     * @param  int  $id the trainer id
     * @return \Illuminate\Http\Response
     */
    public function getNotMyStudents($id)
    {
        $students = User::orderBy('name', 'asc')
            ->where('status', 'active')
            ->whereIn('role_id', function($query){
                $query->select('id')
                    ->from(with(new Role)->getTable())
                    ->whereIn('name', ['member', 'user']); // FIXME should 'user' included here?
            })
            ->whereNotIn('id', function($query) use ($id) {
                $query->select('teammate_id')
                    ->from(with(new UserTeammate)->getTable())
                    ->where('user_id', $id);
            });
        return $students->paginate(request()->all());
    }
}
