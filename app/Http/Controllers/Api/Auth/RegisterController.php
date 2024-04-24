<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(UserRequest $request) {

        $data = $request->validated();
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);
        
        UserWallet::create([
            'user_id' => $user->id,
            'balance' => 0.00,
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
}
