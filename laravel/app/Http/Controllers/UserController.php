<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserRessource;
use Illiminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);
        return UserRessource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->isActive = $request->isActive;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $us=User::where('email',$request->email)->first();
        if($us){
           
            $response['message'] = 'email existe deja';
            $response['code'] = 201;
            $response['user'] = $user; 
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return new UserRessource($user  );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->isActive = $request->isActive;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        if($user->save()){
            $response['message'] = 'user updated';
            $response['code'] = 200;
            $response['user'] = $user;
        }
        else{
            $response['message'] = 'failed to add';
            $response['code'] = 202;
            $response['user'] = $user; 
        }
        return $response;
    }


    //update user
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if($user->delete()){
            $response['message'] = 'user deleted';
            $response['code'] = 200;
            $response['user'] = $user;
        }
        return $response;
    }



    public function Login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!JWTAuth::attempt($credentials)) {
                $response['status'] = 0;
                $response['code'] = 401;
                $response['data'] = null;
                $response['message'] = 'email or password is incorrect';
                return response()->json($response);
            }
        } catch (JWTException $e) {
            $response['data'] = null;
            $response['code'] = 500;
            $response['message'] = 'could not create token';
            return response()->json($response);
        }
        $user = auth()->user();
        $data['token'] = auth()->claims([
            'user_id' => $user->id,
            'email' => $user->email,
            'name'=> $user->name,
            'isActive'=> $user->isActive,
            'role'=> $user->role,
        ])->attempt($credentials);

        $response['data'] = $data;
        $response['status'] = 1;
        $response['code'] = 200;
        $response['message'] = 'login successfully';
        return response()->json($response);
    }


    

    //update profile with token refresh
    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->isActive = $request->isActive;
        $user->role = $request->role;
        if($user->save()){
            $user = auth()->user();
            $data['token'] = auth()->claims([
                'user_id' => $user->id,
                'email' => $user->email,
                'name'=> $user->name,
                'isActive'=> $user->isActive,
                'role'=> $user->role,
            ])->attempt($credentials);
    
            $response['data'] = $data;
            $response['status'] = 1;
            $response['code'] = 200;
            $response['message'] = 'login successfully';
            return response()->json($response);
        }
        else{
            $response['message'] = 'failed to update';
            $response['code'] = 202;
            $response['user'] = $user; 
        }
        return $response;
    }
}
