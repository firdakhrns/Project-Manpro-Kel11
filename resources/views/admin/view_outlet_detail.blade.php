<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Outlet - {{ $outlet->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-red: #E21B21; --im3-yellow: #FFDA00; }
        body { font-family: 'Inter', sans-serif; background-color: var(--im3-yellow); }
        .card { max-width: 1000px; margin: 50px auto; background-color: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); position: relative; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; }
        .detail-row strong { font-weight: 600; color: #333; }
        .status-Aktif { background-color: #d1fae5; color: #065f46; } 
        .status-NonAktif { background-color: #fecaca; color: #991b1b; } 
        .photo-container { height: 250px; width: 100%; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; margin-top: 10px; }
        .back-button-circle { background-color: var(--im3-red); color: white; padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); position: absolute; top: 20px; left: 20px; z-index: 11; }
    </style>
</head>
<body class="p-4">
    <div class="card">
        <a href="{{ route('admin.view_outlet') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>

        <h1 class="text-3xl font-extrabold text-center mb-8 pt-10">Detail Outlet: {{ $outlet->name }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- KOLOM 1: INFORMASI UMUM --}}
            <div class="md:col-span-1">
                <h2 class="text-xl font-bold mb-4 border-b pb-2 text-red-600">Informasi Umum</h2>
                <div class="detail-row">
                    <strong>Nama Outlet:</strong> <span>{{ $outlet->name }}</span>
                </div>
                <div class="detail-row">
                    <strong>Region:</strong> <span>{{ $outlet->region }}</span>
                </div>
                <div class="detail-row">
                    <strong>Status:</strong> 
                    <span class="px-3 py-1 rounded-full text-xs font-semibold status-{{ str_replace(' ', '', $outlet->status) }}">
                        {{ $outlet->status }}
                    </span>
                </div>
                <div class="detail-row">
                    <strong>Tgl Gabung:</strong> <span>{{ Carbon::parse($outlet->join_date)->format('d F Y') }}</span>
                </div>
                <div class="detail-row">
                    <strong>Alamat:</strong> <span>{{ $outlet->address }}</span>
                </div>
            </div>

            {{-- KOLOM 2: KONTAK PEMILIK --}}
            <div class="md:col-span-1">
                <h2 class="text-xl font-bold mb-4 border-b pb-2 text-red-600">Kontak Pemilik</h2>
                <div class="detail-row">
                    <strong>Nama Pemilik:</strong> <span>{{ $outlet->owner_name }}</span>
                </div>
                <div class="detail-row">
                    <strong>No. HP:</strong> <span>{{ $outlet->phone }}</span>
                </div>
                <div class="detail-row">
                    <strong>No. Darurat:</strong> <span>{{ $outlet->emergency_phone ?? '-' }}</span>
                </div>
            </div>

            {{-- KOLOM 3: FOTO DOKUMENTASI --}}
            <div class="md:col-span-1">
                <h2 class="text-xl font-bold mb-4 border-b pb-2 text-red-600">Foto Dokumentasi</h2>

                {{-- Foto Tampak Depan --}}
                <p class="font-semibold mt-4 text-sm text-gray-700">Tampak Depan Outlet</p>
                @if ($outlet->front_photo)
                    <img src="{{ asset('storage/' . $outlet->front_photo) }}" alt="Foto Depan Outlet" class="photo-container">
                @else
                    <p class="text-gray-500 italic mt-2">Foto belum tersedia.</p>
                @endif

                {{-- Foto Etalase --}}
                <p class="font-semibold mt-4 text-sm text-gray-700">Foto Etalase Produk</p>
                @if ($outlet->display_photo)
                    <img src="{{ asset('storage/' . $outlet->display_photo) }}" alt="Foto Etalase" class="photo-container">
                @else
                    <p class="text-gray-500 italic mt-2">Foto belum tersedia.</p>
                @endif
            </div>
        </div>

        <div class="mt-8 pt-4 border-t text-center">
             <p class="text-sm text-gray-500">Data tercatat pada {{ $outlet->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>
</body>
</html>