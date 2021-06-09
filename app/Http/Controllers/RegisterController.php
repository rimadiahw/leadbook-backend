<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    //

    public function register(Request $request){
        //check validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6|confirmed',      
        ]);

        //retrun if validation fail
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        //add user data to table and send verification to email user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ])->sendEmailVerificationNotification();

        //return API
        return response()->json([
            'success'   => true,
            'message'   => 'Register success',
            'data'      => $user
        ]);
    }
}
