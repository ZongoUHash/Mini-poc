<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1d4ed8"><meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}"><link rel="apple-touch-icon" href="{{ asset('icons/icon-192.svg') }}">
    <title>Pointage QR · POC</title>@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
<header class="border-b border-slate-800 bg-slate-900/90"><nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6"><a href="{{ route('hr.dashboard') }}" class="font-bold tracking-tight text-white">Pointage QR <span class="rounded bg-blue-600 px-1.5 py-0.5 text-xs">POC</span></a><div class="flex items-center gap-2 text-sm"><button id="install-app-button" type="button" class="hidden rounded-lg border border-blue-400/50 bg-blue-500/10 px-3 py-2 font-semibold text-blue-200 hover:bg-blue-500/20">Installer l’application</button><a href="{{ route('hr.dashboard') }}" class="rounded-lg px-3 py-2 hover:bg-slate-800">Espace RH</a><a href="{{ route('employee.portal') }}" class="rounded-lg px-3 py-2 hover:bg-slate-800">Espace salarié</a></div></nav></header>
<main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">@yield('content')</main>
</body></html>
