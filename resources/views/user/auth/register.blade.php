<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Akun - Wipa Motor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-8">
    <div class="container mx-auto px-4">
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
                    <h2 class="text-xl font-bold">DAFTAR AKUN</h2>
                </div>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mt-4 mx-6 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                        <div class="mt-3 text-center">
                            <a href="{{ route('login') }}" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none inline-block">
                                Masuk Sekarang
                            </a>
                        </div>
                    </div>
                @endif
                
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mt-4 mx-6 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                
                <form class="py-4 px-6" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border @error('name') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border @error('email') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Masukkan email" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700 text-sm font-medium mb-2">Nomor Telepon</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border @error('phone') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Masukkan nomor telepon" required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-3 py-2 border @error('password') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Masukkan password" required>
                        <p class="mt-1 text-xs text-gray-500">Password minimal 8 karakter dengan huruf dan angka</p>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-3 py-2 border @error('password_confirmation') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Konfirmasi password" required>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="terms" name="terms" class="h-4 w-4 text-gray-800 border-gray-300 rounded focus:ring-gray-800" required>
                            <label for="terms" class="ml-2 block text-sm text-gray-700 @error('terms') @enderror">
                                Saya menyetujui <a href="#" class="text-gray-800 hover:underline">syarat dan ketentuan</a> yang berlaku
                            </label>
                        </div>
                        @error('terms')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <button type="submit" id="registerButton" class="w-full bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-opacity-50">
                            Daftar Sekarang
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-700">Sudah punya akun? <a href="{{ route('login') }}" class="text-gray-800 font-medium hover:underline">Masuk disini</a></p>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-6">
                <a href="/" class="text-sm text-gray-600 hover:underline">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cegah multiple submit pada form register
            const registerForm = document.querySelector('form[action="{{ route("register") }}"]');
            
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    // Cegah multiple submit
                    const registerButton = document.getElementById('registerButton');
                    if (registerButton && registerButton.getAttribute('data-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Set status submitting
                    if (registerButton) {
                        registerButton.setAttribute('data-submitting', 'true');
                        registerButton.disabled = true;
                        registerButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                    }
                });
            }
        });
    </script>
</body>
</html> 