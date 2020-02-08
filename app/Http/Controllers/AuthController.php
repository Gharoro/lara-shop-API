<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;
use Validator;

class AuthController extends Controller
{
    public $successStatus = 200;
    public $clientErrorStatus = 400;
    public $notFoundStatus = 404;

    /**
     * Creates a new user
     *
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
                'role' => 'required|in:admin,customer',
                'state' => 'required',
                'city' => 'required',
                'address' => 'required',
                'phone' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        if (User::where('email', '=', $input['email'])->exists()) {
            return response()->json([
                'status' => $this->clientErrorStatus,
                'error' => 'User already exist, please login'
            ]);
        }
        $user = User::create($input);
        return response()->json([
            'status' => $this->successStatus,
            'message' => 'Account created successfuly',
            'user' => $user
        ]);
    }

    /**
     * Logs in a user
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $bearer['token'] =  $user->createToken('AppName')->accessToken;
            return response()->json([
                'status' => $this->successStatus,
                'message' => 'Login successful...',
                'auth' => $bearer
            ]);
        } else {
            return response()->json([
                'status' => $this->notFoundStatus,
                'error' => 'Invalid credentials'
            ]);
        }
    }

    /**
     * Logs in a user
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json([
            'status' => 200,
            'success' => 'Successfully logged out!',
        ]);
    }
}
