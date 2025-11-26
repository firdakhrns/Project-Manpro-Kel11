<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon; // Import Carbon untuk manipulasi waktu

class HomeController extends Controller
{
    /**
     * Tentukan salam berdasarkan waktu hari (Pagi, Siang, Sore, Malam).
     */
    protected function getGreeting()
    {
        // Ambil jam saat ini di zona waktu server
        $hour = Carbon::now()->hour;

        if ($hour >= 5 && $hour < 11) {
            return 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            return 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            return 'Selamat Sore';
        } else {
            return 'Selamat Malam';
        }
    }

    /**
     * Tampilkan dashboard DSE.
     */
    public function index()
    {
        // Pastikan user sedang login dengan guard 'web' (security check)
        if (Auth::guard('web')->check()) {
            $dseUser = Auth::guard('web')->user();
            
            // 1. Ambil Salam Dinamis
            $greeting = $this->getGreeting();
            
            // 2. Tentukan Tanggal Hari Ini untuk View
            $todayDate = Carbon::now()->isoFormat('dddd, D MMMM YYYY');
            $currentTime = Carbon::now()->isoFormat('HH.mm');

            // 3. Kembalikan View dengan data yang dibutuhkan
            return view('dse.dashboard', compact('dseUser', 'greeting', 'todayDate', 'currentTime'));
        }
        
        // Jika tidak login, redirect ke halaman login
        return redirect()->route('login');
    }
}