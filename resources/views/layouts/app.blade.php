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
    <style>html { background: #020617; } body { margin: 0; background: #020617; color: #f8fafc; font-family: ui-sans-serif, system-ui, sans-serif; } a { color: inherit; } #app-loader { position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; background: #020617; text-align: center; } #app-loader > div { color: #f8fafc; } #app-loader > div > span:first-child { display: grid; width: 3rem; height: 3rem; margin: auto; place-items: center; border-radius: 1rem; background: #38bdf8; color: #020617; font-weight: 700; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-loading min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div id="app-loader" class="fixed inset-0 z-[100] grid place-items-center bg-slate-950 px-6 transition-opacity duration-200">
        <div class="text-center"><span class="mx-auto grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 font-bold text-slate-950">QR</span><p class="mt-4 text-sm font-semibold text-white">Pointage QR</p><p class="mt-1 text-xs text-slate-400">Chargement de votre espace…</p><span class="mx-auto mt-4 block h-1 w-24 overflow-hidden rounded-full bg-slate-800"><span class="block h-full w-1/2 animate-pulse rounded-full bg-blue-400"></span></span></div>
    </div>
    <header class="sticky top-0 z-30 border-b border-slate-800/90 bg-slate-950/90 backdrop-blur">
        <nav class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3 sm:flex-nowrap sm:px-6 lg:px-8">
            <a href="{{ auth()->check() ? (auth()->user()->role === 'hr' ? route('hr.dashboard') : route('employee.portal')) : route('login') }}" class="flex items-center gap-3 font-bold tracking-tight text-white">
                <span class="grid size-9 place-items-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 text-sm text-slate-950">QR</span>
                <span>Pointage QR <span class="ml-1 rounded bg-blue-500/20 px-1.5 py-0.5 text-xs text-blue-200">POC</span></span>
            </a>

            <div class="ml-auto flex items-center gap-1.5 text-xs sm:gap-2 sm:text-sm">
                <button id="install-app-button" type="button" class="hidden rounded-lg border border-blue-400/50 bg-blue-500/10 px-2.5 py-2 font-semibold text-blue-200 transition hover:bg-blue-500/20 sm:px-3">Installer<span class="hidden sm:inline"> l’application</span></button>
                @auth
                    <span class="hidden text-slate-400 sm:inline">{{ auth()->user()->name }}</span>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ auth()->user()->role === 'hr' ? 'bg-violet-400/15 text-violet-200' : 'bg-emerald-400/15 text-emerald-200' }}">{{ auth()->user()->role === 'hr' ? 'Responsable RH' : 'Salarié' }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg px-2.5 py-2 text-slate-300 transition hover:bg-slate-800 hover:text-white sm:px-3">Déconnexion</button></form>
                @endauth
            </div>
        </nav>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">@yield('content')</main>
    <script>window.addEventListener('load', () => document.body.classList.remove('app-loading'), { once: true }); window.setTimeout(() => document.body.classList.remove('app-loading'), 4000);</script>
</body>
</html>
