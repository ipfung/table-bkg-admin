<?php

namespace App\Http\Controllers\Api;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomController extends BaseController
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
        $rooms = Room::orderBy('name', 'asc')
            ->select('rooms.*')
            ->selectRaw("(select id from appointments where room_id=rooms.id and status not in ('canceled', 'rejected') and ? between start_time and end_time) as appointment_id", [($this->getCurrentDateTime())->format('Y-m-d H:i:s')]);

        $editable = false;
        // this module is only for manager.
        if (!$this->isSuperLevel($user)) {
            $rooms->where('status', 1001);   // see active rooms only.
        } else {
            $editable = true;
        }
        if ($request->has('status')) {
            if ($request->status > 0)
                $rooms->where('status', $request->status);
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $rooms->whereRaw('upper(name) LIKE upper(?)', [$request->name]);
        }

        if ($request->expectsJson()) {
            $data = $rooms->with('location')->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            return $data;
        }
        return view("rooms.list", $rooms);
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
            'name' => 'required|max:255',    // room name
            'location_id' => 'required'
        ]);
        $room = new Room($request->all());
        $colors = ["1788FB","FBC22D","FA3C52","D696B8","689BCA","26CC2B","4BBEC6","FD7E35","E38587","774DFB","31CDF3","6AB76C","FD5FA1","A697C5"];
        shuffle($colors);
        foreach ($colors as $color) {
            $color = '#'.$color;
            $dup = Room::where('color', $color)->first();
            if (empty($dup)) {
                $room->color = $color;
                break;
            }
        }
        $room->save();
        return $this->sendResponse($room, 'Create successfully.');
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
        $room = Room::find($id);
        return $room;
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
            'name' => 'required|max:255',    // room name
            'location_id' => 'required'
        ]);
        $room = Room::find($id);
        // update user, we don't use fill here because avatar and roles shouldn't be updated.
        $room->name = $request->name;
        $room->location_id = $request->location_id;
        $room->status = $request->status;
        $room->save();

        return $this->sendResponse($room, 'Updated successfully.');
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

}
