<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'success'   => false,
                'message'   => 'Login Failed'
            ]);
        }
        $token = $user->createToken('authTokens')->accessToken;
        return response()->json([
            'success'   => true,
            'message'   => 'Login success',
            'data'      => $user,
            'token'     => $user->createToken('authTokens')->accessToken,
        ]);
    }

    public function logout(Request $request){
        $removeToken = $request->user()->tokens()->delete();

        if($removeToken){
            return response()->json([
                'success'   => true,
                'message'   => 'Logout success'
            ]);
        }
    }
}
