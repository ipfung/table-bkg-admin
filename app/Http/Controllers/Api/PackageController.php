<?php

namespace App\Http\Controllers\Api;

use App\Facade\AppointmentService;
use App\Models\Appointment;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PackageController extends BaseController
{
    private $appointmentService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AppointmentService $appointmentService)
    {
        $canAccess = config("app.jws.settings.packages");
        if (!$canAccess) {
            abort(404);
        }
        $this->appointmentService = $appointmentService;
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
        $packages = Package::orderBy('name', 'asc');

        $editable = false;
        // this module is only for manager.
        if (!$this->isSuperLevel($user)) {
            $packages->where('status', 1001);   // see active packages only.
        } else {
            $editable = true;
        }
        if ($request->has('status')) {
            if ($request->status > 0)
                $packages->where('status', $request->status);
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $packages->whereRaw('upper(name) LIKE upper(?)', [$request->name]);
        }

        if ($request->expectsJson()) {
            $data = $packages->with('service', 'room', 'trainer', 'appointments')->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            return $data;
        }
        return view("packages.list", $packages);
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
            'name' => 'required|max:255',
            'description' => 'required',
            'no_of_session' => 'required|integer',
            'service_id' => 'required|integer',
            'quantity' => 'required|integer',
            'recurring' => 'required'
        ]);
        $package = new Package($request->all());
        $recurring = $request->input('recurring');
        $package->recurring = json_encode($recurring);

        // start DB transaction.
        DB::beginTransaction();

        $package->save();

        // block the dates in appointments table.
        if ($package->start_date && $package->start_time) {
            $trainer = User::find($package->trainer_id);

            $appointmentStatus = 'approved';
            // save appointment, it is 1st appointment if it is package.
            $dates = $request->lesson_dates;
            $pkg_count = count($dates);
            $parentId = 0;

            $results = [];
            for ($i=0; $i<$pkg_count; $i++) {
                // pass 1st appointment's id as parent_id as ref.
                $appointmentDates = $this->appointmentService->getAppointmentDates($trainer, $dates[$i], $request->start_time, $request->no_of_session, $request->sessionInterval, $request->room_id, false, $package->id);
                $assignedRoom = $appointmentDates['room_id'];
                if ($assignedRoom <= 0) {
                    DB::rollBack();
                    // Room not available for appointment time, throw error.
                    return ['success' => false, 'error' => 'Room is not available at selected time, please choose different time.', 'param' => $assignedRoom];
                }
                if ($this->appointmentService->isTrainerOccupied($package->trainer_id, $appointmentDates['start_time'], $appointmentDates['end_time'])) {   // false = not occupied.
                    DB::rollBack();
                    // Room not available for appointment time, throw error.
                    return ['success' => false, 'error' => 'Trainer is not available at selected time, please choose different time.'];
                }

                $appointment = new Appointment;
                $appointment->start_time = $appointmentDates['start_time'];
                $appointment->end_time = $appointmentDates['end_time'];
                $appointment->room_id = $appointmentDates['room_id'];
                $appointment->package_id = $package->id;
                $appointment->user_id = $trainer->id;
                $appointment->service_id = $request->service_id;
                $appointment->notify_parties = true;
                $appointment->internal_remark = $request->internal_remark;
                $appointment->status = $appointmentStatus;     // get defaults from settings.
                if ($parentId > 0) {
                    $appointment->parent_id = $parentId;
                }

                $savedAppointment = $this->appointmentService->saveAppointment($appointment, $appointmentDates);
                if (0 == $i) {
                    $parentId = $savedAppointment->id;
                }
                $results[] = $savedAppointment;
            }

        }
        DB::commit();

        return $this->sendResponse($package, 'Create successfully.');
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
        $package = Package::find($id);
        return $package;
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
            'name' => 'required|max:255',
            'description' => 'required',
            'no_of_session' => 'required|integer',
            'service_id' => 'required|integer',
            'quantity' => 'required|integer',
            'recurring' => 'required'
        ]);
        $package = Package::find($id);
        // update user, we don't use fill here because avatar and roles shouldn't be updated.
        $package->name = $request->name;
        $package->description = $request->description;
        $package->service_id = $request->service_id;
        $package->room_id = $request->room_id;
        $package->price = $request->price;
        $package->discount = $request->discount;
        $package->status = $request->status;
        $package->quantity = $request->quantity;
        $package->trainer_id = $request->trainer_id;
        $package->no_of_session = $request->no_of_session;
        $package->start_date = $request->start_date;
        $package->end_date = $request->end_date;
        $package->start_time = $request->start_time;
        $package->recurring = $request->recurring;
        $package->save();

        return $this->sendResponse($package, 'Updated successfully.');
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
