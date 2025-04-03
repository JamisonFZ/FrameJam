<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FrameJam Framework')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @yield('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    @yield('header', 'FrameJam Framework')
                </h1>
            </div>
        </header>

        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <footer class="bg-white shadow mt-8">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} FrameJam Framework. Todos os direitos reservados.
                </p>
            </div>
        </footer>
    </div>
    @yield('scripts')
</body>
</html> 