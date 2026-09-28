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
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Administrator Pusat',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'tps_id'   => null,
                'phone'    => '081234567890',
            ]
        );

        // 2. Candidates
        \App\Models\Candidate::firstOrCreate(
            ['nomor_urut' => 1],
            [
                'nama' => 'Nurma Setiawan, SE',
                'is_main_candidate' => true,
                'warna_badge' => '#059669',
            ]
        );
        \App\Models\Candidate::firstOrCreate(
            ['nomor_urut' => 2],
            [
                'nama' => 'Drs. H. Subagyo, M.Si',
                'is_main_candidate' => false,
                'warna_badge' => '#2563eb',
            ]
        );

        // 3. Sample TPS
        $tpsNames = ['TPS 01', 'TPS 02', 'TPS 03', 'TPS 04'];
        $tpsModels = [];

        foreach ($tpsNames as $index => $nama) {
            $tps = Tps::firstOrCreate(['nama_tps' => $nama]);
            $tpsModels[$nama] = $tps;

            // Buat Akun Saksi per TPS
            $saksiNumber = sprintf('%02d', $index + 1);
            User::updateOrCreate(
                ['email' => "saksi{$saksiNumber}@example.com"],
                [
                    'name'     => "Saksi {$nama}",
                    'password' => Hash::make('password'),
                    'role'     => 'saksi',
                    'tps_id'   => $tps->id,
                    'phone'    => "0812345678{$saksiNumber}",
                ]
            );
        }

        // 4. Sample Voters
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
