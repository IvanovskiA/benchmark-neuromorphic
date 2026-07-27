@props(['heading' => 'Dashboard', 'breadcrumb' => null, 'title' => 'Benchmark Neuromorphic'])

<!DOCTYPE html>
<html lang="mk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">
    <div id="app-layout" class="min-h-screen lg:flex">
        @include('partials.sidebar')

        <div id="main-panel" class="flex-1">
            @include('partials.topbar', ['heading' => $heading, 'breadcrumb' => $breadcrumb])

            <main class="mx-auto max-w-[1600px] p-6 lg:p-8">
                @include('partials.flash')
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
