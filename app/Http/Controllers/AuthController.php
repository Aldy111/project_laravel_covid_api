<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        /**
         * fitur register
         * ambil input user yang berupa name,email,password
         * input ke dalam database
         */

        $input = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ];

        $user =  User::create($input);

        $data = [
            'message' => 'register is successfully'
        ];

        return response()->json($data, 200);
    }

    public function login(Request $request)
    {
        /**
         * fitur login
         * ambil input email dan password dari user
         * ambil input email dan password dari database berdasarkan email
         * bandingkan emal dan password dari user dengan database
         */

        $input = [
            'email' => $request->email,
            'password' => $request->password
        ];

        $user = User::where('email', $input['email'])->first();

        if ($input['email'] = $user->email && Hash::check($input['password'], $user->password)) {
            $token = $user->createToken('auth_token');

            $data = [
                'message' => 'login is successfully',
                'token' => $token->plainTextToken
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'message' => 'login is invalid'
            ];
            return response()->json($data, 401);
        }
    }
}
