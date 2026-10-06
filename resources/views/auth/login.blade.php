@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="mx-auto grid max-w-5xl gap-8 lg:grid-cols-[1.1fr_.9fr] lg:items-center">
        <section>
            <p class="text-sm font-semibold tracking-wide text-blue-400">POC DE POINTAGE QR</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-white sm:text-5xl">Un pointage simple, sécurisé et vérifiable.</h1>
            <p class="mt-5 max-w-xl text-lg leading-8 text-slate-400">Cette démonstration reproduit les deux parcours : le responsable RH pilote les QR dynamiques et le salarié pointe depuis son téléphone, avec la géolocalisation de son appareil.</p>
            <div class="mt-8 grid gap-3 sm:grid-cols-3"><div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4"><p class="text-xl">◫</p><p class="mt-2 font-semibold text-white">QR temporaire</p><p class="mt-1 text-sm text-slate-400">Expiration automatique</p></div><div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4"><p class="text-xl">⌖</p><p class="mt-2 font-semibold text-white">GPS demandé</p><p class="mt-1 text-sm text-slate-400">Position et précision</p></div><div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4"><p class="text-xl">✓</p><p class="mt-2 font-semibold text-white">Règles métier</p><p class="mt-1 text-sm text-slate-400">Arrivée avant départ</p></div></div>
        </section>

        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-2xl shadow-black/30 sm:p-8">
            <p class="text-sm font-semibold text-blue-300">CONNEXION</p>
            <h2 class="mt-2 text-2xl font-bold text-white">Accéder à votre espace</h2>
            <p class="mt-2 text-sm leading-6 text-slate-400">Utilisez un compte de démonstration RH ou salarié.</p>
            <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">@csrf
                <div><label for="email" class="text-sm font-medium text-slate-200">Adresse e-mail</label><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-blue-400" placeholder="nom@pointage-poc.test">@error('email')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror</div>
                <div><label for="password" class="text-sm font-medium text-slate-200">Mot de passe</label><input id="password" name="password" type="password" autocomplete="current-password" required class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-800 px-4 py-3 text-white outline-none transition focus:border-blue-400"></div>
                <label class="flex items-center gap-2 text-sm text-slate-400"><input type="checkbox" name="remember" value="1" class="rounded border-slate-600 bg-slate-800 text-blue-500"> Rester connecté sur cet appareil</label>
                <button class="w-full rounded-xl bg-blue-500 px-4 py-3 font-semibold text-white transition hover:bg-blue-400">Se connecter</button>
            </form>
            <div class="mt-7 rounded-xl border border-blue-400/20 bg-blue-400/5 p-4 text-sm"><p class="font-semibold text-blue-100">Comptes de démonstration</p><p class="mt-2 text-slate-300">RH : <code class="text-blue-200">rh@pointage-poc.test</code></p><p class="text-slate-300">Salarié : <code class="text-blue-200">awa@pointage-poc.test</code></p><p class="mt-2 text-xs text-slate-400">Les mots de passe seront communiqués avec le scénario de test.</p></div>
        </section>
    </div>
@endsection
