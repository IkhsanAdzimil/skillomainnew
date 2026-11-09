<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSiswa;
use App\Models\Kelas;
use App\Models\SesiKelas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Export rekap absensi ke Excel atau PDF.
     * Digunakan oleh Admin (semua kelas) dan Guru (kelas sendiri).
     */
    public function export(Request $request)
    {
        $request->validate([
            'format'        => 'required|in:excel,pdf',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'periode'       => 'nullable|in:mingguan,bulanan',
        ]);

        $user     = auth()->user();
        $format   = $request->format;
        $tglMulai = $request->tanggal_mulai;
        $tglAkhir = $request->tanggal_akhir;
        $kelasId  = $request->kelas_id;

        $guruKelasIds = collect();
        if ($user->role === 'guru') {
            $guruKelasIds = Kelas::where('guru_id', $user->id)->pluck('id');
        }

        $query = AbsensiSiswa::with(['sesiKelas.kelas.guru', 'sesiKelas.guru', 'siswa'])
            ->whereHas('sesiKelas', function ($q) use ($tglMulai, $tglAkhir, $kelasId, $guruKelasIds, $user) {
                $q->whereBetween('tanggal', [$tglMulai, $tglAkhir]);

                // Proteksi Otorisasi: Guru hanya boleh export kelas yang ditugaskan kepadanya
                if ($user->role === 'guru') {
                    if ($guruKelasIds->isEmpty()) {
                        $q->whereRaw('1 = 0');
                    } elseif ($kelasId && $guruKelasIds->contains($kelasId)) {
                        $q->where('kelas_id', $kelasId);
                    } else {
                        $q->whereIn('kelas_id', $guruKelasIds);
                    }
                } elseif ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                }
            })
            ->join('sesi_kelas', 'absensi_siswa.sesi_kelas_id', '=', 'sesi_kelas.id')
            ->orderBy('sesi_kelas.tanggal', 'asc')
            ->select('absensi_siswa.*');

        $rows = $query->get();

        $kelasList = Kelas::all()->keyBy('id');
        $filename  = 'Rekap_Absensi_Skillo_' . $tglMulai . '_sd_' . $tglAkhir;

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.pdf', compact('rows', 'kelasList', 'tglMulai', 'tglAkhir'))
                      ->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        // Excel via HTML
        $html = view('exports.excel', compact('rows', 'kelasList', 'tglMulai', 'tglAkhir'))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.xls"');
    }
}
