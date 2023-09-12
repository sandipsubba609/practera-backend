<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        try {
            $request->validate([
                "lastName" => "required|string",
                "firstName" => "required|string",
                "state" => "required|string",
                "city" => "required|string",
                "contactNumber" => "required|string",
                "email" => "required|string|unique:users,email|confirmed",
                "password" => "required|string|confirmed",
            ]);

            $user = User::create([
                'last_name' => $request['lastName'],
                'first_name' => $request['firstName'],
                'name' => $request['firstName'] . ' ' . $request['lastName'],
                'country' => "Australia",
                'state' => $request['state'],
                'city' => $request['city'],
                'contact_number' => $request['contactNumber'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token,
            ];

            return response($response, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            "email" => "required|string|email",
            "password" => "required|string",
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (empty($user) || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Invalid Password',
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 200);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }
}
