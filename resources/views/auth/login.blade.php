<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IM3</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .input-focus-im3:focus {
            border-color: #E21B21 !important;
            box-shadow: 0 0 0 1px #E21B21 !important;
        }

        .login-bg {
            background-color: #FFDA00; 
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

        .im3-logo-container-wrapper {
            background-color: #FFDA00; 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

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
        
        .im3-logo-dot {
            background-color: #E21B21;
            width: 90px;
            height: 90px; 
            border-radius: 50%;
            position: absolute;
            top: -45px;
            right: 15px; 
        }

        .im3-logo-img {
            width: 250px; 
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
    src="{{ asset('asset/img/Logo_IM3-removebg-preview.png') }}" 
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

                <div class="mb-7">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-bold text-gray-800 mb-1">
                        Kata Sandi
                        </label>
                    </div>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password_input"
                            class="block w-full px-3 py-2 border border-gray-300 focus:border-blue-600 rounded-md outline-none pr-10"
                            placeholder="Masukkan Kata Sandi"
                            required>
        
                        <button type="button" id="toggle_password" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                            <svg id="icon_eye" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="icon_eye_slash" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.096 0 2.164.195 3.14.568M19 12c-1.274 4.057-5.064 7-9.542 7-1.096 0-2.164-.195-3.14-.568m5.679 2.518a4 4 0 01-5.657-5.657m1.414 1.414a2 2 0 012.828 0" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-md transition duration-150 shadow-md" style="background-color: #E21B21;">

                    MASUK

                </button>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('toggle_password');
        const passwordInput = document.getElementById('password_input');
        const iconEye = document.getElementById('icon_eye');
        const iconEyeSlash = document.getElementById('icon_eye_slash');

        togglePassword.addEventListener('click', function (e) {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                iconEye.classList.add('hidden');
                iconEyeSlash.classList.remove('hidden');
            } else {
                iconEye.classList.remove('hidden');
                iconEyeSlash.classList.add('hidden');
            }
        });
    });
</script>
</body>
</html>