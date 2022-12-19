<?php

namespace App\Http\Controllers\Api;

use App\Facade\LicenseService;
use App\Models\User;
use App\Models\UserTeammate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $editable = false;
        $newable = false;
        // trainer himself can see self only.
        if ($this->isSuperLevel($user)) {
            $editable = true;
            $newable = true;
        } else if ($this->isExternalCoachLevel($user)) {
            $editable = true;
            $trainers->where('id', $user->id);
        } else {
            $trainers->where('id', $user->id);
        }

        if ($request->has('role_id')) {
            if ($request->role_id > 0)
                $trainers->where('role_id', $request->role_id);
        }

        if ($request->has('status')) {
            if ($request->status != '')
                $trainers->where('status', $request->status);
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $trainers->whereRaw('(upper(name) LIKE upper(?) or upper(second_name) LIKE upper(?))', [$request->name . '%', $request->name . '%']);
        }
        if ($request->has('second_name')) {
            if ($request->second_name != '')
                $trainers->whereRaw('(upper(second_name) LIKE upper(?))', [$request->second_name . '%']);
        }
        if ($request->has('email')) {
            if ($request->email != '')
                $trainers->where('email', 'LIKE', $request->email . '%');
        }
        if ($request->has('mobile_no')) {
            if ($request->mobile_no != '')
                $trainers->where('mobile_no', 'LIKE', $request->mobile_no . '%');
        }

        if ($request->expectsJson()) {
            $data = $trainers->with('role')->with('teammates.role')->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            $data['newable'] = $newable;
            $data['timeslotSetting'] = config('app.jws.settings.timeslots');
            $data['multi_student'] = config('app.jws.settings.trainer_multiple_student');
            return $data;
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
        // validate
        $request->validate([
            'name' => 'required|max:255',    // first name
            'email' => 'required|max:255|unique:users',   //|email
            'role_id' => 'required|exists:roles,id',   //roles
            'mobile_no' => 'required',
            'password' => 'required|min:8',
        ]);
        // license checking.
        $license_checker = new LicenseService;
        $license_res = $license_checker->checkLicense($request->role_id);
        if ($license_res['success'] == false) {
            return $license_res;
        }

        $data = new User($request->all());
        $data->password = Hash::make($request->password);
        $settings = $request->input('settings');
        $data->settings = json_encode($settings);

        DB::beginTransaction();
        $data->save();
        if (config('app.jws.settings.trainer_multiple_student') && $request->has('counter') && $request->counter > 0) {
            $user_id = $data->id;   // use generated id.
            $teammates = $request->teammates;
            if ($request->counter == count($teammates)) {
                // bulk insert
                $data2 = [];
                foreach ($teammates as &$value) {
                    $data2[] = ["user_id" => $user_id, "teammate_id" => $value, "created_by" => $data->id];
                }
                DB::table('user_teammates')->insertOrIgnore($data2);
            } else {
                DB::rollBack();
                return ["success" => false, "error" => "User teammate counter not match."];
            }
        }
        DB::commit();
        $success = true;
        return compact('success', 'data');
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',    // first name
            'role_id' => 'required|exists:roles,id',   //roles
            'password' => 'min:8',
        ]);
        $user = User::find($id);
        if ($request->role_id != $user->role_id) {
            // license checking.
            $license_checker = new LicenseService;
            $license_res = $license_checker->checkLicense($request->role_id, $id);
            if ($license_res['success'] == false) {
                return $license_res;
            }
        }
        // update user, we don't use fill here because avatar and roles shouldn't be updated.
        $user->mobile_no = $request->mobile_no;
        $user->name = $request->name;
        $user->second_name = $request->second_name;
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        if ($request->has('password')) {
            if ($request->password != '') {
                $user->password = Hash::make($request->password);
            }
        }
        DB::beginTransaction();
//        $settings = $this->getReqSettings($request);
        $settings = $request->input('settings');
        $user->settings = json_encode($settings);

        if (config('app.jws.settings.trainer_multiple_student')) {
            $teammates = $request->teammates;
            if ($request->counter == count($teammates)) {
                // delete non-existence teammate
                UserTeammate::where('user_id', $id)
                    ->whereNotIn('teammate_id', $teammates)
                    ->delete();
                // bulk insert
                $data2 = [];
                foreach ($teammates as &$value) {
                    $data2[] = ["user_id" => $id, "teammate_id" => $value, "created_by" => $user->id];
                }
                DB::table('user_teammates')->insertOrIgnore($data2);
            } else {
                DB::rollBack();
                return ["success" => false, "error" => "User teammate counter not match."];
            }
        }

        $user->save();
        DB::commit();
        $success = true;

        return compact('success');
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
        return $students->paginate();
    }
}
