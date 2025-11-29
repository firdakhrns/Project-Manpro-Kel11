<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kritik & Saran - CSE Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; }
        
        /* Latar Belakang Merah-Kuning */
        body { 
            background-color: var(--im3-yellow); 
            font-family: 'Inter', sans-serif; 
            position: relative;
        }
        /* Pola Merah */
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container-card { max-width: 95%; min-width: 1000px; margin: 50px auto; background-color: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); position: relative; z-index: 10; }
        
        .filter-input { border: 1px solid #ccc; padding: 8px 12px; border-radius: 6px; font-size: 0.9rem; height: 40px; }
        .filter-button-red { background-color: var(--im3-red); color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; height: 40px; border: none; }
        
        /* Tabel Styling */
        .data-table th { background-color: var(--im3-red); color: white; font-weight: 700; padding: 10px 15px; text-align: left; }
        .data-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .data-table tr:nth-child(even) { background-color: #f8f8f8; }

    </style>
</head>
<body class="flex items-start justify-center min-h-screen pt-10 pb-10">
    <div class="container-card w-full">
        
        <div class="flex items-center mb-6 border-b pb-4">
            <a href="{{ route('cse.kritik_saran') }}" class="back-button-circle mr-4 text-red-600 hover:text-red-700 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 flex-grow">Hasil Umpan Balik DSE</h1>
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
        <form method="GET" action="{{ route('cse.kritik_saran.hasil') }}" class="mb-8 p-6 border rounded-lg bg-gray-100">
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

                <div class="mt-2 md:mt-0 flex space-x-2">
                    <button type="submit" class="filter-button-red flex-grow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('cse.kritik_saran.hasil') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center h-10 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </a>
                </div>
            </div>
        </form>

        {{-- Tabel Hasil --}}
        <div class="table-wrapper">
            <table class="data-table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="w-[10%]">Tanggal</th>
                        <th class="w-[10%]">DSE Target</th>
                        <th class="w-[10%]">Tipe</th>
                        <th class="w-[60%]">Pesan Feedback</th>
                        <th class="w-[10%]">Dikirim Oleh (CSE)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbackData ?? [] as $feedback)
    <tr>
        <td>{{ \Carbon\Carbon::parse($feedback->created_at)->format('d M Y H:i') }}</td>
        <td>{{ $feedback->dse_target }}</td>
        <td>
            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                {{ $feedback->type == 'kritik' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ ucfirst($feedback->type) }}
            </span>
        </td>
        <td>{{ $feedback->message }}</td>
        <td>{{ $feedback->cse_id }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-6 text-gray-500">
            Tidak ada data umpan balik yang tercatat sesuai filter.
        </td>
    </tr>
@endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 flex justify-end">
            <a href="{{ route('cse.kritik_saran.export.pdf', request()->query()) }}" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center shadow-md transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                EXPORT PDF
            </a>
        </div>
        
    </div>
</body>
</html>