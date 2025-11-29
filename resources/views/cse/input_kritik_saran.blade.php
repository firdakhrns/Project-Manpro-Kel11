<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kritik & Saran untuk DSE - CSE Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
        :root { --im3-yellow: #FFDA00; --im3-red: #E21B21; }
        
        /* Latar Belakang Merah-Kuning (Sesuai Desain) */
        body { 
            background-color: var(--im3-yellow); 
            font-family: 'Inter', sans-serif; 
            position: relative;
        }
        /* Pola Merah (Sesuai Desain) */
        body::before { content: ''; position: fixed; top: 0; right: 0; width: 300px; height: 300px; background-color: var(--im3-red); border-radius: 50%; transform: translate(50%, -50%); z-index: 0; }
        body::after { content: ''; position: fixed; bottom: 0; left: 0; width: 400px; height: 400px; background-color: var(--im3-red); border-radius: 50%; transform: translate(-50%, 50%); z-index: 0; }
</style>
<body class="flex items-center justify-center min-h-screen py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm p-6 md:p-8">
        <!-- Header -->
        <div class="flex items-center mb-6 pb-4 border-b">
            <a href="{{ route('cse.kritik_saran') }}" class="mr-4 text-red-600 hover:text-red-700 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kritik & Saran Regional</h1>
                <p class="text-gray-600 mt-1">Berikan umpan balik konstruktif kepada DSE di cluster Anda</p>
            </div>
        </div>

        <!-- Notifications -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center text-green-700">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('cse.kritik_saran.store') }}" method="POST">
            @csrf

            <!-- DSE Selection -->
            <div class="mb-6">
                <label for="dse_target" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih DSE <span class="text-red-500">*</span>
                </label>
                <select id="dse_target" name="dse_target" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200">
                    <option value="">-- Pilih DSE --</option>
                    @foreach($dseList as $dse)
                        <option value="{{ $dse->id_dse }}" {{ old('dse_target') == $dse->id_dse ? 'selected' : '' }}>
                            {{ $dse->id_dse }} - {{ $dse->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Feedback Type -->
            <div class="mb-6">
                <label for="jenis_feedback" class="block text-sm font-medium text-gray-700 mb-2">
                    Jenis Umpan Balik <span class="text-red-500">*</span>
                </label>
                <select id="jenis_feedback" name="jenis_feedback" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200">
                    <option value="Kritik" {{ old('jenis_feedback') == 'Kritik' ? 'selected' : '' }}>Kritik (Area Perbaikan)</option>
                    <option value="Saran" {{ old('jenis_feedback') == 'Saran' ? 'selected' : '' }}>Saran (Ide Baru)</option>
                </select>
            </div>

            <!-- Feedback Details -->
            <div class="mb-8">
                <label for="feedback_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Detail Feedback <span class="text-red-500">*</span>
                </label>
                <textarea id="feedback_text" name="feedback_text" rows="5" 
                          placeholder="Tuliskan kritik atau saran Anda di sini secara detail dan konstruktif. Berikan contoh dan saran perbaikan jika memungkinkan."
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200">{{ old('feedback_text') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Minimal 10 karakter</p>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform hover:scale-[1.02]">
                KIRIM UMPAN BALIK
            </button>
        </form>
    </div>
</body>
</html>