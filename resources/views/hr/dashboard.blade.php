@extends('layouts.app')

@section('title', 'Espace RH')

@section('content')
    <section class="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
        <div>
            <p class="text-sm font-semibold tracking-wide text-blue-400">ESPACE RESPONSABLE RH</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Pilotage des pointages</h1>
            <p class="mt-3 max-w-2xl text-slate-400">Générez un QR temporaire, affichez-le aux salariés et suivez les présences enregistrées avec leur position et leur précision GPS.</p>
        </div>
        <div class="flex flex-col gap-3 sm:items-end"><a href="{{ route('attendance.board') }}" target="_blank" rel="noopener" class="w-full rounded-xl border border-blue-400/40 bg-blue-500/10 px-4 py-2.5 text-center text-sm font-semibold text-blue-100 transition hover:bg-blue-500/20 sm:w-auto">Ouvrir l’affichage public des QR ↗</a><div class="w-full rounded-xl border border-slate-800 bg-slate-900 px-4 py-3 text-sm text-slate-300 sm:w-auto"><span class="block text-xs font-semibold tracking-wide text-slate-500">AUJOURD’HUI</span><strong class="text-lg text-white">{{ now()->translatedFormat('l d F Y') }}</strong></div></div>
    </section>

    @if(session('status'))
        <div class="mt-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>
    @endif

    <section class="mt-8 grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Salariés actifs</p><p class="mt-2 text-3xl font-bold">{{ $activeEmployeesCount }}</p><p class="mt-2 text-xs text-slate-500">Comptes pouvant pointer</p></article>
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">Pointages du jour</p><p class="mt-2 text-3xl font-bold">{{ $records->count() }}</p><p class="mt-2 text-xs text-slate-500">Arrivées et départs confondus</p></article>
        <article class="rounded-2xl border border-slate-800 bg-slate-900 p-5"><p class="text-sm text-slate-400">QR actuellement actifs</p><p class="mt-2 text-3xl font-bold">{{ $sessions->count() }}</p><p class="mt-2 text-xs text-slate-500">Ils expirent automatiquement</p></article>
    </section>

    <section class="mt-8 grid gap-5 lg:grid-cols-2">
        @foreach (['arrival' => ['Arrivée', 'emerald', 'd’arrivée'], 'departure' => ['Départ', 'amber', 'de départ']] as $type => [$label, $color, $article])
            <form method="POST" action="{{ route('hr.sessions.create') }}" class="rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-black/10">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="flex items-start justify-between gap-4"><div><p class="text-sm font-semibold text-{{ $color }}-400">QR {{ strtoupper($label) }}</p><h2 class="mt-1 text-xl font-bold text-white">Générer le QR {{ $article }}</h2></div><span class="grid size-10 place-items-center rounded-xl bg-{{ $color }}-400/10 text-{{ $color }}-300">{{ $type === 'arrival' ? '↗' : '↘' }}</span></div>
                <label class="mt-6 block text-sm font-medium text-slate-200" for="{{ $type }}_validity">Durée de validité</label>
                <select id="{{ $type }}_validity" name="validity_minutes" class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-800 px-3 py-3 text-white outline-none transition focus:border-{{ $color }}-400">
                    <option value="5">5 minutes</option><option value="10">10 minutes</option><option value="15">15 minutes</option><option value="30">30 minutes</option>
                </select>
                <button class="mt-5 w-full rounded-xl bg-{{ $color }}-400 px-4 py-3 font-semibold text-slate-950 transition hover:bg-{{ $color }}-300">Générer et afficher le QR</button>
            </form>
        @endforeach
    </section>

    <section class="mt-8">
        <div class="mb-4"><h2 class="text-xl font-bold text-white">QR codes actifs</h2><p class="mt-1 text-sm text-slate-400">Chaque nouveau QR du même type remplace automatiquement le précédent.</p></div>
        <div class="grid gap-5 lg:grid-cols-2">
            @forelse($sessions as $attendanceSession)
                <article class="rounded-2xl border {{ $attendanceSession->type === 'arrival' ? 'border-emerald-400/30 bg-emerald-400/5' : 'border-amber-400/30 bg-amber-400/5' }} p-6">
                    <div class="grid gap-5 sm:grid-cols-[1fr_auto] sm:items-center">
                        <div><p class="text-sm font-semibold {{ $attendanceSession->type === 'arrival' ? 'text-emerald-300' : 'text-amber-300' }}">QR {{ $attendanceSession->type === 'arrival' ? 'D’ARRIVÉE' : 'DE DÉPART' }} ACTIF</p><h3 class="mt-1 text-xl font-bold text-white">À afficher aux salariés</h3><p class="mt-2 text-sm text-slate-300">Expire à <strong>{{ $attendanceSession->expires_at->format('H:i:s') }}</strong>. Les salariés connectés le scannent depuis leur espace.</p><p class="mt-3 break-all text-xs text-slate-500">{{ route('attendance.scan', $attendanceSession->token) }}</p><form class="mt-4" method="POST" action="{{ route('hr.sessions.close', $attendanceSession) }}">@csrf @method('DELETE')<button class="text-sm font-medium text-slate-300 underline decoration-slate-600 underline-offset-4 hover:text-white">Désactiver ce QR</button></form></div>
                        <div class="justify-self-center rounded-2xl bg-white p-3 shadow-lg"><canvas class="qr-code" data-qr-value="{{ route('attendance.scan', $attendanceSession->token) }}"></canvas></div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/50 p-8 text-center text-slate-400 lg:col-span-2">Aucun QR n’est actif. Générez un QR d’arrivée ou de départ pour commencer le pointage.</div>
            @endforelse
        </div>
    </section>

    <section class="mt-8 rounded-2xl border border-slate-800 bg-slate-900 p-6">
        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-end"><div><h2 class="text-xl font-bold text-white">Pointages enregistrés aujourd’hui</h2><p class="mt-1 text-sm text-slate-400">La position est demandée au salarié au moment de la validation.</p></div><span class="text-sm text-slate-500">{{ $records->count() }} enregistrement{{ $records->count() > 1 ? 's' : '' }}</span></div>
        <div class="mt-5 overflow-x-auto"><table class="w-full min-w-[720px] text-left text-sm"><thead class="border-b border-slate-800 text-xs uppercase tracking-wide text-slate-500"><tr><th class="pb-3">Salarié</th><th class="pb-3">Évènement</th><th class="pb-3">Heure</th><th class="pb-3">Géolocalisation</th><th class="pb-3">Précision</th></tr></thead><tbody class="divide-y divide-slate-800">@forelse($records as $record)<tr><td class="py-4 font-medium text-white">{{ $record->employee->name }}<span class="mt-1 block text-xs font-normal text-slate-500">{{ $record->employee->email }}</span></td><td class="py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $record->type === 'arrival' ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">{{ $record->type === 'arrival' ? 'Arrivée' : 'Départ' }}</span></td><td class="py-4 text-slate-200">{{ $record->pointed_at->format('H:i:s') }}</td><td class="py-4"><a class="text-blue-300 underline underline-offset-4 hover:text-blue-200" target="_blank" rel="noopener" href="https://www.google.com/maps?q={{ $record->latitude }},{{ $record->longitude }}">Voir sur la carte</a><span class="mt-1 block text-xs text-slate-500">{{ number_format($record->latitude, 5) }}, {{ number_format($record->longitude, 5) }}</span></td><td class="py-4 text-slate-300">{{ $record->accuracy_meters ? '± '.$record->accuracy_meters.' m' : 'Non transmise' }}</td></tr>@empty<tr><td colspan="5" class="py-10 text-center text-slate-400">Aucun pointage enregistré aujourd’hui.</td></tr>@endforelse</tbody></table></div>
    </section>
@endsection
