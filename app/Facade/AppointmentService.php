<?php

namespace App\Facade;

use App\Models\Appointment;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\Timeslot;
use App\Models\TrainerTimeslot;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{

    /**
     * @param $user get minDate & maxDate based on user level.
     * @return array
     */
    public function getDates($user) {
        $today = CarbonImmutable::now();
        if ($user) {
            $minDate = $today->add(1, 'day')->format('Y-m-d');
            $maxDate = $today->add($user->role->book_days_in_adv, 'day')->format('Y-m-d');
        } else {
            $minDate = $today->add(1, 'day')->format('Y-m-d');
            $maxDate = $today->add(1, 'day')->format('Y-m-d');
        }
        return [$minDate, $maxDate];
    }

    /**
     * @param $trainerId
     * @param $startTime
     * @param $endTime
     * @return bool true = occupied, false = not occupied.
     */
    public function isTrainerOccupied($trainerId, $startTime, $endTime, $bookId = 0)
    {
        // release the booked timeslot.
        if ($bookId > 0) {
            $chkDup = DB::table('customer_bookings')
                ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
                ->where('customer_bookings.id', $bookId)
                ->where('appointments.start_time', '<', $endTime)
                ->where('appointments.end_time', '>', $startTime)
                ->first();
            if (!empty($chkDup)) {
                return false;
            }
        }
        $chkDup = Appointment::where('user_id', $trainerId)
            ->whereIn('status', ['approved', 'pending'])
            // ref: https://stackoverflow.com/questions/6571538/checking-a-table-for-time-overlap
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            // TODO check lesson quota.
//            ->whereRaw('(? between start_time1 and end_time OR ? between start_time and end_time)', [$startTime, $endTime])
            ->get();
        return count($chkDup) > 0;
    }

    /**
     * @param $roomId
     * @param $startTime
     * @param $endTime
     * @return bool true = occupied, false = not occupied.
     */
    public function isRoomOccupied($roomId, $startTime, $endTime)
    {
        $chkDup = Appointment::where('room_id', $roomId)
            ->whereIn('status', ['approved', 'pending'])
            // ref: https://stackoverflow.com/questions/6571538/checking-a-table-for-time-overlap
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
//            ->whereRaw('(? between start_time1 and end_time OR ? between start_time and end_time)', [$startTime, $endTime])
            ->get();
        return count($chkDup) > 0;
    }

    public function getAppointmentDates($user, $date, $time, $noOfSession, $sessionInterval, $room_id, $assignRandomRoom, $package_id) {
        // get min & max dates by user
        $dates = $this->getDates($user);
        $minDate = $dates[0];
        $maxDate = $dates[1];
        $appointmentDate = new Carbon($date);
        $dateOk = $appointmentDate->between($minDate, $maxDate);
        if (!$dateOk) {
            // FIXME throw error in case someone hack the appointment date.

        }
        $startTime = $appointmentDate->timestamp + $time;
        $dt = (new DateTime("@$startTime"))->format('Y-m-d H:i:s');
//        echo "<br />startTime2=" . $dt;
        $endTime = $appointmentDate->timestamp + $time + ($noOfSession * $sessionInterval);
        $dt2 = (new DateTime("@$endTime"))->format('Y-m-d H:i:s');
//        echo "<br />startTime3=" . $dt2;

        // Room availability checking.
        $assignedRoom = -1;
        if ($assignRandomRoom) {
            // support to assign dynamic room.
            $rooms = Room::inRandomOrder()->where('status', 1001)->get();   // no need to orderBy, let it return randomly.
            foreach ($rooms as $room) {
                $isRoomOccupied = $this->isRoomOccupied($room->id, $dt, $dt2);
                if (!$isRoomOccupied) {
                    $assignedRoom = $room->id;
                    break;   // exit foreach rooms.
                }
            }
        } else {
            // check duplicate by roomId and appointment time.
            $assignedRoom = $room_id;   // param from client side.
            if (!$package_id) {
                $isRoomOccupied = $this->isRoomOccupied($assignedRoom, $dt, $dt2);
                if ($isRoomOccupied) {   // reset $assignedRoom to negative number if desired room was occupied.
                    $assignedRoom = -2;
                }
            }
        }
        // check duplicate, in Appointment system should not allow same user book same timeslot.
        $isDup = false;
        for ($i=0; $i<2; $i++) {   // do twice, 1 for start_time, another for end_time.
            $paramDate = $i == 0 ? $dt : $dt2;
            $found = DB::table('customer_bookings')
                ->join('appointments', 'customer_bookings.appointment_id', '=', 'appointments.id')
                ->where('customer_bookings.customer_id', $user->id)
                ->whereRaw('? between appointments.start_time and appointments.end_time', $paramDate)->first();
            if (!empty($found)) {
                $isDup = true;
                break;
            }
        }
        return [
            'start_time' => $dt,
            'end_time' => $dt2,
            'room_id' => $assignedRoom,
            'duplicated' => $isDup
        ];
    }

    public function saveAppointment($appointment) {
        if ($appointment->package_id > 0) {
            // get existing package appointment.
            $packageApt = Appointment::where('start_time', $appointment->start_time)
                ->where('end_time', $appointment->end_time)
                ->where('room_id', $appointment->room_id)
                ->where('package_id', $appointment->package_id)
                ->first();
            if (!empty($packageApt)) {
                return $packageApt;
            }
        }
        $appointment->save();

        return $appointment;
    }

    /**
     * @param $start_date the starting date, not need the exact date as it will calculate automatically.
     * @param $quantity no. of lesson.
     * @param $dow_list array day of week.
     * @param $trainer_id the trainer id.
     * @return array
     */
    public function getLessonDates($start_date, $quantity, $dow_list, $trainer_id) {
        $locationId = 1;
        // use Date and WeekNo to find the timeslot.
        $d1 = Carbon::createFromFormat('Y-m-d', $start_date);

        // loop once to find the closest dow from start_date.
        $d2 = Carbon::createFromFormat('Y-m-d', $start_date);
        $first_dow = null;
        if (sizeof($dow_list) > 1) {
            sort($dow_list);
            while ($first_dow == null) {
                foreach ($dow_list as $dow) {
                    // the start_date is not the first element of dow_list.
                    if ($d2->is(Timeslot::WEEKS[$dow])) {
                        $first_dow = $dow;
                        break;
                    }
                }
                if ($first_dow == null) {
                    $d2->addDay();
                }
            }
            // set d1 = d2.
            $d1 = $d2;
            // reorder array
            $newdow_list = [];
            $start_dow_list = [];
            foreach ($dow_list as $dow) {
                if ($dow == $first_dow || sizeof($newdow_list) > 0) {
                    array_push($newdow_list, $dow);
                    if (sizeof($newdow_list) == sizeof($dow_list)) {
                        break;
                    }
                } else {
                    array_push($start_dow_list, $dow);
                }
            }
            $dow_list = array_merge($newdow_list, $start_dow_list);
//echo 'd2==' . $d2->format('Y-m-d') . ', newdow_list=' . json_encode($dow_list) . ', first_dow=' . $first_dow;
        }

        $i = 0;
        $data = [];
        $holidays = [];
        while ($i < $quantity) {
            $j = 0;    // to compare if no date can be obtained from $dow_list.
            foreach ($dow_list as $dow) {
                $d1 = $d1->is(Timeslot::WEEKS[$dow]) ? $d1 : $d1->next(Timeslot::WEEKS[$dow]);
                // check date is public holiday for the office?
                $daysoff = Holiday::where('location_id', $locationId)->whereRaw('(? between start_date and end_date)', $d1->format('Y-m-d'))->first();
                if (!empty($daysoff)) {
                    // is dayoff, add one day and go to next dow.
                    $holidays[] = ["date" => $d1->format('Y-m-d'), "dow" => $dow];
                    $d1->addDay();
                    continue;
                }
                // check date is working day.
                if ($trainer_id > 0) {
                    $dayOfWeek_timeslots = TrainerTimeslot::where('location_id', $locationId)
                        ->where('trainer_id', $trainer_id);
                } else {
                    $dayOfWeek_timeslots = Timeslot::where('location_id', $locationId);
                }
                $workingDay = $dayOfWeek_timeslots->where('day_idx', $dow)
                    ->orderBy('day_idx', 'asc')
                    ->orderBy('from_time', 'asc')
                    ->first();
                if (empty($workingDay)) {
                    $j++;
                    // dow is not a working day, go to next dow without date increment.
                    continue;
                }
                $data[] = ["date" => $d1->format('Y-m-d'), "dow" => $dow];
                $d1->addDay();
                $i++;
                if ($i == $quantity) break;
            }
        }
        return compact('data', 'holidays');
    }
}
