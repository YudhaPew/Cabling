<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LargeNetworkTopologySeeder extends Seeder
{
    public function run(): void
    {
        $this->truncateTopologyTables();

        $now = now();

        $odfNodes = [
            [
                'name' => 'ODF-CCK-01',
                'latitude' => -6.984120,
                'longitude' => 107.829840,
                'description' => 'ODF pusat Cicalengka sisi barat, dekat node agregasi utama.',
            ],
            [
                'name' => 'ODF-CCK-02',
                'latitude' => -6.985410,
                'longitude' => 107.831120,
                'description' => 'ODF pusat Cicalengka sisi timur untuk redundansi core utama.',
            ],
        ];

        $odcNodes = [
            [
                'name' => 'ODC-BYPASS-01',
                'latitude' => -6.978950,
                'longitude' => 107.822850,
                'description' => 'Lemari distribusi koridor Bypass Cicalengka utara.',
            ],
            [
                'name' => 'ODC-BYPASS-02',
                'latitude' => -6.980650,
                'longitude' => 107.828300,
                'description' => 'Lemari distribusi koridor Bypass Cicalengka selatan.',
            ],
            [
                'name' => 'ODC-RAYA-01',
                'latitude' => -6.981350,
                'longitude' => 107.834900,
                'description' => 'Lemari distribusi Jl. Raya Cicalengka-Majalaya sisi timur.',
            ],
            [
                'name' => 'ODC-RAYA-02',
                'latitude' => -6.988250,
                'longitude' => 107.837650,
                'description' => 'Lemari distribusi Jl. Raya Cicalengka-Majalaya sisi tenggara.',
            ],
            [
                'name' => 'ODC-CIKUYA-01',
                'latitude' => -6.992400,
                'longitude' => 107.823650,
                'description' => 'Lemari distribusi area Cikuya untuk pemukiman padat.',
            ],
            [
                'name' => 'ODC-CICALENGKA-01',
                'latitude' => -6.994050,
                'longitude' => 107.830650,
                'description' => 'Lemari distribusi area Cicalengka Kulon menuju gang warga.',
            ],
            [
                'name' => 'ODC-PANENJOAN-01',
                'latitude' => -6.979750,
                'longitude' => 107.835050,
                'description' => 'Lemari distribusi sisi Panenjoan dan jalur pemukiman.',
            ],
            [
                'name' => 'ODC-MAJALAYA-01',
                'latitude' => -6.985850,
                'longitude' => 107.819250,
                'description' => 'Lemari distribusi koridor Majalaya sisi barat.',
            ],
        ];

        $odfIds = [];
        foreach ($odfNodes as $node) {
            $odfIds[] = $this->createNode($node['name'], 'POP', $node['latitude'], $node['longitude'], $node['description'], $now);
        }

        $odcIds = [];
        $odcAssignments = [];
        foreach ($odcNodes as $index => $node) {
            $odcId = $this->createNode($node['name'], 'JB', $node['latitude'], $node['longitude'], $node['description'], $now);
            $odcIds[] = $odcId;

            $assignedOdfId = $odfIds[$index % count($odfIds)];
            $odcAssignments[$odcId] = $assignedOdfId;

            $this->createCable(
                'FEEDER-' . $odfNodes[$index % count($odfNodes)]['name'] . '-' . $node['name'],
                $assignedOdfId,
                $odcId,
                4,
                6,
                'Feeder utama 24 core dari ' . $odfNodes[$index % count($odfNodes)]['name'] . ' ke ' . $node['name'] . '.',
                $now
            );
        }

        $odpClusters = [
            ['prefix' => 'PANGHEGAR', 'label' => 'Panghegar', 'count' => 12, 'odcId' => $odcIds[0]],
            ['prefix' => 'BYPASS', 'label' => 'Bypass', 'count' => 12, 'odcId' => $odcIds[1]],
            ['prefix' => 'RAYA', 'label' => 'Raya', 'count' => 12, 'odcId' => $odcIds[2]],
            ['prefix' => 'CIKUYA', 'label' => 'Cikuya', 'count' => 11, 'odcId' => $odcIds[3]],
            ['prefix' => 'CICALENGKA', 'label' => 'Cicalengka', 'count' => 11, 'odcId' => $odcIds[4]],
            ['prefix' => 'PANENJOAN', 'label' => 'Panenjoan', 'count' => 11, 'odcId' => $odcIds[5]],
            ['prefix' => 'MAJALAYA', 'label' => 'Majalaya', 'count' => 11, 'odcId' => $odcIds[6]],
            ['prefix' => 'KULON', 'label' => 'Kulon', 'count' => 10, 'odcId' => $odcIds[7]],
        ];

        $globalOdpIndex = 1;
        foreach ($odpClusters as $cluster) {
            $odcNode = $this->getNodeById($cluster['odcId']);

            for ($i = 1; $i <= $cluster['count']; $i++) {
                $odpName = sprintf('ODP-%s-%02d', $cluster['prefix'], $i);
                $coordinates = $this->randomizeAround((float) $odcNode->latitude, (float) $odcNode->longitude, 0.0045);

                $odpId = $this->createNode(
                    $odpName,
                    'ODP',
                    $coordinates['latitude'],
                    $coordinates['longitude'],
                    sprintf('Kotak tiang area %s nomor %02d.', $cluster['label'], $i),
                    $now
                );

                $this->createCable(
                    sprintf('DIST-%s-%s', $this->getNodeNameById($cluster['odcId']), $odpName),
                    $cluster['odcId'],
                    $odpId,
                    1,
                    4,
                    sprintf('Kabel distribusi 4 core dari %s ke %s.', $this->getNodeNameById($cluster['odcId']), $odpName),
                    $now
                );

                $globalOdpIndex++;
            }
        }
    }

    private function truncateTopologyTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('splitter_ports')->truncate();
        DB::table('splitters')->truncate();
        DB::table('splicing_connections')->truncate();
        DB::table('cables')->truncate();
        DB::table('nodes')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function createNode(string $name, string $type, float $latitude, float $longitude, string $description, $now): int
    {
        return (int) DB::table('nodes')->insertGetId([
            'name' => $name,
            'type' => $type,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'description' => $description,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function createCable(string $name, int $sourceNodeId, int $destinationNodeId, int $totalTube, int $coresPerTube, string $description, $now): void
    {
        DB::table('cables')->insert([
            'name' => $name,
            'source_node_id' => $sourceNodeId,
            'destination_node_id' => $destinationNodeId,
            'total_tube' => $totalTube,
            'cores_per_tube' => $coresPerTube,
            'description' => $description,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function randomizeAround(float $baseLatitude, float $baseLongitude, float $variance): array
    {
        return [
            'latitude' => $baseLatitude + $this->randomOffset($variance),
            'longitude' => $baseLongitude + $this->randomOffset($variance),
        ];
    }

    private function randomOffset(float $variance): float
    {
        return (((mt_rand() / mt_getrandmax()) * 2) - 1) * $variance;
    }

    private function getNodeById(int $nodeId): object
    {
        return DB::table('nodes')->select('id', 'name', 'latitude', 'longitude')->where('id', $nodeId)->first();
    }

    private function getNodeNameById(int $nodeId): string
    {
        return (string) DB::table('nodes')->where('id', $nodeId)->value('name');
    }
}
