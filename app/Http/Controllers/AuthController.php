<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255|min:3',
            'role' => 'required|string|in:user,admin',
            'email' => 'required|string|email|min:3|max:50|unique:users,email',
            'password' => 'required|string|min:6|max:50|confirmed',
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
    //
}
