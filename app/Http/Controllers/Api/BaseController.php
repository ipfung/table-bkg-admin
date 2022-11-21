<?php


namespace App\Http\Controllers\Api;


use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    public static $timeFormat = 'h:i';
    public static $dateTimeFormat = 'Y-m-d h:i:s';
    public static $dateFormat = 'Y-m-d';

    protected function getCurrentDateTime() {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone(config("app.jws.local_timezone")));   // must set timezone, otherwise the punch-in time use UTC(app.php) and can't checkin.
        return $now;
    }

    protected function isSuperLevel($user) {
        $super_levels = ['manager', 'admin'];
        return in_array($user->role->name, $super_levels);
    }

    protected function isInternalCoachLevel($user) {
        $super_levels = ['manager', 'admin', 'internal_coach'];
        return in_array($user->role->name, $super_levels);
    }

    protected function isExternalCoachLevel($user) {
        $super_levels = ['manager', 'admin', 'internal_coach', 'external_coach'];
        return in_array($user->role->name, $super_levels);
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
    public function sendError($error, $errorMessages = [], $code = 404)
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
}
