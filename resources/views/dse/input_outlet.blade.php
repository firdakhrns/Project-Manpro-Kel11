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
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            padding: 10px;
            height: 44px; 
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
        .required-label { 
            display: flex;
            align-items: center;
            gap: 4px;
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

        {{-- NOTIFIKASI SUKSES / ERROR / VALIDASI --}}
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
            
            <div class="grid grid-cols-3 gap-x-12 gap-y-6">
                {{-- KOLOM KIRI --}}
                <div>
                    <div class="input-group mb-6">
                        <label for="nama_outlet" class="required-label">
                            Nama Outlet <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_outlet" name="nama_outlet" placeholder="Masukkan nama outlet" required value="{{ old('nama_outlet') }}">
                    </div>
                    <div class="input-group mb-6">
                        <label for="alamat_outlet" class="required-label">
                            Alamat Outlet <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="alamat_outlet" name="alamat_outlet" placeholder="Masukkan alamat outlet" required value="{{ old('alamat_outlet') }}">
                    </div>
                    <div class="input-group">
                        <label for="tanggal_bergabung" class="required-label">
                            Tanggal Bergabung <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal_bergabung" name="tanggal_bergabung" placeholder="Masukkan tanggal" required value="{{ old('tanggal_bergabung') }}">
                    </div>
                </div>

                {{-- KOLOM TENGAH --}}
                <div>
                    <div class="input-group mb-6">
                        <label for="nama_pemilik" class="required-label">
                            Nama Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama_pemilik" name="nama_pemilik" placeholder="Masukkan nama" required value="{{ old('nama_pemilik') }}">
                    </div>
                    <div class="input-group mb-6">
                        <label for="no_telepon_pemilik" class="required-label">
                            No. Telepon Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="no_telepon_pemilik" name="no_telepon_pemilik" placeholder="Masukkan nomor telepon" required value="{{ old('no_telepon_pemilik') }}">
                    </div>
                    <div class="input-group">
                        <label for="no_telepon_darurat">
                            No. Telepon Darurat (Opsional)
                        </label>
                        <input type="tel" id="no_telepon_darurat" name="no_telepon_darurat" placeholder="Masukkan nomor telepon" value="{{ old('no_telepon_darurat') }}">
                    </div>
                </div>

                {{-- KOLOM KANAN (FOTO) --}}
                <div>
                    <div class="input-group mb-6">
                        <label for="tampak_depan_outlet_file" class="required-label">
                            Tampak Depan Outlet <span class="text-red-500">*</span>
                        </label>
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
                        <label for="foto_etalase_file" class="required-label">
                            Foto Etalase <span class="text-red-500">*</span>
                        </label>
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
    </div>

    {{-- SCRIPTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInputDepan = document.getElementById('tampak_depan_outlet_file');
            const fileInputEtalase = document.getElementById('foto_etalase_file');
            const submitButton = document.getElementById('submitButton');
            const form = document.getElementById('outletForm');
            
            // Fungsi untuk menampilkan nama file yang sudah diunggah
            function updateFileNameDisplay(inputElement, displayId) {
                const displayElement = document.getElementById(displayId);
                
                inputElement.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const fileName = this.files[0].name;
                        // Menampilkan ikon ceklis hijau dan nama file
                        displayElement.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-green-600 font-medium truncate">${fileName}</span>
                        `;
                    } else {
                        // Kembali ke tampilan default jika file dihapus
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
            
            // Handler kustom untuk tombol submit
            submitButton.addEventListener('click', function(e) {
                
                // Cek Validitas Form HTML5 (termasuk required pada input text/date)
                if (!form.reportValidity()) {
                    return;
                }
                
                // Cek file input kustom (file input required butuh cek kustom)
                if (fileInputDepan.files.length === 0) {
                    // Alert kustom jika file Depan belum diisi
                    alert("GAGAL SUBMIT: Harap unggah foto 'Tampak Depan Outlet' terlebih dahulu.");
                    fileInputDepan.focus();
                    return; 
                }

                if (fileInputEtalase.files.length === 0) {
                    // Alert kustom jika file Etalase belum diisi
                    alert("GAGAL SUBMIT: Harap unggah foto 'Foto Etalase' terlebih dahulu.");
                    fileInputEtalase.focus();
                    return;
                }

                // Jika semua validasi klien (HTML5 dan kustom file) berhasil, submit form
                form.submit();
            });
        });
    </script>
</body>
</html>