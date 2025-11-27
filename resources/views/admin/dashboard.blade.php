<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; }
        body { font-family: 'Inter', sans-serif; background-color: #f5f5f5; }
        .dashboard-container { max-width: 900px; margin: 30px auto; background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header-box { background-color: var(--im3-yellow); color: #333; padding: 25px; border-radius: 10px; position: relative; overflow: hidden; margin-bottom: 25px; }
        .header-box::before { content: ''; position: absolute; top: -50px; right: -50px; width: 120px; height: 120px; background-color: var(--im3-red); border-radius: 50%; opacity: 0.8; }
        .header-box::after { content: ''; position: absolute; top: 10px; right: 80px; width: 50px; height: 50px; background-color: var(--im3-red); border-radius: 50%; opacity: 0.8; }
        
        /* TAMBAHKAN INI - Styling Kotak Tanggal & Jam */
        .datetime-box {
            display: inline-block;
            background-color: white;
            color: #333;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.875rem; 
            font-weight: 600; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Grid 2x2 untuk 4 Menu (Sesuai Mockup) */
        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 30px; } 
        
        .menu-item { display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #fff; border: 1px solid #e2e2e2; padding: 25px 15px; border-radius: 10px; text-align: center; text-decoration: none; color: #333; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .menu-item:hover { background-color: var(--im3-red); color: white; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .menu-item-icon { color: #5a5a5a; margin-bottom: 10px; }
        .menu-item:hover .menu-item-icon { color: white; }
        .logout-btn { background-color: #dc3545; color: white; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="flex items-center mb-8">
            <a href="{{ route('admin.dashboard') }}" class="text-2xl text-gray-500 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h1 class="text-3xl font-extrabold">Dashboard Admin</h1>
        </div>
        
        <!-- Header Kuning Dinamis -->
        <div class="header-box">
            <h2 class="text-2xl font-extrabold mb-1 relative z-10">Selamat Datang, {{ Auth::guard('shared')->user()->username ?? 'Admin' }}!</h2>
            <p class="text-sm relative z-10">Monitoring pencatatan stok hari ini.</p>
            
            <!-- TAMBAHKAN CLASS datetime-box DI SINI -->
            <div class="flex space-x-4 mt-3 relative z-10 font-bold text-sm">
                <span id="date-display" class="datetime-box">
                    {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }}
                </span>
                <span id="time-display" class="datetime-box">
                    {{ \Carbon\Carbon::now('Asia/Makassar')->format('H:i') }}
                </span>
            </div>
        </div>
        
        <h2 class="text-xl font-bold mt-8 mb-4">Menu Monitoring & Data</h2>
        
        <div class="menu-grid">
            
            <!-- 1. Pencatatan Stok Harian (Monitoring) -->
            <a href="{{ route('admin.view_stok') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10h2a2 2 0 002-2V9m0 10h4a2 2 0 002-2V7a2 2 0 00-2-2h-4m-4 5H9m0 6h.01" /></svg></div>
                <span class="font-semibold text-base">Pencatatan Stok Harian</span>
            </a>

            <!-- 2. Data Validasi Outlet (Monitoring/Validasi) -->
            <a href="{{ route('admin.view_outlet') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg></div>
                <span class="font-semibold text-base">Data Validasi Outlet</span>
            </a>
            
            <!-- 3. Pencatatan Retur Harian (Monitoring) -->
            <a href="{{ route('admin.view_retur') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg></div>
                <span class="font-semibold text-base">Pencatatan Retur Harian</span>
            </a>

            <!-- 4. Riwayat Pencatatan (Lihat Log) -->
            <a href="{{ route('admin.riwayat_pencatatan') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg></div>
                <span class="font-semibold text-base">Riwayat Pencatatan</span>
            </a>
            
        </div>
        
        <form id="logout-form-admin" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <button onclick="document.getElementById('logout-form-admin').submit();" type="submit" class="logout-btn">Logout</button>
    </div>
</body>
</html>