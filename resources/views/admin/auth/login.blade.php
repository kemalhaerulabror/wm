<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Wipa Motor</title>
    
    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-10">
            <h1 class="text-white text-3xl font-bold">Wipa <span class="bg-white text-gray-800 px-2 rounded-md">Admin</span></h1>
            <p class="text-gray-300 mt-2">Panel Kontrol Admin</p>
        </div>
        
        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login Admin</h2>
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            @error('login')
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <p>{{ $message }}</p>
                </div>
            @enderror
            
            @error('role')
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <p>{{ $message }}</p>
                    </div>
                </div>
            @enderror
            
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                
                <div class="mb-6">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" class="bg-gray-50 border @error('email') border-red-500 @else @enderror text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full pl-10 p-2.5" placeholder="admin@wipamotor.com" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" class="bg-gray-50 border @error('password') border-red-500 @else @enderror text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full pl-10 p-2.5" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500">
                    <label for="remember" class="ml-2 text-sm text-gray-700">Ingat saya</label>
                </div>
                
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-lg text-sm px-5 py-3 text-center transition-colors duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-gray-300">
            <p>&copy; 2025 Wipa Motor. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
