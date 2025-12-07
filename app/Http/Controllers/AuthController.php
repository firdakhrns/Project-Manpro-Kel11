<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SharedLogin;

class AuthController extends Controller
{
    /**
     * Menampilkan form login universal
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login universal - otomatis detect role & redirect
     */
    public function login(Request $request)
{
    $request->validate([
        'dse_id' => 'required|string',
        'password' => 'required|string',
    ]);

    $identifier = $request->dse_id;

    $user = User::where('id_dse', $identifier)->first();
    if ($user && Hash::check($request->password, $user->password)) {
        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        return redirect()->route('dse.dashboard')->with('success', 'Login DSE berhasil!');
    }

    $admin = SharedLogin::where('username', $identifier)->first();
    if ($admin && Hash::check($request->password, $admin->password)) {
        Auth::guard('shared')->login($admin);
        $request->session()->regenerate();
        
        if ($admin->role === 'Admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Login Admin berhasil!');
        } elseif ($admin->role === 'CSE') {
            return redirect()->route('cse.dashboard')->with('success', 'Login CSE berhasil!');
        } elseif ($admin->role === 'Manajer') {
            return redirect()->route('cse.dashboard')->with('success', 'Login Manajer berhasil!');
        }
    }

    return back()->withErrors([
        'dse_id' => 'ID DSE/Username atau password salah.',
    ])->withInput();
}

    /**
     * Proses logout universal
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('shared')->logout(); 

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}