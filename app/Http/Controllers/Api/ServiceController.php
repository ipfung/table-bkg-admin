<?php

namespace App\Http\Controllers\Api;

use App\Facade\OrderService;
use App\Models\Room;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends BaseController
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
        $services = Service::orderBy('name', 'asc');

        $editable = false;
        // this module is only for manager.
        if ($this->isSuperLevel($user)) {
            $editable = true;
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $services->where('name', $request->name);
        }
        if ($request->has('status')) {
            if ($request->status > 0)
                $services->where('status', $request->status);
        }

        if ($request->expectsJson()) {
            $data = $services->paginate()->toArray();
            $data['editable'] = $editable;   // append to paginate()
            return $data;
        }
        return view("services.list", $services);
    }

    /**
     * get logged user's default service object.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserService(Request $request)
    {
        $user = Auth::user();
        if ($user->service_id > 0) {
            $service = Service::where('id', $user->service_id)
                ->where('status', 1001);
            if ($request->expectsJson()) {
                $data = $service->first();
                if (config('app.jws.settings.timeslots')) {
                    $data->timeslotSetting = config('app.jws.settings.timeslots');
                }
                $data->requiredTrainer = config('app.jws.settings.required_trainer');
                $data->requiredRoom = config('app.jws.settings.required_room');
//                // check any valid token-based orders.
                $orderService = new OrderService;
                $validOrder = $orderService->getValidTokenBasedOrder($user, $request->order_id);
                if ($validOrder) {
                    $data->order_number = $validOrder['order_number'];
                    $data->token_quantity = $validOrder['token_quantity'];
                    $data->no_of_session = $validOrder['no_of_session'];
                    $data->free_quantity = $validOrder['free_quantity'];
                    $data->free_no_of_session = $validOrder['free_no_of_session'];
                    $data->trainers = $validOrder['trainers'];
                }
                if (!empty($user->settings)) {
                    $settings = json_decode($user->settings);
                    if (isset($settings->trainer)) {
                        $trainer = User::find($settings->trainer);
                        $data->trainer = ["id" => $settings->trainer, "name" => $trainer->name, "avatar" => $trainer->avatar, "mobile_no" => $trainer->mobile_no];
                    }
                    if (isset($settings->room)) {
                        $room = Room::find($settings->room);
                        $data->room = ["id" => $settings->room, "name" => $room->name, "color" => $room->color];
                    }
                    return $data;
                }
            }
            return view("services.list", $service);
        }
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
            'description' => 'required',
            'category_id' => 'required|integer',
            'status' => 'required|integer',
            'duration' => 'required',
        ]);
        $service = new Service($request->all());
        $service->save();
        return $this->sendResponse($service, 'Create successfully.');
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
        $service = Service::find($id);
        return $service;
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
            'description' => 'required',
//            'category_id' => 'required|integer',
            'status' => 'required|integer',
            'price' => 'numeric',
            'duration' => 'required',
            'min_duration' => 'required',
            'max_duration' => 'required',
        ]);
        $service = Service::find($id);
//        $service->category_id = $request->category_id;
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->duration = $request->duration;
        $service->min_duration = $request->min_duration;
        $service->max_duration = $request->max_duration;
        $service->status = $request->status;
        $service->save();

        return $this->sendResponse($service, 'Updated successfully.');
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
