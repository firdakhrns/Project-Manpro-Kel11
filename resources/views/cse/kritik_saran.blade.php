<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kritik & Saran - CSE Dashboard</title>
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

        /* Style untuk Tombol Utama */
        .im3-button {
            background-color: var(--im3-red);
            color: white;
            transition: all 0.3s ease;
        }
        .im3-button:hover {
            background-color: #A00000; /* Darker red on hover */
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Tombol Kembali di dalam Box */
        .back-button-in-box { 
            background-color: var(--im3-red); 
            color: white; 
            padding: 6px; /* Lebih kecil */
            border-radius: 50%; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
            position: absolute; 
            top: 15px; /* Posisi di dalam box */
            left: 15px; 
            z-index: 20;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen py-8">
    
    <div class="max-w-xl w-full mx-auto bg-white rounded-xl shadow-lg p-8 pt-16 text-center relative">
        
        {{-- TOMBOL KEMBALI DI DALAM BOX --}}
        <a href="{{ route('cse.dashboard') }}" class="back-button-in-box">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>

        <h1 class="text-3xl font-extrabold text-gray-800 mb-2">
            Dashboard Umpan Balik DSE 
        </h1>
        <p class="text-gray-500 mb-8 border-b pb-4">
            Pilih tindakan yang ingin Anda lakukan terkait kritik dan saran untuk DSE.
        </p>

        {{-- CONTAINER PILIHAN BERDAMPINGAN (Flexbox) --}}
        <div class="flex space-x-4">
            
            {{-- Tombol 1: Input Kritik Saran (Mengambil 1/2 lebar) --}}
            <a href="{{ route('cse.kritik_saran.input') }}" class="im3-button flex-1 py-8 rounded-lg font-bold text-lg shadow-xl hover:shadow-2xl">
                <div class="flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="text-base tracking-wider"> INPUT KRITIK & SARAN</span>
                </div>
            </a>
            
            {{-- Tombol 2: Lihat Hasil Input (Mengambil 1/2 lebar, Warna Kuning IM3) --}}
            <a href="{{ route('cse.kritik_saran.hasil') }}" 
               class="flex-1 py-8 rounded-lg font-bold text-lg shadow-xl hover:shadow-2xl
                      bg-yellow-400 text-gray-900 
                      hover:bg-yellow-500 transition duration-300 transform hover:translate-y-[-2px]">
                <div class="flex flex-col items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="text-base tracking-wider"> LIHAT HASIL UMPAN BALIK</span>
                </div>
            </a>
            
        </div>
    </div>
</body>
</html>