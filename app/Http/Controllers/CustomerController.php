<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Response;
use App\User;
use Validator;

class CustomerController extends Controller
{
    public $successStatus = 200;
    public $clientErrorStatus = 400;
    public $notFoundStatus = 404;
    public $serverErrorStatus = 500;


    /**
     * Customer Profile
     *
     * @return \Illuminate\Http\Response
     */
    public function getUser()
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'status' => 200,
                'user' => $user
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'error' => 'Please login to view this resource'
            ]);
        }
    }

    /**
     * Edit Profile
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function editProfile(Request $request, $userId)
    {
        if (!is_numeric($userId)) {
            return response()->json([
                'status' => $this->clientErrorStatus,
                'message' => 'UserId must be an integer',
            ]);
        }
        $integerId = (int) $userId;
        $user = DB::table('users')->where('id', $integerId)->first();
        if (!$user) {
            return response()->json([
                'status' => $this->clientErrorStatus,
                'message' => 'User with given Id does not exist',
            ]);
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'state' => 'nullable',
                    'city' => 'nullable',
                    'address' => 'nullable',
                    'phone' => 'nullable',
                ]
            );
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            } else {
                $formFields = $request->all();
                if ($formFields['state'] === null) {
                    $formFields['state'] = $user->state;
                };
                if ($formFields['city'] === null) {
                    $formFields['city'] = $user->city;
                };
                if ($formFields['address'] === null) {
                    $formFields['address'] = $user->address;
                };
                if ($formFields['phone'] === null) {
                    $formFields['phone'] = $user->phone;
                };
                $userr = User::findorfail($integerId);
                $updatedUser = $userr->update($formFields);
                if ($updatedUser) {
                    return response()->json([
                        'status' => $this->successStatus,
                        'message' => 'User updated successfuly',
                    ]);
                } else {
                    return response()->json([
                        'status' => $this->serverErrorStatus,
                        'error' => 'Opps! An error occured.',
                    ]);
                }
            }
        }
    }
}
