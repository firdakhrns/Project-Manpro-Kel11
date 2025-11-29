<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Stok Retur DSE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; } 
        
        /* Latar Belakang Merah-Kuning (Sama seperti Stok) */
        body { 
            background-color: var(--im3-yellow); 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; 
            position: relative; 
            /* HAPUSKAN overflow: hidden */
        }
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }

        .container { 
            max-width: 900px; 
            position: relative; 
            z-index: 10; 
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
            top: 20px; /* Posisi di dalam card */
            left: 20px;
            transform: translate(0, 0); 
            z-index: 11;
        }
        .back-button:hover { background-color: #b71c1c; }

        /* Styling Form dan Button */
        .input-group label { font-weight: 600; margin-bottom: 8px; display: block; }
        .input-group input { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; }
        .submit-button { 
            background-color: var(--im3-red); 
            color: white; 
            padding: 12px 40px; 
            border-radius: 9999px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: background-color 0.2s; 
        }
        .submit-button:hover { background-color: #c7171d; }

        /* Category Toggle (Sama dengan Stok) */
        .category-buttons { background-color: #e5e7eb; padding: 4px; border-radius: 25px; display: inline-flex; }
        .category-button {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: transparent;
            color: #4b5563;
        }
        .category-button.active {
            background-color: var(--im3-red);
            color: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
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
            <h1 class="text-4xl font-extrabold text-center mb-2">Pencatatan Stok Retur</h1>
            <p class="text-gray-700 text-center mb-8">Silakan isi sesuai jumlah yang diretur</p>
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

        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-800 mb-2" for="outlet_select_retur">Outlet Retur</label>
            <select name="outlet_select_retur" id="outlet_select_retur" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                <option value="">Pilih Outlet</option>
                {{-- Asumsi variabel $outlets tersedia --}}
                @foreach($outlets ?? [] as $outlet)
                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-center mb-8">
            <div class="category-buttons">
                <button type="button" class="category-button active" data-category="all">All</button>
                <button type="button" class="category-button" data-category="kartu-perdana">Kartu Perdana</button>
                <button type="button" class="category-button" data-category="voucher">Voucher</button>
            </div>
        </div>

        <form action="{{ route('dse.input_retur.store') }}" method="POST">
            @csrf

            <input type="hidden" name="outlet_id" id="outlet_id_hidden_retur">

            <div class="grid grid-cols-2 gap-x-12 gap-y-6">
                
                <div id="kartu-perdana-section" class="category-section">
                    <h2 class="text-xl font-bold mb-4">Kartu Perdana</h2>
                    <div class="grid grid-cols-1 gap-6">
                        @php $kp_products = ['3 GB', '6 GB', '9 GB', '20 GB']; @endphp
                        @foreach($kp_products as $product)
                            @php $name = strtolower(str_replace(' ', '_', $product)); @endphp
                            <div class="input-group">
                                <label for="kp_retur_{{ $name }}">{{ $product }}</label>
                                <input type="number" id="kp_retur_{{ $name }}" name="retur[kp][{{ $name }}]" placeholder="Masukkan jumlah" min="0" value="0">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="voucher-section" class="category-section">
                    <h2 class="text-xl font-bold mb-4">Voucher</h2>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-6">
                        @php
                            $v_products = [
                                '1 GB/2 hari', '15 GB/7 hari',
                                '3 GB/3 hari', '3 GB/28 hari',
                                '5 GB/5 hari', '5 GB/2 hari',
                                '7 GB/7 hari', '5 GB/3 hari',
                            ];
                        @endphp
                        @foreach($v_products as $product)
                            @php $name = strtolower(str_replace([' ', '/', 'hari'], ['_', '_', 'h'], $product)); @endphp
                            <div class="input-group">
                                <label for="v_retur_{{ $name }}">{{ $product }}</label>
                                <input type="number" id="v_retur_{{ $name }}" name="retur[v][{{ $name }}]" placeholder="Masukkan jumlah" min="0" value="0">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="text-center mt-10">
                <button type="submit" class="submit-button">SUBMIT</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.category-button');
            const sections = document.querySelectorAll('.category-section');
            const outletSelect = document.getElementById('outlet_select_retur');
            const outletHidden = document.getElementById('outlet_id_hidden_retur');
            const form = document.querySelector('form');

            // 1. Update hidden outlet_id dan simpan di localStorage
            outletSelect.addEventListener('change', function() {
                outletHidden.value = this.value;
                localStorage.setItem('selected_outlet_id_retur', this.value);
            });
            
            // 2. Load selected outlet dari localStorage
            const storedOutletId = localStorage.getItem('selected_outlet_id_retur');
            if (storedOutletId) {
                outletSelect.value = storedOutletId;
                outletHidden.value = storedOutletId;
            }

            // 3. Logika Show/Hide Kategori
            function showCategory(category) {
                sections.forEach(section => {
                    const isPerdana = section.id === 'kartu-perdana-section';
                    const isVoucher = section.id === 'voucher-section';
                    const parentGrid = document.querySelector('.grid-cols-2');

                    if (category === 'all') {
                        section.style.display = 'block';
                        parentGrid.style.gridTemplateColumns = '1fr 1fr';
                    } else if (category === 'kartu-perdana' && isPerdana) {
                        section.style.display = 'block';
                        parentGrid.style.gridTemplateColumns = '1fr';
                    } else if (category === 'voucher' && isVoucher) {
                        section.style.display = 'block';
                        parentGrid.style.gridTemplateColumns = '1fr';
                    } else {
                        section.style.display = 'none';
                    }
                });
            }

            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    showCategory(this.dataset.category);
                });
            });

            // 4. Validasi Form sebelum Submit
            form.addEventListener('submit', function(e) {
                if (outletHidden.value === "") {
                    e.preventDefault();
                    alert("Mohon pilih Outlet terlebih dahulu.");
                    outletSelect.focus();
                }
            });

            // Initial display
            showCategory('all');
        });
    </script>
</body>
</html>