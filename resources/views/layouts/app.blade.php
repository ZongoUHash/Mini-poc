<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.svg') }}">
    <title>@yield('title', 'Pointage QR') · POC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <header class="sticky top-0 z-30 border-b border-slate-800/90 bg-slate-950/90 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ auth()->check() ? (auth()->user()->role === 'hr' ? route('hr.dashboard') : route('employee.portal')) : route('login') }}" class="flex items-center gap-3 font-bold tracking-tight text-white">
                <span class="grid size-9 place-items-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 text-sm text-slate-950">QR</span>
                <span>Pointage QR <span class="ml-1 rounded bg-blue-500/20 px-1.5 py-0.5 text-xs text-blue-200">POC</span></span>
            </a>

            <div class="flex items-center gap-2 text-sm">
                <button id="install-app-button" type="button" class="hidden rounded-lg border border-blue-400/50 bg-blue-500/10 px-3 py-2 font-semibold text-blue-200 transition hover:bg-blue-500/20">Installer l’application</button>
                @auth
                    <span class="hidden text-slate-400 sm:inline">{{ auth()->user()->name }}</span>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ auth()->user()->role === 'hr' ? 'bg-violet-400/15 text-violet-200' : 'bg-emerald-400/15 text-emerald-200' }}">{{ auth()->user()->role === 'hr' ? 'Responsable RH' : 'Salarié' }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg px-3 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white">Déconnexion</button></form>
                @endauth
            </div>
        </nav>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">@yield('content')</main>
</body>
</html>
