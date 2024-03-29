<?php


namespace App\Http\Controllers\Api;


use App\Facade\PermissionService;
use DateTime;
use DateTimeZone;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    public static $timeFormat = 'h:iA';
    public static $dateTimeFormat = 'Y-m-d H:i:s';
    public static $dateFormat = 'Y-m-d';

    protected function getCurrentDateTime() {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(config("app.jws.local_timezone")));   // must set timezone, otherwise the punch-in time use UTC(app.php) and can't checkin.
        return $now;
    }

    protected function isSuperLevel($user) {
        return PermissionService::isSuperLevel($user);
    }

    protected function isInternalCoachLevel($user) {
        return PermissionService::isInternalCoachLevel($user);
    }

    protected function isExternalCoachLevel($user) {
        return PermissionService::isExternalCoachLevel($user);
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 200)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendPermissionDenied($errorMessages = [], $code = 403)
    {
        $response = [
            'success' => false,
            'message' => "Permission denied.",
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
