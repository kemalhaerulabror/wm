<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Wipa Motor' }}</title>
    @php
    $isProduction = app()->environment('production');
    $manifestPath = $isProduction ? '../public_html/build/manifest.json' : public_path('build/manifest.json');
 @endphp
 
  @if ($isProduction && file_exists($manifestPath))
   @php
    $manifest = json_decode(file_get_contents($manifestPath), true);
   @endphp
    <link rel="stylesheet" href="{{ config('app.url') }}/build/{{ $manifest['resources/css/app.css']['file'] }}">
    <script type="module" src="{{ config('app.url') }}/build/{{ $manifest['resources/js/app.jsx']['file'] }}"></script>
  @else
    @viteReactRefresh
    @vite(['resources/js/app.jsx', 'resources/css/app.css'])
  @endif
 
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
