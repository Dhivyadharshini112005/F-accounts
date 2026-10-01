<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends Controller
{

    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {

        $request->validate([
            'username'=>'required',
            'password'=>'required'
        ]);


        $user = User::where(
            'username',
            $request->username
        )->first();


        if($user && Hash::check(
            $request->password,
            $user->password
        ))
        {

            Auth::login($user);


            if($user->role == 'OWNER')
            {
                return redirect('/dashboard');
            }


            if($user->role == 'MANAGER')
            {
                return redirect('/dashboard');
            }

        }


        return back()->with(
            'error',
            'Invalid username or password'
        );

    }



    public function logout()
    {

        Auth::logout();

        return redirect('/login');

    }

}