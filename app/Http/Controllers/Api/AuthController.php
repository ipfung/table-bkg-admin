<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    // due to VoyagerAdminMiddleware where checks browse_admin, so normal user couldn't logout successfully.

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if ($request->input('loginMethod') == 'phone') {
            $field = 'mobile_no';
        } else {
            $field = 'email';
        }
        if(Auth::attempt([$field => $request->input($field), 'password' => $request->password, 'status' => 'active'])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;
            $success['email'] =  $user->email;
            $success['avatar'] =  $user->avatar;
            $success['color'] =  $user->role->color_name;

            return $success;
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Invalid login details'
            ], 401);
        }
    }
}
