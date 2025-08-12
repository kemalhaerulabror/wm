<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Wipa Motor' }}</title>
    {{-- Kode favicon diletakkan di sini --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    @include('user.layouts.nav')
    
    <main class="flex-grow pt-36 md:pt-44">
        @yield('content')
    </main>
    
    @include('user.layouts.footer')
    
    <!-- Chat component -->
    <livewire:chat.user-chat />
    
    @livewireScripts
    @stack('scripts')
</body>
</html>
