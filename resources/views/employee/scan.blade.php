@extends('layouts.app')

@section('title', 'Confirmation du pointage')

@section('content')
    <div class="mx-auto max-w-xl">
        @if(!$attendanceSession || !$attendanceSession->isOpen())
            <section class="rounded-3xl border border-red-400/30 bg-red-400/5 p-7 text-center"><span class="grid mx-auto size-14 place-items-center rounded-full bg-red-400/15 text-2xl text-red-200">!</span><p class="mt-5 text-sm font-semibold text-red-200">QR INDISPONIBLE</p><h1 class="mt-2 text-2xl font-bold text-white">Ce QR code n’est plus valide</h1><p class="mt-3 text-slate-300">Il a expiré, a été désactivé ou a été remplacé par un QR plus récent.</p><a class="mt-6 inline-block rounded-xl bg-slate-800 px-4 py-3 font-semibold text-white hover:bg-slate-700" href="{{ route('employee.portal') }}">Retour à mon espace</a></section>
        @else
            <section class="rounded-3xl border border-slate-800 bg-slate-900 p-7 shadow-2xl shadow-black/20"><p class="text-sm font-semibold {{ $attendanceSession->type === 'arrival' ? 'text-emerald-300' : 'text-amber-300' }}">POINTAGE {{ strtoupper($attendanceSession->type === 'arrival' ? 'D’ARRIVÉE' : 'DE DÉPART') }}</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-white">Confirmer mon {{ $attendanceSession->type === 'arrival' ? 'arrivée' : 'départ' }}</h1><p class="mt-3 leading-6 text-slate-400">Bonjour {{ $employee->name }}. Ce QR est actif jusqu’à <strong class="text-slate-200">{{ $attendanceSession->expires_at->format('H:i:s') }}</strong>. L’accès à votre position est requis pour finaliser le pointage.</p><div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950/60 p-4 text-sm text-slate-300"><p class="font-semibold text-white">Informations enregistrées</p><ul class="mt-2 space-y-1 text-slate-400"><li>• Type de pointage : {{ $attendanceSession->type === 'arrival' ? 'arrivée' : 'départ' }}</li><li>• Heure de confirmation</li><li>• Position GPS et niveau de précision fourni par votre appareil</li></ul></div><button id="point-button" data-point-url="{{ route('attendance.point', $attendanceSession->token) }}" class="mt-6 w-full rounded-xl bg-blue-500 px-4 py-3 font-semibold text-white transition hover:bg-blue-400">Autoriser ma position et confirmer</button><p id="point-status" class="mt-4 text-center text-sm text-slate-400" aria-live="polite"></p><a class="mt-5 block text-center text-sm text-slate-400 underline underline-offset-4 hover:text-white" href="{{ route('employee.portal') }}">Annuler et revenir à mon espace</a></section>
        @endif
    </div>
@endsection
