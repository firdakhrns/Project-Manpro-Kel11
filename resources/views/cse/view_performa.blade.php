<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Performa DSE Regional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root { 
            --im3-yellow: #FFDA00; 
            --im3-red: #E21B21; 
            --im3-red-dark: #C01519;
            --gray-light: #f8f8f8; 
            --accent-green: #10B981; 
            --accent-red: #EF4444; 
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--im3-yellow); 
            position: relative; 
            min-height: 100vh;
        }
        body::before { 
            content: ''; 
            position: fixed; 
            top: 0; 
            right: 0; 
            width: 350px; 
            height: 350px; 
            background-color: var(--im3-red); 
            opacity: 0.15; 
            border-radius: 50%; 
            transform: translate(40%, -40%); 
            z-index: 0; 
        }
        body::after { 
            content: ''; 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 450px; 
            height: 450px; 
            background-color: var(--im3-red); 
            opacity: 0.15; 
            border-radius: 50%; 
            transform: translate(-40%, 40%); 
            z-index: 0; 
        }
        .dashboard-container { 
            max-width: 1200px; 
            margin: 0 auto; 
            padding: 30px 20px; 
            position: relative; 
            z-index: 10;
        }
        .header-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
            position: relative;
        }
        .back-button-circle { 
            background-color: var(--im3-red); 
            color: white; 
            padding: 8px; 
            border-radius: 50%; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.2); 
            position: absolute; 
            top: 20px; 
            left: 20px; 
            z-index: 10;
            transition: all 0.2s;
        }
        .filter-input { 
            border: 1px solid #D1D5DB; 
            padding: 8px 12px; 
            border-radius: 8px; 
            font-size: 0.9rem; 
            height: 40px; 
            background-color: white; 
        }
        .filter-button-red { 
            background-color: var(--im3-red); color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; height: 40px; border: none; }
        .metric-card { 
            background-color: #fff; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            border-left: 5px solid; 
        }
        .metric-card-best { 
            border-color: var(--accent-green); 
        }
        .metric-card-avg { 
            border-color: var(--im3-red); 
        }
        .metric-card-total { 
            border-color: #3B82F6; }
        .icon-container { 
            width: 40px; 
            height: 40px; 
            background-color: rgba(226, 27, 33, 0.1); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .performa-table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0; 
            overflow: hidden; 
            border-radius: 8px; 
        }
        .performa-table thead th { 
            background-color: var(--im3-red); 
            color: white; 
            font-weight: 700; 
            font-size: 0.9rem; 
            padding: 12px 15px; 
            text-align: left; 
            border: none; 
        }
        .performa-table tbody tr { 
            background-color: #fff; 
            transition: background-color 0.1s; 
        }
        .performa-table tbody tr:hover { 
            background-color: #FFF3F4; 
        }
        .performa-table tbody tr:nth-child(even) { 
            background-color: var(--gray-light); 
        }
        .performa-table td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #E5E7EB; 
            font-size: 0.9rem; 
            color: #374151; 
        }
        .rate-low { 
            color: var(--accent-green); 
            font-weight: 700; 
        } 
        .rate-high { 
            color: var(--accent-red); 
            font-weight: 700; 
        }
        .rate-medium { 
            color: #F59E0B; 
            font-weight: 700; 
        } 
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <div class="header-section">
            <a href="{{ route('cse.dashboard') }}" class="back-button-circle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-extrabold text-center mb-1 ml-10 mr-10">📊 Dashboard Performa DSE Regional</h1>
            <p class="text-gray-500 text-center text-sm">Evaluasi Kinerja DSE Berdasarkan Rasio Retur (Stok Kembali) di Regional</p>
        </div>

        <form method="GET" action="{{ route('cse.view_performa') }}" class="filter-form mb-6 p-4 rounded-xl bg-white shadow-md border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                
                <div class="flex flex-col">
                    <label for="start_date" class="font-semibold text-gray-700 text-sm mb-1">Dari Tanggal:</label>
                    <input type="date" id="start_date" name="start_date" class="filter-input" 
                           value="{{ $startDate ?? \Carbon\Carbon::today()->subDays(30)->toDateString() }}">
                </div>
                
                <div class="flex flex-col">
                    <label for="end_date" class="font-semibold text-gray-700 text-sm mb-1">Sampai Tanggal:</label>
                    <input type="date" id="end_date" name="end_date" class="filter-input" 
                           value="{{ $endDate ?? \Carbon\Carbon::today()->toDateString() }}">
                </div>
                
                <div class="md:col-span-2 mt-2 md:mt-0 flex justify-end">
                    <button type="submit" class="filter-button-red w-full md:w-auto flex items-center justify-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-3.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                        <span>Terapkan Filter Periode</span>
                    </button>
                </div>
            </div>
        </form>

        @if ($isFiltered ?? false)
            @php 
                $topDSE = $performaData[0] ?? null; 
                $avgRate = collect($performaData)->avg('return_rate') ?? 0;
                $rateColorClass = $avgRate < 5 ? 'text-green-600' : ($avgRate < 10 ? 'text-yellow-600' : 'text-red-600');
                
                $dseLabels = json_encode(collect($performaData)->pluck('dse_id'));
                $dseRates = json_encode(collect($performaData)->pluck('return_rate'));
                $barColors = collect($performaData)->map(function ($data) {
                    return $data['return_rate'] < 5 ? '#10B981' : ($data['return_rate'] < 10 ? '#F59E0B' : '#EF4444');
                })->toJson();

                $totalStokMasukRegional = collect($performaData)->sum('total_stok_masuk');
                $totalReturRegional = collect($performaData)->sum('total_retur');
                $totalTerkirim = $totalStokMasukRegional - $totalReturRegional; // Assuming this is the 'Good' delivery
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                
                <div class="metric-card metric-card-best flex items-start space-x-4">
                    <div class="icon-container bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.192-2.058-.512-3.029z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium text-sm mb-1">DSE Performa Terbaik</p>
                        <h3 class="text-xl font-extrabold text-gray-800">{{ $topDSE['dse_id'] ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-600 mt-1">Rasio Retur: <span class="rate-low">{{ $topDSE['return_rate'] ?? 0 }}%</span></p>
                    </div>
                </div>

                <div class="metric-card metric-card-avg flex items-start space-x-4">
                    <div class="icon-container bg-red-100 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 7-7M21 12h-3.582M16 16h-3.582m0 0l-3 3-3-3m11.164-7.859V11.5M7 12V7.5M10.418 7.859V3.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium text-sm mb-1">Rata-rata Rasio Retur Regional</p>
                        <h3 class="text-xl font-extrabold {{ $rateColorClass }}">{{ round($avgRate, 2) }}%</h3>
                        <p class="text-sm text-gray-600 mt-1">Periode: {{ $startDate }} s.d. {{ $endDate }}</p>
                    </div>
                </div>

                <div class="metric-card metric-card-total flex items-start space-x-4">
                    <div class="icon-container bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20h-2m2 0h-2M15 4a3 3 0 11-6 0 3 3 0 016 0zm-5 13a6 6 0 100-12 6 6 0 000 12z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium text-sm mb-1">Total DSE Tercakup</p>
                        <h3 class="text-xl font-extrabold text-gray-800">{{ count($performaData) }}</h3>
                        <p class="text-sm text-gray-600 mt-1">Di 
                            @if(isset($regionsToSearch) && count($regionsToSearch) > 1)
                                Semua Wilayah Banjarmasin
                            @else
                                Region {{ $userRegion ?? 'Regional' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg h-96">
                    <h2 class="text-lg font-bold mb-4 text-gray-800">Perbandingan Rasio Retur (%) DSE</h2>
                    <canvas id="dseBarChart"></canvas>
                </div>

                <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg h-96 flex flex-col justify-center items-center">
                    <h2 class="text-lg font-bold mb-4 text-gray-800">Ringkasan Stok Regional</h2>
                    <canvas id="regionalDoughnutChart" class="max-h-80"></canvas>
                    <p class="text-xs text-gray-500 mt-2">Total Stok Masuk: {{ number_format($totalStokMasukRegional, 0, ',', '.') }} unit</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-xl font-bold mb-4 text-gray-800">Peringkat Detail DSE <span class="text-sm font-normal text-gray-500">(Berdasarkan Rasio Retur Terkecil)</span></h2>
                <div class="overflow-x-auto">
                    <table class="performa-table">
                        <thead>
                            <tr>
                                <th class="w-[5%]">#</th>
                                <th class="w-[20%]">DSE ID</th>
                                <th class="w-[25%] text-right">Total Stok Masuk (Unit)</th>
                                <th class="w-[25%] text-right">Total Retur (Unit)</th>
                                <th class="w-[25%] text-right">Rasio Retur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($performaData as $index => $data)
                                @php
                                    $rateClass = $data['return_rate'] < 5 ? 'rate-low' : ($data['return_rate'] < 10 ? 'rate-medium' : 'rate-high');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="font-medium text-blue-600">{{ $data['dse_id'] }}</td>
                                    <td class="text-right">{{ number_format($data['total_stok_masuk'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($data['total_retur'], 0, ',', '.') }}</td>
                                    <td class="{{ $rateClass }} text-right">
                                        {{ $data['return_rate'] }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-500 bg-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p>Tidak ada data performa untuk periode yang dipilih.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @else
            <div class="text-center py-16 bg-white border border-dashed border-gray-300 rounded-xl shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l-2 2M15 19V6l2 2m-8 0l-4 4 4 4m8-8l4 4-4 4" />
                </svg>
                <p class="text-gray-600 font-bold text-xl mt-4 mb-2">Ayo Lihat Performa DSE!</p>
                <p class="text-gray-500 mb-6">Pilih periode tanggal di atas dan tekan tombol Terapkan Filter Periode** untuk menampilkan data performa terbaru.</p>
            </div>
        @endif
        
    </div>

    @if ($isFiltered ?? false)
    <script>
        const dseLabels = {!! $dseLabels !!};
        const dseRates = {!! $dseRates !!};
        const barColors = {!! $barColors !!};
        const totalTerkirim = {{ $totalTerkirim }};
        const totalReturRegional = {{ $totalReturRegional }};

        const ctxBar = document.getElementById('dseBarChart');
        if (dseLabels.length > 0) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: dseLabels,
                    datasets: [{
                        label: 'Rasio Retur (%)',
                        data: dseRates,
                        backgroundColor: barColors,
                        borderColor: barColors.map(color => color + 'AA'), 
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', 
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Rasio Retur (%)'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        const ctxDoughnut = document.getElementById('regionalDoughnutChart');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Stok Terkirim (Non-Retur)', 'Total Retur'],
                datasets: [{
                    label: 'Stok Regional',
                    data: [totalTerkirim, totalReturRegional],
                    backgroundColor: [
                        '#10B981', 
                        '#E21B21' 
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(context.parsed) + ' Unit';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

    </script>
    @endif
</body>
</html>