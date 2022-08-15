<?php

namespace App\Http\Controllers\Api;

use App\Models\Location;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocationController extends BaseController
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
        $locations = Location::orderBy('name', 'asc')
            ->where('company_id', 1);    // FIXME dynamic company_id.

        if ($request->has('status')) {
            if ($request->status > 0)
                $locations->where('status', $request->status);
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $locations->whereRaw('upper(name) LIKE upper(?)', [$request->name]);
        }

        if ($request->expectsJson()) {
            return $locations->paginate();
        }
        return view("locations.list", $locations);
    }
//Below not allow user to manage until unlimited license.
//    /**
//     * Store a newly created resource in storage.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @return \Illuminate\Http\Response
//     */
//    public function store(Request $request)
//    {
//        // validate
//        $request->validate([
//            'name' => 'required|max:255',    // first name
//            'status' => 'required'
//        ]);
//        $location = Location::create($request->all());
//        return $location;
//    }
//
//    /**
//     * Display the specified resource.
//     *
//     * @param  int  $id
//     * @return \Illuminate\Http\Response
//     */
//    public function show($id)
//    {
//        //
//        $location = Location::find($id);
//        return $location;
//    }
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
//            'name' => 'required|max:255',    // first name
//            'email' => 'required|max:255|unique:users',   //|email
//            'role_id' => 'required|exists:roles',   //roles
//        ]);
//        $location = Location::find($id);
//        // update user, we don't use fill here because avatar and roles shouldn't be updated.
//        $location->name = $request->name;
//        $location->level = $request->level;
//        $location->status = $request->status;
//        if ($request->password && $request->password_confirmation)
//            $location->password = Hash::make($request->password);
//
//        $location->save();
//        $success = $location;
//
//        //return response()->json(['success' => $res]);
//        return $this->sendResponse($success, 'Updated successfully.');
//    }

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

}
