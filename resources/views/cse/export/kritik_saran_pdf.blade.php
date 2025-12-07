<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kritik dan Saran DSE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #E21B21; /* IM3 Red */
            font-size: 16pt;
            margin: 0 0 5px 0;
        }
        .header p {
            font-size: 10pt;
            color: #555;
            margin: 0;
        }
        .info {
            margin-bottom: 15px;
            font-size: 9pt;
        }
        .info strong {
            display: inline-block;
            width: 80px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #E21B21; /* IM3 Red */
            color: white;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .tipe-kritik { background-color: #ffe0e0; color: #a00000; font-weight: bold; border-radius: 3px; padding: 2px 5px; }
        .tipe-saran { background-color: #fffacd; color: #a37b00; font-weight: bold; border-radius: 3px; padding: 2px 5px; }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            text-align: right;
            font-size: 8pt;
            color: #888;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>LAPORAN KRITIK & SARAN DSE</h1>
        <p>Regional: {{ Auth::guard('shared')->user()->region ?? 'N/A' }}</p>
    </div>

    {{-- INFORMASI FILTER --}}
    <div class="info">
        @php
            $startDate = request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : 'Awal Data';
            $endDate = request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : 'Akhir Data';
        @endphp
        
        <p><strong>Periode:</strong> {{ $startDate }} s/d {{ $endDate }}</p>
        <p><strong>Total Data:</strong> {{ $kritikSaran->count() }} record</p>
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 10%;">DSE Target</th>
                <th style="width: 10%;">Tipe</th>
                <th style="width: 55%;">Pesan Feedback</th>
                <th style="width: 10%;">Pengirim (CSE)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kritikSaran as $feedback)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($feedback->created_at)->format('d M Y H:i') }}</td>
                    <td>{{ $feedback->dse_target }}</td>
                    <td>
                        <span class="{{ $feedback->type == 'kritik' ? 'tipe-kritik' : 'tipe-saran' }}">
                            {{ ucfirst($feedback->type) }}
                        </span>
                    </td>
                    <td>{{ $feedback->message }}</td>
                    <td>{{ $feedback->cse_id }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data yang tersedia dalam periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        @php
            $nowWITA = \Carbon\Carbon::now('Asia/Makassar');
        @endphp
 Dicetak oleh: {{ Auth::guard('shared')->user()->username }} pada {{ $nowWITA->format('d M Y H:i') }} WITA </div>

</body>
</html>