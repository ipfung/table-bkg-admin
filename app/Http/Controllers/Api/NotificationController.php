<?php

namespace App\Http\Controllers\Api;

use App\Models\NotifyMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
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
        $messages = NotifyMessage::orderBy('created_at', 'desc');

        $showCustomer = false;
        if ($this->isSuperLevel($user)) {
            if ($request->has('customer_id')) {
                $messages->where('customer_id', $request->customer_id);
            }
            $showCustomer = true;
        } else {
            $messages->where('customer_id', $user->id);
        }

        if ($request->expectsJson()) {
            if ($request->has('limit')) {   // for dashboard
                $data = $messages->with('customer', 'sender')->limit($request->limit)->get();
                return ['showCustomer' => $showCustomer, 'data' => $data];
            } else {
                // ref: https://stackoverflow.com/questions/52559732/how-to-add-custom-properties-to-laravel-paginate-json-response
                $data = $messages->with('customer', 'sender')->paginate()->toArray();
                $data['showCustomer'] = $showCustomer;
                return $data;
            }
        }
        return view("notifications.list", $messages);
    }
}
