@props([
    'title' => config('app.name', 'Laravel'),
    'breadcrumbs' => []
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/9161014f5f.js" crossorigin="anonymous"></script>

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">

    @include('layouts.includes.admin.navigation')
    @include('layouts.includes.admin.sidebar')

    <div class="p-4 sm:ml-64">
        {{-- añadiendo margen superior para evitar que la barra fija tape el contenido --}}
        <div class="pt-20 pb-4 flex items-start justify-between">
            @include('layouts.includes.breadcrumb', ['breadcrumbs' => $breadcrumbs])

            @isset($action)
                <div class="flex-shrink-0">
                    {{ $action }}
                </div>
            @endisset
        </div>
        {{ $slot }}
    </div>

    @stack('modals')

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Flowbite -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    @if(@session('swal'))
        <script>
            Swal.fire(@json(session('swal')));
        </script>
    @endif
</body>
</html>
