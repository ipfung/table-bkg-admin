<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    private function isSuperLevel($user) {
        $super_levels = ['manager', 'admin'];
        return in_array($user->role->name, $super_levels);
    }
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

        // only can see self if user is not in above level.
        if (!$this->isSuperLevel($user)) {
            $users->where('id', $user->id);
        } else {
            // TODO manager cannot see admin.
            if ($user->role->name == 'manager') {
                $users->whereRaw('role_id in (select id from roles where name<>?)', ['admin']);
            }
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
            return $users->with('role')->paginate(request()->all());
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
//            'mobile_no' => 'required|min:8',
            'password' => 'min:8|confirmed',
        ]);
        $user = new User($request->all());
        $user->save();

        return $user;
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
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  int  $id
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request, $id)
//    {
//        $request->validate([
//            'name' => 'required|max:255',
//            'surname' => 'required|max:255',
//            'level' => 'required|min:2|max:2',
////            'email' => 'required|email|max:255',
////            'mobile_no' => 'required|min:8',
//        ]);
//        $user = User::find($id);
//        // update user, we don't use fill here because avatar and roles shouldn't be updated.
//        $user->nickname = $request->nickname;
//        $user->name = $request->name;
//        $user->surname = $request->surname;
//        $user->chiname = $request->chiname;
//        $user->level = $request->level;
//        $user->sex = $request->sex;
//        $user->mobile_no = $request->mobile_no;
//        $user->tel1 = $request->tel1;
//        $user->status = $request->status;
//        if ($request->password && $request->password_confirmation)
//            $user->password = Hash::make($request->password);
//
//        $user->save();
//        $success = $user;
//
//        //return response()->json(['success' => $res]);
//        return $this->sendResponse($success, 'Updated successfully.');
//    }
//
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
}
