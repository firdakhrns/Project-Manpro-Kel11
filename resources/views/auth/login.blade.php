<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IM3</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Perbaikan untuk warna fokus input */
        .input-focus-im3:focus {
            border-color: #E21B21 !important;
            box-shadow: 0 0 0 1px #E21B21 !important;
        }

        .login-bg {
            background-color: #FFDA00; /* Warna Kuning IM3 */
        }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
        
        .main-container {
            width: 80%; 
            max-width: 1100px;
            justify-content: space-between;
        }

        .login-card {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
            width: 450px;
            margin-left: 80px;
        }

        /* Container LOGO MERAH YANG BARU */
        .im3-logo-container-wrapper {
            background-color: #FFDA00; /* Background Kuning */
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Lingkaran Merah Besar di Latar Belakang Logo */
        .im3-logo-container {
            background-color: #E21B21; 
            width: 320px;
            height: 320px; 
            border-radius: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Lingkaran Merah Kecil di Latar Belakang Logo */
        .im3-logo-dot {
            background-color: #E21B21;
            width: 90px;
            height: 90px; 
            border-radius: 50%;
            position: absolute;
            top: -45px;
            right: 15px; 
        }

        /* Style untuk gambar logo yang sebenarnya */
        .im3-logo-img {
            width: 250px; /* Sesuaikan ukuran gambar */
            height: auto;
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center">
    
    <div class="flex items-center main-container">
        
        <div class="flex items-center justify-center im3-logo-container-wrapper">
            <div class="im3-logo-container">
                
                <img 
    src="{{ asset('build/assets/img/im3_logo.png') }}" 
    alt="Logo IM3 Ooredoo" 
    class="im3-logo-img"/>
                <div class="im3-logo-dot"></div> 
            </div>
        </div>
        
        <div class="bg-white p-12 login-card">
            <h1 class="text-4xl font-extrabold mb-2">Selamat Datang!</h1>
            <p class="text-gray-700 mb-8">Silakan Masuk ke Akun Anda</p>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <!-- ID DSE Field -->
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-800 mb-1">
                        ID DSE
                    </label>

                    <input
                        type="text"
                        name="dse_id"  
                        class="block w-full px-3 py-2 border border-gray-300 focus:border-blue-600 rounded-md outline-none"
                        placeholder="Masukkan ID DSE / Username Admin"
                        required
                        autofocus
                        value="{{ old('identifier') }}">
                </div>

                <!-- Password Field -->
                <div class="mb-7">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-bold text-gray-800 mb-1">
                            Kata Sandi
                        </label>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Lupa kata sandi?</a>
                    </div>
                    <input
                        type="password"
                        name="password"
                        class="block w-full px-3 py-2 border border-gray-300 focus:border-blue-600 rounded-md outline-none"
                        placeholder="Masukkan Kata Sandi"
                        required>
                </div>

                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-md transition duration-150 shadow-md" style="background-color: #E21B21;">

                    MASUK

                </button>
            </form>
        </div>
    </div>
</body>
</html>