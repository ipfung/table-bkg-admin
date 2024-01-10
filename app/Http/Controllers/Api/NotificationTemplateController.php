<?php

namespace App\Http\Controllers\Api;

use App\Models\NotificationTemplate;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationTemplateController extends BaseController
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
        $notification_templates = NotificationTemplate::orderBy('name', 'asc')
            ->where('status', '<>', 'reserved');

        $editable = false;
        $wtp_data = null;
        $whatsapp_notifications = config('app.jws.settings.whatsapp_notifications', false);
        // this module is only for manager.
        if ($this->isSuperLevel($user)) {
            $editable = true;
        }
        if ($request->has('name')) {
            if ($request->name != '')
                $notification_templates->where('name', $request->name);
        }
        if ($request->has('type')) {
            if ($request->type != '')
                $notification_templates->where('type', $request->type);
            if ($request->type == 'whatsapp' && true == $whatsapp_notifications) {
                $whatsappService = new WhatsAppService();
                $wtp_data = $whatsappService->getTemplates();
            }
        }
        if ($request->has('entity')) {
            if ($request->entity != '')
                $notification_templates->where('entity', $request->entity);
        }
        if ($request->has('send_to')) {
            if ($request->send_to != '')
                $notification_templates->where('send_to', $request->send_to);
        }
        if ($request->has('whatsapp_tpl')) {
            if ($request->whatsapp_tpl != '')
                $notification_templates->where('whatsapp_tpl', $request->whatsapp_tpl);
        }

        $notifications = $notification_templates->get();
        // find a inject whatsapp message template into our template.
        if ($wtp_data && !empty($wtp_data['data'])) {
//            return compact('wtp_data');
            foreach ($wtp_data['data'] as &$wtp) {
                foreach ($notifications as $notify) {
                    if ($wtp['name'] == $notify->whatsapp_tpl) {
                        $components = $wtp['components'];   // it's an array
                        foreach ($components as &$cmp) {
                            if ($cmp['type'] == 'HEADER') {
                                $notify->subject = $cmp['text'];
                            }
                            if ($cmp['type'] == 'FOOTER') {
                                $notify->footer = $cmp['text'];
                            }
                            if ($cmp['type'] == 'BODY') {
                                $notify->content = $cmp['text'];
                            }
                        }
                        continue;
                    }
                }
            }
        }

        // split customer/trainer notifications.
        $customer_data = [];
        $trainer_data = [];
        foreach ($notifications as &$notify) {
            if (strpos($notify->name, 'customer') !== false) {
                $customer_data[] = $notify;
            } else {
                $trainer_data[] = $notify;
            }
        }

        if ($request->expectsJson()) {
            return compact('customer_data', 'trainer_data', 'editable', 'whatsapp_notifications');
        }
        return view("notification_templates.list", $notification_templates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        // validate
//        $request->validate([
//            'name' => 'required',
//            'type' => 'required',
//            'entity' => 'required',
//            'send_to' => 'required',
//            'subject' => 'required',
//            'content_body' => 'required',
//            'status' => 'required',
//        ]);
//        $notification_template = new NotificationTemplate($request->all());
//        $notification_template->save();
//        return $this->sendResponse($notification_template, 'Create successfully.');
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
        $notification_template = NotificationTemplate::find($id);
        return $notification_template;
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
            'subject' => 'required',
            'content_body' => 'required',
            'status' => 'required',
        ]);
        $notification_template = NotificationTemplate::find($id);
        $notification_template->subject = $request->subject;
        $notification_template->content = $request->content_body;
        $notification_template->status = $request->status;
        $notification_template->save();

        return $this->sendResponse($notification_template, 'Updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function destroy($id)
//    {
//        $notification_template = NotificationTemplate::find($id);
//        if (!empty($order)) {
//            $notification_template->delete();
//
//            return response()->json(['success'=>true]);
//        } else {
//            return response()->json(['success'=>false, 'message' => 'Cannot delete holiday.']);
//        }
//    }

}
