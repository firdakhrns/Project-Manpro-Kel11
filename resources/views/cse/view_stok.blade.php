<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Regional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; --gray-light: #f8f8f8; }
        
        body { font-family: 'Inter', sans-serif; background-color: var(--im3-yellow); position: relative; }
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container-card { max-width: 95%; min-width: 900px; margin: 50px auto; background-color: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); position: relative; z-index: 10; }
        
        .back-button-circle { background-color: var(--im3-red); color: white; padding: 8px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2); position: absolute; top: 20px; left: 20px; z-index: 11; }

        .filter-input { border: 1px solid #ccc; padding: 8px 12px; border-radius: 6px; font-size: 0.9rem; height: 40px; }
        .filter-button-red { background-color: var(--im3-red); color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; height: 40px; border: none; cursor: pointer; }
        .filter-button-red:hover { background-color: #c41217; }
        
        .info-badge { background-color: var(--im3-red); color: white; padding: 6px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; }
        .data-display { background-color: #f0f0f0; padding: 6px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; color: #333; }
        
        .report-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .report-table th { background-color: var(--im3-red); color: white; font-weight: 700; font-size: 0.85rem; padding: 10px 12px; border: 1px solid #e2e8f0; }
        .report-table td { padding: 10px 12px; text-align: center; border: 1px solid #e2e8f0; font-size: 0.85rem; }
        .report-table tbody tr:nth-child(even) td { background-color: var(--gray-light); }
        .dse-id-col { text-align: left; font-weight: 600; }
        
        .table-wrapper { overflow-x: auto; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container-card w-full">
        
        <a href="{{ route('cse.dashboard') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        <h1 class="text-3xl font-extrabold text-center mb-2">Laporan Stok Regional</h1>
        <p class="text-gray-500 text-center mb-8">Ringkasan Stok Masuk Regional Anda</p>

        {{-- FORM FILTER --}}
        <form method="GET" action="{{ route('cse.view_stok') }}" class="mb-8 p-6 border rounded-lg bg-gray-100">
            <h2 class="text-xl font-bold mb-4 text-gray-700">Filter Data</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                <div class="flex flex-col">
                    <label for="start_date" class="font-semibold text-gray-700 mb-1">Dari Tanggal:</label>
                    <input type="date" id="start_date" name="start_date" class="filter-input" value="{{ request('start_date') }}">
                </div>
                
                <div class="flex flex-col">
                    <label for="end_date" class="font-semibold text-gray-700 mb-1">Sampai Tanggal:</label>
                    <input type="date" id="end_date" name="end_date" class="filter-input" value="{{ request('end_date') }}">
                </div>
                
                <div class="flex flex-col">
                    <label for="dse_id" class="font-semibold text-gray-700 mb-1">DSE ID:</label>
                    <select id="dse_id" name="dse_id" class="filter-input">
                        <option value="">Semua DSE</option>
                        @foreach($dseList ?? [] as $dse)
                            <option value="{{ $dse->id_dse }}" {{ request('dse_id') == $dse->id_dse ? 'selected' : '' }}>
                                {{ $dse->id_dse }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-1 mt-2 md:mt-0">
                    <button type="submit" class="filter-button-red w-full">Terapkan Filter</button>
                </div>
            </div>
        </form>

        {{-- TAMPILAN STATUS FILTER --}}
        <div class="mb-8 p-6 border rounded-lg bg-gray-100 flex flex-wrap justify-center gap-4 items-center">
            <span class="font-semibold text-gray-700">Filter Aktif:</span>
            
            <div class="flex gap-2">
                <span class="info-badge">Periode</span>
                <span class="data-display">{{ request('start_date', 'Semua') }} - {{ request('end_date', 'Semua') }}</span>
            </div>
            
            <div class="flex gap-2">
                <span class="info-badge">DSE ID</span>
                <span class="data-display">{{ request('dse_id', 'Semua DSE') }}</span>
            </div>
            
            <div class="flex gap-2">
                <span class="info-badge">Region</span>
                <span class="data-display">{{ Auth::guard('shared')->user()->region }}</span>
            </div>
        </div>

        {{-- Tabel Pivot (Stok) --}}
        <div class="table-wrapper">
            <table class="report-table">
                <thead>
                    <tr>
                        <th class="dse-id-col" style="width: 15%;">DSE ID</th>
                        @foreach($productHeaders ?? [] as $productName)
                            <th>{{ $productName }}</th>
                        @endforeach
                        <th style="width: 10%;">Total Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @forelse($pivotData ?? [] as $dseId => $row)
                        @php $rowTotal = 0; @endphp
                        <tr>
                            <td class="dse-id-col">{{ $dseId }}</td>
                            @foreach($productHeaders ?? [] as $productName)
                                @php $qty = $row[$productName] ?? 0; $rowTotal += $qty; @endphp
                                <td>{{ $qty }}</td>
                            @endforeach
                            <td class="font-bold bg-gray-200">{{ $rowTotal }}</td>
                        </tr>
                        @php $grandTotal += $rowTotal; @endphp
                    @empty
                        <tr>
                            <td colspan="{{ count($productHeaders ?? []) + 2 }}" class="text-center py-6 text-gray-500">
                                Tidak ada data stok yang tercatat sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="dse-id-col">GRAND TOTAL</th>
                        @foreach($productHeaders ?? [] as $productName)
                            <th></th> 
                        @endforeach
                        <th>{{ $grandTotal }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>