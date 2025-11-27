<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Outlet Aktif CSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; }
        
        body { 
            background-color: var(--im3-yellow); 
            font-family: 'Inter', sans-serif; 
            position: relative;
        }
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container-card { 
            max-width: 1000px; 
            margin: 50px auto; 
            background-color: #fff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.15); 
            position: relative; 
            z-index: 10;
        }
        
        .back-button-circle { 
            background-color: var(--im3-red); 
            color: white; padding: 8px; border-radius: 50%; display: inline-flex; 
            align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
            position: absolute; top: 20px; left: 20px; z-index: 11;
        }
        .back-button-circle:hover { background-color: #b71c1c; }

        h1 { font-size: 2.5rem; font-weight: 800; color: #333; text-align: center; margin-bottom: 30px; }
        
        .outlet-table { 
            width: 100%; 
            border-collapse: collapse; 
            border-radius: 8px; 
            overflow: hidden; 
        }
        .outlet-table th { 
            background-color: #f7f7f7;
            font-weight: 600; 
            color: #4a5568; 
            padding: 15px 12px; 
            text-align: left; 
            border-bottom: 2px solid #e2e8f0;
        }
        .outlet-table td { 
            padding: 12px; 
            border-bottom: 1px solid #e2e8f0; 
            font-size: 0.95rem;
        }
        .outlet-table tbody tr:nth-child(even) { background-color: #fcfcfc; }
        .outlet-table tbody tr:hover { background-color: #f8f8f8; }

        .status-badge {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-Aktif { background-color: #d1fae5; color: #065f46; }
        .status-NonAktif { background-color: #fee2e2; color: #991b1b; }

        .export-btn {
            background-color: var(--im3-red);
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.2s;
            border: none;
            cursor: pointer;
        }
        .export-btn:hover { background-color: #c7171d; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="container-card w-full">
        
        <a href="{{ route('cse.dashboard') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        <div class="header-content pt-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-center mb-2">Daftar Outlet Aktif</h1>
        </div>

        @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Sukses!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

        <div class="overflow-x-auto">
            <table class="outlet-table">
                <thead>
                    <tr>
                        <th class="pl-4">Nama Outlet</th>
                        <th>Alamat Outlet</th>
                        <th>Nama Pemilik</th>
                        <th>No. HP Pemilik</th>
                        <th>Tanggal Bergabung</th>
                        <th>Status Outlet</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- DATA DINAMIS DARI DATABASE --}}
                    @forelse($outlets as $outlet)
                        <tr>
                            <td class="pl-4">
                                <div class="font-semibold">{{ $outlet->name }}</div>
                            </td>
                            <td>{{ $outlet->address }}</td>
                            <td>{{ $outlet->owner_name }}</td>
                            <td>{{ $outlet->phone ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($outlet->join_date)->format('d/m/Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ str_replace(' ', '', $outlet->status) }}">
                                    {{ $outlet->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">
                                Tidak ada outlet yang tercatat di region {{ Auth::guard('shared')->user()->region }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="text-right mt-8">
            {{-- TOMBOL EXPORT PDF YANG BERFUNGSI --}}
            <a href="{{ route('cse.export.outlet') }}" class="export-btn inline-block">
                EKSPOR PDF
            </a>
        </div>
        
    </div>
</body>
</html>