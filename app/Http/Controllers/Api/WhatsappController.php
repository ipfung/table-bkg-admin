<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use App\Models\WhatsApp\MessageTemplate;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappController extends BaseController
{
    private $whatsappService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(WhatsAppService $whatsappService)
    {
        $canAccess = config("app.jws.settings.whatsapp_notifications");
        if (!$canAccess) {
            abort(404);
        }
        $this->whatsappService = $whatsappService;
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
        $editable = false;
        // this module is only for internal coach to read.
        if ($this->isInternalCoachLevel($user)) {
            // this module is only for manager to edit.
            if ($this->isSuperLevel($user)) {
                $editable = true;
            }

            $data = $this->whatsappService->getTemplates();
            $data['editable'] = $editable;   // append to paginate()
            if ($request->expectsJson()) {
                return $data;
            }
            return view("whatsapp.message_templates.list", $data);
        }
        abort(404);
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
            'components' => 'required',
            'category' => 'required|integer',
        ]);
        $messageTemplate = new MessageTemplate;
        $messageTemplate->setName($request->name);
        $messageTemplate->setCategory($request->category);
        $messageTemplate->setComponents($request->components);
        $messageTemplate->setLanguage('zh_HK');

        $this->whatsappService->createTemplate($messageTemplate);
        return $this->sendResponse($messageTemplate, 'Create successfully.');
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
        // 2022-12 Whatsapp don't support modify template
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($templateName)
    {
        if (empty($order)) {
            $this->whatsappService->deleteTemplate($templateName);

            return response()->json(['success'=>true]);
        } else {
            return response()->json(['success'=>false, 'message' => 'User cannot be deleted because it is used in Order.']);
        }
    }

}
