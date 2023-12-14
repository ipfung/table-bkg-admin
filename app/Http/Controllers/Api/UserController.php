<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use App\Models\CustomerBooking;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserTeammate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Models\Role;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
        $users = User::orderBy('name', 'asc')
            ->orderBy('id', 'desc');

        $editable = false;
        $newable = false;
        // only can see self if user is not in above level.
        if (!$this->isSuperLevel($user)) {
            if ($user->role->name == User::$INTERNAL_STAFF) {
                $users->whereRaw('role_id in (select id from roles where name in (?, ?, ?, ?))', [User::$INTERNAL_STAFF, User::$EXTERNAL_STAFF, User::$MEMBER, User::$USER]);
            } else {
                $users->where('id', $user->id);
            }
        } else {
            // TODO manager cannot see admin.
            if ($user->role->name == User::$MANAGER) {
                $users->whereRaw('role_id in (select id from roles where name<>?)', [User::$ADMIN]);
            }
            $editable = true;
            $newable = true;
        }

        if ($request->has('role')) {
            if ($request->role == 'User') {
//                $users->whereRaw('role_id in (select id from roles where name=?)', ['admin']);
            }
            if ($request->role == 'Trainer')
                $users->whereRaw('role_id in (select id from roles where name in (?, ?, ?))', [User::$INTERNAL_STAFF, User::$EXTERNAL_STAFF, User::$MANAGER]);
            if ($request->role == 'Student')
                $users->whereRaw('role_id in (select id from roles where name in (?, ?))', [User::$MEMBER, User::$USER]);
        }

        if ($request->has('q')) {
            if ($request->q != '')
                $users->whereRaw('(upper(name) LIKE upper(?) or upper(mobile_no) LIKE upper(?) or upper(email) LIKE upper(?) or upper(second_name) LIKE upper(?))', [$request->q . '%', $request->q . '%', $request->q . '%', $request->q . '%']);
        }

        if ($request->has('role_id')) {
            if ($request->role_id > 0)
                $users->where('role_id', $request->role_id);
        }

        if ($request->has('status')) {
            if ($request->status != '')
                $users->where('status', $request->status);
        }
        if ($request->has('id')) {
            if ($request->id != '')
                $users->where('id', $request->id);
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $users->whereRaw('(upper(name) LIKE upper(?) or upper(second_name) LIKE upper(?))', [$request->name . '%', $request->name . '%']);
        }
        if ($request->has('second_name')) {
            if ($request->second_name != '')
                $users->whereRaw('(upper(second_name) LIKE upper(?))', [$request->second_name . '%']);
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
            $data['newable'] = $newable;
            $data['student_qr'] = config('app.jws.settings.student_qr');
            $data['multi_student'] = config('app.jws.settings.trainer_multiple_student');
            $data['requiredRoom'] = config("app.jws.settings.required_room");
            $data['requiredTrainer'] = config("app.jws.settings.required_trainer");
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
            'mobile_no' => 'required|max:8|unique:users',
            'password' => 'required|min:8',
        ]);
        if (config("app.jws.settings.required_trainer")) {
            $request->validate([
                'settings.trainer' => 'required',    // trainer id.
            ]);
        }
        if (config("app.jws.settings.required_room")) {
            $request->validate([
                'settings.room' => 'required',    // room id.
            ]);
        }
        // license checking.
//        $license_checker = $this->checkLicense($request->role_id);

        $data = new User($request->all());
        $data->password = Hash::make($request->password);
        $settings = $request->input('settings');
        $data->settings = json_encode($settings);

        DB::beginTransaction();
        $data->save();
        $saveTrainer = false;
        if (!config('app.jws.settings.trainer_multiple_student') && $settings && isset($settings['trainer'])) {
            // add to user_teammates
            $userTeammate = new UserTeammate;
            $userTeammate->user_id = $settings['trainer'];
            $userTeammate->teammate_id = $data->id;
            $userTeammate->created_by = $data->id;
            $userTeammate->save();
            $saveTrainer = true;
        }
        DB::commit();
        $success = true;
        return compact('success', 'data', 'saveTrainer');
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
            'mobile_no' => 'required|max:8',
            'password' => 'min:8',
        ]);
        $user = User::find($id);
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
        $settings = $request->input('settings');
        $user->settings = json_encode($settings);
        DB::beginTransaction();
//        $settings = $this->getReqSettings($request);
        $saveTrainer = false;
        if (!config('app.jws.settings.trainer_multiple_student') && $settings && isset($settings['trainer'])) {
            // update user_teammates
            $userTeammate = UserTeammate::where('teammate_id', $id)->first();
            if (empty($userTeammate)) {
                $userTeammate = new UserTeammate;
                $userTeammate->teammate_id = $id;
                $userTeammate->created_by = $user->id;
            }
            $userTeammate->user_id = $settings['trainer'];
            $userTeammate->save();
            $saveTrainer = true;
        }
        $user->save();
        DB::commit();
        $success = true;

        return compact('success', 'saveTrainer');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = CustomerBooking::where('customer_id', $id)->first();
        if (empty($order)) {
            $appointment = Appointment::where('user_id', $id)->first();
            if (empty($appointment)) {
                User::where('id', $id)->delete();
                return response()->json(['success'=>true]);
            } else {
                return response()->json(['success' => false, 'error' => 'User cannot be deleted because found user in Appointment.']);
            }
        } else {
            return response()->json(['success'=>false, 'error' => 'User cannot be deleted because found user in Booking.']);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getUserProfile(Request $request) {
//    return $request->user();
        $user = $request->user();
        $role = Role::find($user->role_id);
        $service_name = '';
        $room_name = '';
        $trainer_name = '';
        $settings = false;
        if ($user->service_id > 0) {
            $service = Service::find($user->service_id);
            $service_name = $service->name;
        }
        if ($user->settings) {
            $settings = json_decode($user->settings);

            if (!empty($settings->room)) {
                $room = Room::find($settings->room);
                $room_name = $room->name;
            }
            if (!empty($settings->trainer)) {
                $trainer = User::find($settings->trainer);
                $trainer_name = $trainer->name;
            }
        }
        return [
            'id' => $user->id,
            'mobile_no' => $user->mobile_no,
            'role_name' => $role->name,
            'role_color_name' => $role->color_name,
            'room_name' => $room_name,
            'second_name' => $user->second_name,
            'service_name' => $service_name,
            'trainer_name' => $trainer_name,
            'notifications' => $settings && !empty($settings->notifications) ? $settings->notifications : [
            ],
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePwd(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);
        $user = User::find($id);
        if (Hash::check($request->old_password, $user->password)) {
            // update user password.
            $user->password = Hash::make($request->password);
            $user->save();

            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Old password not correct'];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeNotifications(Request $request, $id)
    {
        $request->validate([
            'notifications' => 'required',
        ]);
        $user = User::find($id);
        $settings = json_decode($user->settings);
        $settings->notifications = $request->input('notifications');
        $user->settings = json_encode($settings);
        $user->save();
        return ['success' => true];
    }

    /**
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function generatStudentQr($id) {
        $user = User::find($id);
        $separator = '::';
        $str = base64_encode($user->id . $separator . $user->created_at . $separator . $user->email . $separator . $user->mobile_no);
        $result['content'] = base64_encode(QrCode::size(200)->generate($str));
        $result['format'] = 'svg';
        return $this->sendResponse($result, "Student QR code generated.");
    }

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
        } else if ($device->status != 'approved') {
            // update back to approved.
            $device->status = 'approved';
            $device->save();
        }
        return ['success' => true, 'id' => $device->id];
    }
}
