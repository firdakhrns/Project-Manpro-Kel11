<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        /* Desain tetap seperti yang terakhir kita sepakati */
        :root { --im3-red: #E21B21; --im3-yellow: #FFDA00; }
        
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            padding: 0; 
            color: #333;
            font-size: 10pt;
        }
        .header {
            background-color: var(--im3-red);
            color: white;
            padding: 15px 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
        }
        .metadata {
            text-align: right;
            font-size: 8pt;
            margin-bottom: 15px;
            padding-right: 20px;
        }
        .outlet-table { 
            width: 95%; 
            margin: 0 auto;
            border-collapse: collapse; 
            border: 1px solid #ddd;
        }
        .outlet-table th, 
        .outlet-table td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
            vertical-align: top;
        }
        .outlet-table th { 
            background-color: #f2f2f2; 
            color: #444;
            font-size: 9pt;
            font-weight: bold;
            border-bottom: 3px solid var(--im3-yellow);
        }
        .outlet-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        .status-Aktif { color: green; font-weight: bold; }
        .status-NonAktif { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($title) }}</h1>
    </div>

    <div class="metadata">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
    
    <table class="outlet-table">
        <thead>
            <tr>
                <th>Nama Outlet</th>
                <th>Alamat</th>
                <th>Pemilik</th>
                <th>Telepon</th>
                <th>Tanggal Bergabung</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            {{-- Menggunakan loop PHP standar untuk DomPDF yang lebih baik --}}
            @php
                use Carbon\Carbon;
            @endphp
            @foreach($outlets as $outlet)
            <tr>
                <td>{{ $outlet->name }}</td>
                <td>{{ $outlet->address }}</td>
                <td>{{ $outlet->owner_name }}</td>
                <td>{{ $outlet->phone ?? '-' }}</td>
                <td>
                    {{-- Menggunakan Carbon di dalam loop dengan namespace penuh/use statement --}}
                    @if($outlet->join_date)
                        {{ Carbon::parse($outlet->join_date)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    <span class="status-{{ str_replace('-', '', $outlet->status) }}">
                        {{ $outlet->status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>