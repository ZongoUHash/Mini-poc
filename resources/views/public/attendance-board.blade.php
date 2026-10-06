@extends('layouts.app')

@section('title', 'Affichage public des QR')

@section('content')
    <div id="attendance-board" data-board-signature="{{ $boardSignature }}" data-board-status-url="{{ route('attendance.board.sessions') }}" class="mx-auto max-w-5xl">
        <section class="text-center">
            <p class="text-sm font-semibold tracking-wide text-blue-400">POINTAGE DE L’ENTREPRISE</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-5xl">Scannez le QR correspondant</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-slate-400 sm:text-base">Ouvrez l’application de pointage sur votre téléphone, choisissez « Scanner le QR affiché par le RH », puis autorisez votre position pour confirmer.</p>
        </section>

        <section class="mt-8 grid gap-6 {{ $sessions->count() > 1 ? 'md:grid-cols-2' : 'mx-auto max-w-xl' }}">
            @forelse($sessions as $attendanceSession)
                <article data-board-session data-expires-at="{{ $attendanceSession->expires_at->toIso8601String() }}" class="rounded-3xl border {{ $attendanceSession->type === 'arrival' ? 'border-emerald-400/40 bg-emerald-400/5' : 'border-amber-400/40 bg-amber-400/5' }} p-5 text-center shadow-2xl shadow-black/20 sm:p-8">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold tracking-wide {{ $attendanceSession->type === 'arrival' ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">{{ $attendanceSession->type === 'arrival' ? 'ARRIVÉE' : 'DÉPART' }}</span>
                    <h2 class="mt-4 text-2xl font-bold text-white sm:text-3xl">QR de {{ $attendanceSession->type === 'arrival' ? 'pointage arrivée' : 'pointage départ' }}</h2>
                    <div class="mx-auto mt-6 w-fit rounded-2xl bg-white p-4 shadow-xl"><canvas class="qr-code" data-qr-value="{{ route('attendance.scan', $attendanceSession->token) }}"></canvas></div>
                    <p class="mt-6 text-sm text-slate-300">Valable encore <strong data-countdown class="text-white">—</strong></p>
                    <p class="mt-2 text-xs text-slate-500">Le QR disparaît automatiquement lorsqu’il expire ou est remplacé.</p>
                </article>
            @empty
                <article class="rounded-3xl border border-dashed border-slate-700 bg-slate-900/70 p-10 text-center"><span class="grid mx-auto size-14 place-items-center rounded-full bg-slate-800 text-2xl text-slate-400">⌛</span><h2 class="mt-5 text-2xl font-bold text-white">Aucun QR actif</h2><p class="mx-auto mt-3 max-w-md text-slate-400">Le responsable RH n’a pas encore généré de QR de pointage, ou le dernier QR a expiré. Cette page se met à jour automatiquement.</p></article>
            @endforelse
        </section>

        <p class="mt-8 text-center text-xs text-slate-500">Page publique d’affichage. Elle ne contient ni nom de salarié, ni historique de présence.</p>
    </div>
@endsection
