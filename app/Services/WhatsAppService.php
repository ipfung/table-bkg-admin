<?php

namespace App\Services;

use App\Models\NotifyMessage;
use App\Models\WhatsApp\MessageTemplate;

class WhatsAppService
{
    const URL = 'https://graph.facebook.com/v15.0/';

    /**
     * @param $route
     * @param $method
     * @param $data
     * @param bool $authorize
     *
     * @return mixed
     */
    public function sendRequest($route, $method, $data = null, $authorize = true)
    {
        $ch = curl_init($route);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $accessToken     = config("app.jws.whatsapp.access_token");

        if ($authorize) {
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json'
                ]
            );
        }


        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);

        $response = json_decode($response, true);

        curl_close($ch);
        return $response;
    }

    /**
     * @param $to
     * @param $template
     * @param $components
     * @param string $languageCode
     *
     * @return mixed
     */
    public function send($to, $template, $components, $languageCode)
    {
        $whatsAppPhoneId = config("app.jws.whatsapp.phone_number_id");

        $route = self::URL . $whatsAppPhoneId . '/messages';

        $to   = str_replace('+', '', $to);
        $data = [
            "messaging_product" => "whatsapp",
            'to'            => $to,
            "type"          =>  "template",
            "template"      => [
                "name"  => $template,
                "language" => [
                    "code"  => $languageCode
                ],
                "components" => $components
            ]
        ];
//        echo 'whatsapp data=' . json_encode($data);

        return $this->sendRequest($route, 'POST', $data);
    }

    /**
     * "Text" can be sent only if recipient(human?) did reply in the conversation. Tested 20221114.
     * It means this is not suitable for sending approval...etc messages.
     *
     * @param $to
     * @param string $text message body
     * @param bool $previewUrl
     *PUSER1
     * @return mixed
     */
    public function sendText($to, $text, $previewUrl = false)
    {
        $whatsAppPhoneId = config("app.jws.whatsapp.phone_number_id");

        $route = self::URL . $whatsAppPhoneId . '/messages';

        $to   = str_replace('+', '', $to);
        $data = [
            "messaging_product" => "whatsapp",
            "recipient_type"    => "individual",
            'to'            => $to,
            "type"          =>  "text",
            "text"      => [
                "body"  => $text,
                "preview_url" => $previewUrl
            ]
        ];

        return $this->sendRequest($route, 'POST', $data);
    }

    public function sendAndLog($sendTo, $template, $placeholders, $language = 'zh_HK', $logNotificationId = null) {
        $placeholders = $this->getComponentData($template, $placeholders);
//echo 'sendAndLog=' . json_encode($placeholders);
//echo 'whatsapp_tpl=' . $template['whatsapp_tpl'];
        $apiResponse = $this->send(
            $sendTo,
            $template['whatsapp_tpl'],
            $placeholders,
            $language
//                $language ?: $defaultLanguage
        );
//        if (!empty($apiResponse['messages'])) {
//            if ($log_to_db) {
//                $message = new NotifyMessage;
//                $message->customer_id = $recipient->id;
//                $message->title = 'Error: ' . $payload['title'];
//                $message->body = json_encode($apiResponse['messages']) . ': ' . $payload['body'];
//                $message->params = json_encode($payload['data']);
//                $message->created_by = $created_by;
//                $message->save();
//            }
//        } else if ($apiResponse['error']) {
//            // requested language doesn't exist for this template, try with default language
//            if ($apiResponse['error']['code'] === 132001 && !empty($language)) {
////                    $apiResponse = $whatsAppService->send(
////                        $sendTo,
////                        $template,
////                        $placeholders,
////                        $defaultLanguage
////                    );
//                if (!empty($apiResponse['messages'])) {
//                    if ($log_to_db) {
//                        $message = new NotifyMessage;
//                        $message->customer_id = $recipient->id;
//                        $message->title = 'Error2: ' . $payload['title'];
//                        $message->body = json_encode($apiResponse['messages']) . '2: ' . $payload['body'];
//                        $message->params = json_encode($payload['data']);
//                        $message->created_by = $created_by;
//                        $message->save();
//                    }
//                }
//            }
//        }
        return $apiResponse;
    }

    /**
     * @return mixed
     */
    public function getTemplates()
    {
        $businessId      = config("app.jws.whatsapp.business_account_id");
        $accessToken     = config("app.jws.whatsapp.access_token");

        $route = self::URL . $businessId . '/message_templates?access_token=' . $accessToken;

        return $this->sendRequest($route, 'GET', null, false);
    }

    /**
     * @return mixed
     */
    public function createTemplate(MessageTemplate $messageTemplate)
    {
        $businessId      = config("app.jws.whatsapp.business_account_id");

        $route = self::URL . $businessId . '/message_templates';
        $data = [
            "category"      => $messageTemplate->getCategory(),
            'components'    => $messageTemplate->getComponents(),
            "name"          => $messageTemplate->getName(),
            "language"      => $messageTemplate->getLanguage()
        ];

        return $this->sendRequest($route, 'POST', null, false);
    }

    /**
     * @param $template_name string name
     * @return mixed
     */
    public function deleteTemplate($template_name)
    {
        $businessId      = config("app.jws.whatsapp.business_account_id");

        $route = self::URL . $businessId . '/message_templates?name=' . $template_name;

        return $this->sendRequest($route, 'DELETE', null, false);
    }

    /**
     * @param $notification the notification template object
     * @param $data the placeholder data
     * @return array sorted parameters by index-based
     */
    private function getComponentData($notification, $data)
    {
        $placeholdersBody   = $this->getPlaceholdersObject($notification->content, $data);
        $placeholdersHeader = $notification->subject ? $this->getPlaceholdersObject($notification->subject, $data) : null;

        $components = [];
        if ($placeholdersHeader) {
            $components[] = [
                "type" => "header",
                "parameters" => $placeholdersHeader
            ];
        }
        $components[] = [
            "type" => "body",
            "parameters" => $placeholdersBody
        ];
        return $components;
    }

    /**
     * @param $content
     * @param $data
     * @return array
     */
    private function getPlaceholdersObject($content, $data)
    {
        $parameters   = explode('%', $content);
        $placeholders = [];
        foreach ($parameters as $parameter) {
            $parameter = trim($parameter);
            if (!empty($parameter)) {
                $data[$parameter] = !empty($data[$parameter]) ? $data[$parameter] : ' ';
                $placeholders[]   = [
                    'type'  =>  'text',
                    'text'  =>  $data[$parameter]
                ];
            }
        }
        return $placeholders;
    }

}
