<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Splicing Matrix - {{ $cable->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-900 text-slate-100">
    @php
        $connections = collect($connections ?? []);
        $connectionMap = $connections->keyBy(fn ($item) => $item->tube_number . '-' . $item->core_number);
        $totalSlots = (int) $cable->total_tube * (int) $cable->cores_per_tube;
        $usedCount = $connections->where('status', 'Terpakai')->count();
        $brokenCount = $connections->where('status', 'Rusak')->count();
        $availableCount = max(0, $totalSlots - $usedCount - $brokenCount);

        $fiberColors = [
            1 => ['name' => 'Blue', 'hex' => '#3b82f6', 'text' => '#ffffff'],
            2 => ['name' => 'Orange', 'hex' => '#f97316', 'text' => '#ffffff'],
            3 => ['name' => 'Green', 'hex' => '#22c55e', 'text' => '#ffffff'],
            4 => ['name' => 'Brown', 'hex' => '#78350f', 'text' => '#ffffff'],
            5 => ['name' => 'Slate', 'hex' => '#64748b', 'text' => '#ffffff'],
            6 => ['name' => 'White', 'hex' => '#ffffff', 'text' => '#0f172a'],
            7 => ['name' => 'Red', 'hex' => '#ef4444', 'text' => '#ffffff'],
            8 => ['name' => 'Black', 'hex' => '#000000', 'text' => '#ffffff'],
            9 => ['name' => 'Yellow', 'hex' => '#eab308', 'text' => '#0f172a'],
            10 => ['name' => 'Violet', 'hex' => '#a855f7', 'text' => '#ffffff'],
            11 => ['name' => 'Pink', 'hex' => '#ec4899', 'text' => '#ffffff'],
            12 => ['name' => 'Aqua', 'hex' => '#06b6d4', 'text' => '#ffffff'],
        ];

        $formatLabel = fn ($text) => trim((string) $text) !== '' ? $text : 'Belum ada koneksi';
    @endphp

    <div class="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-4 pb-32 md:px-6 md:py-6">
        <header class="sticky top-0 z-30 -mx-4 mb-4 border-b border-slate-800 bg-slate-950/95 px-4 py-4 backdrop-blur md:-mx-6 md:px-6">
            <div class="flex flex-col gap-4">
                <a href="/" class="inline-flex h-12 w-full items-center justify-center rounded-xl border border-slate-700 bg-slate-900 px-4 text-sm font-semibold text-slate-200 transition hover:border-sky-500 hover:text-white md:w-fit md:px-5">
                    &larr; Kembali
                </a>

                <div class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 shadow-2xl shadow-black/20">
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-400">Splicing Matrix</p>
                            <h1 class="mt-2 text-2xl font-bold text-white md:text-3xl">{{ $cable->name }}</h1>
                            <p class="mt-1 text-sm text-slate-400">
                                {{ $cable->description ?: 'Tidak ada deskripsi kabel.' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1 text-emerald-200">Kosong {{ $availableCount }}</span>
                            <span class="rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-amber-200">Terpakai {{ $usedCount }}</span>
                            <span class="rounded-full border border-rose-500/20 bg-rose-500/10 px-3 py-1 text-rose-200">Rusak {{ $brokenCount }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
                        <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-3">
                            <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Dari</span>
                            <strong class="mt-1 block truncate text-slate-100">{{ $cable->source_node?->name ?? 'Node tidak ditemukan' }}</strong>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-3">
                            <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Ke</span>
                            <strong class="mt-1 block truncate text-slate-100">{{ $cable->destination_node?->name ?? 'Node tidak ditemukan' }}</strong>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-3">
                            <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total Core</span>
                            <strong class="mt-1 block text-slate-100">{{ $totalSlots }}</strong>
                        </div>
                        <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-3">
                            <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Tube x Core</span>
                            <strong class="mt-1 block text-slate-100">{{ $cable->total_tube }} x {{ $cable->cores_per_tube }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <main class="space-y-4">
            @for ($tube = 1; $tube <= $cable->total_tube; $tube++)
                @php
                    $tubeColor = $fiberColors[((($tube - 1) % 12) + 1)];
                @endphp

                <section class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-950/75 shadow-2xl shadow-black/20">
                    <button type="button" class="tube-toggle flex w-full items-center justify-between gap-3 border-b border-slate-800 px-4 py-4 text-left transition hover:bg-slate-900/50" data-target="tube-body-{{ $tube }}">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl text-sm font-black shadow-lg" style="background-color: {{ $tubeColor['hex'] }}; color: {{ $tubeColor['text'] }};">
                                {{ $tube }}
                            </span>
                            <div>
                                <h2 class="text-base font-bold text-white">TUBE {{ $tube }}</h2>
                                <p class="text-xs text-slate-400">{{ $tubeColor['name'] }} Tube Color</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <span class="rounded-full border border-slate-700 bg-slate-900 px-3 py-1">{{ $cable->cores_per_tube }} Core</span>
                            <span class="tube-indicator rounded-full border border-slate-700 bg-slate-900 px-2.5 py-1 text-slate-300">+</span>
                        </div>
                    </button>

                    <div id="tube-body-{{ $tube }}" class="tube-body grid grid-cols-3 gap-3 p-4 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                        @for ($core = 1; $core <= $cable->cores_per_tube; $core++)
                            @php
                                $coreColor = $fiberColors[((($core - 1) % 12) + 1)];
                                $key = $tube . '-' . $core;
                                $coreInfo = $connectionMap[$key] ?? null;
                                $status = $coreInfo->status ?? 'Kosong';
                                $connectedTo = $coreInfo->connected_to ?? '';
                                $portLabel = 'T' . $tube . ' / C' . $core;
                                $isUsed = $status === 'Terpakai';
                                $isBroken = $status === 'Rusak';
                            @endphp

                            <button
                                type="button"
                                class="core-button group flex aspect-square flex-col items-center justify-center rounded-2xl border border-slate-800 bg-slate-900/80 p-2 text-center shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50"
                                data-tube="{{ $tube }}"
                                data-core="{{ $core }}"
                                data-status="{{ $status }}"
                                data-connected-to="{{ e($connectedTo) }}"
                                data-core-label="{{ $portLabel }}"
                                data-core-name="{{ $coreColor['name'] }}"
                                data-core-hex="{{ $coreColor['hex'] }}"
                                data-core-text="{{ $coreColor['text'] }}"
                                data-core-status="{{ $status }}"
                                data-core-existing-id="{{ $coreInfo->id ?? '' }}"
                            >
                                <span class="relative flex h-14 w-14 items-center justify-center rounded-full border border-black/10 font-extrabold shadow-[0_10px_25px_rgba(15,23,42,0.35)]" style="background-color: {{ $coreColor['hex'] }}; color: {{ $coreColor['text'] }};">
                                    {{ $core }}

                                    @if($isUsed)
                                        <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-[11px] font-black text-white shadow">✓</span>
                                    @elseif($isBroken)
                                        <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[11px] font-black text-white shadow">✕</span>
                                    @endif
                                </span>

                                <span class="mt-2 text-[11px] font-semibold tracking-wide text-slate-300">{{ $coreColor['name'] }}</span>
                                <span class="mt-1 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $isUsed ? 'bg-amber-500/15 text-amber-200' : ($isBroken ? 'bg-rose-500/15 text-rose-200' : 'bg-emerald-500/15 text-emerald-200') }}">
                                    {{ $status }}
                                </span>

                                @if($connectedTo)
                                    <span class="mt-1 line-clamp-2 text-[9px] leading-4 text-slate-500">{{ $connectedTo }}</span>
                                @endif
                            </button>
                        @endfor
                    </div>
                </section>
            @endfor
        </main>
    </div>

    <div id="coreModal" class="fixed inset-0 z-50 hidden">
        <div id="coreModalBackdrop" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

        <div class="absolute inset-x-0 bottom-0 flex max-h-[92vh] justify-center px-3 pb-3 md:items-end md:pb-4">
            <div class="w-full max-w-2xl translate-y-0 rounded-t-4xl border border-slate-700 bg-slate-950 shadow-2xl shadow-black/50 md:rounded-b-2xl md:rounded-t-2xl">
                <div class="flex items-center justify-between gap-3 border-b border-slate-800 px-4 py-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-400">Update Core</p>
                        <h3 id="coreModalTitle" class="mt-1 text-lg font-bold text-white">Tube - Core</h3>
                    </div>
                    <button type="button" id="coreModalClose" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-700 bg-slate-900 text-slate-200 transition hover:bg-slate-800" aria-label="Tutup modal">&times;</button>
                </div>

                <form id="coreModalForm" action="{{ route('cable.update', $cable->id) }}" method="POST" class="space-y-4 px-4 py-4">
                    @csrf
                    <input type="hidden" name="tube_number" id="modalTubeNumber">
                    <input type="hidden" name="core_number" id="modalCoreNumber">

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
                        <div class="flex items-center gap-3">
                            <span id="modalCoreColor" class="flex h-14 w-14 items-center justify-center rounded-full border border-black/10 text-sm font-black shadow-lg">1</span>
                            <div>
                                <h4 class="text-base font-bold text-white" id="modalCoreLabel">T1 / C1</h4>
                                <p class="text-sm text-slate-400" id="modalCoreColorName">Fiber Color</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="modalStatus" class="mb-1 block text-sm font-semibold text-slate-200">Status Core</label>
                        <select id="modalStatus" name="status" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 focus:border-sky-500 focus:outline-none">
                            <option value="Kosong">Kosong</option>
                            <option value="Terpakai">Terpakai</option>
                            <option value="Rusak">Rusak</option>
                        </select>
                    </div>

                    <div>
                        <label for="modalConnectedTo" class="mb-1 block text-sm font-semibold text-slate-200">connected_to</label>
                        <input id="modalConnectedTo" name="connected_to" type="text" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-500 focus:border-sky-500 focus:outline-none" placeholder="ODP-PANGHEGAR-A1 / Port 3">
                    </div>

                    <div class="flex gap-2">
                        <button type="button" id="modalCancel" class="h-12 flex-1 rounded-xl border border-slate-700 bg-slate-900 px-4 font-semibold text-slate-200 transition hover:bg-slate-800">Batal</button>
                        <button type="submit" class="h-12 flex-1 rounded-xl bg-sky-600 px-4 font-semibold text-white transition hover:bg-sky-500">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tubeToggleButtons = document.querySelectorAll('.tube-toggle');
        const tubeBodies = document.querySelectorAll('.tube-body');
        const coreButtons = document.querySelectorAll('.core-button');

        const coreModal = document.getElementById('coreModal');
        const coreModalBackdrop = document.getElementById('coreModalBackdrop');
        const coreModalClose = document.getElementById('coreModalClose');
        const modalCancel = document.getElementById('modalCancel');
        const modalTubeNumber = document.getElementById('modalTubeNumber');
        const modalCoreNumber = document.getElementById('modalCoreNumber');
        const modalStatus = document.getElementById('modalStatus');
        const modalConnectedTo = document.getElementById('modalConnectedTo');
        const coreModalTitle = document.getElementById('coreModalTitle');
        const modalCoreColor = document.getElementById('modalCoreColor');
        const modalCoreColorName = document.getElementById('modalCoreColorName');
        const modalCoreLabel = document.getElementById('modalCoreLabel');

        const fiberColors = @json($fiberColors);

        function openModal() {
            coreModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            coreModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function setCoreIndicator(status) {
            modalStatus.value = status || 'Kosong';
        }

        function openCoreModal(button) {
            const tube = button.dataset.tube;
            const core = button.dataset.core;
            const status = button.dataset.status || 'Kosong';
            const connectedTo = button.dataset.connectedTo || '';
            const coreLabel = button.dataset.coreLabel || `T${tube} / C${core}`;
            const coreName = button.dataset.coreName || 'Fiber';
            const coreHex = button.dataset.coreHex || '#3b82f6';
            const coreText = button.dataset.coreText || '#ffffff';

            modalTubeNumber.value = tube;
            modalCoreNumber.value = core;
            setCoreIndicator(status);
            modalConnectedTo.value = connectedTo;
            coreModalTitle.textContent = `Tube ${tube} - Core ${core}`;
            modalCoreLabel.textContent = coreLabel;
            modalCoreColorName.textContent = coreName;
            modalCoreColor.textContent = core;
            modalCoreColor.style.backgroundColor = coreHex;
            modalCoreColor.style.color = coreText;

            openModal();
        }

        function syncTubeAccordion(targetId) {
            tubeBodies.forEach((body) => {
                const shouldShow = body.id === targetId;
                body.classList.toggle('hidden', !shouldShow);
            });
        }

        tubeToggleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                syncTubeAccordion(button.dataset.target);
            });
        });

        coreButtons.forEach((button) => {
            button.addEventListener('click', () => openCoreModal(button));
        });

        coreModalBackdrop.addEventListener('click', closeModal);
        coreModalClose.addEventListener('click', closeModal);
        modalCancel.addEventListener('click', closeModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        document.getElementById('coreModalForm').addEventListener('submit', function () {
            closeModal();
        });

        syncTubeAccordion('tube-body-1');
    </script>
</body>
</html>
