<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Handle the admin login attempt.
     */
   public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if ($validator->fails()) {
        return redirect('/login')->withErrors($validator)->withInput();
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // This line sends the user to the dashboard
        return redirect()->intended('/dashboard');
    }

    return redirect('/login')->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->withInput();
}
    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}