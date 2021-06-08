<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Redirect;

class ForgotController extends Controller
{
    //
    public function forgot(Request $request){
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        try {
            $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject($this->getEmailSubject());
            });
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
                case Password::INVALID_USER:
                    return \Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
            }
        } catch (\Swift_TransportException $ex) {
            return response()->json([
                'success'   => false,
                'message'   => $ex->getMessage(),
                'status'    => 400,
                'data'      => []
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success'   => false,
                'message'   => $ex->getMessage(),
                'status'    => 400,
                'data'      => []
            ]);
        }
        
    }

    public function reset(Request $request) {
        $validator = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);
    
        $reset_password_status = Password::reset($validator, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return response()->json([
                'success'   => false,
                'message'   => 'INVALID TOKEN',
                'status'    => 400,
            ]);
        }

        //return $this->respondWithMessage("Password has been successfully changed");
        return response()->json([
            'success'   => TRUE,
            'message'   => 'Password has been successfully changed',
        ]);
    }

    public function redirectVue(){
        $token = $_GET['token'];
        $email = $_GET['email'];
        $url = 'http://localhost:8080/v-reset-password/?token='.$token.'&email='.$email;
        // redirects to http://localhost:8888/www.google.com
        return Redirect::to($url);
    }

}
