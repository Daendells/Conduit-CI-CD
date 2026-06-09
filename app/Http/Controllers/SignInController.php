<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SignInController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect('/');
        }

        return view('sign-in.index', [
            'navbar_active' => 'sign-in',
            'page_title' => 'Sign In —',
        ]);
    }

    public function signIn(SignInRequest $request)
    {
        $validated = $request->safe()->only(['email', 'password']);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return back()->withErrors(['email' => 'Email and password did not match'])->withInput();
        }

        auth()->login($user);

        return redirect('/');
    }

    public function logout()
    {
        auth()->logout();

        return redirect('/');
    }
}
