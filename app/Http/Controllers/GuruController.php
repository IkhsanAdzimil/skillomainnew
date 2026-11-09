<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\SesiKelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function dashboard()
    {
        $guru  = auth()->user();
        $kelas = Kelas::where('guru_id', $guru->id)
                      ->with(['siswa' => fn($q) => $q->orderBy('nama')])
                      ->first();

        // Validasi input tanggal agar aman dari format invalid
        $rawTanggal = request('tanggal');
        try {
            $tanggal = $rawTanggal ? Carbon::parse($rawTanggal)->format('Y-m-d') : now()->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggal = now()->format('Y-m-d');
        }

        $absensiHari = [];
        $sesi = null;

        if ($kelas) {
            // Ambil sesi jurnal kelas pada tanggal yang dipilih beserta detail absensi murid
            $sesi = SesiKelas::where('kelas_id', $kelas->id)
                ->where('tanggal', $tanggal)
                ->with('absensiSiswa')
                ->first();

            $absensiHari = $sesi ? $sesi->absensiSiswa->keyBy('siswa_id') : collect();
        }

        return view('guru.dashboard', compact('kelas', 'tanggal', 'absensiHari', 'sesi'));
    }

    public function simpanAbsensi(Request $request)
    {
        $data = $request->validate([
            'kelas_id'  => 'required|exists:kelas,id',
            'tanggal'   => 'required|date',
            'materi'    => 'nullable|string|max:255',
            'absensi'   => 'nullable|array',
            'absensi.*' => 'in:hadir,sakit,izin,alfa',
            'notes'     => 'nullable|array',
            'notes.*'   => 'nullable|string|max:500',
        ]);

        // Pastikan guru hanya bisa simpan absensi untuk kelas miliknya
        $kelas = Kelas::where('id', $data['kelas_id'])
                      ->where('guru_id', auth()->id())
                      ->firstOrFail();

        $absensiInput = $data['absensi'] ?? [];
        $notesInput   = $data['notes']   ?? [];

        // Bungkus dalam database transaction untuk integritas data (Master-Detail)
        DB::transaction(function () use ($kelas, $data, $absensiInput, $notesInput) {
            // 1. Master/Header: Dapatkan atau buat sesi jurnal pertemuan kelas
            $sesi = SesiKelas::firstOrCreate(
                [
                    'kelas_id' => $kelas->id,
                    'tanggal'  => $data['tanggal'],
                ],
                [
                    'guru_id' => auth()->id(),
                ]
            );

            // Perbarui guru_id dan materi ajar sesi ini
            $sesi->update([
                'guru_id' => auth()->id(),
                'materi'  => $data['materi'] ?? $sesi->materi,
            ]);

            // 2. Detail: Simpan status kehadiran dan catatan progres tiap siswa
            foreach ($kelas->siswa as $siswa) {
                AbsensiSiswa::updateOrCreate(
                    [
                        'sesi_kelas_id' => $sesi->id,
                        'siswa_id'      => $siswa->id,
                    ],
                    [
                        'status' => $absensiInput[$siswa->id] ?? 'hadir',
                        'notes'  => $notesInput[$siswa->id] ?? null,
                    ]
                );
            }
        });

        return back()->with('success', 'Absensi dan jurnal tanggal ' . $data['tanggal'] . ' berhasil disimpan!');
    }

    public function simpanCatatanSingle(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal'  => 'required|date',
            'notes'    => 'nullable|string|max:500',
            'status'   => 'nullable|in:hadir,sakit,izin,alfa',
        ]);

        $kelas = Kelas::where('id', $data['kelas_id'])
                      ->where('guru_id', auth()->id())
                      ->firstOrFail();

        // 1. Pastikan Master Sesi Kelas tersedia
        $sesi = SesiKelas::firstOrCreate(
            [
                'kelas_id' => $kelas->id,
                'tanggal'  => $data['tanggal'],
            ],
            [
                'guru_id' => auth()->id(),
            ]
        );

        // 2. Perbarui atau buat detail catatan murid pada sesi ini
        $absensi = AbsensiSiswa::where('sesi_kelas_id', $sesi->id)
            ->where('siswa_id', $data['siswa_id'])
            ->first();

        if ($absensi) {
            $absensi->notes = $data['notes'] ?? null;
            if (!empty($data['status'])) {
                $absensi->status = $data['status'];
            }
            $absensi->save();
        } else {
            AbsensiSiswa::create([
                'sesi_kelas_id' => $sesi->id,
                'siswa_id'      => $data['siswa_id'],
                'tanggal'       => $data['tanggal'],
                'status'        => $data['status'] ?? 'hadir',
                'notes'         => $data['notes'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
