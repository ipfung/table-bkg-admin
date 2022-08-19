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
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 'active'])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;
            $success['avatar'] =  $user->avatar;

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
