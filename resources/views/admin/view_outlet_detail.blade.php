<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Outlet: {{ $outlet->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { 
            --im3-yellow: #FFDA00; 
            --im3-red: #E21B21; 
        }
        body { 
            background-color: var(--im3-yellow); 
            font-family: 'Inter', sans-serif; 
            position: relative; 
        }
        body::before { 
            content: ''; 
            position: fixed; 
            top: 0; 
            right: 0; 
            width: 300px; 
            height: 300px; 
            background-color: var(--im3-red); 
            border-radius: 50%; 
            transform: translate(50%, -50%); 
            z-index: 0; 
        }
        body::after { 
            content: ''; 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 400px; 
            height: 400px; 
            background-color: var(--im3-red); 
            border-radius: 50%; 
            transform: translate(-50%, 50%); 
            z-index: 0; 
        }
        .container-card { 
            max-width: 800px; 
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
            transition: background-color 0.2s;
        }
        .back-button-circle:hover { background-color: #b71c1c; }
        h1 { 
            font-size: 2.25rem; 
            font-weight: 800; 
            color: #333; 
            margin-bottom: 5px;
        }
        h2 {
             font-size: 1.25rem; 
             font-weight: 600;
             color: #6b7280; 
             margin-bottom: 25px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-label {
            font-weight: 600;
            color: #4b5563;
        }
        .detail-value {
            color: #1f2937;
            text-align: right;
            max-width: 60%;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-Aktif { background-color: #d1fae5; color: #065f46; } 
        .status-Non-Aktif { background-color: #fecaca; color: #991b1b; } 
        
        .section-header {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--im3-red);
            border-bottom: 2px solid var(--im3-red);
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .image-container {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        .image-container img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 6px;
        }
        .image-label {
            font-weight: 600;
            margin-top: 8px;
            color: #374151;
        }
        .no-photo {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            color: #9ca3af;
            border-radius: 6px;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body class="flex items-start justify-center min-h-screen p-4">
    <div class="container-card w-full">
        
        <a href="{{ route('admin.view_outlet') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        <div class="header-content pt-10 text-center">
            <h1 class="text-3xl font-extrabold text-gray-800 mb-2">Detail Outlet: {{ $outlet->name }}</h1>
            <h2>Region {{ $outlet->region }}</h2>
        </div>
        
        <div class="detail-section mt-6">
            <div class="section-header">
                Informasi Utama Outlet
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status Outlet</span>
                <span class="detail-value">
                    <span class="status-badge status-{{ str_replace(' ', '-', $outlet->status) }}">
                        {{ $outlet->status }}
                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Alamat Lengkap</span>
                <span class="detail-value text-right">{{ $outlet->address }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Nama Pemilik</span>
                <span class="detail-value">{{ $outlet->owner_name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">No. HP Pemilik</span>
                <span class="detail-value">{{ $outlet->phone }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Dibuat</span>
                <span class="detail-value">{{ date('d F Y, H:i', strtotime($outlet->created_at)) }}</span>
            </div>

            <div class="detail-row" style="border-bottom: none;">
                <span class="detail-label">Terakhir Diupdate</span>
                <span class="detail-value">{{ date('d F Y, H:i', strtotime($outlet->updated_at)) }}</span>
            </div>
        </div>

        <div class="section-header">
            Dokumentasi Foto
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <div class="image-container">
                    @if ($outlet->front_photo)
                        <img src="{{ asset('storage/' . $outlet->front_photo) }}" alt="Foto Tampak Depan">
                    @else
                        <div class="no-photo">Foto Tampak Depan Belum Tersedia</div>
                    @endif
                </div>
                <div class="image-label text-center">Tampak Depan Outlet</div>
            </div>

            {{-- Foto Etalase --}}
            <div>
                <div class="image-container">
                    @if ($outlet->display_photo)
                        <img src="{{ asset('storage/' . $outlet->display_photo) }}" alt="Foto Etalase">
                    @else
                        <div class="no-photo">Foto Etalase Belum Tersedia</div>
                    @endif
                </div>
                <div class="image-label text-center">Foto Etalase</div>
            </div>
        </div>

        <div class="mt-8 flex justify-center space-x-4">
            <a href="{{ route('admin.export.outlet_detail_pdf', $outlet->id) }}" 
                target="_blank"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                Export Detail PDF
            </a>
        </div>
        
    </div>
</body>
</html>