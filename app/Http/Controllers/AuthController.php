<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255|min:3',
            'role' => 'required|string|in:user,admin',
            'email' => 'required|string|email|min:3|max:50|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }

        User::created([
            'title' => $request->title,
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email|min:3|max:50',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attemp($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            return response()->json(compact('token'));
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
    }

    public function getAuthenticatedUser(){
        $user = Auth::user();
        return response()->json($user);
    }

    public function logout(Request $request){
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'User successfully signed out']);
    }


    //
}
