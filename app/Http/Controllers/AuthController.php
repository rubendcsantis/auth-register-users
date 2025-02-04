<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    public function index(){
        $users = User::all();

        if($users->isEmpty()){
            return response()->json(['message'=>'No posts found.'],404);
        }

        return response()->json($users);
    }

    public function updateUser(Request $request, $id){
        $user = User::find($id);

        if(is_null($user)){
            return response()->json(['message'=>'Post not found.'],404);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255|min:3',
            'role' => 'required|string|in:user,admin',
            'email' => 'required|string|email|min:3|max:50',
            // TODO validate profile
            // 'password' => 'required|string|min:6|confirmed',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }

        if ($request->has('name')) {
            $user->name = $request->get('name');
        }
        if ($request->has('email')) {
            $user->email = $request->get('email');
        }
        if ($request->has('role')) {
            $user->role = $request->get('role');
        }

        $user->update();

        return response()->json(['message' => 'User updated successfully.']);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255|min:3',
            'role' => 'required|string|in:user,admin',
            'email' => 'required|string|email|min:3|max:50|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }

        User::create([
            'name' => $request->name,
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
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 401);
            }

            return  $this->respondWithToken($token);
//            return response()->json(compact('token'));
        } catch (JWTException $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function getAuthenticatedUser(){
        $user = auth()->user();
        return response()->json($user);
    }

    public function logout(Request $request){
        auth()->invalidate();
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    //
}
