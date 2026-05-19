<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\SignUpRequest;

class SignUpController extends Controller
{
    public function index()
    {
        return view('sign-up.index', [
            'navbar_active' => 'sign-up',
            'page_title' => 'Sign Up —'
        ]);
    }

    public function signUp(SignUpRequest $request)
    {
        $validated = $request->safe()->only(['username', 'email', 'password']);

        $user = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        auth()->login($user);

        return redirect('/');
    }
}