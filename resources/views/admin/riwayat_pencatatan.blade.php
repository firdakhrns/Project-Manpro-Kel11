<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Global Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; --gray-light: #f8f8f8; }
        
        /* General Styles */
        body { font-family: 'Inter', sans-serif; background-color: var(--im3-yellow); position: relative; }
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container-card { max-width: 95%; min-width: 1200px; margin: 50px auto; background-color: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); position: relative; z-index: 10; }
        
        /* Tombol Kembali Merah Bulat */
        .back-button-circle { background-color: var(--im3-red); color: white; padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); position: absolute; top: 20px; left: 20px; z-index: 11; }
        
        /* JUDUL DIPERBESAR (FIX) */
        h1 { font-size: 3rem; font-weight: 800; color: #333; text-align: center; margin-bottom: 5px; }

        /* Filter & Info Header Styling */
        .filter-header-group { display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 30px; }
        .info-toggle-item { display: inline-flex; align-items: center; border-radius: 20px; overflow: hidden; border: 1px solid var(--im3-red); height: 38px; }
        
        /* Style untuk teks Tanggal dan Waktu (bagian statis) */
        .header-badge-text { 
            padding: 0 12px; 
            font-size: 0.9rem; 
            font-weight: 600; 
            color: white; 
            background-color: var(--im3-red); /* Latar Belakang Merah */
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        /* Perbaikan Style Input Tanggal di Header (FIX) */
        .header-date-input {
            padding: 0 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            height: 100%;
            border: none;
            outline: none;
            background-color: white; /* Background putih */
        }
        
        /* Waktu Statis (FIX - Merah total) */
        .time-badge-red {
            padding: 0 12px;
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
            background-color: var(--im3-red);
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        /* Tabel Pivot Styling */
        .pivot-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .pivot-table th, .pivot-table td { padding: 10px 8px; text-align: center; border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
        .pivot-table th { background-color: white; color: #4a5568; font-weight: 700; font-size: 0.8rem; border-color: #e2e8f0; } 
        .pivot-table tr:last-child td { border-bottom: none; }
        .pivot-table tbody tr:nth-child(even) td { background-color: var(--gray-light); }
        .dse-id-col { text-align: left; font-weight: 600; }
        .bg-red-header { background-color: var(--im3-red); color: white; border-color: var(--im3-red); }
        
        /* Toggle Button (Stok/Retur/Gabungan) */
        .category-buttons-wrapper { background-color: #e5e7eb; border-radius: 25px; display: inline-flex; padding: 4px; }
        .category-button-toggle { 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-weight: 600; 
            cursor: pointer; 
            background-color: transparent; 
            color: #4b5563; 
            border: none; 
            font-size: 0.9rem; 
            transition: background-color 0.2s, color 0.2s;
        }
        .category-button-toggle.active { 
            background-color: var(--im3-red); 
            color: white; 
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); 
        }
    </style>
</head>
<body class="flex items-start justify-center min-h-screen pt-10 pb-10">
    
    <div class="container-card w-full">
        
        <a href="{{ route('admin.dashboard') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        <div class="header-content pt-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-center mb-2">Detail Stok</h1>
        </div>
        
        {{-- FORM FILTER & INFO HEADER --}}
        <form method="GET" action="{{ route('admin.riwayat_pencatatan') }}" id="filterFormAdmin" class="filter-header-group">
            
            {{-- 1. Tanggal (Input - FIX) --}}
            <div class="info-toggle-item">
                <span class="header-badge-text">Tanggal</span>
                <input 
                    type="date" 
                    name="tanggal" 
                    class="header-date-input" 
                    value="{{ request('tanggal', Carbon\Carbon::today()->toDateString()) }}"
                    onchange="this.form.submit()"
                >
            </div>
            
            {{-- 2. Waktu (Badge Merah Statis - FIX) --}}
            <div class="info-toggle-item" style="border: 1px solid var(--im3-red);">
                <span class="header-badge-text" style="background-color: var(--im3-red); color: white;">Waktu</span>
                {{-- Waktu Statis --}}
                <span id="time-display" class="header-badge-text" style="background-color: white; color: #333;">{{ Carbon\Carbon::now()->format('H:i') }} WITA</span>
            </div>

            
            {{-- 3. TOGGLE TIPE LOG (STOK / RETUR / GABUNGAN) --}}
            <input type="hidden" name="tipe" id="tipe_hidden" value="{{ $tipe ?? 'stok' }}">

            <div class="category-buttons-wrapper">
                <button type="button" onclick="document.getElementById('tipe_hidden').value='stok'; this.form.submit();" class="category-button-toggle @if(($tipe ?? 'stok') == 'stok') active @endif">Stok</button>
                <button type="button" onclick="document.getElementById('tipe_hidden').value='retur'; this.form.submit();" class="category-button-toggle @if(($tipe ?? 'stok') == 'retur') active @endif">Retur</button>
                <button type="button" onclick="document.getElementById('tipe_hidden').value='all'; this.form.submit();" class="category-button-toggle @if(($tipe ?? 'stok') == 'all') active @endif">Gabungan</button>
            </div>
            
        </form>
        
        <hr class="mb-8 border-gray-200">

        {{-- Tabel Pivot --}}
        <div class="table-wrapper overflow-x-auto">
            <table class="pivot-table">
                <thead>
                    {{-- Header Row 1: Kategori Utama --}}
                    <tr>
                        <th class="dse-id-col bg-red-header" rowspan="2" style="width: 100px;">DSE ID</th>
                        <th colspan="4" class="bg-red-header">Kartu Perdana</th>
                        <th colspan="8" class="bg-red-header">Voucher</th>
                    </tr>
                    
                    {{-- Header Row 2: Nama Produk Individual --}}
                    <tr>
                        {{-- Kartu Perdana 4 Produk --}}
                        <th class="text-xs">SP 3 GB</th>
                        <th class="text-xs">SP 6 GB</th>
                        <th class="text-xs">SP 9 GB</th>
                        <th class="text-xs">SP 20 GB</th>
                        
                        {{-- Voucher 8 Produk --}}
                        <th class="text-xs">1 GB/2hr</th>
                        <th class="text-xs">3 GB/3hr</th>
                        <th class="text-xs">5 GB/5hr</th>
                        <th class="text-xs">7 GB/7hr</th>
                        <th class="text-xs">15 GB/7hr</th>
                        <th class="text-xs">3 GB/28hr</th>
                        <th class="text-xs">5 GB/2hr</th>
                        <th class="text-xs">5 GB/3hr</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // List Product Codes yang harus dicari (sesuai urutan header)
                        $productCodes = [
                            'FI3_1D', 'FI7_7D', 'FI15_7D', 'FI15_7D', 
                            'FI15_1D', 'FI3_3D', 'FI5_5D', 'FI7_7D', 'FI15_7D', 'FI3_3D', 'FI5_2D', 'FI5_3D'
                        ];
                        $tipe = $tipe ?? 'stok'; // Pastikan tipe default
                    @endphp

                    @forelse($pivotData ?? [] as $dseId => $row)
                        <tr>
                            <td class="dse-id-col">{{ $dseId }}</td>
                            @foreach($productCodes as $productCode)
                                @php 
                                    $stokQty = $row['stok'][$productCode] ?? 0;
                                    $returQty = $row['retur'][$productCode] ?? 0;

                                    if ($tipe == 'stok') {
                                        $qty = $stokQty;
                                    } elseif ($tipe == 'retur') {
                                        $qty = $returQty;
                                    } else { // 'all' (Gabungan/Netto)
                                        $netto = $stokQty - $returQty;
                                        $qty = $netto;
                                    }
                                @endphp
                                <td class="{{ $qty < 0 ? 'text-red-600 font-semibold' : '' }}">{{ $qty }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($productCodes) + 1 }}" class="text-center py-6 text-gray-500">
                                Tidak ada data yang tercatat sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        // JS untuk memperbarui jam real-time di header
        function updateTimeDisplay() {
            const timeElement = document.getElementById('time-display');
            if (timeElement) {
                const now = new Date();
                const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Makassar' };
                const formattedTime = now.toLocaleTimeString('id-ID', timeOptions).replace(/\./g, ':') + ' WITA';
                timeElement.textContent = formattedTime;
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            updateTimeDisplay();
            setInterval(updateTimeDisplay, 1000); 
        });
    </script>
</body>
</html>