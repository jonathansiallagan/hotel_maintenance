<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Attempt to reset the user's password.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                // FITUR TAMBAHAN: Auto Login setelah reset password
                Auth::login($user);
            }
        );

        // Jika Reset Berhasil
        if ($status == Password::PASSWORD_RESET) {

            // Cek Role user yang sedang login untuk menentukan arah redirect
            $role = Auth::user()->role;

            $route = match ($role) {
                'technician' => 'technician.dashboard',
                'admin'      => 'admin.dashboard',
                default      => 'staff.dashboard',
            };

            return redirect()->route($route)->with('status', __($status));
        }

        // Jika Gagal
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
