<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users ---
        $admin = User::create([
            'name'     => 'Administrator',
            'username' => 'admin_skillo',
            'email'    => 'admin@skillo.id',
            'no_hp'    => '08111000001',
            'role'     => 'admin',
            'password' => Hash::make('password123'),
        ]);

        $guru = User::create([
            'name'     => 'Budi Santoso',
            'username' => 'budi_guru',
            'email'    => 'guru@skillo.id',
            'no_hp'    => '08222000002',
            'role'     => 'guru',
            'password' => Hash::make('password123'),
        ]);

        $guru2 = User::create([
            'name'     => 'Siti Rahayu',
            'username' => 'siti_guru',
            'email'    => 'siti@skillo.id',
            'no_hp'    => '08333000003',
            'role'     => 'guru',
            'password' => Hash::make('password123'),
        ]);

        // --- Kelas ---
        $kelas1 = Kelas::create([
            'nama_kelas' => 'Game Development',
            'guru_id'    => $guru->id,
            'deskripsi'  => 'Kelas pengembangan game menggunakan Unity dan C#.',
        ]);

        $kelas2 = Kelas::create([
            'nama_kelas' => 'UI/UX Design',
            'guru_id'    => $guru2->id,
            'deskripsi'  => 'Kelas desain antarmuka dan pengalaman pengguna.',
        ]);

        // --- Siswa ---
        $siswaDummy = [
            ['nim' => 'SKL2026001', 'nama' => 'Andi Pratama', 'no_hp' => '081234567801'],
            ['nim' => 'SKL2026002', 'nama' => 'Budi Santoso Jr.', 'no_hp' => '081234567802'],
            ['nim' => 'SKL2026003', 'nama' => 'Citra Dewi', 'no_hp' => '081234567803'],
            ['nim' => 'SKL2026004', 'nama' => 'Deni Maulana', 'no_hp' => '081234567804'],
            ['nim' => 'SKL2026005', 'nama' => 'Eka Putri', 'no_hp' => '081234567805'],
            ['nim' => 'SKL2026006', 'nama' => 'Faris Rahman', 'no_hp' => '081234567806'],
            ['nim' => 'SKL2026007', 'nama' => 'Gita Novia', 'no_hp' => '081234567807'],
            ['nim' => 'SKL2026008', 'nama' => 'Hendra Wijaya', 'no_hp' => '081234567808'],
        ];

        $siswaObjs = [];
        foreach ($siswaDummy as $s) {
            $siswaObjs[] = Siswa::create($s);
        }

        // Daftarkan 5 siswa pertama ke kelas 1
        $kelas1->siswa()->attach(
            collect($siswaObjs)->take(5)->pluck('id')->toArray()
        );

        // Daftarkan 5 siswa terakhir ke kelas 2
        $kelas2->siswa()->attach(
            collect($siswaObjs)->slice(3)->pluck('id')->toArray()
        );
    }
}
