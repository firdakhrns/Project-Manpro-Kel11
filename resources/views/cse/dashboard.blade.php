<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Manajer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* IM3 Color Palette */
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; }
        body { font-family: 'Inter', sans-serif; background-color: #f5f5f5; }
        .dashboard-container { max-width: 900px; margin: 30px auto; background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        /* Header Box Custom Style */
        .header-box { 
            background-color: var(--im3-yellow); 
            color: #333; 
            padding: 25px; 
            border-radius: 10px; 
            position: relative; 
            overflow: hidden; 
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-box::before { content: ''; position: absolute; top: -50px; right: -50px; width: 120px; height: 120px; background-color: var(--im3-red); border-radius: 50%; opacity: 0.8; }
        .header-box::after { content: ''; position: absolute; top: 10px; right: 80px; width: 50px; height: 50px; background-color: var(--im3-red); border-radius: 50%; opacity: 0.8; }
        
        /* Style untuk Tanggal dan Jam di Header */
        .datetime-badge {
            background-color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
            margin-right: 5px;
            font-size: 0.875rem;
        }
        
        .form-link { 
            background-color: white; 
            border: 2px solid var(--im3-red); 
            padding: 10px 15px; 
            border-radius: 8px; 
            text-decoration: none; 
            color: var(--im3-red); 
            font-weight: bold; 
            transition: all 0.2s ease; 
            position: relative; 
            z-index: 10; 
            white-space: nowrap;
            display: flex;
            align-items: center;
        }

        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 30px; }
        .menu-item { display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #fff; border: 1px solid #e2e2e2; padding: 25px 15px; border-radius: 10px; text-align: center; text-decoration: none; color: #333; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .menu-item:hover { background-color: var(--im3-red); transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .menu-item-icon { color: #5a5a5a; margin-bottom: 10px; }
        .logout-btn { background-color: #dc3545; color: white; padding: 8px 15px; border-radius: 5px; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="flex items-center mb-8">
            <h1 class="text-3xl font-extrabold">Dashboard Manajer</h1>
        </div>
        
        <div class="header-box">
            <div class="relative z-10">
                @php 
                    $sharedUser = Auth::guard('shared')->user();
                @endphp
                <h2 class="text-2xl font-extrabold mb-1">Selamat Datang Manajer!</h2>
                <p class="text-sm">Silakan akses laporan dan evaluasi DSE regional hari ini</p>
                
                <div class="flex space-x-2 mt-3 text-sm">
                    <span id="date-display" class="datetime-badge">Senin, 24 November 2025</span>
                    <span id="time-display" class="datetime-badge">09.00 WITA</span>
                </div>
            </div>
            
            <a href="{{ route('cse.kritik_saran') }}" class="form-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Form Masukan dan Saran DSE
            </a>
        </div>

        <div class="menu-grid">
            
            <a href="{{ route('cse.view_stok') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10h2a2 2 0 002-2V9m0 10h4a2 2 0 002-2V7a2 2 0 00-2-2h-4m-4 5H9m0 6h.01" /></svg>
                </div>
                <span class="font-semibold text-base">Pencatatan Stok Harian</span>
            </a>

            <a href="{{ route('cse.view_retur') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg></div>
                <span class="font-semibold text-base">Pencatatan Retur Harian</span>
            </a>

            <a href="{{ route('cse.view_outlet') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg></div>
                <span class="font-semibold text-base">Data Validasi Outlet</span>
            </a>

            <a href="{{ route('cse.view_performa') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg></div>
                <span class="font-semibold text-base">Data Hasil Performa DSE</span>
            </a>
            
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <button onclick="document.getElementById('logout-form').submit();" type="submit" class="logout-btn">LOGOUT</button>
    </div>
    
    <script>
        // JS untuk memperbarui jam real-time
        function updateTime() {
            const now = new Date();
            
            // Tanggal
            const dateOptions = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            const formattedDate = now.toLocaleDateString('id-ID', dateOptions);
            
            // Waktu (misalnya WITA)
            const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: false };
            const formattedTime = now.toLocaleTimeString('id-ID', timeOptions).replace(/\./g, ':') + ' WITA';
            
            const dateElement = document.getElementById('date-display');
            const timeElement = document.getElementById('time-display');
            
            if (dateElement) dateElement.textContent = formattedDate;
            if (timeElement) timeElement.textContent = formattedTime;
        }

        updateTime(); 
        setInterval(updateTime, 1000); 
    </script>
</body>
</html>