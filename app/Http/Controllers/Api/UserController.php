<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use App\Http\Resources\Api\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
     {
        $this->middleware('CheckRole:admin')->except('userProfile');
     }

    public function index()
    {
        $user = User::with('giftCards', 'wallet')->get();
        return response()->json(UserResource::collection($user), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::whereId($id)->whereStatus(true)->with('giftCards', 'wallet')->get();
        return response()->json($user, 200);
    }

    /**
     * Display the User Profile.
     */
    public function userProfile()
    {
        $user = auth()->user(); 
        return new UserResource($user);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::find($id);
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        // Add Password to $data
        trim($request->password) != '' ? $data['password'] = Hash::make($request->password) : '';

        $user->update($data);
        return response()->json([
            'message' => 'User successfully Updated',
            'user' => $user
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::find($id)->delete();
        return response()->json([
            'message' => 'User Successfully Deleted'
        ], 400);
    }
}
