<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request){
        $field = $request->validated();
        $user = User::create($field);
        return [
            'user' =>$user
        ];
    }//
    public function login(Request $request){
        $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('name', $request->name)->first();

        if (!$user){
            return new Response('Username salah silahkan coba lagi', 418);
        } else if(!Hash::check($request->password, $user->password)){
            return new Response('Password salah silahkan coba lagi', 418);
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
            'message' => 'berhasil log out.'
        ];
    }//
}
