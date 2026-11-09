<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // 1. BUAT TABEL BARU
        // ============================================================

        // Tabel sekolah (menggantikan kelas)
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();
        });

        // Tabel sesi (kelompok belajar di bawah sekolah)
        Schema::create('sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolah')->cascadeOnDelete();
            $table->string('nama_sesi'); // "Sesi 1", "Sesi Pagi", dll. (bebas diketik)
            $table->timestamps();
        });

        // Pivot: sesi <-> guru (many-to-many, multi-guru per sesi)
        Schema::create('sesi_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['sesi_id', 'guru_id']);
        });

        // Pivot: sesi <-> siswa (dengan kolom course)
        Schema::create('sesi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('course')->nullable();
            $table->timestamps();
            $table->unique(['sesi_id', 'siswa_id']);
        });

        // Tabel pertemuan (menggantikan sesi_kelas yang berfungsi sebagai pertemuan harian)
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_id')->constrained('sesi')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('materi')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
            $table->unique(['sesi_id', 'tanggal']);
        });

        // Tabel absensi guru per pertemuan
        Schema::create('absensi_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuan')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'tidak_hadir'])->default('hadir');
            $table->timestamps();
            $table->unique(['pertemuan_id', 'guru_id']);
        });

        // Tambah kolom nama_panggilan di tabel siswa
        if (!Schema::hasColumn('siswa', 'nama_panggilan')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->string('nama_panggilan')->nullable()->after('nama');
            });
        }

        // ============================================================
        // 2. MIGRASI DATA LAMA KE STRUKTUR BARU
        // ============================================================

        // Peta kelas lama → sesi baru (untuk referensi ID)
        $kelasToSesi = [];

        if (Schema::hasTable('kelas')) {
            $kelasList = DB::table('kelas')->get();

            foreach ($kelasList as $kelas) {
                // kelas → sekolah
                DB::table('sekolah')->insert([
                    'id'            => $kelas->id,
                    'nama_sekolah'  => $kelas->nama_kelas,
                    'deskripsi'     => $kelas->deskripsi,
                    'status'        => $kelas->status ?? 'aktif',
                    'created_at'    => $kelas->created_at,
                    'updated_at'    => $kelas->updated_at,
                ]);

                // Buat 1 sesi default "Sesi 1" untuk setiap kelas lama
                $sesiId = DB::table('sesi')->insertGetId([
                    'sekolah_id' => $kelas->id,
                    'nama_sesi'  => 'Sesi 1',
                    'created_at' => $kelas->created_at,
                    'updated_at' => $kelas->updated_at,
                ]);

                $kelasToSesi[$kelas->id] = $sesiId;

                // Migrasi guru pengampu → sesi_guru
                if (!empty($kelas->guru_id)) {
                    DB::table('sesi_guru')->insert([
                        'sesi_id'    => $sesiId,
                        'guru_id'    => $kelas->guru_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Migrasi kelas_siswa → sesi_siswa
            if (Schema::hasTable('kelas_siswa')) {
                $kelasSiswaList = DB::table('kelas_siswa')->get();
                foreach ($kelasSiswaList as $ks) {
                    $sesiId = $kelasToSesi[$ks->kelas_id] ?? null;
                    if ($sesiId) {
                        // Cek duplikat sebelum insert
                        $exists = DB::table('sesi_siswa')
                            ->where('sesi_id', $sesiId)
                            ->where('siswa_id', $ks->siswa_id)
                            ->exists();
                        if (!$exists) {
                            DB::table('sesi_siswa')->insert([
                                'sesi_id'    => $sesiId,
                                'siswa_id'   => $ks->siswa_id,
                                'course'     => null,
                                'created_at' => $ks->created_at ?? now(),
                                'updated_at' => $ks->updated_at ?? now(),
                            ]);
                        }
                    }
                }
            }

            // Migrasi sesi_kelas → pertemuan + update absensi_siswa
            if (Schema::hasTable('sesi_kelas')) {
                $sesiKelasList = DB::table('sesi_kelas')->get();
                $oldToNewPertemuan = []; // mapping sesi_kelas.id → pertemuan.id

                foreach ($sesiKelasList as $sk) {
                    $sesiId = $kelasToSesi[$sk->kelas_id] ?? null;
                    if ($sesiId) {
                        $pertemuanId = DB::table('pertemuan')->insertGetId([
                            'sesi_id'       => $sesiId,
                            'tanggal'       => $sk->tanggal,
                            'materi'        => $sk->materi,
                            'catatan_guru'  => $sk->catatan_guru,
                            'created_at'    => $sk->created_at,
                            'updated_at'    => $sk->updated_at,
                        ]);
                        $oldToNewPertemuan[$sk->id] = $pertemuanId;
                    }
                }

                // Update absensi_siswa: ganti sesi_kelas_id lama → pertemuan_id baru
                foreach ($oldToNewPertemuan as $oldId => $newId) {
                    DB::table('absensi_siswa')
                        ->where('sesi_kelas_id', $oldId)
                        ->update(['sesi_kelas_id' => $newId]);
                }
            }
        }

        // ============================================================
        // 3. MODIFIKASI TABEL ABSENSI_SISWA
        // ============================================================

        // Rename kolom sesi_kelas_id → pertemuan_id
        Schema::table('absensi_siswa', function (Blueprint $table) {
            // Drop foreign key dan unique constraint lama
            $table->dropForeign(['sesi_kelas_id']);
        });

        // Cek dan drop unique constraint (nama bisa berbeda di MySQL)
        try {
            Schema::table('absensi_siswa', function (Blueprint $table) {
                $table->dropUnique('sesi_siswa_unique');
            });
        } catch (\Exception $e) {
            // Constraint mungkin tidak ada, lanjutkan
        }

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->renameColumn('sesi_kelas_id', 'pertemuan_id');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->foreign('pertemuan_id')->references('id')->on('pertemuan')->cascadeOnDelete();
            $table->unique(['pertemuan_id', 'siswa_id'], 'pertemuan_siswa_unique');
        });

        // Rename notes → notes_1, tambah notes_2
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->renameColumn('notes', 'notes_1');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->text('notes_2')->nullable()->after('notes_1');
        });

        // ============================================================
        // 4. HAPUS TABEL LAMA
        // ============================================================
        Schema::dropIfExists('kelas_siswa');
        Schema::dropIfExists('sesi_kelas');
        Schema::dropIfExists('kelas');
    }

    public function down(): void
    {
        // Rollback: buat ulang tabel lama (struktur dasar)
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->foreignId('guru_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('kelas_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['kelas_id', 'siswa_id']);
        });

        Schema::create('sesi_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal');
            $table->string('materi')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
            $table->unique(['kelas_id', 'tanggal'], 'sesi_kelas_tanggal_unique');
        });

        // Rollback absensi_siswa
        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->dropColumn('notes_2');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->renameColumn('notes_1', 'notes');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->dropForeign(['pertemuan_id']);
            $table->dropUnique('pertemuan_siswa_unique');
            $table->renameColumn('pertemuan_id', 'sesi_kelas_id');
        });

        Schema::table('absensi_siswa', function (Blueprint $table) {
            $table->foreign('sesi_kelas_id')->references('id')->on('sesi_kelas')->cascadeOnDelete();
            $table->unique(['sesi_kelas_id', 'siswa_id'], 'sesi_siswa_unique');
        });

        if (Schema::hasColumn('siswa', 'nama_panggilan')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('nama_panggilan');
            });
        }

        Schema::dropIfExists('absensi_guru');
        Schema::dropIfExists('pertemuan');
        Schema::dropIfExists('sesi_siswa');
        Schema::dropIfExists('sesi_guru');
        Schema::dropIfExists('sesi');
        Schema::dropIfExists('sekolah');
    }
};
