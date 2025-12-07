<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Outlet Aktif Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; }
        
        /* Latar Belakang Merah-Kuning (Sesuai Desain) */
        body { 
            background-color: var(--im3-yellow); 
            font-family: 'Inter', sans-serif; 
            position: relative;
        }
        /* Pola Merah (Sesuai Desain) */
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
        
        /* Tombol Kembali Merah Bulat */
        .back-button-circle { 
            background-color: var(--im3-red); 
            color: white; padding: 8px; border-radius: 50%; display: inline-flex; 
            align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
            position: absolute; top: 20px; left: 20px; z-index: 11;
        }
        .back-button-circle:hover { background-color: #b71c1c; }

        h1 { font-size: 2.5rem; font-weight: 800; color: #333; text-align: center; margin-bottom: 30px; }
        
        /* Styling Tabel Sesuai Desain */
        .outlet-table { 
            width: 100%; 
            border-collapse: collapse; 
            border-radius: 8px; 
            overflow: hidden; 
        }
        .outlet-table th { 
            background-color: #f7f7f7; /* Header Background Putih/Abu-abu */
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

        .edit-group {
            display: flex;
            align-items: center;
            font-weight: 600; /* Nama outlet tebal */
            color: #333;
        }
        .edit-icon-btn { 
            color: #4a5568; 
            margin-right: 8px; 
            cursor: pointer;
            transition: color 0.2s;
        }
        .edit-icon-btn:hover {
            color: var(--im3-red);
        }
        
        /* Status Badge Styling */
        .status-badge {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-Aktif { background-color: #d1fae5; color: #065f46; } /* Hijau Muda */
        .status-NonAktif { background-color: #fecaca; color: #991b1b; } /* Merah Muda */
        .status-Ditinjau { background-color: #ffedd5; color: #9a3412; } /* Jingga Muda */

        /* Export Button */
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

        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-delete {
            background-color: #ef4444;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-delete:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="container-card w-full">
        
        <a href="{{ route('admin.dashboard') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        <div class="header-content pt-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-center mb-2">Daftar Outlet</h1>
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
                        <th>Status Outlet</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outlets as $outlet)
                        <tr>
                            {{-- KOLOM 1: NAMA OUTLET (LINK EDIT) --}}
                            <td class="pl-4">
                                <a href="{{ route('admin.outlet.edit', $outlet->id) }}" class="edit-group">
                                    <span class="edit-icon-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21H3v-3.5L14.732 3.732z" />
                                        </svg>
                                    </span>
                                    <span>{{ $outlet->name }}</span>
                                </a>
                            </td>
                            
                            {{-- KOLOM 2-4: DETAIL OUTLET --}}
                            <td>{{ $outlet->address }}</td>
                            <td>{{ $outlet->owner_name }}</td>
                            <td>{{ $outlet->phone }}</td>
                            
                            {{-- KOLOM 5: STATUS --}}
                            <td>
                                <span class="status-badge status-{{ str_replace(' ', '', $outlet->status) }}">
                                    {{ $outlet->status }}
                                </span>
                            </td>
                            
                            {{-- KOLOM 6: AKSI (DETAIL + HAPUS) --}}
                            <td>
                                <div class="action-buttons justify-center">
                                    
                                    {{-- TOMBOL DETAIL --}}
                                    <a href="{{ route('admin.outlet.detail', $outlet->id) }}" 
                                       class="btn-delete px-3 py-1" 
                                       style="background-color: #10b981; /* Hijau */">
                                        Detail
                                    </a> 
                                    
                                    {{-- Tombol Hapus --}}
                                    <form method="POST" action="{{ route('admin.outlet.delete', $outlet->id) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus outlet {{ $outlet->name }}?')"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete px-3 py-1">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="text-right mt-8">
            <a href="{{ route('admin.export.outlet_pdf') }}" target="_blank">
                <button class="export-btn">EKSPOR PDF</button>
            </a>
        </div>
        
    </div>

    <script>
        function confirmDelete(outletName) {
            return confirm(`Apakah Anda yakin ingin menghapus outlet ${outletName}?`);
        }
    </script>
</body>
</html>