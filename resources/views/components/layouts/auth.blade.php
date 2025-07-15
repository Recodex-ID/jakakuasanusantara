<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col">
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>
