<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        // print_r($request->all());
        // die;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required'],  // Add this line
        ]);

         // Generate and assign unique empuniq_id
         $lastId = User::max('id') ?? 0;

        $userempuniq_id = 'EMP' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,  // Add this line
            'empuniq_id' => $userempuniq_id, 
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($user->role == 'employer') {
            return redirect()->route('dashboard.employer');  // Employer dashboard
        } elseif( in_array($user->role,['team_leader', 'manager'])){
            return redirect()->route('dashboard.team_leader');  // Employer dashboard
        }else{
            return redirect()->route('dashboard.employee');  // Employee dashboard  
        }

        //return redirect(RouteServiceProvider::HOME);
        // If login failed, redirect back with an error message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
