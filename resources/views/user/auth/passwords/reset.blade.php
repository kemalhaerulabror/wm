<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password - Wipa Motor</title>
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
                    <h2 class="text-xl font-bold">RESET PASSWORD</h2>
                </div>
                
                <div class="py-4 px-6">
                    <p class="text-gray-700 text-sm mb-4">
                        Silakan masukkan password baru untuk akun Anda.
                    </p>
                    
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" class="w-full px-3 py-2 border @error('email') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" required autofocus>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password Baru</label>
                            <input type="password" id="password" name="password" class="w-full px-3 py-2 border @error('password') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" required>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" required>
                        </div>
                        
                        <div class="mb-4">
                            <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-opacity-50">
                                Reset Password
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