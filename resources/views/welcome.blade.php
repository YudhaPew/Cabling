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
                    <!-- Cards akan disuntikkan secara dinamis oleh JavaScript -->
                </div>
            </div>
        </div>

        <!-- TAB 2: MAP JARINGAN -->
        <div id="content-map" class="hidden">
            <div class="bg-slate-800 p-2 rounded-xl border border-slate-700">
                <div id="map-mobile" class="w-full h-[65vh] rounded-lg bg-slate-950 z-0"></div>
            </div>
            <div class="mt-4 bg-slate-800 p-4 rounded-xl border border-slate-700">
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

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200" for="modal_description">Deskripsi</label>
                        <textarea id="modal_description" name="description" rows="4" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-4 py-3 text-slate-100 placeholder-slate-400 focus:border-sky-500 focus:outline-none" placeholder="Catatan lokasi, tiang, atau keterangan teknis..."></textarea>
                    </div>

                    <button type="submit" class="h-12 w-full rounded-xl bg-sky-600 font-semibold text-white hover:bg-sky-500">Simpan Infrastruktur</button>
                </form>
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
        const tabListBtn = document.getElementById('tab-list');
        const tabMapBtn = document.getElementById('tab-map');
        const contentList = document.getElementById('content-list');
        const contentMap = document.getElementById('content-map');

        let mapInstance = null;

        // Fungsi Render List Daftar Data Real-Time
        function renderList(filterText = '') {
            nodeListContainer.innerHTML = '';
            const lowerFilter = filterText.toLowerCase();

            // Tampilkan hanya ODP dan ODC/JB di list (Sembunyikan ODF kecuali dicari spesifik jika perlu)
            const filteredNodes = nodes.filter(n => {
                if (n.type === 'POP' || n.type === 'ODF') return false;
                return n.name.toLowerCase().includes(lowerFilter);
            });

            if (filteredNodes.length === 0) {
                nodeListContainer.innerHTML = '<div class="text-center text-slate-500 py-8 text-sm">Tidak ada data titik ditemukan.</div>';
                return;
            }

            filteredNodes.forEach(node => {
                const isODP = node.type === 'ODP';
                const typeLabel = isODP ? 'ODP' : 'ODC';
                const iconColor = isODP ? 'text-green-400 bg-green-400/10 border-green-500/20' : 'text-blue-400 bg-blue-400/10 border-blue-500/20';

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
                                    ${isODP ? 'Hulu: ODC Terdekat (TBA)' : 'Kapasitas Core / Jalur Distribusi'}
                                </p>
                            </div>
                        </div>

                        ${isODP ? portVisuals : ''}

                        <div class="mt-4 pt-4 border-t border-slate-700/50">
    <a href="${actionUrl || '#'}" class="flex items-center justify-center w-full h-12 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg transition ${actionUrl ? '' : 'pointer-events-none opacity-50'}">
        ${actionLabel}
    </a>
</div>
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
                var color = (node.type === 'POP' || node.type === 'ODF') ? '#ef4444' : ((node.type === 'JB' || node.type === 'ODC') ? '#3b82f6' : '#22c55e');
                const marker = L.circleMarker([node.latitude, node.longitude], {
                    color: color,
                    fillColor: color,
                    radius: 8,
                    fillOpacity: 0.8,
                    weight: 2
                }).addTo(mapInstance);

                const nodeActionUrl = node.type === 'ODP' ? '/odp/' + node.id : relatedCableUrlForNode(node);
                const nodeActionLabel = node.type === 'ODP' ? 'Kelola Port Pelanggan' : 'Buka Splicing Matrix';

                marker.bindPopup('<b>' + node.name + '</b><br><span class="text-xs font-bold">' + node.type + '</span>' + (nodeActionUrl ? '<br><a href="' + nodeActionUrl + '" class="text-blue-500 font-bold mt-1 inline-block">' + nodeActionLabel + '</a>' : ''));
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
        const nodeModal = document.getElementById('nodeModal');
        const nodeModalBackdrop = document.getElementById('nodeModalBackdrop');
        const closeNodeModalBtn = document.getElementById('closeNodeModalBtn');
        const modalType = document.getElementById('modal_type');
        const modalSplitterWrap = document.getElementById('modal_splitter_wrap');
        const modalSplitterCapacity = document.getElementById('modal_splitter_capacity');
        const modalGpsBtn = document.getElementById('modalGpsBtn');
        const modalFillDemoBtn = document.getElementById('modalFillDemoBtn');
        const modalLatitude = document.getElementById('modal_latitude');
        const modalLongitude = document.getElementById('modal_longitude');

        function openNodeModal() {
            nodeModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeNodeModal() {
            nodeModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function syncModalSplitter() {
            if (modalType.value === 'ODP') {
                modalSplitterWrap.classList.remove('hidden');
                return;
            }

            modalSplitterWrap.classList.add('hidden');
            modalSplitterCapacity.value = 'Tanpa Splitter';
        }

        openNodeModalBtn.addEventListener('click', openNodeModal);
        nodeModalBackdrop.addEventListener('click', closeNodeModal);
        closeNodeModalBtn.addEventListener('click', closeNodeModal);

        modalType.addEventListener('change', syncModalSplitter);
        syncModalSplitter();

        modalGpsBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung GPS.');
                return;
            }

            navigator.geolocation.getCurrentPosition(function(position) {
                modalLatitude.value = position.coords.latitude.toFixed(6);
                modalLongitude.value = position.coords.longitude.toFixed(6);
            }, function() {
                alert('Gagal mengambil lokasi GPS.');
            }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
        });

        modalFillDemoBtn.addEventListener('click', function() {
            if (mapInstance) {
                const center = mapInstance.getCenter();
                modalLatitude.value = center.lat.toFixed(6);
                modalLongitude.value = center.lng.toFixed(6);
            }
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
