<?php

namespace App\Http\Controllers\Api;

use App\Models\Timeslot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeslotController extends BaseController
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
        $timeslots = Timeslot::orderBy('day_idx', 'asc')
            ->orderBy('from_time', 'asc')
            ->where('location_id', 1);

        $editable = false;
        // this module is only for manager.
        if ($this->isSuperLevel($user)) {
            $editable = true;
        }
        if ($request->has('day_idx')) {
            if ($request->day_idx > 0)
                $timeslots->where('day_idx', $request->day_idx);
        }

        if ($request->expectsJson()) {
            $data = $timeslots->with('location')->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            return $data;
        }
        return view("timeslots.list", $timeslots);
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
            'day_idx' => 'required|integer',    // Monday = 1, Sunday = 7
            'location_id' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);
        $timeslot = new Timeslot($request->all());
        $timeslot->save();
        return $this->sendResponse($timeslot, 'Create successfully.');
    }

    public function copyMonday(Request $request) {
        // get Monday only timeslots.
        $monday_timeslots = Timeslot::where('day_idx', 1)->where('location_id', 1)->get();
        DB::beginTransaction();
        // delete all except Monday timeslots.
        Timeslot::where('day_idx', '<>', 1)->where('location_id', 1)->delete();
        // loop Tue to Sun in order to copy Monday timeslots.
        for ($i=2; $i<8; $i++) {
            foreach ($monday_timeslots as $monday_timeslot) {
                $day = new Timeslot;
                $day->day_idx = $i;
                $day->location_id = $monday_timeslot->location_id;
                $day->from_time = $monday_timeslot->from_time;
                $day->to_time = $monday_timeslot->to_time;
                $day->save();
            }
        }
        DB::commit();
        return $this->sendResponse($monday_timeslots, 'Copy successfully.');
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
        $timeslot = Timeslot::find($id);
        return $timeslot;
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
            'day_idx' => 'required|integer',    // Monday = 1, Sunday = 7
            'location_id' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);
        $timeslot = Timeslot::find($id);
//        $timeslot->location_id = $request->location_id;
        $timeslot->from_time = $request->from_time;
        $timeslot->to_time = $request->to_time;
        $timeslot->save();

        return $this->sendResponse($timeslot, 'Updated successfully.');
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
