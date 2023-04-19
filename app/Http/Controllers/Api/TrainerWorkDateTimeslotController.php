<?php

namespace App\Http\Controllers\Api;

use App\Facade\PermissionService;
use App\Models\TrainerWorkdateTimeslot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainerWorkDateTimeslotController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct($permissionService);
        $canAccess = (config("app.jws.settings.timeslots") == 'trainer_date');
        if (!$canAccess) {
            abort(404);
        }
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
        $timeslots = TrainerWorkdateTimeslot::orderBy('work_date', 'desc')
            ->orderBy('from_time', 'asc')
            ->where('location_id', 1);

        $editable = false;
        // this module is only for manager.
        if ($this->isSuperLevel($user)) {
            $editable = true;
            $timeslots->where('trainer_id', $request->trainer_id);
        } else if ($this->isExternalCoachLevel($user)) {
            $editable = true;
            $timeslots->where('trainer_id', $user->id);
        } else {
            $timeslots->where('trainer_id', $user->id);
        }
        if ($request->has('work_date')) {
            $timeslots->where('work_date', $request->work_date);
        }

        if ($request->expectsJson()) {
            $data = $timeslots->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            return $data;
        }
        return view("trainer-work_date-timeslots.list", $timeslots);
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
            'work_date' => 'required|date',
            'trainer_id' => 'required',
            'location_id' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);
        $timeslot = new TrainerWorkdateTimeslot($request->all());
        $timeslot->save();
        return $this->sendResponse($timeslot, 'Create successfully.');
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
        $timeslot = TrainerWorkdateTimeslot::find($id);
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
            'work_date' => 'required|date',
            'trainer_id' => 'required',
            'location_id' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
        ]);
        $timeslot = TrainerWorkdateTimeslot::find($id);
//        $timeslot->location_id = $request->location_id;
        $timeslot->work_date = $request->work_date;
        $timeslot->from_time = $request->from_time;
        $timeslot->to_time = $request->to_time;
        $timeslot->save();

        return $this->sendResponse($timeslot, 'Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $timeslot = TrainerWorkdateTimeslot::find($id);
        if (Carbon::createFromFormat('Y-m-d', $timeslot->work_date)->isAfter(Carbon::today())) {
            $timeslot->delete();
            return response()->json(['success'=>true]);
        } else {
            return response()->json(['success'=>false, 'message' => 'Cannot delete work date that is past.']);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTrainerNonWorkDates(Request $request, $trainer_id)
    {
        $user = Auth::user();
        if ($request->has('y')) {
            $year = $request->y;
        }
        if ($request->has('m')) {
            $month = $request->m;
        }
        //
        DB::enableQueryLog(); // Enable query log
        $timeslots = TrainerWorkdateTimeslot::orderBy('work_date', 'asc')
            ->where('location_id', 1)
            ->where('trainer_id', $trainer_id)
            ->whereRaw('year(work_date)=?', $year)
            ->whereRaw('month(work_date)=?', $month)
            ->where('work_date', '>', Carbon::today()->format(BaseController::$dateFormat))
            ->select('work_date')
            ->distinct()
            ->pluck('work_date')
        ->toArray();

        $month_dates = [];
        $firstDateOfMonth = Carbon::create($year, $month, 1);
        $month_dates[] = $firstDateOfMonth->format(BaseController::$dateFormat);
        $lastDay = $firstDateOfMonth->daysInMonth;
        for ($d=1; $d<$lastDay; $d++) {
            $month_dates[] = $firstDateOfMonth->addDay()->format(BaseController::$dateFormat);
        }
        $data = array_diff($month_dates, $timeslots);
        return array_values($data);
    }

}
