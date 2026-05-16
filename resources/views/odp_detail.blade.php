<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Port - {{ $odp->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 pb-12">

    <div class="max-w-md mx-auto">
        <a href="/" class="inline-flex items-center justify-center w-full h-12 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition mb-4 border border-slate-700 shadow-md">
            &larr; Kembali ke Dashboard Utama
        </a>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-xl font-medium text-sm shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <header class="bg-slate-800 p-5 rounded-2xl border border-slate-700 shadow-xl mb-6">
            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-wider rounded-md border border-emerald-500/20">Optical Distribution Point</span>
            <h1 class="text-xl font-bold text-white mt-2">{{ $odp->name }}</h1>
            <p class="text-slate-400 text-xs mt-1">{{ $odp->description ?? 'Tidak ada deskripsi lokasi.' }}</p>

            <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-slate-700/50 text-center text-[11px] font-semibold">
                <div class="bg-slate-900/50 p-2 rounded-lg"><span class="w-2 h-2 inline-block bg-emerald-500 rounded-full mr-1"></span> Kosong</div>
                <div class="bg-slate-900/50 p-2 rounded-lg"><span class="w-2 h-2 inline-block bg-amber-500 rounded-full mr-1"></span> Isi</div>
                <div class="bg-slate-900/50 p-2 rounded-lg"><span class="w-2 h-2 inline-block bg-rose-500 rounded-full mr-1"></span> Rusak</div>
            </div>
        </header>

        <div class="space-y-6">
            @forelse($odp->splitters as $splitter)
                <div class="bg-slate-800 rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
                    <div class="bg-slate-700/30 px-5 py-3 border-b border-slate-700/50 flex justify-between items-center">
                        <h3 class="font-bold text-sm text-slate-200">Splitter Utama (Kapasitas {{ $splitter->capacity }})</h3>
                    </div>

                    <div class="p-4 grid grid-cols-1 gap-3">
                        @foreach($splitter->ports->sortBy('port_number') as $port)
                            @php
                                // Tentukan warna box berdasarkan status port
                                $bgClass = $port->status === 'Terpakai' ? 'bg-amber-500/10 border-amber-500/30' : ($port->status === 'Rusak' ? 'bg-rose-500/10 border-rose-500/30' : 'bg-emerald-500/10 border-emerald-500/30');
                                $iconClass = $port->status === 'Terpakai' ? 'bg-amber-500' : ($port->status === 'Rusak' ? 'bg-rose-500' : 'bg-emerald-500');
                            @endphp

                            <div onclick="bukaFormPort('{{ $port->id }}', '{{ $port->port_number }}', '{{ $port->status }}', '{{ $port->customer_name }}', '{{ $port->customer_id }}', '{{ $port->description }}')"
                                 class="flex items-center justify-between p-4 rounded-xl border {{ $bgClass }} bg-slate-900/30 active:scale-[0.98] transition cursor-pointer">

                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 {{ $iconClass }} text-slate-900 font-bold text-sm rounded-xl flex items-center justify-center shadow-md">
                                        #{{ $port->port_number }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-white">
                                            {{ $port->status === 'Terpakai' ? $port->customer_name : 'PORT ' . $port->status }}
                                        </h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            {{ $port->status === 'Terpakai' ? 'ID: ' . $port->customer_id : 'Ketuk untuk konfigurasi' }}
                                        </p>
                                    </div>
                                </div>

                                <span class="text-slate-500 text-xs">&rarr;</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-8 bg-slate-800 rounded-2xl border border-slate-700 text-slate-500 text-sm">
                    Belum ada splitter terpasang di ODP ini.
                </div>
            @endforelse
        </div>
    </div>

    <div id="modalPort" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden items-end sm:items-center justify-center p-0 sm:p-4 z-50">
        <div class="bg-slate-800 w-full sm:max-w-md p-6 rounded-t-3xl sm:rounded-2xl border-t sm:border border-slate-700 shadow-2xl transform transition-transform duration-300">
            <h3 class="text-base font-bold text-white mb-4">Konfigurasi Jalur: <span id="modalTitle" class="text-blue-400"></span></h3>

            <form id="formUpdatePort" action="" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Status Port Fisik</label>
                    <select name="status" id="portStatus" onchange="toggleFormInputs()" class="w-full h-12 px-3 border border-slate-700 rounded-xl bg-slate-900 text-white focus:ring-2 focus:ring-blue-500 outline-none text-sm font-medium">
                        <option value="Kosong">Kosong (Ready Belum Terpakai)</option>
                        <option value="Terpakai">Terpakai (Aktif ke Rumah User)</option>
                        <option value="Rusak">Rusak (Port Loss/Patah)</option>
                    </select>
                </div>

                <div id="userFields" class="space-y-4 mb-4 hidden">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nama Pelanggan (Homepass)</label>
                        <input type="text" name="customer_name" id="portCustomerName" placeholder="Contoh: Bpk. Jhon Doe"
                               class="w-full h-12 px-3 border border-slate-700 rounded-xl bg-slate-900 text-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">ID Pelanggan / No. Jaringan</label>
                        <input type="text" name="customer_id" id="portCustomerId" placeholder="Contoh: PHG-00234"
                               class="w-full h-12 px-3 border border-slate-700 rounded-xl bg-slate-900 text-white focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Catatan Teknis Lapangan</label>
                    <textarea name="description" id="portDesc" rows="2" placeholder="Keterangan tambahan (misal: redaman -18dB)"
                              class="w-full p-3 border border-slate-700 rounded-xl bg-slate-900 text-white focus:ring-2 focus:ring-blue-500 outline-none text-sm"></textarea>
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit" class="w-full h-12 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-lg transition active:scale-[0.98]">
                        Simpan Pembaruan Port
                    </button>
                    <button type="button" onclick="tutupFormPort()" class="w-full h-12 bg-slate-700 hover:bg-slate-600 text-slate-300 font-medium text-sm rounded-xl transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaFormPort(id, number, status, name, custId, desc) {
            // Pasang action URL form secara dinamis mengarah ke id port yang diklik
            document.getElementById('formUpdatePort').action = '/odp-port/' + id + '/update';

            document.getElementById('modalTitle').innerText = 'PORT ' + number;
            document.getElementById('portStatus').value = status;
            document.getElementById('portCustomerName').value = name !== 'null' ? name : '';
            document.getElementById('portCustomerId').value = custId !== 'null' ? custId : '';
            document.getElementById('portDesc').value = desc !== 'null' ? desc : '';

            toggleFormInputs(); // Cek visibilitas input user

            document.getElementById('modalPort').classList.remove('hidden');
            document.getElementById('modalPort').classList.add('flex');
        }

        function tutupFormPort() {
            document.getElementById('modalPort').classList.remove('flex');
            document.getElementById('modalPort').classList.add('hidden');
        }

        function toggleFormInputs() {
            var status = document.getElementById('portStatus').value;
            var userFields = document.getElementById('userFields');

            if (status === 'Terpakai') {
                userFields.classList.remove('hidden');
            } else {
                userFields.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
