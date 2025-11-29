<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard DSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Variabel Warna */
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; } 
        
        /* General Styling */
        body { font-family: 'Inter', sans-serif; background-color: #f7f7f7; } 
        .dashboard-container { 
            max-width: 900px; 
            margin: 30px auto; 
            background-color: #fff; 
            padding: 20px 30px 40px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
        }

        /* Header Box Kuning */
        .header-box { 
            background-color: var(--im3-yellow); 
            color: #333; 
            padding: 20px; 
            border-radius: 10px; 
            position: relative; 
            overflow: hidden; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        /* Lingkaran Merah */
        .header-box::before { 
            content: ''; 
            position: absolute; 
            top: -50px; 
            right: -10px; 
            width: 100px; 
            height: 100px; 
            background-color: var(--im3-red); 
            border-radius: 50%; 
        }
        .header-box::after { 
            content: ''; 
            position: absolute; 
            top: 20px; 
            right: 80px; 
            width: 30px; 
            height: 30px; 
            background-color: var(--im3-red); 
            border-radius: 50%; 
        }
        
        /* Styling Kotak Tanggal & Jam di dalam Header */
        .datetime-box {
            display: inline-block;
            background-color: white;
            color: #333;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.875rem; 
            font-weight: 600; 
        }
        
        /* Grid Menu */
        .menu-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 20px; 
            margin-top: 30px; 
        }
        
        /* Menu Item */
        .menu-item { 
            display: flex; 
            flex-direction: column; 
            align-items: flex-start; 
            justify-content: flex-start; 
            background-color: #fff; 
            border: 1px solid #e0e0e0; 
            padding: 20px; 
            border-radius: 10px; 
            text-decoration: none; 
            color: #333; 
            transition: all 0.2s ease; 
        }
        /* Style Hover/Active */
        .menu-item:hover, .menu-item.active { 
            background-color: var(--im3-red); 
            color: white;
            border-color: var(--im3-red);
            box-shadow: 0 4px 8px rgba(226, 27, 33, 0.3); 
        }
        /* Ikon menu item */
        .menu-item-icon { 
            color: var(--im3-red); 
            margin-bottom: 10px; 
        }
        /* Ikon saat di-hover/active harus putih */
        .menu-item:hover .menu-item-icon, .menu-item.active .menu-item-icon {
            color: red;
        }
        
        /* CSS Tombol Logout di Footer */
        .logout-btn-footer {
            /* width: 100%; // Hapus atau jadikan auto */
            display: inline-flex; /* Agar ikon dan teks sejajar */
            align-items: center; /* Pusatkan vertikal */
            padding: 10px 18px; /* Sesuaikan padding */
            margin-top: 30px; 
            background-color: var(--im3-red); 
            color: white;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.2s;
            /* text-align: center; // Tidak perlu karena inline-flex */
            border: none; 
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .logout-btn-footer:hover {
            background-color: #b71c1c; 
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif
        <div class="flex items-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800">Dashboard</h1>
        </div>
        
        <div class="header-box">
    <h2 class="text-xl font-extrabold mb-1 relative z-10">Selamat Datang, {{ Auth::user()->id_dse ?? 'CSOB-BJM1' }}!</h2>
    <p class="text-sm relative z-10 font-semibold">Harap lakukan pencatatan stok dan retur hari ini</p>

    <div class="flex space-x-2 mt-3 relative z-10 text-xs">
        <div id="date-display" class="datetime-box">
            {{ \Carbon\Carbon::now('Asia/Makassar')->locale('id')->translatedFormat('l, d F Y') }}
        </div>
        <div id="time-display" class="datetime-box">
            {{ \Carbon\Carbon::now('Asia/Makassar')->format('H:i:s') }}
        </div>
    </div>
</div>

        <div class="menu-grid">
            
            <a href="{{ route('dse.input_stok') }}" class="menu-item"> <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                </div>
                <span class="font-semibold text-base">Pencatatan Stok Harian</span>
            </a>

            <a href="{{ route('dse.input_outlet') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <span class="font-semibold text-base">Data Validasi Outlet</span>
            </a>

            <a href="{{ route('dse.input_retur') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <span class="font-semibold text-base">Pencatatan Retur Harian</span>
            </a>

            <a href="{{ route('dse.riwayat_pencatatan') }}" class="menu-item">
                <div class="menu-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                </div>
                <span class="font-semibold text-base">Riwayat Pencatatan</span>
            </a>

        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            {{-- Menggunakan class yang benar --}}
            <button type="submit" class="logout-btn-footer">LOGOUT</button>
        </form>

    </div>

    <script>
        // Zona waktu yang digunakan (WITA: Asia/Makassar)
        const TIME_ZONE = 'Asia/Makassar'; 
        
        function updateTime() {
            const now = new Date();
            const timeOptions = { 
                hour: '2-digit', 
                minute: '2-digit', 
                hour12: false,
                timeZone: TIME_ZONE 
            };
            const formattedTime = now.toLocaleTimeString('id-ID', timeOptions) + ' WITA';
            
            document.getElementById('time-display').textContent = formattedTime.replace('.', ':');
        }

        updateTime(); 
        setInterval(updateTime, 1000); 
    </script>
</body>
</html>