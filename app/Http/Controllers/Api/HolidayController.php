<?php

namespace App\Http\Controllers\Api;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HolidayController extends BaseController
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
        $holidays = Holiday::orderBy('start_date', 'desc')
            ->orderBy('end_date', 'desc')
            ->where('location_id', 1);

        $editable = false;
        // this module is only for manager.
        if ($this->isSuperLevel($user)) {
            $editable = true;
        }
        if ($request->has('year')) {
            if ($request->year > 0)
                $holidays->where('start_date', $request->start_date);
        }

        if ($request->expectsJson()) {
            $data = $holidays->with('location')->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            return $data;
        }
        return view("holidays.list", $holidays);
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
            'name' => 'required',
            'start_date' => 'required|date',
//            'end_date' => 'required|date',
        ]);
        $holiday = new Holiday($request->all());
        if (!$request->has('end_date')) {
            // end_date = start_date if not provided.
            $holiday->end_date = $holiday->start_date;
        }
        $holiday->save();
        return $this->sendResponse($holiday, 'Create successfully.');
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
        $holiday = Holiday::find($id);
        return $holiday;
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
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $holiday = Holiday::find($id);
        $holiday->name = $request->name;
        $holiday->start_date = $request->start_date;
        $holiday->end_date = $request->end_date;
        $holiday->save();

        return $this->sendResponse($holiday, 'Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Holiday::find($id);
        if (!empty($order)) {
            $holiday->delete();

            return response()->json(['success'=>true]);
        } else {
            return response()->json(['success'=>false, 'message' => 'Cannot delete holiday.']);
        }
    }

}
