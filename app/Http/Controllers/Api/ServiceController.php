<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
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
            'category_id' => 'required|integer',
            'status' => 'required|integer',
            'duration' => 'required',
        ]);
        $service = Service::find($id);
        $service->category_id = $request->category_id;
        $service->name = $request->name;
        $service->description = $request->description;
        $service->duration = $request->duration;
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
