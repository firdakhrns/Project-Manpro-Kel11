<!DOCTYPE html>
<html>
<head>
    <title>Daftar Outlet - {{ Auth::guard('shared')->user()->region }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Daftar Outlet Aktif - Regional {{ Auth::guard('shared')->user()->region }}</h1>
    <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    
    <table>
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
            @foreach($outlets as $outlet)
            <tr>
                <td>{{ $outlet->name }}</td>
                <td>{{ $outlet->address }}</td>
                <td>{{ $outlet->owner_name }}</td>
                <td>{{ $outlet->phone ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($outlet->join_date)->format('d/m/Y') }}</td>
                <td>{{ $outlet->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>