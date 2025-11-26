<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pencatatan DSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; --gray-light: #f4f4f4; }
        
        /* ... (Styling body, container-card, back-button-circle, h1, table, th, td, filter-input, filter-button-red TIDAK BERUBAH) ... */
        body { 
            background-color: var(--im3-yellow); 
            font-family: 'Inter', sans-serif; 
            position: relative;
        }
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container-card { 
            max-width: 900px;
            margin: 50px auto; 
            background-color: #fff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.15); 
            position: relative; 
            z-index: 10;
        }
        
        .back-button-circle { /* ... */ background-color: var(--im3-red); color: white; padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); transition: background-color 0.2s; position: absolute; top: 20px; left: 20px; z-index: 11; }
        .back-button-circle:hover { background-color: #b71c1c; }

        h1 { font-size: 2.5rem; font-weight: 800; color: #333; text-align: center; padding-top: 10px; margin-bottom: 5px; }
        
        table { width: 100%; border-collapse: separate; border-spacing: 0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background-color: var(--im3-red); color: white; font-weight: 700; font-size: 0.95rem; border-bottom: none; }
        tr:nth-child(even) { background-color: var(--gray-light); } 
        
        .filter-input, .filter-button-red { height: 40px; } 

        /* Styling Toggle Button */
        .category-buttons-wrapper { 
            background-color: #e5e7eb; 
            border-radius: 25px; 
            display: inline-flex; 
            padding: 4px;
        }
        .category-button-toggle {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: transparent;
            color: #4b5563;
        }
        .category-button-toggle.active {
            background-color: var(--im3-red);
            color: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        /* Styling Badge Info Header */
        .info-badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-right: 12px; white-space: nowrap; }
        .info-badge-red { background-color: var(--im3-red); color: white; }
        .info-badge-text { background-color: #f0f0f0; color: #333; }
    </style>
</head>
<body class="flex items-start justify-center min-h-screen pt-10 pb-10">
    
    <div class="container-card w-full">
        
        <a href="{{ route('dse.dashboard') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        {{-- Menggunakan $judulRiwayat dari controller jika tersedia, jika tidak default ke "Riwayat" --}}
        <h1 class="mb-8">{{ $judulRiwayat ?? 'Riwayat Pencatatan' }}</h1> 
        <p class="text-gray-500 text-center -mt-6 mb-8">Pencarian Riwayat Pencatatan Inventaris DSE</p>

        {{-- FORM FILTER UTAMA (Mengandung Tanggal dan Tipe Log) --}}
        <form method="GET" action="{{ route('dse.riwayat_pencatatan') }}" class="flex flex-col sm:flex-row justify-center items-center mb-8 gap-3">
            
            {{-- 1. INPUT TANGGAL --}}
            <div class="flex items-center">
                <label for="filter_date" class="font-semibold text-gray-700 mr-2 whitespace-nowrap">Cari Tanggal:</label>
                <input 
                    type="date" 
                    id="filter_date" 
                    name="tanggal" 
                    class="filter-input" 
                    {{-- Menggunakan request('tanggal') atau default hari ini --}}
                    value="{{ request('tanggal', now()->toDateString()) }}"
                >
            </div>
            
            {{-- 2. TOGGLE TIPE LOG (STOK / RETUR / SEMUA) --}}
            @php $tipe = request('tipe', 'stok'); @endphp
            <div class="category-buttons-wrapper">
                {{-- PENTING: Tombol submit harus mengirimkan semua parameter di form! --}}
                
                {{-- Stok --}}
                <button type="submit" name="tipe" value="stok" 
                    class="category-button-toggle @if($tipe == 'stok') active @endif">Stok</button>
                
                {{-- Retur --}}
                <button type="submit" name="tipe" value="retur" 
                    class="category-button-toggle @if($tipe == 'retur') active @endif">Retur</button>
                
                {{-- Semua --}}
                <button type="submit" name="tipe" value="all" 
                    class="category-button-toggle @if($tipe == 'all') active @endif">Semua</button>
            </div>
            
            {{-- Tombol Tampilkan Data (Submit) --}}
            <button type="submit" class="filter-button-red">Tampilkan Data</button>

            {{-- CATATAN: Karena semua elemen ada di dalam satu form, hanya satu tombol submit yang diperlukan. --}}
        </form>
        
        <hr class="mb-8 border-gray-200">

        {{-- INFO HEADER --}}
        @php
            // $tanggalFilter harusnya dikirim dari Controller
            $tanggalTampil = \Carbon\Carbon::parse($tanggalFilter ?? Carbon\Carbon::today()->toDateString())->format('d F Y');
        @endphp

        <div class="flex justify-center items-center mb-8 flex-wrap gap-x-4 gap-y-3">
            
            <div class="flex items-center">
                <div class="info-badge info-badge-red">DSE ID</div>
                <div class="info-badge-text font-semibold text-sm">{{ $dseId ?? 'N/A' }}</div>
            </div>
            
            <div class="flex items-center">
                <div class="info-badge info-badge-red">Tanggal Tampil</div>
                <div class="info-badge-text font-semibold text-sm">{{ $tanggalTampil }}</div>
            </div>

        </div>
        
        {{-- Tabel Detail --}}
        {{-- Tabel Detail --}}
<div class="overflow-x-auto">
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jenis Produk</th>
                @if($tipe == 'all')
                    <th>Stok</th>
                    <th>Retur</th>
                    <th>Total</th>
                @else
                    <th>Jumlah {{ $tipe == 'retur' ? 'Retur' : 'Stok' }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($dataToDisplay as $item)
                <tr>
                    <td>{{ $item[0] }}</td>
                    <td>{{ $item[1] }}</td>
                    @if($tipe == 'all')
                        <td>{{ $item[2] }}</td>
                        <td>{{ $item[3] }}</td>
                        <td class="{{ $item[4] < 0 ? 'text-red-600 font-bold' : '' }}">
                            {{ $item[4] }}
                        </td>
                    @else
                        <td>{{ $item[2] }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $tipe == 'all' ? 5 : 3 }}" class="text-center py-6 text-gray-500">
                        @if($tipe == 'all')
                            Data tidak ditemukan untuk tanggal ini.
                        @else
                            Data {{ $tipe == 'retur' ? 'Retur' : 'Stok' }} tidak ditemukan untuk tanggal ini.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
    </div>
</body>
</html>