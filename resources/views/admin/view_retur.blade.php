<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Retur Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; }
        /* Latar Belakang Merah-Kuning */
        body { 
            background-color: var(--im3-yellow); 
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif; 
            position: relative;
            z-index: 1;
        }
        /* Pola Merah (Sesuai Gambar Desain) */
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }
        
        .container { max-width: 1000px; position: relative; z-index: 10; }
        
        /* Tombol Kembali Merah Bulat */
        .back-button-circle {
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
        .back-button-circle:hover { 
            background-color: #b71c1c; 
        }

        /* Styling Input dan Label */
        .input-group label { 
            font-weight: 600; 
            margin-bottom: 8px; 
            display: block; 
            color: #374151; 
        }
        .input-group input, .input-group select { 
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            box-sizing: border-box; 
        }

        /* Category Toggle */
        .category-buttons-wrapper { background-color: #e5e7eb; padding: 4px; border-radius: 25px; display: inline-flex; }
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
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        /* Submit Button */
        .submit-button { 
            background-color: var(--im3-red); 
            color: white; 
            padding: 12px 40px; 
            border-radius: 9999px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: background-color 0.2s; 
            border: none;
        }
        .submit-button:hover { background-color: #c7171d; }

        /* Grid Setup (Sama dengan Stok Admin) */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr 1fr; /* 3 kolom */
            }
        }
        
    </style>
</head>
<body class="flex items-start justify-center min-h-screen p-4">
    <div class="bg-white p-6 md:p-10 rounded-2xl shadow-xl container">
        
        <a href="{{ route('admin.dashboard') }}" class="back-button-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>

        <div class="header-content pt-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-center mb-2">Pencatatan Retur Admin</h1>
            <p class="text-gray-700 text-center mb-8">Pilih DSE dan Outlet sebelum mencatat jumlah retur.</p>
        </div>

        <form id="returFormAdmin" action="{{ route('admin.view_retur') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="input-group">
                    <label for="dse_id">Pilih DSE Target</label>
                    <select name="dse_id" id="dse_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">Pilih DSE</option>
                        @foreach($dseUsers ?? [] as $user)
                            <option value="{{ $user->id_dse }}">{{ $user->name }} ({{ $user->id_dse }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="input-group">
                    <label for="outlet_id_select">Pilih Outlet</label>
                    <select name="outlet_id" id="outlet_id_select" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                        <option value="">Pilih Outlet</option>
                        @foreach($outlets ?? [] as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }} - {{ $outlet->region }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-center mb-8">
                <div class="category-buttons-wrapper">
                    <button type="button" class="category-button active" data-category="all">All</button>
                    <button type="button" class="category-button" data-category="kartu-perdana">Kartu Perdana</button>
                    <button type="button" class="category-button" data-category="voucher">Voucher</button>
                </div>
            </div>

            <div class="grid form-grid">
                
                <div id="kartu-perdana-section" class="category-section" data-product-type="kartu-perdana">
                    <h2 class="text-xl font-bold mb-4">Kartu Perdana</h2>
                    <div class="space-y-4">
                        {{-- FIELD NAMES DIUBAH MENJADI 'retur' --}}
                        <div class="input-group"><label for="kp_3gb">3 GB</label><input type="number" id="kp_3gb" name="retur[kp][3gb]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        <div class="input-group"><label for="kp_6gb">6 GB</label><input type="number" id="kp_6gb" name="retur[kp][6gb]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        <div class="input-group"><label for="kp_9gb">9 GB</label><input type="number" id="kp_9gb" name="retur[kp][9gb]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        <div class="input-group"><label for="kp_20gb">20 GB</label><input type="number" id="kp_20gb" name="retur[kp][20gb]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                    </div>
                </div>

                <div id="voucher-section" class="category-section md:col-span-2" data-product-type="voucher">
                    <h2 class="text-xl font-bold mb-4">Voucher</h2>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                        
                        {{-- FIELD NAMES DIUBAH MENJADI 'retur' --}}
                        <div class="input-group"><label for="v_1gb_2h">1 GB/2 hari</label><input type="number" id="v_1gb_2h" name="retur[v][1gb_2h]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        <div class="input-group"><label for="v_15gb_7h">15 GB/7 hari</label><input type="number" id="v_15gb_7h" name="retur[v][15gb_7h]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        
                        <div class="input-group"><label for="v_3gb_3h">3 GB/3 hari</label><input type="number" id="v_3gb_3h" name="retur[v][3gb_3h]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        <div class="input-group"><label for="v_3gb_28h">3 GB/28 hari</label><input type="number" id="v_3gb_28h" name="retur[v][3gb_28h]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        
                        <div class="input-group"><label for="v_5gb_5h">5 GB/5 hari</label><input type="number" id="v_5gb_5h" name="retur[v][5gb_5h]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        <div class="input-group"><label for="v_5gb_2h">5 GB/2 hari</label><input type="number" id="v_5gb_2h" name="retur[v][5gb_2h]" placeholder="Masukkan jumlah" min="0" value="0"></div>

                        <div class="input-group"><label for="v_7gb_7h">7 GB/7 hari</label><input type="number" id="v_7gb_7h" name="retur[v][7gb_7h]" placeholder="Masukkan jumlah" min="0" value="0"></div>
                        <div class="input-group"><label for="v_5gb_3h">5 GB/3 hari</label><input type="number" id="v_5gb_3h" name="retur[v][5gb_3h]" placeholder="Masukkan jumlah" min="0" value="0"></div>
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
            const dseSelect = document.getElementById('dse_id');
        const outletSelect = document.getElementById('outlet_id_select');

    
        const form = document.getElementById('returFormAdmin');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!dseSelect.value || !outletSelect.value) {
                    e.preventDefault();
                    alert('Harap pilih DSE dan Outlet terlebih dahulu!');
                    return false;
                }
            });
        }

            const categoryButtons = document.querySelectorAll('.category-button');
            const kpSection = document.getElementById('kartu-perdana-section');
            const voucherSection = document.getElementById('voucher-section');

            function showCategory(category) {
                if (category === 'all') {
                    kpSection.style.display = 'block';
                    voucherSection.style.display = 'block';
                    document.querySelector('.container > form .grid').style.gridTemplateColumns = '1fr 2fr';
                } else if (category === 'kartu-perdana') {
                    kpSection.style.display = 'block';
                    voucherSection.style.display = 'none';
                    document.querySelector('.container > form .grid').style.gridTemplateColumns = '1fr';
                } else if (category === 'voucher') {
                    kpSection.style.display = 'none';
                    voucherSection.style.display = 'block';
                    document.querySelector('.container > form .grid').style.gridTemplateColumns = '1fr';
                }
            }

            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    showCategory(this.dataset.category);
                });
            });

            showCategory('all');
        });
    </script>
</body>
</html>