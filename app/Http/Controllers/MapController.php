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
            'splitter_capacity' => ['nullable', 'in:Tanpa Splitter,1:4,1:8,1:16'],
            'description' => ['nullable', 'string'],
        ]);

        $nodeType = match ($validated['type']) {
            'ODF' => 'POP',
            'ODC' => 'JB',
            default => 'ODP',
        };

        $node = Node::create([
            'name' => $validated['name'],
            'type' => $nodeType,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => $validated['description'] ?? null,
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
