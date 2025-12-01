<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Outlet - Manajer/CSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { 
            --im3-yellow: #FFDA00; --im3-red: #E21B21; 
        }
        /* ... (Styling Latar Belakang & Card) ... */
        body { 
            background-color: var(--im3-yellow); 
            font-family: 'Inter', sans-serif; 
            position: relative; 
        }
        body::before { 
            content: ''; 
            position: fixed; 
            top: 0; 
            right: 0; 
            width: 300px; 
            height: 300px; 
            background-color: var(--im3-red); 
            border-radius: 50%; 
            transform: translate(50%, -50%); 
            z-index: 0; 
        }
        body::after { 
            content: ''; 
            position: fixed; 
            bottom: 0; 
            left: 0; 
            width: 400px; 
            height: 400px; 
            background-color: var(--im3-red); 
            border-radius: 50%; 
            transform: translate(-50%, 50%); 
            z-index: 0; 
        }
        .container-card { 
            max-width: 700px; /* Lebar dilebarkan untuk layout foto */
            margin: 50px auto; 
            background-color: #fff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.15); 
            position: relative; 
            z-index: 10; 
        }
        .back-button-circle { 
            position: absolute; 
            top: 20px; 
            left: 20px; 
            z-index: 11; 
            background-color: var(--im3-red); 
            color: white; padding: 8px; border-radius: 50%; display: inline-flex; 
            align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: background-color 0.2s;
        }
        .back-button-circle:hover { background-color: #b71c1c; }
        h1 { 
            font-size: 2rem; 
            font-weight: 800; 
            color: #333; 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .form-input { 
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            font-size: 0.95rem; 
        }
        .form-input:focus { 
            outline: none; 
            border-color: var(--im3-red); 
            box-shadow: 0 0 0 3px rgba(226, 27, 33, 0.1); 
        }
        .btn-primary { 
            background-color: var(--im3-red); 
            color: white; 
            padding: 12px 24px; 
            border-radius: 6px; 
            font-weight: 600; 
            border: none; 
            cursor: pointer; 
            transition: background-color 0.2s; 
        }
        .btn-primary:hover { 
            background-color: #c7171d; 
        }
        .btn-secondary { 
            background-color: #6b7280; 
            color: white; 
            padding: 12px 24px; 
            border-radius: 6px; 
            font-weight: 600; 
            border: none; 
            cursor: pointer; 
            transition: background-color 0.2s; 
        }
        .btn-secondary:hover { 
            background-color: #4b5563; 
        }
        .photo-preview-container {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .photo-box-edit {
            flex: 1;
            text-align: center;
        }
        .photo-box-edit img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .photo-box-edit p {
            font-size: 0.8rem;
            color: #4b5563;
            margin-top: 5px;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="container-card w-full">
        
        <a href="{{ route('admin.view_outlet') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>

        <div class="header-content pt-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-center mb-2">Edit Outlet</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.outlet.update', $outlet->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <strong class="font-bold">Gagal Validasi Input:</strong>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="space-y-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Outlet</label>
                    <input type="text" name="name" value="{{ old('name', $outlet->name) }}" class="form-input" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Outlet</label>
                    <textarea name="address" rows="3" class="form-input" required>{{ old('address', $outlet->address) }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name', $outlet->owner_name) }}" class="form-input" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $outlet->phone) }}" class="form-input" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                    <select name="region" class="form-input" required>
                        <option value="">Pilih Region</option>
                        <option value="Banjarmasin Utara" {{ old('region', $outlet->region) == 'Banjarmasin Utara' ? 'selected' : '' }}>Banjarmasin Utara</option>
                        <option value="Banjarmasin Selatan" {{ old('region', $outlet->region) == 'Banjarmasin Selatan' ? 'selected' : '' }}>Banjarmasin Selatan</option>
                        <option value="Banjarmasin Barat" {{ old('region', $outlet->region) == 'Banjarmasin Barat' ? 'selected' : '' }}>Banjarmasin Barat</option>
                        <option value="Banjarmasin Tengah" {{ old('region', $outlet->region) == 'Banjarmasin Tengah' ? 'selected' : '' }}>Banjarmasin Tengah</option>
                        <option value="Banjarmasin Timur" {{ old('region', $outlet->region) == 'Banjarmasin Timur' ? 'selected' : '' }}>Banjarmasin Timur</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="form-input" required>
                        <option value="Aktif" {{ old('status', $outlet->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Non-Aktif" {{ old('status', $outlet->status) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>

                <hr class="mt-6 mb-4 border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Dokumentasi Foto</h3>

                <div class="photo-preview-container">
                    
                    <div class="photo-box-edit">
                        @if($outlet->front_photo)
                            <img src="{{ asset('storage/' . $outlet->front_photo) }}" alt="Foto Depan Saat Ini">
                            <p>Foto Depan Saat Ini</p>
                        @else
                            <div class="h-[150px] bg-gray-100 flex items-center justify-center rounded-lg text-sm text-gray-500">Tidak ada foto lama</div>
                            <p>Tampak Depan (Belum Ada)</p>
                        @endif
                    </div>
                    
                    <div class="photo-box-edit">
                        @if($outlet->display_photo)
                            <img src="{{ asset('storage/' . $outlet->display_photo) }}" alt="Foto Etalase Saat Ini">
                            <p>Foto Etalase Saat Ini</p>
                        @else
                            <div class="h-[150px] bg-gray-100 flex items-center justify-center rounded-lg text-sm text-gray-500">Tidak ada foto lama</div>
                            <p>Foto Etalase (Belum Ada)</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Tampak Depan (Max 2MB)</label>
                        <input type="file" name="front_photo" class="form-input p-1" accept="image/*">
                        @error('front_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto Etalase (Max 2MB)</label>
                        <input type="file" name="display_photo" class="form-input p-1" accept="image/*">
                        @error('display_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                
            </div>
            
            <div class="flex gap-3 mt-8">
                <button type="submit" class="btn-primary flex-1">Update Outlet</button>
                <a href="{{ route('admin.view_outlet') }}" class="btn-secondary flex-1 text-center">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>