<?php

namespace App\Http\Controllers\Api;

use App\Facade\AppointmentService;
use App\Facade\PermissionService;
use App\Models\Appointment;
use App\Models\CustomerBooking;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PackageController extends BaseController
{
    private $appointmentService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PermissionService $permissionService, AppointmentService $appointmentService)
    {
        parent::__construct($permissionService);
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
        if (!Gate::allows('packages')) {
            return $this->sendPermissionDenied();
        }

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
        if ($request->has('trainerId')) {
            if ($request->trainerId > 0)
                $packages->where('trainer_id', $request->trainerId);
        }
        if ($request->has('roomId')) {
            if ($request->roomId > 0)
                $packages->where('room_id', $request->roomId);
        }
        if ($request->has('status')) {
            if ($request->status > 0)
                $packages->where('status', $request->status);
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $packages->whereRaw('upper(name) LIKE upper(?)', $request->name . '%');
        }
        if ($request->has('id')) {
            if ($request->id > 0)
                $packages->where('id', $request->id);
        }

        if ($request->expectsJson()) {
            $data = $packages->with('service', 'room', 'trainer', 'appointments.customerBookings')->paginate()->toArray();
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
        if (!Gate::allows('packages')) {
            return $this->sendPermissionDenied();
        }

        // validate
        $request->validate([
            'name' => 'required|max:255|unique:packages',
            'description' => 'required',
            'no_of_session' => 'required|integer',
            'total_space' => 'required|integer',
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
            $this->createPackageAppointments($package, $request->lesson_dates, $request->sessionInterval);
        }
        DB::commit();

        return $this->sendResponse($package, 'Create successfully.');
    }

    private function createPackageAppointments($package, $dates, $sessionInterval) {
        if ($package->trainer) {
            $trainer = $package->trainer;
        } else {
            $trainer = User::find($package->trainer_id);
        }

        $appointmentStatus = 'approved';
        // save appointment, it is 1st appointment if it is package.
        $pkg_count = count($dates);
        $parentId = 0;

        $results = [];
        for ($i=0; $i<$pkg_count; $i++) {
            // pass 1st appointment's id as parent_id as ref.
            $appointmentDates = $this->appointmentService->getAppointmentDates($trainer, $dates[$i], $package->start_time, $package->no_of_session, $sessionInterval, $package->room_id, false, $package->id);
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
            $appointment->service_id = $package->service_id;
            $appointment->notify_parties = true;
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
        return $results;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Gate::allows('packages')) {
            return $this->sendPermissionDenied();
        }

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
        if (!Gate::allows('packages')) {
            return $this->sendPermissionDenied();
        }

        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('packages')->ignore($id),
            ],
            'description' => 'required',
            'no_of_session' => 'required|integer',
            'total_space' => 'required|integer',
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
        $package->total_space = $request->total_space;
        $package->trainer_id = $request->trainer_id;
        $package->no_of_session = $request->no_of_session;
        $package->start_date = $request->start_date;
        $package->end_date = $request->end_date;
        $package->start_time = $request->start_time;
        $package->recurring = $request->recurring;
        $package->save();

        return $this->sendResponse($package, 'Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Gate::allows('packages')) {
            return $this->sendPermissionDenied();
        }

        $appointment = Appointment::where('package_id','=',$id)->first();
        if (empty($appointment)) {
            Package::where('id', $id)->delete();

            return response()->json(['success'=>true]);
        } else {
            return response()->json(['success'=>false, 'error' => 'Package cannot be deleted because it is used in appointment.']);
        }
    }

    public function generateMoreLessons(Request $request, $packageId) {
        $package = Package::find($packageId);
        // repeat(days of week) is in recurring field.
        $recurring = json_decode($package->recurring);
        // get the last saved appointment.
        $lastApt = Appointment::orderBy('start_time', 'desc')
                ->where('package_id', $packageId)
                ->first();
        // find next lesson dates
        $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $lastApt->start_time);
        $nextDay = $start_date->modify('+1 day');
        $dates = $this->appointmentService->getLessonDates($nextDay->format(BaseController::$dateFormat), $package->quantity, $recurring->repeat, $package->trainer_id, $package->end_date);
        $startDates = [];
        foreach ($dates['data'] as $d) {
            $startDates[] = $d['date'];
        }
        // save package, validate will be done in the function.
        $results = $this->createPackageAppointments($package, $startDates, $package->service->duration_epoch);
        if (sizeof($results) > 0) {
            return $dates;
        }
        return response()->json(['success'=>false, 'error' => 'Cannot generate more lessons.']);
    }

    public function createLessonDate(Request $request, $id) {
        $request->validate([
            'new_date' => 'required|date',
            'sessionInterval' => 'required|integer',
        ]);
        $package = Package::find($id);
        if (empty($package)) {
            return response()->json(['success'=>false, 'error' => 'Package not found']);
        }
        $appointment = Appointment::where('package_id', $id)
            ->whereRaw('date(start_time)=?', $request->new_date)->first();
        if (!empty($appointment)) {
            return response()->json(['success'=>false, 'error' => 'Date already existed', 'params' => $request->new_date]);
        }
        $results = $this->createPackageAppointments($package, [$request->new_date], $request->sessionInterval);
        if (sizeof($results) > 0) {
            return response()->json(['success'=>true]);
        }
        return response()->json(['success'=>false, 'error' => 'Cannot create custom lesson date.']);
    }

    public function updateLessonDate(Request $request, $id) {
        $request->validate([
            'old_date' => 'required|date',
            'new_date' => 'required|date',
            'sessionInterval' => 'required|integer',
        ]);
        $package = Package::find($id);
        if (empty($package)) {
            return response()->json(['success'=>false, 'error' => 'Package not found']);
        }
        $appointment = Appointment::where('package_id', $id)
            ->whereRaw('date(start_time)=?', $request->new_date)->first();
        if (!empty($appointment)) {
            return response()->json(['success'=>false, 'error' => 'Date already existed', 'params' => $request->new_date]);
        }
        $appointment = Appointment::where('package_id', $id)
            ->whereRaw('date(start_time)=?', $request->old_date)->first();
        if (empty($appointment)) {
            // no record found by old_date, error.
            return response()->json(['success'=>false, 'error' => 'No record found by old date', 'params' => $request->old_date]);
        }
        $appointmentDate = new Carbon($request->new_date);
        // ref: https://stackoverflow.com/questions/47086164/replace-date-with-another-date-but-keep-the-same-time-php
        $start_date = DateTime::createFromFormat(BaseController::$dateTimeFormat, $appointment->start_time);
        $start_date->setDate($appointmentDate->year, $appointmentDate->month, $appointmentDate->day);
        $end_date = DateTime::createFromFormat(BaseController::$dateTimeFormat, $appointment->end_time);
        $end_date->setDate($appointmentDate->year, $appointmentDate->month, $appointmentDate->day);
        $appointment->start_time = $start_date->format(BaseController::$dateTimeFormat);
        $appointment->end_time = $end_date->format(BaseController::$dateTimeFormat);
        $appointment->save();
        return response()->json(['success'=>true]);
    }

    public function deleteLessonDate(Request $request, $id) {
        $request->validate([
            'old_date' => 'required|date',
        ]);
        $appointment = Appointment::where('package_id', $id)
            ->whereRaw('date(start_time)=?', $request->old_date)
            ->first();
        if (empty($appointment)) {
            return response()->json(['success'=>false, 'error' => 'No such lesson date', 'params' => $request->old_date]);
        }
        $bookings = CustomerBooking::where('appointment_id', $appointment->id)->get();
        if (sizeof($bookings) > 0) {
            return response()->json(['success'=>false, 'error' => 'Found customer bookings', 'params' => $bookings]);
        }
        $appointment->delete();
        return response()->json(['success'=>true]);
    }
}
