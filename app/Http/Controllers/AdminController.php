<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\SesiKelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $kelasList  = Kelas::with(['guru', 'siswa'])->withCount('siswa')->latest()->get();
        $totalGuru  = User::where('role', 'guru')->count();
        $totalSiswa = Siswa::count();
        $guruList      = User::where('role', 'guru')->with('kelas')->orderBy('name')->get();
        $arsipGuruList = User::onlyTrashed()->where('role', 'guru')->orderBy('name')->get();
        $allSiswa      = Siswa::with('kelas')->orderBy('nama')->get();

        return view('admin.dashboard', compact('kelasList', 'totalGuru', 'totalSiswa', 'guruList', 'arsipGuruList', 'allSiswa'));
    }

    // --- Manajemen Kelas ---
    public function storeKelas(Request $request)
    {
        $data = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'guru_id'    => 'required|exists:users,id',
            'deskripsi'  => 'nullable|string|max:1000',
        ]);

        Kelas::create($data);

        return back()->with('success', 'Kelas "' . $data['nama_kelas'] . '" berhasil dibuat!');
    }

    public function updateKelas(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'guru_id'    => 'required|exists:users,id',
            'deskripsi'  => 'nullable|string|max:1000',
        ]);

        $kelas->update($data);

        return back()->with('success', 'Kelas berhasil diperbarui!');
    }

    public function destroyKelas(Kelas $kelas)
    {
        $namaKelas = $kelas->nama_kelas;

        DB::transaction(function () use ($kelas) {
            // 1. Ambil daftar murid yang terdaftar di kelas ini
            $siswaList = $kelas->siswa()->get();

            // 2. Hapus seluruh absensi siswa pada setiap sesi pertemuan kelas ini
            $sesiIds = $kelas->sesiKelas()->pluck('id');
            if ($sesiIds->isNotEmpty()) {
                AbsensiSiswa::whereIn('sesi_kelas_id', $sesiIds)->delete();
            }

            // 3. Hapus seluruh sesi pertemuan kelas
            $kelas->sesiKelas()->delete();

            // 4. Lepaskan keanggotaan kelas pada tabel pivot kelas_siswa
            $kelas->siswa()->detach();

            // 5. Hapus permanen murid yang hanya terdaftar di kelas ini
            foreach ($siswaList as $siswa) {
                if ($siswa->kelas()->count() === 0) {
                    $siswa->delete();
                }
            }

            // 6. Hapus kelas secara permanen
            $kelas->delete();
        });

        return back()->with('success', 'Kelas "' . $namaKelas . '" beserta seluruh isinya (sesi jurnal, riwayat absensi, dan data murid terkait) berhasil dihapus secara permanen.');
    }

    // --- Manajemen Murid / Pendaftaran Murid ke Kelas ---
    public function storeSiswa(Request $request)
    {
        $request->validate([
            'kelas_id'   => 'required|exists:kelas,id',
            'tipe_input' => 'nullable|in:baru,pilih',
        ]);

        $kelas     = Kelas::findOrFail($request->kelas_id);
        $tipeInput = $request->input('tipe_input', 'baru');

        // 1. Input Murid Baru (NAMA dan No Telepon / WhatsApp - NIM Otomatis oleh Sistem)
        if ($tipeInput === 'baru') {
            $data = $request->validate([
                'nama'  => 'required|string|max:255',
                'no_hp' => 'nullable|string|max:25',
            ]);

            // Auto-generate NIM unik: SKL + Tahun Saat Ini + 3 Digit Nomor Urut
            $lastId = Siswa::max('id') ?? 0;
            $generatedNim = 'SKL' . date('Y') . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            while (Siswa::where('nim', $generatedNim)->exists()) {
                $lastId++;
                $generatedNim = 'SKL' . date('Y') . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            }

            $siswa = Siswa::create([
                'nim'   => $generatedNim,
                'nama'  => trim($data['nama']),
                'no_hp' => !empty($data['no_hp']) ? trim($data['no_hp']) : null,
            ]);

            $kelas->siswa()->attach($siswa->id);

            return back()->with('success', 'Murid baru "' . $siswa->nama . '" (NIM: ' . $siswa->nim . ') berhasil ditambahkan ke kelas ' . $kelas->nama_kelas . '!');
        }

        // 2. Pilih Murid yang Sudah Terdaftar
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);

        if ($kelas->siswa()->where('siswa_id', $siswa->id)->exists()) {
            return back()->with('error', 'Murid "' . $siswa->nama . '" sudah ada di kelas ini.');
        }

        $kelas->siswa()->attach($siswa->id);
        return back()->with('success', 'Murid "' . $siswa->nama . '" berhasil didaftarkan ke kelas ' . $kelas->nama_kelas . '!');
    }

    public function destroyKelasSiswa(Kelas $kelas, Siswa $siswa)
    {
        $kelas->siswa()->detach($siswa->id);
        return back()->with('success', 'Murid "' . $siswa->nama . '" berhasil dikeluarkan dari kelas ' . $kelas->nama_kelas . '.');
    }

    public function destroySiswa(Siswa $siswa)
    {
        $nama = $siswa->nama;
        $siswa->delete();
        return back()->with('success', 'Data murid "' . $nama . '" berhasil dihapus secara permanen dari sistem.');
    }

    // --- Manajemen Guru ---
    public function storeGuru(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'no_hp'    => $data['no_hp'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'guru',
        ]);

        return back()->with('success', 'Akun Guru "' . $data['name'] . '" berhasil dibuat!');
    }

    public function destroyGuru(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->role !== 'guru') {
            return back()->with('error', 'Akun bukan guru.');
        }

        $nama = $user->name;
        $tipe = $request->input('tipe_hapus', 'daftar');

        if ($tipe === 'permanen') {
            // Hapus permanen dari sistem database
            Kelas::where('guru_id', $user->id)->update(['guru_id' => null]);
            $user->forceDelete();
            return back()->with('success', 'Akun Guru "' . $nama . '" berhasil dihapus secara permanen dari sistem.');
        } else {
            // Hapus dari daftar: lepas penugasan kelas dan soft delete (arsip)
            Kelas::where('guru_id', $user->id)->update(['guru_id' => null]);
            $user->delete();
            return back()->with('success', 'Akun Guru "' . $nama . '" berhasil dihapus dari daftar aktif.');
        }
    }

    public function restoreGuru($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->role !== 'guru') {
            return back()->with('error', 'Akun bukan guru.');
        }

        $user->restore();
        return back()->with('success', 'Akun Guru "' . $user->name . '" berhasil dipulihkan ke daftar aktif.');
    }
}
