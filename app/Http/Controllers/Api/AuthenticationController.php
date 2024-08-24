<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthenticationController extends Controller
{
    public function store()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            // successfull authentication
            $user = User::find(Auth::user()->id);

            $user_token['token'] = $user->createToken('appToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $user_token,
                'user' => $user,
            ], 200);
            
        } else {
            // failure to authenticate
            return response()->json([
                'success' => false,
                'message' => 'Invalid Credentials !',
            ], 401);
        }
    }


    public function destroy(Request $request)
    {
        if (Auth::user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        }
    }

    public function createUser(Request $request)
    {
    
        $validation = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [Password::min(6)->mixedCase()->letters()->numbers()->symbols(), 'confirmed'],
        ]);


        if($validation->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validation->messages()
            ]);
        }else{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                // successfull authentication
                
    
                $user_token['token'] = $user->createToken('appToken')->accessToken;
    
                return response()->json([
                    'success' => true,
                    'token' => $user_token,
                    'user' => $user,
                ], 200);
            }else {
                // failure to authenticate
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authenticate.',
                ], 401);
            }

        }
        
    }

    public function refresher(Request $request)
    {
        if(Carbon::parse($request->user()->token()->expires_at) < Carbon::now()){
            return response([
                'status' => true
            ]);
        }
        return response([
            'status' => false
        ]);
    }
}
