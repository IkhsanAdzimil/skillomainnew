<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['nama_kelas', 'guru_id', 'deskripsi', 'status'];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswa', 'kelas_id', 'siswa_id')
                    ->withTimestamps();
    }

    public function sesiKelas()
    {
        return $this->hasMany(SesiKelas::class, 'kelas_id');
    }

    public function absensiSiswa()
    {
        return $this->hasManyThrough(AbsensiSiswa::class, SesiKelas::class, 'kelas_id', 'sesi_kelas_id');
    }
}
