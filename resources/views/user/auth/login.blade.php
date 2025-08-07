<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Masuk Akun - Wipa Motor</title>
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
                        <span class="text-2xl font-bold text-gray-800">Wipa Motor</span>
                    </div>
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="py-4 px-6 bg-gray-800 text-white text-center">
                    <h2 class="text-xl font-bold">MASUK AKUN</h2>
                </div>
                
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mt-4 mx-6 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                        
                        @if (strpos(session('error'), 'belum diverifikasi') !== false)
                            <div class="mt-2">
                                <form action="{{ route('verification.resend.guest') }}" method="POST" class="text-center">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ old('email') }}">
                                    <button type="submit" class="bg-gray-800 text-white px-3 py-1 text-xs rounded-md hover:bg-gray-700">
                                        Kirim Ulang Link Verifikasi
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if (session('next_available'))
                            <div class="mt-2 text-center text-sm">
                                <span>Dapat mengirim ulang dalam: </span>
                                <span id="countdown" class="font-semibold" data-timestamp="{{ session('next_available') }}"></span>
                                <form id="resendForm" action="{{ route('verification.resend.guest') }}" method="POST" class="mt-2 hidden">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ old('email') }}">
                                    <button type="submit" id="resendButton" class="bg-gray-800 text-white px-3 py-1 text-xs rounded-md hover:bg-gray-700">
                                        Kirim
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mt-4 mx-6 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('verification_sent'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 mt-4 mx-6 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('verification_sent') }}</span>
                    </div>
                @endif
                
                <form class="py-4 px-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border @error('email') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Masukkan email" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-3 py-2 border @error('password') border-red-500 @else @enderror rounded-md focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent" placeholder="Masukkan password" required>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-gray-800 border-gray-300 rounded focus:ring-gray-800">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm text-gray-700 hover:underline">Lupa password?</a>
                    </div>
                    
                    <div class="mb-4">
                        <button type="submit" id="loginButton" class="w-full bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-opacity-50">
                            Masuk
                        </button>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-700">Belum punya akun? <a href="{{ route('register') }}" class="text-gray-800 font-medium hover:underline">Daftar sekarang</a></p>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-6">
                <a href="/" class="text-sm text-gray-600 hover:underline">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        // Countdown Timer
        document.addEventListener('DOMContentLoaded', function() {
            const countdownElement = document.getElementById('countdown');
            const resendForm = document.getElementById('resendForm');
            
            if (countdownElement) {
                const targetTimestamp = parseInt(countdownElement.dataset.timestamp) * 1000;
                
                // Update timer setiap detik
                const timer = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = targetTimestamp - now;
                    
                    // Jika waktu sudah habis
                    if (distance < 0) {
                        clearInterval(timer);
                        countdownElement.innerHTML = '<span class="text-green-600">Sekarang</span>';
                        
                        // Tampilkan tombol kirim
                        if (resendForm) {
                            resendForm.classList.remove('hidden');
                        }
                        
                        return;
                    }
                    
                    // Hitung menit dan detik
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    // Format tampilan waktu
                    let timeString = '';
                    if (minutes > 0) {
                        timeString += minutes + ' menit ';
                    }
                    timeString += seconds + ' detik';
                    
                    countdownElement.textContent = timeString;
                }, 1000);
            }
            
            // Simpan nilai email saat form login disubmit
            const loginForm = document.querySelector('form[action="{{ route("login") }}"]');
            const emailInput = document.getElementById('email');
            
            if (loginForm && emailInput) {
                loginForm.addEventListener('submit', function(e) {
                    // Cegah multiple submit
                    const loginButton = document.getElementById('loginButton');
                    if (loginButton && loginButton.getAttribute('data-submitting') === 'true') {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Set status submitting
                    if (loginButton) {
                        loginButton.setAttribute('data-submitting', 'true');
                        loginButton.disabled = true;
                        loginButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                    }
                    
                    localStorage.setItem('rememberedEmail', emailInput.value);
                });
            }
            
            // Cek dan isi kembali nilai email jika ada
            if (emailInput) {
                // Prioritaskan nilai dari old('email') jika ada
                if (!emailInput.value && localStorage.getItem('rememberedEmail')) {
                    emailInput.value = localStorage.getItem('rememberedEmail');
                }
                
                // Update hidden input untuk form verifikasi
                const hiddenEmails = document.querySelectorAll('input[type="hidden"][name="email"]');
                hiddenEmails.forEach(function(input) {
                    if (emailInput.value) {
                        input.value = emailInput.value;
                    } else if (localStorage.getItem('rememberedEmail')) {
                        input.value = localStorage.getItem('rememberedEmail');
                    }
                });
            }
        });
    </script>
</body>
</html> 