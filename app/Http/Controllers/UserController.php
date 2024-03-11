<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'role' => 'string'
        ]);

        $input = $request->all();

        $validationRules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
            'role' => 'string'
        ];

        $validator = Validator::make($input, $validationRules);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $plainPassword = $request->input('password');
        $user->role = $request->input('role');
        $user->password = app('hash')->make($plainPassword);
        $user->save();

        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $validationRules = [
            'email' => 'required|string',
            'password' => 'required|string',
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_id' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
