<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainerRate;

class TrainerRateController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = Auth::user();
        //
        $trainerrates = TrainerRate::OrderBy('id')->with('users');

        return $data = $trainerrates->paginate()->toArray();
        //return view("trainerrates.list", $trainerrates);
    }

    public function listByStudentId(Request $request)
    {
        $user = Auth::user();
        //
        $trainerrates = TrainerRate::orderby('id');
        if ($request->user_id != '' ){
            $trainerrates->where("student_id", $request->user_id);
        }

        return $data = $trainerrates->with('users')->paginate()->toArray();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        /* if (!Gate::allows('room')) {
            return $this->sendPermissionDenied();
        } */

        // validate
        $request->validate([

        ]);
        $trainerrate = new TrainerRate($request->all());


        $trainerrate->save();

        return $this->sendResponse($trainerrate, 'Create successfully.');
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
        /* if (!Gate::allows('room')) {
            return $this->sendPermissionDenied();
        } */

        $request->validate([

        ]);
        $trainerrate = TrainerRate::find($id);
        // update user, we don't use fill here because avatar and roles shouldn't be updated.
        $trainerrate->trainer = $request->trainer;
        $trainerrate->rate_type = $request->rate_type;
        $trainerrate->trainer_commission = $request->trainer_commission;
        $trainerrate->trainer_charge = $request->trainer_charge;
        $company_income = 0;
        if ($request->trainer_charge > 0 &&  $request->trainer_commission >=0) {
            $company_income = $request->trainer_charge - $request->trainer_commission;
        }
        $trainerrate->company_income = $company_income;

        //$trainerrate->student_id = $request->student_id;
        $trainerrate->save();

        return $this->sendResponse($trainerrate, 'Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       /*  if (!Gate::allows('room')) {
            return $this->sendPermissionDenied();
        } */


        if ($id) {
            TrainerRate::where('id', $id)->delete();

            return response()->json(['success'=>true]);
        } else {
            return response()->json(['success'=>false, 'error' => 'Trainer Rate cannot be deleted because it is used in appointment.']);
        }
    }
}
