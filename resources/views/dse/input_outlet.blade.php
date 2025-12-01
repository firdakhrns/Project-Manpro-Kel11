<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Validasi Outlet DSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; } 
        
        /* Latar Belakang Merah-Kuning (Sama seperti Stok) */
        body { 
            background-color: var(--im3-yellow); 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; 
            position: relative; 
        }
        /* Visual Merah (Pola Latar Belakang) */
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container { 
            max-width: 900px; 
            position: relative; 
            z-index: 10; 
            /* Tambahkan margin atas/bawah untuk centering yang lebih baik di layar besar */
            margin-top: 50px; 
            margin-bottom: 50px;
        }

        /* Tombol Kembali Merah (DIUBAH POSISI) */
        .back-button {
            background-color: var(--im3-red);
            color: white;
            padding: 8px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: background-color 0.2s;
            position: absolute;
            top: 20px; 
            left: 20px;
            z-index: 11;
        }
        .back-button:hover { background-color: #b71c1c; }

        /* Styling Form dan Input */
        .input-group label { font-weight: 600; margin-bottom: 8px; display: block; }
        .input-group input[type="text"], .input-group input[type="date"], .input-group input[type="tel"] { 
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            box-sizing: border-box; 
        }
        
        /* Area Upload Foto (DIUBAH) */
        .upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db; /* Border solid, bukan dashed */
            border-radius: 6px; /* Sesuai input field */
            padding: 10px; /* Padding lebih kecil */
            height: 44px; /* Tinggi disesuaikan agar sejajar dengan input */
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .upload-area:hover { background-color: #f9f9f9; }
        .upload-area input[type="file"] { display: none; }
        .upload-area-content {
            display: flex;
            align-items: center;
            font-size: 0.9em;
            color: #4b5563;
        }

        /* Tombol Submit */
        .submit-button { 
            background-color: #E21B21; 
            color: white; 
            padding: 12px 40px; 
            border-radius: 9999px; /* DIUBAH: Rounded penuh */
            font-weight: bold; 
            cursor: pointer; 
            transition: background-color 0.2s; 
        }
        .submit-button:hover { background-color: #c7171d; }
        
        .header-content {
            padding-top: 40px; /* Memberi ruang di bawah tombol back */
        }
    </style>
</head>
<body class="flex items-start justify-center min-h-screen p-4">
    <div class="bg-white p-10 rounded-2xl shadow-xl container">
        
        <a href="{{ route('dse.dashboard') }}" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>

        <div class="header-content">
            <h1 class="text-4xl font-extrabold text-center mb-2">Data Validasi Outlet</h1>
            <p class="text-gray-700 text-center mb-8">Silakan diisi dengan data sebenar-benarnya</p>
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

@if ($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        <strong class="font-bold">Gagal Validasi!</strong>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form id="outletForm" action="{{ route('dse.input_outlet.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- Hapus duplikasi @csrf yang ada di kode lama --}}
            {{-- Hapus duplikasi tag <form> yang ada di kode lama --}}

            {{-- <input type="hidden" name="tanggal_input" value="<?php echo date('Y-m-d'); ?>"> --}}

            <div class="grid grid-cols-3 gap-x-12 gap-y-6">
                <div>
                    <div class="input-group mb-6">
                        <label for="nama_outlet">Nama Outlet</label>
                        <input type="text" id="nama_outlet" name="nama_outlet" placeholder="Masukkan nama outlet" required>
                    </div>
                    <div class="input-group mb-6">
                        <label for="alamat_outlet">Alamat Outlet</label>
                        <input type="text" id="alamat_outlet" name="alamat_outlet" placeholder="Masukkan alamat outlet" required>
                    </div>
                    <div class="input-group">
                        <label for="tanggal_bergabung">Tanggal Bergabung</label>
                        <input type="date" id="tanggal_bergabung" name="tanggal_bergabung" placeholder="Masukkan tanggal" required>
                    </div>
                </div>

                <div>
                    <div class="input-group mb-6">
                        <label for="nama_pemilik">Nama Pemilik</label>
                        <input type="text" id="nama_pemilik" name="nama_pemilik" placeholder="Masukkan nama" required>
                    </div>
                    <div class="input-group mb-6">
                        <label for="no_telepon_pemilik">No. Telepon Pemilik</label>
                        <input type="tel" id="no_telepon_pemilik" name="no_telepon_pemilik" placeholder="Masukkan nomor telepon" required>
                    </div>
                    <div class="input-group">
                        <label for="no_telepon_darurat">No. Telepon Darurat</label>
                        <input type="tel" id="no_telepon_darurat" name="no_telepon_darurat" placeholder="Masukkan nomor telepon">
                    </div>
                </div>

                <div>
                    <div class="input-group mb-6">
                        <label for="tampak_depan_outlet_file">Tampak Depan Outlet</label>
                        <label class="upload-area" for="tampak_depan_outlet_file">
                            <div class="upload-area-content" id="filename_depan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Unggah Foto</span>
                            </div>
                            <input type="file" id="tampak_depan_outlet_file" name="tampak_depan_outlet_file" accept="image/*" required>
                        </label>
                    </div>

                    <div class="input-group">
                        <label for="foto_etalase_file">Foto Etalase</label>
                        <label class="upload-area" for="foto_etalase_file">
                            <div class="upload-area-content" id="filename_etalase">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Unggah Foto</span>
                            </div>
                            <input type="file" id="foto_etalase_file" name="foto_etalase_file" accept="image/*" required>
                        </label>
                    </div>
                </div>
            </div>

            <div class="text-center mt-10">
                <button type="button" id="submitButton" class="submit-button">SUBMIT</button>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Definisikan semua elemen di dalam scope ini
                const fileInputDepan = document.getElementById('tampak_depan_outlet_file');
                const fileInputEtalase = document.getElementById('foto_etalase_file');
                const submitButton = document.getElementById('submitButton');
                const form = document.getElementById('outletForm');
                
                function updateFileNameDisplay(inputElement, displayId) {
                    const displayElement = document.getElementById(displayId);
                    
                    inputElement.addEventListener('change', function() {
                        if (this.files.length > 0) {
                            const fileName = this.files[0].name;
                            // Tampilkan nama file yang dipilih dengan icon centang
                            displayElement.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-green-600 font-medium truncate">${fileName}</span>
                            `;
                        } else {
                            // Kembalikan ke tampilan default jika file dibatalkan
                            displayElement.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Unggah Foto</span>
                            `;
                        }
                    });
                }

                updateFileNameDisplay(fileInputDepan, 'filename_depan');
                updateFileNameDisplay(fileInputEtalase, 'filename_etalase');
                
                // Event Listener Submit
                submitButton.addEventListener('click', function(e) {
                    
                    // 1. Validasi Foto Depan
                    if (fileInputDepan.files.length === 0) {
                        alert("❗ GAGAL SUBMIT: Harap unggah foto 'Tampak Depan Outlet' terlebih dahulu.");
                        return; 
                    }

                    // 2. Validasi Foto Etalase
                    if (fileInputEtalase.files.length === 0) {
                        alert("❗ GAGAL SUBMIT: Harap unggah foto 'Foto Etalase' terlebih dahulu.");
                        return;
                    }

                    // 3. Validasi HTML5 untuk field required lainnya
                    if (!form.reportValidity()) {
                        // reportValidity akan menampilkan pesan error bawaan browser untuk field required non-file
                        return;
                    }

                    // Jika semua validasi lolos, lakukan submit
                    form.submit();
                });
            });
        </script>
        
    </div>
</body>
</html>