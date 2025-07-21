<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-800 antialiased">

    <div class="min-h-screen flex">
        <!-- Left Side - Blue Background -->
        <div class="flex-1 flex items-center justify-center" style="background-color: #132C5E;">
            <div class="text-center text-white">
                <h1 class="text-4xl font-bold mb-4" style="color: #AF913C;">{{ config('app.name') }}</h1>
                <p class="text-xl opacity-90">Sistem Presensi Face Recognition</p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="mx-auto w-24 h-auto">
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
