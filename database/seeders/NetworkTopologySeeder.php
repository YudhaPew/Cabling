<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NetworkTopologySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan data lama agar tidak duplikat saat di-seed ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cables')->truncate();
        DB::table('nodes')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Input Data Titik (Nodes): POP, JB, dan ODP
        $popId = DB::table('nodes')->insertGetId([
            'name' => 'POP-PUSAT-CICALENGKA',
            'type' => 'POP',
            'latitude' => -6.985000,
            'longitude' => 107.830000,
            'description' => 'Kantor Utama / Ruang Server ISP',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $jb01Id = DB::table('nodes')->insertGetId([
            'name' => 'JB-UTAMA-TIANG-04',
            'type' => 'JB',
            'latitude' => -6.986200,
            'longitude' => 107.832500,
            'description' => 'Joint Closure di Tiang Depan Indomaret',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $odpA1Id = DB::table('nodes')->insertGetId([
            'name' => 'ODP-PANGHEGAR-A1',
            'type' => 'ODP',
            'latitude' => -6.988000,
            'longitude' => 107.835000,
            'description' => 'ODP Kapasitas Splitter 1:8 Perumahan Panghegar Block A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Input Data Jalur Kabel (Cables) yang menghubungkan titik-titik di atas
        DB::table('cables')->insert([
            [
                'name' => 'Kabel Feeder Utama (POP Ke JB-01)',
                'source_node_id' => $popId,
                'destination_node_id' => $jb01Id,
                'total_tube' => 4,         // Kabel Gede 4 Tube
                'cores_per_tube' => 6,     // 6 Core per tube (Total 24 Core)
                'description' => 'Kabel Udara 24 Core, rute jalan raya utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kabel Distribusi R-01 (JB-01 Ke ODP-A1)',
                'source_node_id' => $jb01Id,
                'destination_node_id' => $odpA1Id,
                'total_tube' => 1,         // Kabel Distribusi Lebih Kecil
                'cores_per_tube' => 4,     // 4 Core
                'description' => 'Kabel masuk ke area gang perumahan',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
