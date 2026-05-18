<?php

namespace App\Http\Controllers;

use App\Models\Node;
use App\Models\Cable;
use App\Models\Splitter;
use App\Models\SplitterPort;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * TAMPILAN DASHBOARD UTAMA
     * Mengambil seluruh data Node & Kabel untuk halaman depan
     */
    public function index()
    {
        // Ambil data untuk dilempar ke welcome.blade.php
        $nodes = Node::all();
        // Pastikan relasi source_node dan destination_node ikut terbawa jika ada
        $cables = Cable::with(['source_node', 'destination_node'])->get();

        // Hitung agregat statistik untuk Stat Cards gaya Portainer
        $totalOdf = Node::where('type', 'ODF')->count();
        $totalOdc = Node::where('type', 'ODC')->count();
        $totalOdp = Node::where('type', 'ODP')->count();

        return view('welcome', compact('nodes', 'cables', 'totalOdf', 'totalOdc', 'totalOdp'));
    }

    /**
     * SIMPAN NODE BARU DARI MODAL MOBILE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:ODF,ODC,ODP'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'cable_total_tube' => ['nullable', 'integer', 'min:1', 'max:99', 'required_if:type,ODF,ODC'],
            'cable_core_per_tube' => ['nullable', 'integer', 'min:1', 'max:288', 'required_if:type,ODF,ODC'],
            'cable_tube_number' => ['nullable', 'integer', 'min:1', 'max:99', 'required_if:type,ODF,ODC'],
            'cable_core_number' => ['nullable', 'integer', 'min:1', 'max:288', 'required_if:type,ODF,ODC'],
            'splitter_capacity' => ['nullable', 'in:Tanpa Splitter,1:4,1:8,1:16'],
            'description' => ['nullable', 'string'],
        ]);

        $nodeType = match ($validated['type']) {
            'ODF' => 'POP',
            'ODC' => 'JB',
            default => 'ODP',
        };

        $descriptionParts = [];

        if (!empty($validated['description'])) {
            $descriptionParts[] = trim($validated['description']);
        }

        if (in_array($validated['type'], ['ODF', 'ODC'], true)) {
            $descriptionParts[] = sprintf(
                'Kabel: %d tube, %d core/tube, tube terpakai %d, core terpakai %d',
                (int) $validated['cable_total_tube'],
                (int) $validated['cable_core_per_tube'],
                (int) $validated['cable_tube_number'],
                (int) $validated['cable_core_number']
            );
        }

        if ($validated['type'] === 'ODP' && !empty($validated['splitter_capacity']) && $validated['splitter_capacity'] !== 'Tanpa Splitter') {
            $descriptionParts[] = 'Splitter: ' . $validated['splitter_capacity'];
        }

        $node = Node::create([
            'name' => $validated['name'],
            'type' => $nodeType,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => count($descriptionParts) ? implode(' | ', $descriptionParts) : null,
        ]);

        if ($validated['type'] === 'ODP' && !empty($validated['splitter_capacity']) && $validated['splitter_capacity'] !== 'Tanpa Splitter') {
            Splitter::create([
                'node_id' => $node->id,
                'capacity' => $validated['splitter_capacity'],
            ]);
        }

        return redirect()->back()->with('success', 'Data infrastruktur berhasil ditambahkan.');
    }

    /**
     * SIMPAN KABEL BARU ANTAR NODE
     */
    public function storeCable(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'source_node_id' => ['required', 'integer', 'exists:nodes,id', 'different:destination_node_id'],
            'destination_node_id' => ['required', 'integer', 'exists:nodes,id'],
            'total_tube' => ['required', 'integer', 'min:1', 'max:99'],
            'cores_per_tube' => ['required', 'integer', 'min:1', 'max:288'],
            'description' => ['nullable', 'string'],
        ]);

        $sourceNode = Node::findOrFail($validated['source_node_id']);
        $destinationNode = Node::findOrFail($validated['destination_node_id']);

        Cable::create([
            'name' => $validated['name'] ?: sprintf('Kabel %s -> %s', $sourceNode->name, $destinationNode->name),
            'source_node_id' => $sourceNode->id,
            'destination_node_id' => $destinationNode->id,
            'total_tube' => $validated['total_tube'],
            'cores_per_tube' => $validated['cores_per_tube'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Kabel berhasil dibuat dan bisa dibuka di Splicing Matrix.');
    }

    /**
     * HAPUS NODE BESERTA DATA TURUNANNYA
     */
    public function destroy($id)
    {
        $node = Node::findOrFail($id);
        $nodeName = $node->name;
        $node->delete();

        return redirect()->back()->with('success', sprintf('Data %s berhasil dihapus.', $nodeName));
    }

    /**
     * HALAMAN DETAIL PORT SPLITTER ODP
     */
    public function showOdp($id)
    {
        $odp = Node::where('type', 'ODP')->with(['splitters.ports'])->findOrFail($id);

        return view('odp_detail', compact('odp'));
    }

    /**
     * HALAMAN DETAIL KABEL & SPLICING MATRIX
     * Berfungsi saat tombol "Buka Splicing Matrix" diklik
     */
    public function show($id)
    {
        // Ambil data kabel beserta relasi hulu (source) dan hilir (destination)
        $cable = Cable::with(['source_node', 'destination_node'])->findOrFail($id);

        // Ambil data splicing_connections yang terikat dengan kabel ini (jika ada)
        // Sesuaikan dengan nama model splicing Anda (misal: SplicingConnection)
        $connections = \App\Models\SplicingConnection::where('cable_id', $id)
            ->orderBy('tube_number')
            ->orderBy('core_number')
            ->get();

        // Lempar ke halaman view detail core kabel Anda (misal: cable_detail)
        return view('cable_detail', compact('cable', 'connections'));
    }

    /**
     * UPDATE STATUS CORE DI SPLICING MATRIX
     */
    public function updateConnection(Request $request, $id)
    {
        $cable = Cable::findOrFail($id);

        $validated = $request->validate([
            'tube_number' => ['required', 'integer', 'min:1', 'max:' . (int) $cable->total_tube],
            'core_number' => ['required', 'integer', 'min:1', 'max:' . (int) $cable->cores_per_tube],
            'status' => ['required', 'in:Kosong,Terpakai,Rusak'],
            'connected_to' => ['nullable', 'string', 'max:255'],
        ]);

        $connection = \App\Models\SplicingConnection::updateOrCreate(
            [
                'cable_id' => $cable->id,
                'tube_number' => $validated['tube_number'],
                'core_number' => $validated['core_number'],
            ],
            [
                'status' => $validated['status'],
                'connected_to' => $validated['status'] === 'Kosong' ? null : ($validated['connected_to'] ?? null),
                'updated_by' => optional(auth()->user())->name ?? 'Teknisi',
            ]
        );

        return redirect()->back()->with('success', sprintf('Core T%d/C%d berhasil diperbarui (%s).', $connection->tube_number, $connection->core_number, $connection->status));
    }

    /**
     * PROSES UPDATE PORT PELANGGAN (DARI MODAL)
     */
    public function updatePort(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Kosong,Terpakai,Rusak',
            'customer_name' => 'nullable|string|max:255',
            'customer_id' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $port = SplitterPort::findOrFail($id);
        $port->update([
            'status' => $request->status,
            'customer_name' => $request->status === 'Kosong' ? null : $request->customer_name,
            'customer_id' => $request->status === 'Kosong' ? null : $request->customer_id,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Data Port Pelanggan Berhasil Diperbarui!');
    }


}
