<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
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
        DB::enableQueryLog(); // Enable query log
        $users = User::orderBy('id', 'desc');

        $editable = false;
        // only can see self if user is not in above level.
        if (!$this->isSuperLevel($user)) {
            if ($user->role->name == 'internal_coach') {
                $users->whereRaw('role_id in (select id from roles where name in (?, ?, ?, ?))', ['internal_coach', 'external_coach', 'member', 'user']);
            } else {
                $users->where('id', $user->id);
            }
        } else {
            // TODO manager cannot see admin.
            if ($user->role->name == 'manager') {
                $users->whereRaw('role_id in (select id from roles where name<>?)', ['admin']);
            }
            $editable = true;
        }

        if ($request->has('role')) {
            if ($request->role == 'User') {
//                $users->whereRaw('role_id in (select id from roles where name=?)', ['admin']);
            }
            if ($request->role == 'Trainer')
                $users->whereRaw('role_id in (select id from roles where name in (?, ?))', ['internal_coach', 'external_coach']);
            if ($request->role == 'Student')
                $users->whereRaw('role_id in (select id from roles where name in (?, ?))', ['member', 'user']);
        }

        if ($request->has('status')) {
            if ($request->status > 0)
                $users->where('status', $request->status);
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $users->whereRaw('(upper(name) LIKE upper(?) or upper(mobile_no) = upper(?))', [$request->name . '%', $request->name]);
        }
        if ($request->has('email')) {
            if ($request->email != '')
                $users->where('email', 'LIKE', $request->email . '%');
        }
        if ($request->has('mobile_no')) {
            if ($request->mobile_no != '')
                $users->where('mobile_no', 'LIKE', $request->mobile_no . '%');
        }
//      $users->get()    // debug
//        ;
//        $aaa = DB::getQueryLog(); // debug, Show results of log
//        $results = end($aaa);    // debug
//        return $this->sendResponse($results, "ok");    // debug
        if ($request->expectsJson()) {
            $data = $users->with('role')->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            return $data;
        }
        return view("users.list", $users);
    }

    /**
     * Store a newly created resource in storage.
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
//            'mobile_no' => 'required|min:8',
            'password' => 'required|min:8',
        ]);
        $data = new User($request->all());
        $data->password = Hash::make($request->password);
        $data->save();
        $success = true;
        return compact('success', 'data');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::find($id);
        return $user;
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
        // update user, we don't use fill here because avatar and roles shouldn't be updated.
        $user->name = $request->name;
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        if ($request->has('password')) {
            if ($request->password != '') {
                $user->password = Hash::make($request->password);
            }
        }
        $user->save();
        $success = true;

        return compact('success');
    }

//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param  int  $id
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy($id)
//    {
//        $order = Order::where('student_id','=',$id)->first();
//        if (empty($order)) {
//            User::where('id', $id)->delete();
//
//            return response()->json(['success'=>true]);
//        } else {
//            return response()->json(['success'=>false, 'message' => 'User cannot be deleted because it is used in Order.']);
//        }
//    }
//
//    public function generatStudentQr($id) {
//        $user = Student::with('info')->find($id);
//        $str = $user->id . '::' . $user->info->card_no;
//        $result['content'] = base64_encode(QrCode::size(200)->generate($str));
//        $result['format'] = 'svg';
//        return $this->sendResponse($result, "Student QR code generated.");
//    }

    /**
     * get total number of new student for today/this week/this month.
     * @param Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    public function banUser(Request $request, $id)
    {
        $user = Auth::user();

        if ($this->isSuperLevel($user)) {
            $banUser = User::find($id);
            $banUser->status = 'banned';
            $banUser->save();
            $results = ['success' => true, 'status' => $banUser->status];
        } else {
            $results = ['success' => false, 'error' => 'You don\'t have permission to ban user.', 'params' => $user->role->name];
        }

        if ($request->expectsJson()) {
            return $results;
        }
    }

    /**
     * get total number of new student for today/this week/this month.
     * @param Request $request
     * @return \Illuminate\Database\Query\Builder
     */
    public function activeUser(Request $request, $id)
    {
        $user = Auth::user();

        if ($this->isSuperLevel($user)) {
            $activeUser = User::find($id);
            $activeUser->status = 'active';
            $activeUser->save();
            $results = ['success' => true, 'status' => $activeUser->status];
        } else {
            $results = ['success' => false, 'error' => 'You don\'t have permission to active user.', 'params' => $user->role->name];
        }

        if ($request->expectsJson()) {
            return $results;
        }
    }

    /**
     * @param Request $request the push registration key.
     * @return void
     */
    public function registerPush(Request $request) {
        $request->validate([
            'reg_id' => 'required',
        ]);

        $user = Auth::user();
        $device = UserDevice::where('user_id', $user->id)
            ->where('reg_id', $request->reg_id)
            ->first();
        if (empty($device)) {
            $device = new UserDevice($request->all());
            $device->user_id = $user->id;
            $device->status = 'approved';
            $device->save();
        } else if ($device->status == 'canceled') {
            // update back to approved.
            $device->status = 'approved';
            $device->save();
        }
        return ['success' => true];
    }
}
