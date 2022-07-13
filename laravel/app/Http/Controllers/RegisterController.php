<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illiminate\Support\Facades\Hash;
use App\Http\Resources\UserRessource;
class RegisterController extends Controller
{
    public function postRegister(Request $request)
    {
        

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->isActive = $request->isActive;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $us=User::where('email',$request->email)->first();
        if($us){
            $response['message'] = 'user already exist';
            $response['code'] = 201;
            $response['user'] = $us;
        }

        else if($user->save()){
            $response['message'] = 'user Added';
            $response['code'] = 200;
            $response['user'] = $user;
        }
        else{
            $response['message'] = 'failed to add';
            $response['code'] = 202;
            $response['user'] = $user; 
        }
        return $response;
   
        return response()->json($response);

    }
}
