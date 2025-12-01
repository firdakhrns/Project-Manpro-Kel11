<!DOCTYPE html>
<html>
<head>
    <title>Detail Outlet: {{ $outlet->name }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
        }
        .header { 
            background-color: #E21B21; 
            color: white; 
            padding: 15px; 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 20px; 
        }
        .content { 
            padding: 0 20px; 
        }
        .section-header { 
            border-bottom: 2px solid #FFDA00; 
            padding-bottom: 5px; 
            margin-top: 20px; 
            margin-bottom: 10px; 
            font-size: 16px; 
            font-weight: bold; 
            color: #444; 
        }
        .detail-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .detail-table td { 
            padding: 8px 0; 
            border-bottom: 1px solid #eee; 
        }
        .detail-table td:first-child { 
            width: 30%; 
            font-weight: 
            bold; color: #555; 
        }
        .status-Aktif { 
            color: green; 
            font-weight: bold; 
        }
        .status-Non-Aktif { 
            color: red; 
            font-weight: bold; 
        }
        
        .photo-container { width: 100%; display: block; margin-top: 10px; page-break-inside: avoid; }
        .photo-box { width: 48%; display: inline-block; margin-right: 1%; vertical-align: top; }
        .photo-box img { width: 100%; height: auto; border: 1px solid #ddd; margin-bottom: 5px; }
        .photo-box p { text-align: center; font-size: 11px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DETAIL OUTLET: {{ strtoupper($outlet->name) }}</h1>
    </div>

    <div class="content">
        <div class="section-header">Informasi Dasar</div>
        <table class="detail-table">
            <tr>
                <td>Region</td>
                <td>{{ $outlet->region }}</td>
            </tr>
            <tr>
                <td>Status Outlet</td>
                <td>
                    <span class="status-{{ str_replace(' ', '-', $outlet->status) }}">
                        {{ $outlet->status }}
                    </span>
                </td>
            </tr>
            <tr>
                <td>Nama Pemilik</td>
                <td>{{ $outlet->owner_name }}</td>
            </tr>
            <tr>
                <td>No. Telepon</td>
                <td>{{ $outlet->phone }}</td>
            </tr>
            <tr>
                <td>Tanggal Bergabung</td>
                <td>{{ $outlet->join_date ? date('d F Y', strtotime($outlet->join_date)) : '-' }}</td>
            </tr>
            <tr>
                <td>Dibuat Pada</td>
                <td>{{ date('d F Y, H:i', strtotime($outlet->created_at)) }}</td>
            </tr>
            <tr>
                <td>Terakhir Diupdate</td>
                <td>{{ date('d F Y, H:i', strtotime($outlet->updated_at)) }}</td>
            </tr>
        </table>

        <div class="section-header">Alamat</div>
        <p>{{ $outlet->address }}</p>

        <div class="section-header">Dokumentasi Foto</div>
        <div class="photo-container">
            <div class="photo-box">
                @if ($outlet->front_photo)
                    <img src="{{ public_path('storage/' . $outlet->front_photo) }}" alt="Tampak Depan">
                @else
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiByeD0iOCIgZmlsbD0iI0YzRjRGNCIvPgo8cGF0aCBkPSJNNTYuNDQ0IDUwLjQ0NFYxNDkuNTU2SDY1LjI2N0wxNDMuNTU2IDcwLjIyMlYxNDkuNTU2SDE1Ni40NDRWMzcuNTU2SDE0My41NjhMMTU2LjQ0NCA1MC40NDRWNDcuMzY3TDU2LjQ0NCA1MC40NDRaIiBmaWxsPSIjREREREREIi8+CjxwYXRoIGQ9Ik0xMDAgNTBINjUuMjY2N0M2Mi45NDg5IDU2LjczOTMgNjEuNzgyOCA2MC4xMTU2IDY1LjI2NjcgNjQuNTc4VjEzOS41MzlIMTU2LjQ0NFY1MEgxMDVaIiBmaWxsPSIjREREREREIi8+Cjx0ZXh0IHg9IjUwJSIgeT0iNzAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOUFBM0FGIj5GQUlMIFdPTEFEIFBPVE88L3RleHQ+Cjwvc3ZnPg==" style="width:100%; height:150px; object-fit:cover;">
                @endif
                <p>Tampak Depan</p>
            </div>

            <div class="photo-box">
                @if ($outlet->display_photo)
                    <img src="{{ public_path('storage/' . $outlet->display_photo) }}" alt="Foto Etalase">
                @else
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiByeD0iOCIgZmlsbD0iI0YzRjRGNCIvPgo8cGF0aCBkPSJNNTYuNDQ0IDUwLjQ0NFYxNDkuNTU2SDY1LjI2N0wxNDMuNTU2IDcwLjIyMlYxNDkuNTU2SDE1Ni40NDRWMzcuNTU2SDE0My41NjhMMTU2LjQ0NCA1MC40NDRWNDcuMzY3TDU2LjQ0NCA1MC40NDRaIiBmaWxsPSIjREREREREIi8+CjxwYXRoIGQ9Ik0xMDAgNTBINjUuMjY2N0M2Mi45NDg5IDU2LjczOTMgNjEuNzgyOCA2MC4xMTU2IDY1LjI2NjcgNjQuNTc4VjEzOS41MzlIMTU2LjQ0NFY1MEgxMDVaIiBmaWxsPSIjREREREREIi8+Cjx0ZXh0IHg9IjUwJSIgeT0iNzAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LXNpemU9IjEyIiBmaWxsPSIjOUFBM0FGIj5GQUlMIFdPTEFEIFBPVE88L3RleHQ+Cjwvc3ZnPg==" style="width:100%; height:150px; object-fit:cover;">
                @endif
                <p>Foto Etalase</p>
            </div>
        </div>
    </div>
</body>
</html>