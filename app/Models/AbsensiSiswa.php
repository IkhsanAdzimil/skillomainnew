<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSiswa extends Model
{
    use HasFactory;

    protected $table = 'absensi_siswa';

    protected $fillable = [
        'sesi_kelas_id',
        'siswa_id',
        'status',
        'notes',
    ];

    public function sesiKelas()
    {
        return $this->belongsTo(SesiKelas::class, 'sesi_kelas_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Accessor praktis untuk kemudahan akses langsung ke tanggal, kelas, dan guru sesi
    public function getTanggalAttribute()
    {
        return $this->sesiKelas?->tanggal;
    }

    public function getKelasAttribute()
    {
        return $this->sesiKelas?->kelas;
    }

    public function getGuruAttribute()
    {
        return $this->sesiKelas?->guru ?? $this->sesiKelas?->kelas?->guru;
    }
}
