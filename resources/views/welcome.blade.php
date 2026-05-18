<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Fiber Control Center - Mobile Dashboard</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen font-sans pb-10">

    <!-- STICKY TOP COMPONENT -->
    <div class="sticky top-0 z-50 bg-slate-900 border-b border-slate-800 p-4 space-y-4 shadow-md">
        <!-- Header & Search -->
        <div>
            <h1 class="text-xl font-bold text-white mb-3">Fiber Control Center</h1>
                <div class="space-y-3">
                    <div class="relative">
                        <input type="text" id="searchInput" class="w-full h-12 bg-slate-800 border border-slate-700 rounded-xl px-4 pl-11 text-slate-200 placeholder-slate-400 focus:outline-none focus:border-blue-500 transition-colors" placeholder="Cari ODP / ODC...">
                        <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <button type="button" id="openNodeModalBtn" class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-sky-600 font-semibold text-white shadow-lg shadow-sky-500/20 transition-colors hover:bg-sky-500">
                        <span class="text-lg leading-none">+</span>
                        <span>Tambah Titik</span>
                    </button>

                    <button type="button" id="openCableModalBtn" class="flex h-12 w-full items-center justify-center gap-2 rounded-xl border border-slate-700 bg-slate-800 font-semibold text-slate-100 transition-colors hover:bg-slate-700">
                        <span class="text-lg leading-none">↔</span>
                        <span>Tambah Kabel</span>
                    </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 bg-slate-800 p-1 rounded-xl">
            <button id="tab-list" class="flex-1 h-12 rounded-lg bg-blue-600 text-white font-semibold text-sm transition-colors" onclick="switchTab('list')">Daftar Data</button>
            <button id="tab-map" class="flex-1 h-12 rounded-lg text-slate-400 font-semibold text-sm hover:text-slate-200 transition-colors bg-transparent" onclick="switchTab('map')">Peta Jaringan</button>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="p-4">

        <!-- TAB 1: DATA LIST -->
        <div id="content-list" class="space-y-6">
            <!-- Stat Cards (Portainer Style) -->
            <div class="grid grid-cols-1 gap-3">
                <!-- ODF / POP Card -->
                <div class="bg-blue-600 rounded-xl p-4 flex items-center justify-between shadow-lg">
                    <div>
                        <p class="text-blue-100 text-xs font-semibold uppercase tracking-wider">Total ODF (POP)</p>
                        <h2 class="text-3xl font-bold text-white mt-1" id="stat-odf">0</h2>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center opacity-80">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    </div>
                </div>

                <!-- ODC / JB Card -->
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total ODC</p>
                        <h2 class="text-3xl font-bold text-white mt-1" id="stat-odc">0</h2>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                </div>

                <!-- ODP Card -->
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Total ODP</p>
                        <h2 class="text-3xl font-bold text-white mt-1" id="stat-odp">0</h2>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Node List -->
            <div>
                <h3 class="text-sm font-bold text-slate-300 mb-3 uppercase tracking-wider border-b border-slate-800 pb-2">Daftar Lokasi</h3>
                <div id="node-list" class="space-y-4">
                    @foreach($nodes as $node)
                        @php
                            $displayType = $node->type === 'POP' ? 'ODF' : ($node->type === 'JB' ? 'ODC' : 'ODP');
                            $isOdp = $displayType === 'ODP';
                            $isOdc = $displayType === 'ODC';
                            $iconColor = $isOdp ? 'text-green-400 bg-green-400/10 border-green-500/20' : ($isOdc ? 'text-blue-400 bg-blue-400/10 border-blue-500/20' : 'text-red-400 bg-red-400/10 border-red-500/20');
                            $relatedCable = $isOdp ? null : $cables->first(function ($cable) use ($node) {
                                $sourceId = $cable->source_node ? $cable->source_node->id : $cable->source_node_id;
                                $destinationId = $cable->destination_node ? $cable->destination_node->id : $cable->destination_node_id;

                                return (int) $sourceId === (int) $node->id || (int) $destinationId === (int) $node->id;
                            });
                            $actionUrl = $isOdp ? route('odp.show', $node->id) : ($relatedCable ? route('cable.show', $relatedCable->id) : null);
                        @endphp
                        <div class="bg-slate-800 border border-slate-700 hover:border-slate-500 transition-colors rounded-xl p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="px-2 py-0.5 rounded border text-[10px] font-bold {{ $iconColor }}">{{ $displayType }}</span>
                                        <h4 class="text-base font-bold text-white truncate max-w-50">{{ $node->name }}</h4>
                                    </div>
                                    <p class="text-xs text-slate-400 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $isOdp ? 'Hulu: ODC Terdekat (TBA)' : ($isOdc ? 'Splicing Matrix Distribusi' : 'Splicing Matrix Pusat') }}
                                    </p>
                                </div>
                            </div>

                            @if($isOdp)
                                <div class="inline-flex items-center gap-1.5 mt-3 bg-slate-900/80 px-3 py-2 rounded-lg border border-slate-700">
                                    <span class="text-[10px] text-slate-400 mr-1 font-semibold uppercase">Ports:</span>
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_5px_#22c55e]"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_5px_#22c55e]"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-400 shadow-[0_0_5px_#facc15]"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_5px_#ef4444]"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                                    <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                                </div>
                            @endif

                            <div class="mt-4 pt-4 border-t border-slate-700/50">
                                <a href="{{ $actionUrl ?: '#' }}" class="flex items-center justify-center w-full h-12 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg transition {{ $actionUrl ? '' : 'pointer-events-none opacity-50' }}">
                                    {{ $isOdp ? 'Kelola Port Pelanggan' : 'Buka Splicing Matrix' }}
                                </a>
                                @if(! $actionUrl && ! $isOdp)
                                    <p class="mt-2 text-xs text-slate-500">Belum ada kabel yang terhubung ke titik ini.</p>
                                @endif

                                <form id="delete-node-form-{{ $node->id }}" method="POST" action="{{ route('nodes.destroy', $node->id) }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="delete-node-trigger flex h-11 w-full items-center justify-center rounded-xl border border-rose-500/30 bg-rose-500/10 text-sm font-semibold text-rose-200 transition hover:bg-rose-500/20" data-form-id="delete-node-form-{{ $node->id }}" data-node-name="{{ $node->name }}">
                                        Hapus Data
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TAB 2: MAP JARINGAN -->
        <div id="content-map" class="hidden">
            <div id="mapPickerToolbar" class="hidden mb-3 rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 shadow-lg shadow-amber-950/20">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-amber-300">Mode Pilih Titik</p>
                        <h3 class="mt-1 text-sm font-bold text-white">Tekan lama 2 detik pada peta</h3>
                        <p id="mapPickerStatus" class="mt-1 text-xs text-amber-100/80">Pilih titik untuk mengisi latitude dan longitude. Setelah itu tekan X untuk kembali ke form.</p>
                    </div>
                    <button type="button" id="mapPickerCloseBtn" class="h-10 w-10 rounded-full border border-white/10 bg-slate-900 text-lg font-bold text-white hover:bg-slate-800">&times;</button>
                </div>
            </div>

            <div class="bg-slate-800 p-2 rounded-xl border border-slate-700">
                <div id="map-mobile" class="w-full h-[65vh] rounded-lg bg-slate-950 z-0"></div>
            </div>
            <div id="mapGuideCard" class="mt-4 bg-slate-800 p-4 rounded-xl border border-slate-700">
                <h3 class="text-sm font-bold text-slate-300 mb-1">Panduan Peta</h3>
                <p class="text-xs text-slate-500">Ketuk titik atau garis kabel pada peta untuk memunculkan detail jalur atau koneksi core.</p>
            </div>
        </div>

        <!-- MODAL TAMBAH TITIK -->
        <div id="nodeModal" class="fixed inset-0 z-60 hidden">
            <div id="nodeModalBackdrop" class="absolute inset-0 bg-black/70"></div>
            <div class="absolute inset-x-0 bottom-0 max-h-[88vh] overflow-y-auto rounded-t-3xl border-t border-slate-700 bg-slate-950 p-4 shadow-2xl shadow-black/50">
                <div class="mx-auto mb-4 h-1.5 w-14 rounded-full bg-slate-600"></div>
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Infrastruktur Baru</h3>
                        <p class="text-xs text-slate-400 mt-1">ODF, ODC, ODP akan dipetakan ke struktur data jaringan saat disimpan.</p>
                    </div>
                    <button type="button" id="closeNodeModalBtn" class="h-10 w-10 rounded-full bg-slate-800 text-slate-300 hover:bg-slate-700">&times;</button>
                </div>

                <form method="POST" action="{{ route('nodes.store') }}" class="space-y-4" id="nodeModalForm">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200" for="modal_name">Nama Infrastruktur</label>
                        <input id="modal_name" name="name" type="text" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="Contoh: ODP-PANGHEGAR-01" required>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200" for="modal_type">Tipe</label>
                        <select id="modal_type" name="type" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 focus:border-sky-500 focus:outline-none" required>
                            <option value="ODF">ODF</option>
                            <option value="ODC">ODC</option>
                            <option value="ODP">ODP</option>
                        </select>
                    </div>

                    <div id="modal_odf_odc_wrap" class="hidden rounded-2xl border border-slate-700 bg-slate-900/80 p-4 space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-400">Detail Kabel</p>
                                <h4 id="modal_odf_odc_title" class="mt-1 text-sm font-bold text-white">ODF / ODC</h4>
                            </div>
                            <span id="modal_odf_odc_badge" class="rounded-full border border-sky-500/20 bg-sky-500/10 px-3 py-1 text-[11px] font-semibold text-sky-200">ODF</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400" for="modal_cable_total_tube">Jumlah Tube</label>
                                <input id="modal_cable_total_tube" name="cable_total_tube" type="number" min="1" max="99" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="12">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400" for="modal_cable_core_per_tube">Core / Tube</label>
                                <input id="modal_cable_core_per_tube" name="cable_core_per_tube" type="number" min="1" max="288" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="12">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400" for="modal_cable_tube_number">Tube yang Dipakai</label>
                                <input id="modal_cable_tube_number" name="cable_tube_number" type="number" min="1" max="99" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="2">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400" for="modal_cable_core_number">Core yang Dipakai</label>
                                <input id="modal_cable_core_number" name="cable_core_number" type="number" min="1" max="288" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="8">
                            </div>
                        </div>
                    </div>

                    <div id="modal_splitter_wrap" class="hidden">
                        <label class="mb-1 block text-sm font-semibold text-slate-200" for="modal_splitter_capacity">Kapasitas Splitter</label>
                        <select id="modal_splitter_capacity" name="splitter_capacity" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 focus:border-sky-500 focus:outline-none">
                            <option value="Tanpa Splitter">Tanpa Splitter</option>
                            <option value="1:4">1:4</option>
                            <option value="1:8">1:8</option>
                            <option value="1:16">1:16</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200" for="modal_latitude">Latitude</label>
                            <input id="modal_latitude" name="latitude" type="text" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="-6.985000" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200" for="modal_longitude">Longitude</label>
                            <input id="modal_longitude" name="longitude" type="text" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="107.830000" required>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" id="modalGpsBtn" class="flex-1 h-12 rounded-xl bg-slate-800 px-4 text-sm font-semibold text-white hover:bg-slate-700">Gunakan GPS HP</button>
                        <button type="button" id="modalFillDemoBtn" class="flex-1 h-12 rounded-xl border border-slate-700 px-4 text-sm font-semibold text-slate-200 hover:bg-slate-800">Gunakan Titik Peta</button>
                    </div>

                    <p id="gpsStatus" class="text-xs text-slate-500">GPS HP hanya bisa dipakai di localhost atau HTTPS, lalu browser harus diberi izin lokasi.</p>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200" for="modal_description">Deskripsi</label>
                        <textarea id="modal_description" name="description" rows="4" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-4 py-3 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="Catatan lokasi, tiang, atau keterangan teknis..."></textarea>
                    </div>

                    <button type="submit" class="h-12 w-full rounded-xl bg-sky-600 font-semibold text-white hover:bg-sky-500">Simpan Infrastruktur</button>
                </form>
            </div>
        </div>

        <!-- MODAL TAMBAH KABEL -->
        <div id="cableModal" class="fixed inset-0 z-60 hidden">
            <div id="cableModalBackdrop" class="absolute inset-0 bg-black/70"></div>
            <div class="absolute inset-x-0 bottom-0 max-h-[88vh] overflow-y-auto rounded-t-3xl border-t border-slate-700 bg-slate-950 p-4 shadow-2xl shadow-black/50">
                <div class="mx-auto mb-4 h-1.5 w-14 rounded-full bg-slate-600"></div>
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Kabel Antar Node</h3>
                        <p class="text-xs text-slate-400 mt-1">Pilih sumber dan tujuan untuk menyambungkan ODF, ODC, atau ODP.</p>
                    </div>
                    <button type="button" id="closeCableModalBtn" class="h-10 w-10 rounded-full bg-slate-800 text-slate-300 hover:bg-slate-700">&times;</button>
                </div>

                <form method="POST" action="{{ route('cables.store') }}" class="space-y-4" id="cableModalForm">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200" for="cable_name">Nama Kabel</label>
                        <input id="cable_name" name="name" type="text" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="Contoh: Kabel Feeder ODF-01 ke ODC-01">
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200" for="source_node_id">Node Sumber</label>
                            <select id="source_node_id" name="source_node_id" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 focus:border-sky-500 focus:outline-none" required>
                                <option value="">Pilih node sumber</option>
                                @foreach($nodes as $node)
                                    <option value="{{ $node->id }}">{{ $node->name }} ({{ $node->type === 'POP' ? 'ODF' : ($node->type === 'JB' ? 'ODC' : 'ODP') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200" for="destination_node_id">Node Tujuan</label>
                            <select id="destination_node_id" name="destination_node_id" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 focus:border-sky-500 focus:outline-none" required>
                                <option value="">Pilih node tujuan</option>
                                @foreach($nodes as $node)
                                    <option value="{{ $node->id }}">{{ $node->name }} ({{ $node->type === 'POP' ? 'ODF' : ($node->type === 'JB' ? 'ODC' : 'ODP') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400" for="cable_total_tube">Jumlah Tube</label>
                            <input id="cable_total_tube" name="total_tube" type="number" min="1" max="99" value="1" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-400" for="cable_cores_per_tube">Core / Tube</label>
                            <input id="cable_cores_per_tube" name="cores_per_tube" type="number" min="1" max="288" value="4" class="h-12 w-full rounded-xl border border-slate-600 bg-slate-900 px-4 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200" for="cable_description">Deskripsi</label>
                        <textarea id="cable_description" name="description" rows="4" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-4 py-3 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="Catatan jalur kabel, misal feeder atau distribusi..."></textarea>
                    </div>

                    <button type="submit" class="h-12 w-full rounded-xl bg-sky-600 font-semibold text-white hover:bg-sky-500">Simpan Kabel</button>
                </form>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS -->
        <div id="deleteModal" class="fixed inset-0 z-70 hidden">
            <div id="deleteModalBackdrop" class="absolute inset-0 bg-black/70"></div>
            <div class="absolute inset-x-0 bottom-0 flex max-h-[88vh] items-end justify-center px-4 pb-4 md:items-center">
                <div class="w-full max-w-md rounded-3xl border border-slate-700 bg-slate-950 p-5 shadow-2xl shadow-black/60">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-rose-400">Konfirmasi Hapus</p>
                            <h3 class="mt-1 text-lg font-bold text-white">Hapus data ini?</h3>
                            <p id="deleteModalMessage" class="mt-2 text-sm text-slate-400">Data kabel dan splitter terkait juga akan ikut terhapus.</p>
                        </div>
                        <button type="button" id="deleteModalCloseBtn" class="h-10 w-10 rounded-full border border-slate-700 bg-slate-900 text-lg font-bold text-slate-200 hover:bg-slate-800">&times;</button>
                    </div>

                    <div class="mt-5 flex gap-3">
                        <button type="button" id="deleteModalCancelBtn" class="h-12 flex-1 rounded-xl border border-slate-700 bg-slate-900 px-4 font-semibold text-slate-200 hover:bg-slate-800">Batal</button>
                        <button type="button" id="deleteModalConfirmBtn" class="h-12 flex-1 rounded-xl bg-rose-600 px-4 font-semibold text-white hover:bg-rose-500">Hapus</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Data dari Laravel Controller
        // (Mendukung mapping legacy POP->ODF, JB->ODC dari DB Lama ke Konvensi Baru)
        const nodes = @json($nodes);
        const cables = @json($cables);

        // Kalkulasi Statistik Stat Cards
        let countOdf = 0, countOdc = 0, countOdp = 0;
        nodes.forEach(n => {
            if (n.type === 'POP' || n.type === 'ODF') countOdf++;
            if (n.type === 'JB' || n.type === 'ODC') countOdc++;
            if (n.type === 'ODP') countOdp++;
        });

        document.getElementById('stat-odf').innerText = countOdf;
        document.getElementById('stat-odc').innerText = countOdc;
        document.getElementById('stat-odp').innerText = countOdp;

        // Deklarasi Elemen DOM
        const searchInput = document.getElementById('searchInput');
        const nodeListContainer = document.getElementById('node-list');
        const deleteModal = document.getElementById('deleteModal');
        const deleteModalBackdrop = document.getElementById('deleteModalBackdrop');
        const deleteModalCloseBtn = document.getElementById('deleteModalCloseBtn');
        const deleteModalCancelBtn = document.getElementById('deleteModalCancelBtn');
        const deleteModalConfirmBtn = document.getElementById('deleteModalConfirmBtn');
        const deleteModalMessage = document.getElementById('deleteModalMessage');
        const tabListBtn = document.getElementById('tab-list');
        const tabMapBtn = document.getElementById('tab-map');
        const contentList = document.getElementById('content-list');
        const contentMap = document.getElementById('content-map');

        let mapInstance = null;
        const defaultMapCenter = { lat: -6.985000, lng: 107.830000 };
        let pendingDeleteForm = null;

        // Fungsi Render List Daftar Data Real-Time
        function displayNodeType(node) {
            if (node.type === 'POP') return 'ODF';
            if (node.type === 'JB') return 'ODC';
            return 'ODP';
        }

        function renderList(filterText = '') {
            nodeListContainer.innerHTML = '';
            const lowerFilter = filterText.toLowerCase();

            const filteredNodes = nodes.filter(n => {
                return n.name.toLowerCase().includes(lowerFilter);
            });

            if (filteredNodes.length === 0) {
                nodeListContainer.innerHTML = '<div class="text-center text-slate-500 py-8 text-sm">Tidak ada data titik ditemukan.</div>';
                return;
            }

            filteredNodes.forEach(node => {
                const displayType = displayNodeType(node);
                const isODP = displayType === 'ODP';
                const isODC = displayType === 'ODC';
                const typeLabel = displayType;
                const iconColor = isODP ? 'text-green-400 bg-green-400/10 border-green-500/20' : (isODC ? 'text-blue-400 bg-blue-400/10 border-blue-500/20' : 'text-red-400 bg-red-400/10 border-red-500/20');

                // Visual Indikator Port Pelanggan untuk ODP (Simulasi Port 8 Core)
                let portVisuals = '';
                if (isODP) {
                    portVisuals = `
                        <div class="flex items-center gap-1.5 mt-3 bg-slate-900/80 inline-flex px-3 py-2 rounded-lg border border-slate-700">
                            <span class="text-[10px] text-slate-400 mr-1 font-semibold uppercase">Ports:</span>
                            <div class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_5px_#22c55e]"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_5px_#22c55e]"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-yellow-400 shadow-[0_0_5px_#facc15]"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_5px_#ef4444]"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-slate-600"></div>
                        </div>
                    `;
                }

                const actionUrl = isODP ? '/odp/' + node.id : relatedCableUrlForNode(node);
                const actionLabel = isODP ? 'Kelola Port Pelanggan' : 'Buka Splicing Matrix';
                const card = `
                    <div class="bg-slate-800 border border-slate-700 hover:border-slate-500 transition-colors rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="px-2 py-0.5 rounded border text-[10px] font-bold ${iconColor}">${typeLabel}</span>
                                    <h4 class="text-base font-bold text-white truncate max-w-[200px]">${node.name}</h4>
                                </div>
                                <p class="text-xs text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    ${isODP ? 'Hulu: ODC Terdekat (TBA)' : (isODC ? 'Splicing Matrix Distribusi' : 'Splicing Matrix Pusat')}
                                </p>
                            </div>
                        </div>

                        ${isODP ? portVisuals : ''}

                        <div class="mt-4 pt-4 border-t border-slate-700/50">
    <a href="${actionUrl || '#'}" class="flex items-center justify-center w-full h-12 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg transition ${actionUrl ? '' : 'pointer-events-none opacity-50'}">
        ${actionLabel}
    </a>
</div>

                        <form id="delete-node-form-${node.id}" method="POST" action="/nodes/${node.id}" class="mt-2">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="delete-node-trigger flex h-11 w-full items-center justify-center rounded-xl border border-rose-500/30 bg-rose-500/10 text-sm font-semibold text-rose-200 transition hover:bg-rose-500/20" data-form-id="delete-node-form-${node.id}" data-node-name="${node.name}">
                                Hapus Data
                            </button>
                        </form>
                    </div>
                `;
                nodeListContainer.innerHTML += card;
            });
        }

        function relatedCableUrlForNode(node) {
            const relatedCable = cables.find(cable => {
                const sourceId = cable.source_node ? cable.source_node.id : cable.source_node_id;
                const destinationId = cable.destination_node ? cable.destination_node.id : cable.destination_node_id;
                return Number(sourceId) === Number(node.id) || Number(destinationId) === Number(node.id);
            });

            return relatedCable ? '/cable/' + relatedCable.id : null;
        }

        // Logika Switch Tab (Data vs Peta)
        window.switchTab = function(tab) {
            if (tab === 'list') {
                tabListBtn.className = "flex-1 h-12 rounded-lg bg-blue-600 text-white font-semibold text-sm transition-colors";
                tabMapBtn.className = "flex-1 h-12 rounded-lg text-slate-400 font-semibold text-sm hover:text-slate-200 transition-colors bg-transparent";

                contentList.classList.remove('hidden');
                contentMap.classList.add('hidden');
            } else {
                tabMapBtn.className = "flex-1 h-12 rounded-lg bg-blue-600 text-white font-semibold text-sm transition-colors";
                tabListBtn.className = "flex-1 h-12 rounded-lg text-slate-400 font-semibold text-sm hover:text-slate-200 transition-colors bg-transparent";

                contentMap.classList.remove('hidden');
                contentList.classList.add('hidden');

                // Lazy load Leaflet Map
                if (!mapInstance) {
                    initMap();
                } else {
                    setTimeout(() => mapInstance.invalidateSize(), 100);
                }
            }
        }

        // Event Listener Pencarian
        searchInput.addEventListener('input', (e) => {
            renderList(e.target.value);
        });

        // Inisialisasi Peta
        function initMap() {
            var defaultCenter = [-6.985000, 107.830000];
            var defaultZoom = 14;
            mapInstance = L.map('map-mobile').setView(defaultCenter, defaultZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapInstance);

            nodes.forEach(function(node) {
                var displayType = displayNodeType(node);
                var color = (displayType === 'ODF') ? '#ef4444' : ((displayType === 'ODC') ? '#3b82f6' : '#22c55e');
                const marker = L.circleMarker([node.latitude, node.longitude], {
                    color: color,
                    fillColor: color,
                    radius: 8,
                    fillOpacity: 0.8,
                    weight: 2
                }).addTo(mapInstance);

                const nodeActionUrl = displayType === 'ODP' ? '/odp/' + node.id : relatedCableUrlForNode(node);
                const nodeActionLabel = displayType === 'ODP' ? 'Kelola Port Pelanggan' : 'Buka Splicing Matrix';

                marker.bindPopup('<b>' + node.name + '</b><br><span class="text-xs font-bold">' + displayType + '</span>' + (nodeActionUrl ? '<br><a href="' + nodeActionUrl + '" class="text-blue-500 font-bold mt-1 inline-block">' + nodeActionLabel + '</a>' : ''));
            });

            cables.forEach(function(cable) {
                var path = [
                    [cable.source_node.latitude, cable.source_node.longitude],
                    [cable.destination_node.latitude, cable.destination_node.longitude]
                ];

                var polyline = L.polyline(path, {
                    color: '#3b82f6',
                    weight: 4,
                    opacity: 0.8
                }).addTo(mapInstance);

                polyline.bindPopup('<b>' + cable.name + '</b><br><span class="text-xs">Kapasitas: ' + (cable.total_tube * cable.cores_per_tube) + ' Core</span><br><a href="/cable/' + cable.id + '" class="text-blue-500 font-bold mt-1 inline-block">Kelola Jalur Kabel</a>');
            });
        }

        // Modal tambah infrastruktur
        const openNodeModalBtn = document.getElementById('openNodeModalBtn');
        const openCableModalBtn = document.getElementById('openCableModalBtn');
        const nodeModal = document.getElementById('nodeModal');
        const nodeModalBackdrop = document.getElementById('nodeModalBackdrop');
        const closeNodeModalBtn = document.getElementById('closeNodeModalBtn');
        const cableModal = document.getElementById('cableModal');
        const cableModalBackdrop = document.getElementById('cableModalBackdrop');
        const closeCableModalBtn = document.getElementById('closeCableModalBtn');
        const sourceNodeId = document.getElementById('source_node_id');
        const destinationNodeId = document.getElementById('destination_node_id');
        const modalType = document.getElementById('modal_type');
        const modalOdfOdcWrap = document.getElementById('modal_odf_odc_wrap');
        const modalOdfOdcTitle = document.getElementById('modal_odf_odc_title');
        const modalOdfOdcBadge = document.getElementById('modal_odf_odc_badge');
        const modalContextOdf = document.getElementById('modal_context_odf');
        const modalContextOdc = document.getElementById('modal_context_odc');
        const modalSplitterWrap = document.getElementById('modal_splitter_wrap');
        const modalSplitterCapacity = document.getElementById('modal_splitter_capacity');
        const modalCableTotalTube = document.getElementById('modal_cable_total_tube');
        const modalCableCorePerTube = document.getElementById('modal_cable_core_per_tube');
        const modalCableTubeNumber = document.getElementById('modal_cable_tube_number');
        const modalCableCoreNumber = document.getElementById('modal_cable_core_number');
        const modalGpsBtn = document.getElementById('modalGpsBtn');
        const modalFillDemoBtn = document.getElementById('modalFillDemoBtn');
        const modalLatitude = document.getElementById('modal_latitude');
        const modalLongitude = document.getElementById('modal_longitude');
        const gpsStatus = document.getElementById('gpsStatus');
        const mapMobile = document.getElementById('map-mobile');
        const mapPickerToolbar = document.getElementById('mapPickerToolbar');
        const mapPickerCloseBtn = document.getElementById('mapPickerCloseBtn');
        const mapPickerStatus = document.getElementById('mapPickerStatus');
        const mapGuideCard = document.getElementById('mapGuideCard');

        let mapPickMode = false;
        let mapPickTimer = null;
        let pickedMapLatLng = null;
        let pickedMapMarker = null;
        let mapPickerHandlersBound = false;

        function openNodeModal() {
            nodeModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeNodeModal() {
            nodeModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openCableModal() {
            cableModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeCableModal() {
            cableModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function openDeleteModal(formId, nodeName) {
            pendingDeleteForm = document.getElementById(formId);

            if (!pendingDeleteForm) {
                return;
            }

            if (deleteModalMessage) {
                deleteModalMessage.textContent = nodeName
                    ? 'Data ' + nodeName + ' akan dihapus bersama kabel dan splitter terkait.'
                    : 'Data kabel dan splitter terkait juga akan ikut terhapus.';
            }

            deleteModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDeleteModal() {
            deleteModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            pendingDeleteForm = null;
        }

        function syncModalSplitter() {
            if (modalType.value === 'ODP') {
                modalOdfOdcWrap.classList.add('hidden');
                modalSplitterWrap.classList.remove('hidden');
                modalCableTotalTube.value = '';
                modalCableCorePerTube.value = '';
                modalCableTubeNumber.value = '';
                modalCableCoreNumber.value = '';
                return;
            }

            modalSplitterWrap.classList.add('hidden');
            modalSplitterCapacity.value = 'Tanpa Splitter';

            modalOdfOdcWrap.classList.remove('hidden');
            modalOdfOdcBadge.textContent = modalType.value;
            modalOdfOdcTitle.textContent = modalType.value === 'ODF' ? 'Pusat / Feeder' : 'Distribusi / Area';

            const isOdf = modalType.value === 'ODF';
            if (modalContextOdf && modalContextOdc) {
                modalContextOdf.classList.toggle('ring-2', isOdf);
                modalContextOdf.classList.toggle('ring-red-300/60', isOdf);
                modalContextOdf.classList.toggle('border-red-500', isOdf);
                modalContextOdc.classList.toggle('ring-2', !isOdf);
                modalContextOdc.classList.toggle('ring-blue-300/60', !isOdf);
                modalContextOdc.classList.toggle('border-blue-500', !isOdf);
            }

            modalCableTotalTube.focus();
        }

        function setGpsStatus(message, tone = 'neutral') {
            gpsStatus.textContent = message;
            gpsStatus.className = tone === 'error'
                ? 'text-xs text-rose-300'
                : (tone === 'success'
                    ? 'text-xs text-emerald-300'
                    : 'text-xs text-slate-500');
        }

        function setModalCoordinates(latitude, longitude) {
            modalLatitude.value = Number(latitude).toFixed(6);
            modalLongitude.value = Number(longitude).toFixed(6);
        }

        function setMapPickerStatus(message, tone = 'neutral') {
            if (!mapPickerStatus) {
                return;
            }

            mapPickerStatus.textContent = message;
            mapPickerStatus.className = tone === 'error'
                ? 'mt-1 text-xs text-rose-200'
                : (tone === 'success'
                    ? 'mt-1 text-xs text-emerald-200'
                    : 'mt-1 text-xs text-amber-100/80');
        }

        function clearMapPickTimer() {
            if (mapPickTimer) {
                window.clearTimeout(mapPickTimer);
                mapPickTimer = null;
            }
        }

        function placePickedMapMarker(latitude, longitude) {
            if (!mapInstance) {
                return;
            }

            if (pickedMapMarker) {
                pickedMapMarker.remove();
            }

            pickedMapMarker = L.circleMarker([latitude, longitude], {
                color: '#f59e0b',
                fillColor: '#f59e0b',
                radius: 10,
                fillOpacity: 0.95,
                weight: 3
            }).addTo(mapInstance);

            pickedMapMarker.bindPopup('<b>Titik terpilih</b><br><span class="text-xs">' + Number(latitude).toFixed(6) + ', ' + Number(longitude).toFixed(6) + '</span>').openPopup();
            pickedMapMarker.bringToFront();
        }

        function setPickedMapPoint(latitude, longitude) {
            pickedMapLatLng = {
                lat: Number(latitude),
                lng: Number(longitude)
            };

            setModalCoordinates(pickedMapLatLng.lat, pickedMapLatLng.lng);
            placePickedMapMarker(pickedMapLatLng.lat, pickedMapLatLng.lng);
            setMapPickerStatus('Titik dipilih. Tekan X untuk kembali ke form.', 'success');
        }

        function bindMapPickerHandlers() {
            if (!mapInstance || mapPickerHandlersBound) {
                return;
            }

            const startLongPress = function(event) {
                if (!mapPickMode || !event.latlng) {
                    return;
                }

                clearMapPickTimer();
                setMapPickerStatus('Tahan terus selama 2 detik...');

                const latlng = event.latlng;
                mapPickTimer = window.setTimeout(function() {
                    if (!mapPickMode) {
                        return;
                    }

                    setPickedMapPoint(latlng.lat, latlng.lng);
                }, 2000);
            };

            const stopLongPress = function() {
                clearMapPickTimer();
            };

            mapInstance.on('mousedown touchstart', startLongPress);
            mapInstance.on('mouseup mouseout dragstart zoomstart touchend touchcancel', stopLongPress);
            mapInstance.on('click move', stopLongPress);
            mapPickerHandlersBound = true;
        }

        function openMapPickerMode() {
            mapPickMode = true;
            pickedMapLatLng = null;

            if (pickedMapMarker) {
                pickedMapMarker.remove();
                pickedMapMarker = null;
            }

            closeNodeModal();
            mapGuideCard.classList.add('hidden');
            mapPickerToolbar.classList.remove('hidden');
            mapMobile.classList.remove('h-[65vh]');
            mapMobile.classList.add('h-[78vh]');
            setMapPickerStatus('Tekan lama 2 detik pada titik yang ingin dipakai.');

            switchTab('map');

            setTimeout(function() {
                if (mapInstance) {
                    mapInstance.invalidateSize();
                }

                bindMapPickerHandlers();
            }, 120);
        }

        function closeMapPickerMode() {
            mapPickMode = false;
            clearMapPickTimer();
            mapPickerToolbar.classList.add('hidden');
            mapGuideCard.classList.remove('hidden');
            mapMobile.classList.remove('h-[78vh]');
            mapMobile.classList.add('h-[65vh]');

            if (mapInstance) {
                setTimeout(function() {
                    mapInstance.invalidateSize();
                }, 120);
            }
        }

        function returnPickedPointToForm() {
            if (pickedMapLatLng) {
                setModalCoordinates(pickedMapLatLng.lat, pickedMapLatLng.lng);
                setGpsStatus('Koordinat diisi dari titik peta.', 'success');
            }

            closeMapPickerMode();
            openNodeModal();
        }

        openNodeModalBtn.addEventListener('click', openNodeModal);
        openCableModalBtn.addEventListener('click', openCableModal);
        nodeModalBackdrop.addEventListener('click', closeNodeModal);
        closeNodeModalBtn.addEventListener('click', closeNodeModal);
        cableModalBackdrop.addEventListener('click', closeCableModal);
        closeCableModalBtn.addEventListener('click', closeCableModal);
        deleteModalBackdrop.addEventListener('click', closeDeleteModal);
        deleteModalCloseBtn.addEventListener('click', closeDeleteModal);
        deleteModalCancelBtn.addEventListener('click', closeDeleteModal);
        deleteModalConfirmBtn.addEventListener('click', function() {
            if (pendingDeleteForm) {
                pendingDeleteForm.submit();
            }
        });

        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('.delete-node-trigger');
            if (!trigger) {
                return;
            }

            openDeleteModal(trigger.dataset.formId, trigger.dataset.nodeName || '');
        });

        sourceNodeId.addEventListener('change', function() {
            if (sourceNodeId.value && sourceNodeId.value === destinationNodeId.value) {
                destinationNodeId.value = '';
            }
        });

        destinationNodeId.addEventListener('change', function() {
            if (destinationNodeId.value && destinationNodeId.value === sourceNodeId.value) {
                sourceNodeId.value = '';
            }
        });

        modalType.addEventListener('change', syncModalSplitter);
        syncModalSplitter();

        modalGpsBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                setGpsStatus('Browser Anda tidak mendukung GPS.', 'error');
                alert('Browser Anda tidak mendukung GPS.');
                return;
            }

            setGpsStatus('Meminta izin lokasi ke browser...');
            modalGpsBtn.disabled = true;
            modalGpsBtn.classList.add('opacity-70', 'cursor-wait');

            navigator.geolocation.getCurrentPosition(function(position) {
                setModalCoordinates(position.coords.latitude, position.coords.longitude);
                setGpsStatus('Lokasi berhasil diambil dari GPS HP.', 'success');
                modalGpsBtn.disabled = false;
                modalGpsBtn.classList.remove('opacity-70', 'cursor-wait');
            }, function(error) {
                let message = 'Gagal mengambil lokasi GPS.';

                if (error && error.code === 1) {
                    message = 'Izin lokasi ditolak. Aktifkan izin lokasi di browser.';
                } else if (error && error.code === 2) {
                    message = 'Lokasi tidak tersedia. Coba pindah ke area dengan sinyal GPS yang lebih baik.';
                } else if (error && error.code === 3) {
                    message = 'GPS timeout. Coba lagi setelah beberapa detik.';
                }

                setGpsStatus(message, 'error');
                alert(message);
                modalGpsBtn.disabled = false;
                modalGpsBtn.classList.remove('opacity-70', 'cursor-wait');
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        });

        modalFillDemoBtn.addEventListener('click', function() {
            openMapPickerMode();
        });

        mapPickerCloseBtn.addEventListener('click', function() {
            returnPickedPointToForm();
        });

        // Render data saat pertama kali dimuat
        renderList();

        // Aksi dari hasil klik peta juga harus konsisten ke route yang benar
        document.addEventListener('click', function(event) {
            const anchor = event.target.closest('a[href^="/cable/"] , a[href^="/odp/"]');
            if (!anchor) return;
        });
    </script>
</body>
</html>
