<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $field = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);
        $user = User::create($field);
        return [
            'user' =>$user
        ];
    }//
    public function login(Request $request){
        $request->validate([
            'name' => 'required|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('name', $request->name)->first();

        if (!$user||!Hash::check($request->password, $user->password)){
            return [
                'message'=> 'the provided credentials are incorrect'
            ];
        }
        $token = $user->createToken($user->name);
        return [
            'user' =>$user,
            'token' => $token->plainTextToken
        ];        
    }//
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message' => 'you are logged out.'
        ];
    }//
}
