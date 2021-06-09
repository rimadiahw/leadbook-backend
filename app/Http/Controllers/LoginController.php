<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //
    //login API
    public function login(Request $request){
        //check validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        //show response is validation fail
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        //select user data
        $user = User::where('email', $request->email)->first();

        //check if password or user (email) is false
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'success'   => false,
                'message'   => 'Login Failed'
            ]);
        }

        //create token 
        $token = $user->createToken('authTokens')->accessToken;

        //return api
        return response()->json([
            'success'   => true,
            'message'   => 'Login success',
            'data'      => $user,
            'token'     => $user->createToken('authTokens')->accessToken,
        ]);
    }

    //logout API
    public function logout(Request $request){
        //get token data
        $removeToken = $request->user()->tokens()->delete();

        //remove token and shoe return
        if($removeToken){
            return response()->json([
                'success'   => true,
                'message'   => 'Logout success'
            ]);
        }
    }
}
