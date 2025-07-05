<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lupa Password - Wipa Motor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-6">
                <a href="/" class="inline-block">
                    <div class="flex items-center justify-center">
                        <i class="fa-solid fa-motorcycle text-gray-800 text-3xl mr-2"></i>
                        <span class="text-2xl font-bold text-gray-800">WP Motor</span>
                    </div>
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="py-4 px-6 bg-gray-800 text-white text-center">
                    <h2 class="text-xl font-bold">LUPA PASSWORD</h2>
                </div>
                
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mt-4 mx-6 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                
                <div class="py-4 px-6">
                    <p class="text-gray-700 text-sm mb-4">
                        Masukkan alamat email yang terdaftar, kami akan mengirimkan link untuk mengatur ulang password Anda.
                    </p>
                    
                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border @error('email') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Masukkan email" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-opacity-50">
                                Kirim Link Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:underline">Kembali ke halaman login</a>
            </div>
        </div>
    </div>
</body>
</html> 