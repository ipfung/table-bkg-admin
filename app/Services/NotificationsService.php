<?php

namespace App\Services;

use App\Facade\PlaceholderService;
use App\Mail\PayloadNotification;
use App\Models\NotificationTemplate;
use App\Models\NotifyMessage;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Mail;

class NotificationsService
{
    public function sendToCustomer($customer, $payload, $created_by, $log_to_db = true)
    {
        $payload['notification_template'] = 'customer_' . $payload['template'];
        return $this->sendNotifications($customer, $payload, $created_by, $log_to_db);
    }

    public function sendToEmployee($employee, $payload, $created_by, $log_to_db = true)
    {
        $payload['notification_template'] = 'trainer_' . $payload['template'];
        return $this->sendNotifications($employee, $payload, $created_by, $log_to_db);
    }

    private function getTitle($template_name) {
        if ($template_name) {
            return ucfirst(implode(" ", explode("_", $template_name)));
        }
    }

    private function sendNotifications($recipient, $payload, $created_by, $log_to_db = true)
    {
        $placeholderService = new PlaceholderService();
        $defaultLanguage = config("app.jws.whatsapp.default_language");
        $settings = json_decode($recipient->settings);
        // email once.
        if ($settings && !empty($settings->notifications)) {
            $log_body = [];
            $results = [];
            if ($settings->notifications->email) {
                $template = NotificationTemplate::where('name', $payload['notification_template'])->where('type', 'email')->first();
                if (!empty($template)) {
                    $subject = $placeholderService->applyPlaceholders($template->subject, $payload['placeholders']);

                    $body = $placeholderService->applyPlaceholders($template->content, $payload['placeholders']);

                    $email_content = ['title' => $subject, 'body' => $body];
                    Mail::to($recipient->email)
                        ->bcc(config('mail.from.address'))
                        ->send(new PayloadNotification($email_content));
                    $log_body['email'] = $email_content;
                    $results['email'] = true;
                    if ($settings->notifications->whatsapp) {
                        if (strlen($recipient->mobile_no) == 8 && config("app.jws.settings.whatsapp_notifications")) {
                            $whatsAppService = new WhatsAppService;
                            $mobile_no = '852' . $recipient->mobile_no;
                            $apiResponse = $whatsAppService->sendText($mobile_no, $subject);
                            if (!empty($apiResponse['messages'])) {
                                $log_body['whatsapp'] = ['mobile_no' => $mobile_no, 'text' => $subject];
                                $results['whatsapp'] = true;
                            } else if ($apiResponse['error']) {
                                $results['whatsapp'] = false;
                                $log_body['whatsapp_error'] = $apiResponse;
                            }
                        }
                    }
                }
            }

            // whatsapp notification.
            if ($settings->notifications->whatsapp) {
                if (strlen($recipient->mobile_no) == 8 && config("app.jws.settings.whatsapp_notifications")) {
                    $template = NotificationTemplate::where('name', $payload['notification_template'])->where('type', 'whatsapp')->first();
//echo 'whatsapp lang=' . $defaultLanguage . ' template=' . json_encode($template);
                    if (!empty($template)) {
                        $whatsAppService = new WhatsAppService;
                        $mobile_no = '852' . $recipient->mobile_no;
                        $apiResponse = $whatsAppService->sendAndLog(
                            $mobile_no,
                            $template,
                            $payload['placeholders'],
                            $defaultLanguage
//                $language ?: $defaultLanguage
                        );
                        if (!empty($apiResponse['messages'])) {
                            $log_body['whatsapp'] = ['mobile_no' => $mobile_no, 'template' => $template['whatsapp_tpl']];
                            $results['whatsapp'] = true;
                        } else if ($apiResponse['error']) {
                            if ($apiResponse['error']['code'] === 132001 && !empty($language)) {
                                $apiResponse = $whatsAppService->send(
                                    $mobile_no,
                                    $template,
                                    $payload['placeholders'],
                                    $defaultLanguage
                                );
                                if (!empty($apiResponse['messages'])) {
                                    $log_body['whatsapp'] = ['mobile_no' => $mobile_no, 'template' => $template['whatsapp_tpl'], 'language' => $defaultLanguage];
                                    $results['whatsapp'] = true;
                                }
                            } else {
                                $results['whatsapp'] = false;
                                $log_body['whatsapp_error'] = $apiResponse;
                            }
                        }
//                    } else {
//                        $results['whatsapp'] = false;
//                        $log_body['whatsapp_error'] = "cannot find whatsapp template in DB";
                    }
                }
            }
            // app push notification.
            if (!empty($settings->notifications->app)) {
                if ($settings->notifications->app) {
                    $userDevice = UserDevice::where('user_id', $recipient->id)
                        ->where('status', 'approved');
                    $counter = $userDevice->count();
//echo 'push $counter=' . $counter;
                    if ($counter > 0) {
                        $responseCode = FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
                        if (200 == $responseCode) {
                            $log_body['app'] = ['reg_id' => $userDevice->pluck('reg_id')->toArray()];
                            $results['app'] = true;
                        }
                    }
                }
            }

            if ($log_to_db) {
                $message = new NotifyMessage;
                $message->customer_id = $recipient->id;
                $message->title = $this->getTitle($payload['template']);
                $message->body = json_encode($log_body);
                $message->params = json_encode($payload['data']);
                $message->created_by = $created_by;
                $message->save();
            }
            return $results;
        }
        return -1;   // no need to push
    }

    public static function sendToAll($payload)
    {
        $userDevice = UserDevice::where('status', 'approved');
        $responseCode = FcmService::sendMultiple($userDevice->pluck('reg_id')->toArray(), $payload);
        return $responseCode;
    }
}
