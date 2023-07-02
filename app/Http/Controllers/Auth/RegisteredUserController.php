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
        $request->validate([
            'login' => 'required|string|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'nullable|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $registerData = [
            'login' => $request->login,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthday' => $request->birthday,
            'password' => Hash::make($request->password),
        ];

        if ($request->birthday) {
            $registerData['birthday'] = $request->birthday;
        }

        $user = User::create($registerData);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
