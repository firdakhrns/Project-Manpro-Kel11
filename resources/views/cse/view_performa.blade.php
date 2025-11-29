<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Hasil Performa DSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; --gray-light: #f8f8f8; }
        /* ... (CSS Styling tetap sama) ... */
        body { font-family: 'Inter', sans-serif; background-color: var(--im3-yellow); position: relative; }
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }
        .container-card { max-width: 1000px; margin: 50px auto; background-color: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); position: relative; z-index: 10; }
        .back-button-circle { background-color: var(--im3-red); color: white; padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); position: absolute; top: 20px; left: 20px; z-index: 11; }
        h1 { font-size: 2.5rem; font-weight: 800; color: #333; margin-bottom: 5px; }
        .filter-input { border: 1px solid #ccc; padding: 8px 12px; border-radius: 6px; font-size: 0.9rem; height: 40px; }
        .filter-button-red { background-color: var(--im3-red); color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; height: 40px; border: none; }
        .performa-table th { background-color: var(--im3-red); color: white; font-weight: 700; font-size: 0.9rem; padding: 12px 15px; text-align: left; border: 1px solid #e2e8f0; }
        .performa-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; font-size: 0.9rem; }
        .performa-table tbody tr:nth-child(even) { background-color: var(--gray-light); }
        .rate-low { color: #065f46; font-weight: 700; } 
        .rate-high { color: #991b1b; font-weight: 700; }
        .metric-card { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container-card w-full">
        
        <a href="{{ route('cse.dashboard') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>

        <h1 class="text-3xl font-extrabold text-center mb-2">Data Hasil Performa DSE</h1>
        <p class="text-gray-500 text-center mb-8">Evaluasi Berdasarkan Rasio Retur Regional</p>

         @if ($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 rounded-lg p-4">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        {{-- FORM FILTER PERIODE --}}
        <form method="GET" action="{{ route('cse.view_performa') }}" class="mb-8 p-6 border rounded-lg bg-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                <div class="flex flex-col">
                    <label for="start_date" class="font-semibold text-gray-700 mb-1">Dari Tanggal:</label>
                    <input type="date" id="start_date" name="start_date" class="filter-input" value="{{ $startDate }}">
                </div>
                
                <div class="flex flex-col">
                    <label for="end_date" class="font-semibold text-gray-700 mb-1">Sampai Tanggal:</label>
                    <input type="date" id="end_date" name="end_date" class="filter-input" value="{{ $endDate }}">
                </div>
                
                <div class="md:col-span-2 mt-2 md:mt-0">
                    <button type="submit" class="filter-button-red w-full md:w-auto">Terapkan Filter Periode</button>
                </div>
            </div>
        </form>

        {{-- CONTAINER KONTEN (HANYA MUNCUL JIKA DATA SUDAH DI-FILTER) --}}
        @if ($isFiltered ?? false)
            {{-- KARTU METRIK RINGKASAN --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Kartu Peringkat Atas --}}
                @php $topDSE = $performaData[0] ?? null; @endphp
                <div class="metric-card bg-green-50 border-green-200">
                    <p class="text-green-700 font-bold text-sm mb-2">DSE Performa Terbaik</p>
                    <h3 class="text-2xl font-extrabold text-green-800">{{ $topDSE['dse_id'] ?? 'N/A' }}</h3>
                    <p class="text-sm text-gray-600 mt-1">Rasio Retur: {{ $topDSE['return_rate'] ?? 0 }}%</p>
                </div>

                {{-- Kartu Rata-Rata Retur Regional --}}
                @php 
                    $avgRate = collect($performaData)->avg('return_rate');
                @endphp
                <div class="metric-card">
                    <p class="text-gray-700 font-bold text-sm mb-2">Rata-rata Retur Regional</p>
                    <h3 class="text-2xl font-extrabold text-red-600">{{ round($avgRate, 2) }}%</h3>
                    <p class="text-sm text-gray-600 mt-1">Periode: {{ $startDate }} s.d. {{ $endDate }}</p>
                </div>

                {{-- Kartu Total DSE di Region --}}
                <div class="metric-card">
                    <p class="text-gray-700 font-bold text-sm mb-2">DSE Tercakup</p>
                    <h3 class="text-2xl font-extrabold text-gray-800">{{ count($performaData) }}</h3>
                    <p class="text-sm text-gray-600 mt-1">Total DSE di region {{ $userRegion }}</p>
                </div>
            </div>

            {{-- TABEL PERINGKAT DETAIL --}}
            <h2 class="text-xl font-bold mb-4">Peringkat Detail DSE (Berdasarkan Rasio Retur Terkecil)</h2>
            <div class="table-wrapper">
                <table class="performa-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">DSE ID</th>
                            <th style="width: 25%;">Total Stok Masuk</th>
                            <th style="width: 25%;">Total Retur</th>
                            <th style="width: 25%;">Rasio Retur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performaData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="dse-id-col">{{ $data['dse_id'] }}</td>
                                <td>{{ number_format($data['total_stok_masuk']) }}</td>
                                <td>{{ number_format($data['total_retur']) }}</td>
                                <td class="{{ $data['return_rate'] < 5 ? 'rate-low' : 'rate-high' }}">
                                    {{ $data['return_rate'] }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-500">
                                    Tidak ada data performa untuk periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @else
            {{-- Pesan Awal Sebelum Filter Diterapkan --}}
            <div class="text-center py-10 bg-gray-50 border rounded-lg">
                <p class="text-gray-600 font-semibold text-lg">Silakan tentukan periode Tanggal Mulai dan Sampai untuk melihat hasil performa DSE di regional Anda.</p>
                <p class="text-sm text-gray-500 mt-2">Default: Data 30 hari terakhir.</p>
            </div>
        @endif
        
    </div>
</body>
</html>