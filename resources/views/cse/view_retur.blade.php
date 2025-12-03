<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Retur Regional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; --gray-light: #f8f8f8; }
        
        body { font-family: 'Inter', sans-serif; background-color: var(--im3-yellow); position: relative; }
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container-card { max-width: 95%; min-width: 1200px; margin: 50px auto; background-color: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); position: relative; z-index: 10; }
        
        .back-button-circle { 
            background-color: var(--im3-red); color: white; padding: 8px; border-radius: 50%; 
            display: inline-flex; align-items: center; justify-content: center; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
            z-index: 11; 
        }

        h1 { font-size: 2.5rem; font-weight: 800; color: #333; margin-bottom: 5px; }
        
        .filter-input { border: 1px solid #ccc; padding: 8px 12px; border-radius: 6px; font-size: 0.9rem; height: 40px; }
        .filter-button-red { background-color: var(--im3-red); color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; height: 40px; border: none; }
        
        .pivot-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .pivot-table th { 
            background-color: var(--im3-red); color: white; font-weight: 700; 
            font-size: 0.85rem; padding: 10px 12px; border: 1px solid #e2e8f0; 
            min-width: 120px;
        }
        .pivot-table td { padding: 10px 12px; text-align: center; border: 1px solid #e2e8f0; font-size: 0.85rem; }
        .pivot-table tbody tr:nth-child(even) td { background-color: var(--gray-light); }
        .dse-id-col { text-align: left; font-weight: 600; }
        
        .pivot-table tfoot th {
            background-color: #E21B21;
            color: white;
            font-size: 0.9rem;
        }
        
        .table-wrapper { overflow-x: auto; border-radius: 8px; }
    </style>
</head>
<body class="flex items-start justify-center min-h-screen pt-10 pb-10">
    <div class="container-card w-full">
        <div class="flex items-center mb-6">
            <a href="{{ route('cse.dashboard') }}" class="back-button-circle mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            
            <div class="flex-grow text-center">
                <h1 class="text-2xl font-bold text-gray-900">Monitoring Retur Regional</h1>
                <p class="text-gray-600 mt-1">Ringkasan Retur Masuk DSE</p>
            </div>
             
            <div class="w-10"></div>
        </div>

        @if ($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 rounded-lg p-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- FORM FILTER --}}
        <form method="GET" action="{{ route('cse.view_retur') }}" class="mb-8 p-6 border rounded-lg bg-gray-100">
            <h2 class="text-xl font-bold mb-4 text-gray-700">Filter Data Retur</h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                
                <div class="flex flex-col">
                    <label for="start_date" class="font-semibold text-gray-700 mb-1">Dari Tanggal:</label>
                    <input type="date" id="start_date" name="start_date" class="filter-input" 
                           value="{{ $startDate ?? request('start_date') }}">
                </div>
                
                <div class="flex flex-col">
                    <label for="end_date" class="font-semibold text-gray-700 mb-1">Sampai Tanggal:</label>
                    <input type="date" id="end_date" name="end_date" class="filter-input" 
                           value="{{ $endDate ?? request('end_date') }}">
                </div>

                <div class="flex flex-col">
                    <label for="dse_id" class="font-semibold text-gray-700 mb-1">DSE ID:</label>
                    <select id="dse_id" name="dse_id" class="filter-input">
                        <option value="">Semua DSE</option>
                        @foreach($dseList ?? [] as $dse)
                            <option value="{{ $dse->id_dse }}" 
                                @if(request('dse_id') == $dse->id_dse || $dseId == $dse->id_dse) selected @endif>
                                {{ $dse->id_dse }} ({{ $dse->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-2 md:mt-0">
                    <button type="submit" class="filter-button-red w-full">Terapkan Filter</button>
                </div>
            </div>
        </form>

        {{-- TABEL PIVOT --}}
        <div class="table-wrapper">
            <table class="pivot-table">
                <thead>
                    <tr>
                        <th class="dse-id-col" style="width: 15%;">DSE ID</th>
                        @foreach($productHeaders ?? [] as $productName)
                            <th>{{ $productName }}</th>
                        @endforeach
                        <th style="width: 10%;">Total Retur</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $grandTotal = 0; 
                        $columnTotals = array_fill_keys($productHeaders ?? [], 0); 
                    @endphp
                    
                    @forelse($pivotData ?? [] as $dseId => $data)
                        @php $rowTotal = 0; @endphp
                        <tr>
                            <td class="dse-id-col">
                                {{ $dseId }}
                                @if(isset($data['dse_name']) && $data['dse_name'])
                                    <br><small class="text-gray-500">{{ $data['dse_name'] }}</small>
                                @endif
                            </td>
                            @foreach($productHeaders ?? [] as $productName)
                                @php 
                                    $qty = $data['counts'][$productName] ?? 0; 
                                    $rowTotal += $qty;
                                    $columnTotals[$productName] = ($columnTotals[$productName] ?? 0) + $qty; 
                                @endphp
                                <td>{{ $qty }}</td>
                            @endforeach
                            <td class="font-bold bg-gray-200">{{ $rowTotal }}</td>
                        </tr>
                        @php $grandTotal += $rowTotal; @endphp
                    @empty
                        <tr>
                            <td colspan="{{ count($productHeaders ?? []) + 2 }}" class="text-center py-6 text-gray-500">
                                @if (!($isFiltered ?? false))
                                    Silakan terapkan filter Tanggal atau DSE ID untuk menampilkan data.
                                @else
                                    Tidak ada data retur yang tercatat sesuai filter yang Anda terapkan.
                                    <br><small class="text-sm">Periode: {{ $startDate ?? '' }} s.d. {{ $endDate ?? '' }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="dse-id-col">GRAND TOTAL</th>
                        @foreach($productHeaders ?? [] as $productName)
                            <th>{{ $columnTotals[$productName] ?? 0 }}</th> 
                        @endforeach
                        <th>{{ $grandTotal }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>