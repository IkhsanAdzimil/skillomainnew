<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = ['nim', 'nama', 'no_hp', 'status'];

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_siswa', 'siswa_id', 'kelas_id')
                    ->withTimestamps();
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class, 'siswa_id');
    }
}
