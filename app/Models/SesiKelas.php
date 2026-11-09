<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiKelas extends Model
{
    use HasFactory;

    protected $table = 'sesi_kelas';

    protected $fillable = [
        'kelas_id',
        'guru_id',
        'tanggal',
        'materi',
        'catatan_guru',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'sesi_kelas_id');
    }
}
