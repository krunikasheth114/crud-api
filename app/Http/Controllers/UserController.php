<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'firstname' => ['required'],
            'lastname' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required'],
            'gender' => ['required'],
            'password' => ['required', 'min:6'],
            'lang_id' => ['required', 'min:1']
        ]);


        $array = explode(',', $validatedData['lang_id']);

        $json = json_encode($array);

        $user = User::create([
            'firstname' => $validatedData['firstname'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'mobile' => $validatedData['mobile'],
            'gender' => $validatedData['gender'],
            'lang_id' => $json,
            'password' => bcrypt($validatedData['password']),
        ]);

        $token = $user->createToken('CrudApp')->accessToken;

        return response()->json(['message' => "Registeration SuccessFull", 'token' => $token, 'user' => $user], 200);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('CrudApp')->accessToken;
            return response()->json([
                'message' => "Logged in successfully",
                'token' => $token,
                'user' => $user
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }


    public function logout(Request $request)
    {

        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    //get languages
    public function languages()
    {
        $lang = Language::all();
        return response()->json(['languages' => $lang]);
    }

    public function getUsers(Request $request)
    {
        $getUsers = User::where('id', '!=', Auth::user()->id)->get();
        return response()->json(['Users' => $getUsers]);
    }
}
