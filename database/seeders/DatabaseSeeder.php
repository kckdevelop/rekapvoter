<?php

namespace Database\Seeders;

use App\Models\Tps;
use App\Models\User;
use App\Models\Voter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Sample TPS
        $tpsNames = ['TPS 01', 'TPS 02', 'TPS 03', 'TPS 04'];
        $tpsModels = [];

        foreach ($tpsNames as $nama) {
            $tpsModels[$nama] = Tps::firstOrCreate(['nama_tps' => $nama]);
        }

        // 3. Sample Voters
        $sampleVoters = [
            // TPS 01
            ['nama' => 'Budi Santoso', 'tps' => 'TPS 01', 'is_supporter' => true],
            ['nama' => 'Siti Aminah', 'tps' => 'TPS 01', 'is_supporter' => true],
            ['nama' => 'Agus Setiawan', 'tps' => 'TPS 01', 'is_supporter' => false],
            ['nama' => 'Dewi Lestari', 'tps' => 'TPS 01', 'is_supporter' => false],
            ['nama' => 'Eko Prasetyo', 'tps' => 'TPS 01', 'is_supporter' => true],

            // TPS 02
            ['nama' => 'Hendra Wijaya', 'tps' => 'TPS 02', 'is_supporter' => false],
            ['nama' => 'Rina Kurniawati', 'tps' => 'TPS 02', 'is_supporter' => true],
            ['nama' => 'Bambang Hermanto', 'tps' => 'TPS 02', 'is_supporter' => true],
            ['nama' => 'Sri Wahyuni', 'tps' => 'TPS 02', 'is_supporter' => false],

            // TPS 03
            ['nama' => 'Dedi Iskandar', 'tps' => 'TPS 03', 'is_supporter' => true],
            ['nama' => 'Lilis Suryani', 'tps' => 'TPS 03', 'is_supporter' => true],
            ['nama' => 'Joko Widodo', 'tps' => 'TPS 03', 'is_supporter' => true],

            // TPS 04
            ['nama' => 'Rudi Hartono', 'tps' => 'TPS 04', 'is_supporter' => false],
            ['nama' => 'Maya Indah', 'tps' => 'TPS 04', 'is_supporter' => false],
        ];

        foreach ($sampleVoters as $data) {
            Voter::firstOrCreate(
                [
                    'tps_id' => $tpsModels[$data['tps']]->id,
                    'nama'   => $data['nama'],
                ],
                [
                    'is_supporter' => $data['is_supporter'],
                ]
            );
        }
    }
}
