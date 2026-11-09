@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

{{-- Sapaan & Tanggal --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="skillo-tag">
                <i class="bi bi-mortarboard-fill"></i> Admin Console
            </span>
            <span class="text-secondary small fw-semibold" id="currentDate"></span>
        </div>
        <h2 class="fw-black mb-0 font-monsserat" style="font-size: 1.85rem; letter-spacing: -0.03em;">
            Selamat Datang, {{ auth()->user()->name }}! 👋
        </h2>
        <p class="text-secondary small mb-0 mt-1">Kelola kelas, staf pengajar, dan data siswa Skillo Academy secara terpadu.</p>
    </div>
</div>

{{-- Statistik Card ala Skillo Company Web --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card h-100 p-2">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-2xl d-flex align-items-center justify-content-center shrink-0"
                     style="width:56px; height:56px; background: #fff4f2; border: 1.5px solid #fee2e2; border-radius: 16px;">
                    <i class="bi bi-collection-play-fill fs-3" style="color: var(--skillo-primary);"></i>
                </div>
                <div>
                    <span class="text-uppercase fw-bold text-secondary font-monsserat" style="font-size: 0.72rem; letter-spacing: 0.5px;">Kelas Aktif</span>
                    <h3 class="fw-black mb-0 font-monsserat" style="color: var(--skillo-primary); font-size: 1.85rem;">{{ $kelasList->count() }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Program & Ekstrakurikuler</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card h-100 p-2">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-2xl d-flex align-items-center justify-content-center shrink-0"
                     style="width:56px; height:56px; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 16px;">
                    <i class="bi bi-person-video3 fs-3" style="color: #059669;"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="text-uppercase fw-extrabold text-secondary font-monsserat d-block" style="font-size: 0.75rem; letter-spacing: 0.75px;">TOTAL GURU</span>
                    <h3 class="fw-black mb-0 font-monsserat text-success" style="font-size: 1.85rem; line-height: 1.15;">{{ $totalGuru }}</h3>
                    <a href="javascript:void(0)" class="text-decoration-none fw-bold small text-primary d-inline-flex align-items-center gap-1 mt-1"
                       data-bs-toggle="modal" data-bs-target="#modalSemuaGuru" style="font-size: 0.8rem;">
                        Lihat Semua <i class="bi bi-arrow-right-short fs-6"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-4">
        <div class="card h-100 p-2">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-2xl d-flex align-items-center justify-content-center shrink-0"
                     style="width:56px; height:56px; background: #fff9e8; border: 1.5px solid #fef3c7; border-radius: 16px;">
                    <i class="bi bi-mortarboard-fill fs-3" style="color: #d97706;"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="text-uppercase fw-extrabold text-secondary font-monsserat d-block" style="font-size: 0.75rem; letter-spacing: 0.75px;">TOTAL MURID</span>
                    <h3 class="fw-black mb-0 font-monsserat" style="color: #d97706; font-size: 1.85rem; line-height: 1.15;">{{ $totalSiswa }}</h3>
                    <a href="javascript:void(0)" class="text-decoration-none fw-bold small text-primary d-inline-flex align-items-center gap-1 mt-1"
                       data-bs-toggle="modal" data-bs-target="#modalSemuaMurid" style="font-size: 0.8rem;">
                        Lihat Semua <i class="bi bi-arrow-right-short fs-6"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Kelas Aktif --}}
<div class="card mb-4">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h5 class="fw-black mb-0 font-monsserat" style="font-size: 1.15rem;">
                <i class="bi bi-grid-3x3-gap-fill me-2" style="color: var(--skillo-primary);"></i>Daftar Kelas Aktif
            </h5>
            <small class="text-muted">Master data kelas dan alokasi guru pengampu</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button class="btn btn-skillo-amber" data-bs-toggle="modal" data-bs-target="#modalInputSiswa">
                <i class="bi bi-person-plus-fill me-1"></i>+ Tambah Murid
            </button>
            <button class="btn btn-skillo-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                <i class="bi bi-plus-circle me-1"></i>+ Tambah Kelas
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($kelasList->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 text-secondary opacity-50"></i>
                <p class="mt-2 fw-semibold">Belum ada kelas aktif saat ini.</p>
                <button class="btn btn-skillo-primary btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                    + Buat Kelas Pertama
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light" style="border-bottom: 2px solid #f1f5f9;">
                        <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            <th class="px-4 py-3">Nama Kelas</th>
                            <th class="py-3">Guru / Mentor</th>
                            <th class="py-3 text-center">Jumlah Murid</th>
                            <th class="py-3">Deskripsi Program</th>
                            <th class="px-4 py-3 text-end text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelasList as $kelas)
                        <tr>
                            <td class="px-4">
                                <span class="fw-bold font-monsserat text-dark" style="font-size: 0.95rem;">{{ $kelas->nama_kelas }}</span>
                            </td>
                            <td>
                                @if($kelas->guru)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($kelas->guru->name) }}&background=fee2e2&color=ee4c37&bold=true&rounded=true"
                                             class="avatar-sm border" alt="">
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $kelas->guru->name }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $kelas->guru->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge rounded-pill bg-light text-muted border fw-semibold">Belum Ditugaskan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-dark font-monsserat" style="font-size: 0.95rem;">{{ $kelas->siswa_count }}</span>
                            </td>
                            <td class="text-secondary small" style="max-width:240px">
                                {{ Str::limit($kelas->deskripsi, 60) ?? '-' }}
                            </td>
                            <td class="px-4 text-end text-nowrap">
                                <button class="btn btn-sm btn-skillo-dark me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDaftarMurid-{{ $kelas->id }}"
                                        title="Lihat Daftar Murid di Kelas Ini">
                                    <i class="bi bi-eye-fill me-1"></i>Murid
                                </button>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditKelas"
                                        data-kelas-id="{{ $kelas->id }}"
                                        data-kelas-nama="{{ $kelas->nama_kelas }}"
                                        data-guru-id="{{ $kelas->guru_id }}"
                                        data-deskripsi="{{ $kelas->deskripsi }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalKonfirmasiHapusKelas"
                                        data-action="{{ route('admin.kelas.destroy', $kelas) }}"
                                        data-kelas-nama="{{ $kelas->nama_kelas }}"
                                        data-murid-count="{{ $kelas->siswa_count }}"
                                        title="Hapus Permanen Kelas Beserta Isinya">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Daftar Guru / Mentor --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-black mb-0 font-monsserat" style="font-size: 1.15rem;">
                <i class="bi bi-people-fill me-2" style="color: #059669;"></i>Daftar Akun Guru & Mentor
            </h5>
            <small class="text-muted">Kredensial dan kelas yang diampu oleh staf pengajar</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(isset($arsipGuruList) && $arsipGuruList->isNotEmpty())
                <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalArsipGuru">
                    <i class="bi bi-archive me-1"></i>Arsip Guru ({{ $arsipGuruList->count() }})
                </button>
            @endif
            <button class="btn btn-skillo-amber btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                <i class="bi bi-person-plus me-1"></i>+ Guru Baru
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if(!\App\Models\User::where('role','guru')->exists())
            <div class="text-center py-4 text-muted small fw-semibold">Belum ada akun guru yang terdaftar.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light" style="border-bottom: 2px solid #f1f5f9;">
                        <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            <th class="px-4 py-3">Nama Mentor</th>
                            <th class="py-3">Username</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">WhatsApp</th>
                            <th class="py-3">Kelas Yang Diampu</th>
                            <th class="px-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\User::where('role','guru')->with('kelas')->get() as $guru)
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($guru->name) }}&background=f0fdf4&color=059669&bold=true&rounded=true"
                                         class="avatar-sm border" alt="">
                                    <span class="fw-bold font-monsserat text-dark small">{{ $guru->name }}</span>
                                </div>
                            </td>
                            <td class="small text-muted font-monospace">{{ $guru->username }}</td>
                            <td class="small fw-medium text-dark">{{ $guru->email }}</td>
                            <td class="small text-muted">{{ $guru->no_hp ?? '-' }}</td>
                            <td>
                                @foreach($guru->kelas as $k)
                                    <span class="badge rounded-pill bg-light text-dark border me-1 px-2.5 py-1 fw-semibold">{{ $k->nama_kelas }}</span>
                                @endforeach
                                @if($guru->kelas->isEmpty())
                                    <span class="text-muted small fst-italic">Belum mengampu kelas</span>
                                @endif
                            </td>
                            <td class="px-4 text-end text-nowrap">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-danger rounded-pill dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Pilihan Hapus">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="border-radius: 14px; min-width: 240px; z-index: 1050;">
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item p-2.5 rounded-3 d-flex align-items-center gap-2.5 text-start"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalKonfirmasiHapusGuruDaftar"
                                                    data-action="{{ route('admin.guru.destroy', $guru->id) }}"
                                                    data-guru-nama="{{ $guru->name }}"
                                                    data-guru-email="{{ $guru->email }}">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                                                     style="width: 32px; height: 32px; background: #fff9e8; border: 1px solid #fef3c7;">
                                                    <i class="bi bi-person-dash-fill text-warning"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark d-block" style="font-size: 0.85rem;">Hapus dari Daftar</span>
                                                    <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">Lepas dari pengajar aktif</small>
                                                </div>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item p-2.5 rounded-3 d-flex align-items-center gap-2.5 text-start"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalKonfirmasiHapusGuruPermanen"
                                                    data-action="{{ route('admin.guru.destroy', $guru->id) }}"
                                                    data-guru-nama="{{ $guru->name }}"
                                                    data-guru-email="{{ $guru->email }}">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                                                     style="width: 32px; height: 32px; background: #fff5f5; border: 1px solid #fee2e2;">
                                                    <i class="bi bi-trash-fill text-danger"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-danger d-block" style="font-size: 0.85rem;">Hapus Permanen</span>
                                                    <small class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.2;">Hapus total dari database</small>
                                                </div>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Tambah Kelas --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0"><i class="bi bi-plus-circle me-2" style="color: var(--skillo-primary);"></i>Tambah Kelas Baru</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control rounded-3" required placeholder="Contoh: Game Development Unity">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Guru / Mentor Pengampu <span class="text-danger">*</span></label>
                        <select name="guru_id" class="form-select rounded-3" required>
                            <option value="">— Pilih Guru Pengampu —</option>
                            @foreach($guruList as $g)
                                <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Jelaskan fokus kurikulum atau materi kelas ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skillo-primary">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Edit Kelas --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalEditKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Data Kelas</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditKelas" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" id="editNamaKelas" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Guru / Mentor Pengampu <span class="text-danger">*</span></label>
                        <select name="guru_id" id="editGuruId" class="form-select rounded-3" required>
                            @foreach($guruList as $g)
                                <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Deskripsi Kelas</label>
                        <textarea name="deskripsi" id="editDeskripsi" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skillo-dark">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Tambah Murid ke Kelas (Vertikal: Input Baru & Pilih Terdaftar, NIM Otomatis) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalInputSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0">
                    <i class="bi bi-person-plus-fill me-2" style="color: var(--skillo-primary);"></i>Tambah Murid ke Kelas
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.siswa.store') }}" method="POST" id="formTambahMurid">
                @csrf
                <div class="modal-body p-4">
                    {{-- Dropdown Pilihan Kelas Tujuan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary">Pilih Kelas Tujuan <span class="text-danger">*</span></label>
                        <select name="kelas_id" id="inputSiswaKelasId" class="form-select rounded-3" required>
                            <option value="">— Pilih Program Kelas —</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Mentor: {{ $k->guru?->name ?? 'Belum Ditugaskan' }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Pilih kelas tempat murid ini akan didaftarkan.</small>
                    </div>

                    {{-- Opsi Vertikal: Input Murid Baru & Pilih Murid Terdaftar --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-secondary d-block mb-2">Pilih Metode Penambahan:</label>
                        <div class="d-flex flex-column gap-2">
                            <!-- Opsi 1: Input Murid Baru -->
                            <div class="p-3 rounded-3 border custom-metode-box" id="boxMetodeBaru" onclick="pilihMetodeMurid('baru')"
                                 style="border: 2px solid var(--skillo-primary) !important; background: #fff4f2; cursor: pointer; transition: all 0.2s ease;">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-3 mt-0" type="radio" name="tipe_input" id="radioTipeBaru" value="baru" checked style="cursor: pointer;">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer flex-grow-1" for="radioTipeBaru">
                                        <i class="bi bi-person-plus-fill me-1" style="color: var(--skillo-primary);"></i>Input Murid Baru
                                        <span class="d-block text-muted small fw-normal mt-0.5">Daftarkan murid baru ke kelas ini (NIM dibuat otomatis oleh sistem)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Opsi 2: Pilih Murid Terdaftar -->
                            <div class="p-3 rounded-3 border custom-metode-box" id="boxMetodePilih" onclick="pilihMetodeMurid('pilih')"
                                 style="border: 1.5px solid #e2e8f0; background: #ffffff; cursor: pointer; transition: all 0.2s ease;">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input me-3 mt-0" type="radio" name="tipe_input" id="radioTipePilih" value="pilih" style="cursor: pointer;">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer flex-grow-1" for="radioTipePilih">
                                        <i class="bi bi-person-check-fill me-1 text-success"></i>Pilih Murid Terdaftar
                                        <span class="d-block text-muted small fw-normal mt-0.5">Pilih murid yang sudah ada di database untuk dimasukkan ke kelas</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Bidang 1: Input Murid Baru --}}
                    <div id="sectionFormBaru">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Nama Lengkap Murid <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="inputNamaMuridBaru" class="form-control rounded-3" required placeholder="Contoh: Muhammad Danendra">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">No. Telepon / WhatsApp <span class="text-muted fw-normal">(Murid / Orang Tua)</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-light text-muted"><i class="bi bi-telephone-fill"></i></span>
                                <input type="text" name="no_hp" id="inputNoHpMuridBaru" class="form-control border-start-0 rounded-end-3" placeholder="Contoh: 08123456789">
                            </div>
                            <small class="text-muted d-block mt-1">Untuk keperluan pencatatan absensi dan komunikasi progres belajar.</small>
                        </div>

                        <div class="p-2.5 px-3 rounded-3 d-flex align-items-center gap-2" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                            <i class="bi bi-magic fs-5" style="color: #d97706;"></i>
                            <div class="small text-secondary">
                                Nomor Induk Siswa (NIM) akan <strong>dibuat otomatis oleh sistem</strong>.
                            </div>
                        </div>
                    </div>

                    {{-- Form Bidang 2: Pilih Murid Terdaftar --}}
                    <div id="sectionFormPilih" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Pilih Murid Yang Sudah Terdaftar <span class="text-danger">*</span></label>
                            @if($allSiswa->isEmpty())
                                <div class="alert alert-warning small mb-0 rounded-3">Belum ada murid di database. Silakan pilih opsi "Input Murid Baru".</div>
                            @else
                                <select name="siswa_id" id="selectSiswaTerdaftar" class="form-select rounded-3">
                                    <option value="">— Cari / Pilih Nama Murid —</option>
                                    @foreach($allSiswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->nim }}) {{ $s->no_hp ? '— WA: ' . $s->no_hp : '' }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">Pilih murid dari database yang belum masuk ke kelas ini.</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skillo-primary" id="btnSubmitMurid">
                        <i class="bi bi-check-circle-fill me-1"></i>Simpan & Daftarkan Murid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL LIST: Lihat Daftar Murid di Setiap Kelas Yang Aktif --}}
{{-- ============================================================ --}}
@foreach($kelasList as $kelas)
<div class="modal fade" id="modalDaftarMurid-{{ $kelas->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <div>
                    <h6 class="modal-title fw-bold font-monsserat mb-0">
                        <i class="bi bi-mortarboard-fill me-2" style="color: var(--skillo-amber);"></i>Daftar Murid — {{ $kelas->nama_kelas }}
                    </h6>
                    <small class="text-white-50">Mentor Pengampu: {{ $kelas->guru?->name ?? 'Belum Ditugaskan' }}</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center p-3 mb-3 rounded-3 gap-2" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                    <div>
                        <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Informasi Program Kelas</span>
                        <h6 class="fw-black font-monsserat mb-0" style="color: var(--skillo-dark);">{{ $kelas->nama_kelas }}</h6>
                        <small class="text-muted">{{ $kelas->deskripsi ? Str::limit($kelas->deskripsi, 85) : 'Tidak ada deskripsi program.' }}</small>
                    </div>
                    <div class="text-sm-end">
                        <span class="skillo-tag-amber fs-6">
                            <i class="bi bi-people-fill me-1"></i>{{ $kelas->siswa->count() }} Murid Terdaftar
                        </span>
                    </div>
                </div>

                @if($kelas->siswa->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #fff4f2; border: 1px solid #fee2e2;">
                            <i class="bi bi-people fs-2" style="color: var(--skillo-primary);"></i>
                        </div>
                        <h6 class="fw-bold font-monsserat text-dark">Belum Ada Murid di Kelas Ini</h6>
                        <p class="small text-muted mb-0">Belum ada data murid yang terdaftar pada program kelas ini.</p>
                    </div>
                @else
                    <div class="table-responsive border rounded-3 overflow-hidden">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light" style="border-bottom: 2px solid #f1f5f9;">
                                <tr class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <th class="px-3 py-2.5" style="width: 50px;">No</th>
                                    <th class="py-2.5" style="width: 140px;">NIM</th>
                                    <th class="py-2.5">Nama Murid</th>
                                    <th class="py-2.5">No. Telepon / WhatsApp</th>
                                    <th class="px-3 py-2.5 text-end" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelas->siswa as $idx => $m)
                                <tr>
                                    <td class="px-3 fw-bold text-muted small">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border font-monospace px-2.5 py-1 fw-bold">{{ $m->nim }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monsserat text-dark" style="font-size: 0.92rem;">{{ $m->nama }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($m->no_hp))
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $m->no_hp) }}" target="_blank"
                                               class="badge rounded-pill bg-light text-success border text-decoration-none py-1 px-2.5 fw-semibold" title="Chat WhatsApp">
                                                <i class="bi bi-whatsapp me-1 text-success"></i>{{ $m->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted small fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-end text-nowrap">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-semibold"
                                                style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKonfirmasiKeluarkanMurid"
                                                data-action="{{ route('admin.kelas.siswa.destroy', [$kelas, $m]) }}"
                                                data-murid-nama="{{ $m->nama }}"
                                                data-murid-nim="{{ $m->nim }}"
                                                data-kelas-nama="{{ $kelas->nama_kelas }}"
                                                title="Keluarkan dari kelas ini">
                                            <i class="bi bi-box-arrow-right me-1"></i>Keluarkan
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-sm btn-skillo-outline" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ============================================================ --}}
{{-- MODAL: Kelola Seluruh Data Murid (Master Data & Hapus Permanen) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalSemuaMurid" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <div>
                    <h6 class="modal-title fw-bold font-monsserat mb-0">
                        <i class="bi bi-mortarboard-fill me-2" style="color: var(--skillo-amber);"></i>Master Data Seluruh Murid
                    </h6>
                    <small class="text-white-50">Total {{ $allSiswa->count() }} murid yang terdaftar di sistem Skillo Academy</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 mb-3 rounded-3" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                    <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Informasi Master Data</span>
                    <h6 class="fw-black font-monsserat mb-0" style="color: var(--skillo-dark);">Database Seluruh Murid</h6>
                    <small class="text-muted">Kelola data murid, kontak WhatsApp, serta opsi penghapusan data secara permanen.</small>
                </div>

                @if($allSiswa->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #fff4f2; border: 1px solid #fee2e2;">
                            <i class="bi bi-people fs-2" style="color: var(--skillo-primary);"></i>
                        </div>
                        <h6 class="fw-bold font-monsserat text-dark">Belum Ada Murid Terdaftar</h6>
                        <p class="small text-muted mb-0">Belum ada data murid yang terdaftar di sistem.</p>
                    </div>
                @else
                    <div class="table-responsive border rounded-3 overflow-hidden">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light" style="border-bottom: 2px solid #f1f5f9;">
                                <tr class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <th class="px-3 py-2.5" style="width: 50px;">No</th>
                                    <th class="py-2.5" style="width: 140px;">NIM</th>
                                    <th class="py-2.5">Nama Murid</th>
                                    <th class="py-2.5">No. Telepon / WhatsApp</th>
                                    <th class="py-2.5">Kelas Diikuti</th>
                                    <th class="px-3 py-2.5 text-end" style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allSiswa as $idx => $s)
                                <tr>
                                    <td class="px-3 fw-bold text-muted small">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border font-monospace px-2.5 py-1 fw-bold">{{ $s->nim }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monsserat text-dark" style="font-size: 0.92rem;">{{ $s->nama }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($s->no_hp))
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $s->no_hp) }}" target="_blank"
                                               class="badge rounded-pill bg-light text-success border text-decoration-none py-1 px-2.5 fw-semibold" title="Chat WhatsApp">
                                                <i class="bi bi-whatsapp me-1 text-success"></i>{{ $s->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted small fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse($s->kelas as $k)
                                            <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5 me-1 mb-1 fw-semibold" style="font-size: 0.7rem;">
                                                {{ $k->nama_kelas }}
                                            </span>
                                        @empty
                                            <span class="badge rounded-pill bg-light text-muted border px-2 py-0.5 fst-italic" style="font-size: 0.7rem;">
                                                Belum masuk kelas
                                            </span>
                                        @endforelse
                                    </td>
                                    <td class="px-3 text-end text-nowrap">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-bold"
                                                style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKonfirmasiHapusMuridPermanen"
                                                data-action="{{ route('admin.siswa.destroy', $s) }}"
                                                data-murid-nama="{{ $s->nama }}"
                                                data-murid-nim="{{ $s->nim }}"
                                                title="Hapus Data Murid Secara Permanen">
                                            <i class="bi bi-trash-fill me-1"></i>Hapus Data Murid
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-sm btn-skillo-outline" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Kelola Seluruh Data Guru & Mentor (Master Data) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalSemuaGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <div>
                    <h6 class="modal-title fw-bold font-monsserat mb-0">
                        <i class="bi bi-person-video3 me-2" style="color: #10b981;"></i>Master Data Seluruh Guru & Mentor
                    </h6>
                    <small class="text-white-50">Total {{ $totalGuru }} guru & mentor yang terdaftar di sistem Skillo Academy</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 mb-3 rounded-3" style="background: #f0fdf4; border: 1.5px solid #bbf7d0;">
                    <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Informasi Master Data</span>
                    <h6 class="fw-black font-monsserat mb-0" style="color: var(--skillo-dark);">Database Seluruh Guru & Mentor</h6>
                    <small class="text-muted">Kelola data mentor, kredensial login, kontak WhatsApp, kelas yang diampu, serta opsi penghapusan permanen.</small>
                </div>

                @if($guruList->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <i class="bi bi-person-badge fs-2" style="color: #059669;"></i>
                        </div>
                        <h6 class="fw-bold font-monsserat text-dark">Belum Ada Guru Terdaftar</h6>
                        <p class="small text-muted mb-0">Belum ada akun guru atau mentor yang terdaftar di sistem.</p>
                    </div>
                @else
                    <div class="table-responsive border rounded-3 overflow-hidden">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light" style="border-bottom: 2px solid #f1f5f9;">
                                <tr class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                    <th class="px-3 py-2.5" style="width: 50px;">No</th>
                                    <th class="py-2.5">Nama Mentor</th>
                                    <th class="py-2.5">Username</th>
                                    <th class="py-2.5">Email</th>
                                    <th class="py-2.5">No. Telepon / WhatsApp</th>
                                    <th class="py-2.5">Kelas Yang Diampu</th>
                                    <th class="px-3 py-2.5 text-end" style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guruList as $idx => $g)
                                <tr>
                                    <td class="px-3 fw-bold text-muted small">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($g->name) }}&background=f0fdf4&color=059669&bold=true&rounded=true"
                                                 class="avatar-sm border" alt="">
                                            <span class="fw-bold font-monsserat text-dark" style="font-size: 0.92rem;">{{ $g->name }}</span>
                                        </div>
                                    </td>
                                    <td class="small text-muted font-monospace">{{ $g->username }}</td>
                                    <td class="small fw-medium text-dark">{{ $g->email }}</td>
                                    <td>
                                        @if(!empty($g->no_hp))
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $g->no_hp) }}" target="_blank"
                                               class="badge rounded-pill bg-light text-success border text-decoration-none py-1 px-2.5 fw-semibold" title="Chat WhatsApp">
                                                <i class="bi bi-whatsapp me-1 text-success"></i>{{ $g->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-muted small fst-italic">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @forelse($g->kelas as $k)
                                            <span class="badge rounded-pill bg-light text-dark border px-2 py-0.5 me-1 mb-1 fw-semibold" style="font-size: 0.7rem;">
                                                {{ $k->nama_kelas }}
                                            </span>
                                        @empty
                                            <span class="badge rounded-pill bg-light text-muted border px-2 py-0.5 fst-italic" style="font-size: 0.7rem;">
                                                Belum mengampu kelas
                                            </span>
                                        @endforelse
                                    </td>
                                    <td class="px-3 text-end text-nowrap">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-bold"
                                                style="font-size: 0.75rem;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKonfirmasiHapusGuruPermanen"
                                                data-action="{{ route('admin.guru.destroy', $g->id) }}"
                                                data-guru-nama="{{ $g->name }}"
                                                data-guru-email="{{ $g->email }}"
                                                title="Hapus Data Mentor Secara Permanen">
                                            <i class="bi bi-trash-fill me-1"></i>Hapus Data Mentor
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-sm btn-skillo-outline" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Tambah Guru --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalTambahGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0"><i class="bi bi-person-badge-fill me-2 text-warning"></i>Buat Akun Guru / Mentor</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nama Lengkap Mentor <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" required placeholder="Contoh: Muhammad Darussalam">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control rounded-3" required placeholder="darussalam_mentor">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-secondary">Nomor WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control rounded-3" placeholder="0857...">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3" required placeholder="mentor@skillo.id">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Password Awal <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control rounded-3" required placeholder="Minimal 6 karakter">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skillo-amber">Buat Akun Mentor</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Arsip Guru Yang Dihapus dari Daftar --}}
{{-- ============================================================ --}}
@if(isset($arsipGuruList) && $arsipGuruList->isNotEmpty())
<div class="modal fade" id="modalArsipGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <div>
                    <h6 class="modal-title fw-bold font-monsserat mb-0">
                        <i class="bi bi-archive me-2 text-warning"></i>Arsip Akun Guru & Mentor
                    </h6>
                    <small class="text-white-50">Daftar akun guru yang sebelumnya dihapus dari daftar aktif</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light" style="border-bottom: 2px solid #f1f5f9;">
                            <tr class="text-secondary small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <th class="px-3 py-2.5">Nama Mentor</th>
                                <th class="py-2.5">Username</th>
                                <th class="py-2.5">Email</th>
                                <th class="px-3 py-2.5 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arsipGuruList as $ag)
                            <tr>
                                <td class="px-3">
                                    <span class="fw-bold text-dark small font-monsserat">{{ $ag->name }}</span>
                                </td>
                                <td class="small text-muted font-monospace">{{ $ag->username }}</td>
                                <td class="small text-muted">{{ $ag->email }}</td>
                                <td class="px-3 text-end text-nowrap">
                                    <form action="{{ route('admin.guru.restore', $ag->id) }}" method="POST" class="d-inline me-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan ke Daftar
                                        </button>
                                    </form>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1 fw-bold"
                                            style="font-size: 0.75rem;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalKonfirmasiHapusGuruPermanen"
                                            data-action="{{ route('admin.guru.destroy', $ag->id) }}"
                                            data-guru-nama="{{ $ag->name }}"
                                            data-guru-email="{{ $ag->email }}"
                                            title="Hapus Permanen">
                                        <i class="bi bi-trash-fill me-1"></i>Hapus Permanen
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-sm btn-skillo-outline" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI: Hapus Permanen Kelas Beserta Isinya --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalKonfirmasiHapusKelas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: var(--skillo-primary);"></i>Konfirmasi Hapus Kelas
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formHapusKelas" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: #fff4f2; border: 1.5px solid #fee2e2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                             style="width: 48px; height: 48px; background: #fee2e2; color: var(--skillo-primary);">
                            <i class="bi bi-trash-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Hapus Permanen Kelas</span>
                            <h6 class="fw-black font-monsserat mb-0 text-dark" id="hapusKelasNama">Nama Kelas</h6>
                            <small class="text-muted" id="hapusKelasInfo">Kelas ini beserta seluruh data di dalamnya akan dihapus.</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-circle-fill fs-5 shrink-0" style="color: #d97706;"></i>
                            <div class="small text-secondary">
                                Tindakan ini bersifat <strong>permanen dan tidak dapat dibatalkan</strong>. Seluruh sesi pertemuan jurnal, riwayat kehadiran absensi, dan data murid yang hanya terdaftar di kelas ini akan ikut terhapus dari sistem.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skillo-primary rounded-pill px-4 fw-bold shadow-xs">
                        <i class="bi bi-trash-fill me-1"></i>Hapus Permanen Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI: Keluarkan Murid dari Kelas --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalKonfirmasiKeluarkanMurid" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: var(--skillo-amber);"></i>Konfirmasi Keluarkan Murid
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formKeluarkanMurid" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                             style="width: 48px; height: 48px; background: #fef3c7; color: #d97706;">
                            <i class="bi bi-box-arrow-right fs-4"></i>
                        </div>
                        <div>
                            <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Keluarkan dari Program Kelas</span>
                            <h6 class="fw-black font-monsserat mb-0 text-dark" id="keluarkanMuridNama">Nama Murid</h6>
                            <small class="text-muted" id="keluarkanMuridInfo">NIM: - &bull; Kelas: -</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle-fill text-secondary fs-5 shrink-0"></i>
                            <div class="small text-secondary">
                                Murid hanya akan dikeluarkan dari program kelas ini. <strong>Data akun dan master profil murid tetap aman tersimpan di sistem</strong> dan dapat dimasukkan kembali ke kelas kapan saja.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skillo-amber rounded-pill px-4 fw-bold shadow-xs">
                        <i class="bi bi-box-arrow-right me-1"></i>Ya, Keluarkan Murid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI: Hapus Data Murid Secara Permanen --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalKonfirmasiHapusMuridPermanen" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: var(--skillo-primary);"></i>Konfirmasi Hapus Data Murid
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formHapusMuridPermanen" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: #fff4f2; border: 1.5px solid #fee2e2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                             style="width: 48px; height: 48px; background: #fee2e2; color: var(--skillo-primary);">
                            <i class="bi bi-trash-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Hapus Permanen Murid</span>
                            <h6 class="fw-black font-monsserat mb-0 text-dark" id="hapusMuridNama">Nama Murid</h6>
                            <small class="text-muted" id="hapusMuridInfo">NIM: -</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-circle-fill fs-5 shrink-0" style="color: #d97706;"></i>
                            <div class="small text-secondary">
                                Tindakan ini bersifat <strong>permanen dan tidak dapat dibatalkan</strong>. Data murid beserta seluruh keanggotaan kelas dan riwayat absensinya akan dihapus dari seluruh sistem.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-xs">
                        <i class="bi bi-trash-fill me-1"></i>Hapus Permanen Murid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI: Hapus Permanen Akun Guru & Mentor --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalKonfirmasiHapusGuruPermanen" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: var(--skillo-primary);"></i>Konfirmasi Hapus Permanen Mentor
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formHapusGuruPermanen" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tipe_hapus" value="permanen">
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: #fff4f2; border: 1.5px solid #fee2e2;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                             style="width: 48px; height: 48px; background: #fee2e2; color: var(--skillo-primary);">
                            <i class="bi bi-trash-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Hapus Permanen Mentor</span>
                            <h6 class="fw-black font-monsserat mb-0 text-dark" id="hapusGuruNama">Nama Mentor</h6>
                            <small class="text-muted" id="hapusGuruEmail">mentor@skillo.id</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-exclamation-circle-fill fs-5 shrink-0" style="color: #d97706;"></i>
                            <div class="small text-secondary">
                                Tindakan ini bersifat <strong>permanen dan tidak dapat dibatalkan</strong>. Akun mentor beserta seluruh kredensial login dan penugasan kelasnya akan dihapus secara total dari sistem database.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-xs">
                        <i class="bi bi-trash-fill me-1"></i>Hapus Permanen Mentor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI: Hapus Guru dari Daftar Aktif --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalKonfirmasiHapusGuruDaftar" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: var(--skillo-dark);">
                <h6 class="modal-title fw-bold font-monsserat mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2" style="color: var(--skillo-amber);"></i>Konfirmasi Hapus dari Daftar
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formHapusGuruDaftar" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="tipe_hapus" value="daftar">
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: #fff9e8; border: 1.5px solid #fef3c7;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                             style="width: 48px; height: 48px; background: #fef3c7; color: #d97706;">
                            <i class="bi bi-person-dash-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-secondary small fw-bold d-block text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Hapus dari Daftar Aktif</span>
                            <h6 class="fw-black font-monsserat mb-0 text-dark" id="hapusDaftarGuruNama">Nama Mentor</h6>
                            <small class="text-muted" id="hapusDaftarGuruInfo">mentor@skillo.id</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-info-circle-fill text-secondary fs-5 shrink-0"></i>
                            <div class="small text-secondary">
                                Guru hanya akan dilepas dari pengampu kelas dan daftar pengajar aktif. <strong>Data akun dan riwayat catatan mengajar tetap tersimpan aman di database</strong> serta dapat dipulihkan kapan saja melalui arsip.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-skillo-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-skillo-amber rounded-pill px-4 fw-bold shadow-xs">
                        <i class="bi bi-person-dash-fill me-1"></i>Hapus dari Daftar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Tanggal dinamis format Indonesia
    document.getElementById('currentDate').textContent =
        new Date().toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

    // Fungsi Pengalih Opsi Vertikal (Input Murid Baru vs Pilih Murid Terdaftar)
    function pilihMetodeMurid(metode) {
        const radioBaru   = document.getElementById('radioTipeBaru');
        const radioPilih  = document.getElementById('radioTipePilih');
        const boxBaru     = document.getElementById('boxMetodeBaru');
        const boxPilih    = document.getElementById('boxMetodePilih');
        const secBaru     = document.getElementById('sectionFormBaru');
        const secPilih    = document.getElementById('sectionFormPilih');
        const inputNama   = document.getElementById('inputNamaMuridBaru');
        const selectMurid = document.getElementById('selectSiswaTerdaftar');

        if (metode === 'baru') {
            radioBaru.checked = true;
            boxBaru.style.border = '2px solid var(--skillo-primary)';
            boxBaru.style.background = '#fff4f2';
            boxPilih.style.border = '1.5px solid #e2e8f0';
            boxPilih.style.background = '#ffffff';

            secBaru.classList.remove('d-none');
            secPilih.classList.add('d-none');

            inputNama.setAttribute('required', 'required');
            if (selectMurid) selectMurid.removeAttribute('required');
        } else {
            radioPilih.checked = true;
            boxPilih.style.border = '2px solid #059669';
            boxPilih.style.background = '#f0fdf4';
            boxBaru.style.border = '1.5px solid #e2e8f0';
            boxBaru.style.background = '#ffffff';

            secBaru.classList.add('d-none');
            secPilih.classList.remove('d-none');

            inputNama.removeAttribute('required');
            if (selectMurid) selectMurid.setAttribute('required', 'required');
        }
    }

    // Isi modal Tambah Murid saat dibuka
    document.getElementById('modalInputSiswa').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        const form = document.getElementById('formTambahMurid');
        if (form) form.reset();

        const kelasSelect = document.getElementById('inputSiswaKelasId');
        if (btn && btn.dataset && btn.dataset.kelasId && kelasSelect) {
            kelasSelect.value = btn.dataset.kelasId;
        } else if (kelasSelect) {
            kelasSelect.selectedIndex = 0;
        }
        pilihMetodeMurid('baru');
    });

    // Isi modal Edit Kelas
    document.getElementById('modalEditKelas').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        const form = document.getElementById('formEditKelas');
        form.action = '/admin/kelas/' + btn.dataset.kelasId;
        document.getElementById('editNamaKelas').value = btn.dataset.kelasNama;
        document.getElementById('editGuruId').value   = btn.dataset.guruId;
        document.getElementById('editDeskripsi').value = btn.dataset.deskripsi;
    });

    // Modal Konfirmasi Hapus Kelas
    const modalHapusKelas = document.getElementById('modalKonfirmasiHapusKelas');
    if (modalHapusKelas) {
        modalHapusKelas.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (btn) {
                document.getElementById('formHapusKelas').action = btn.dataset.action;
                document.getElementById('hapusKelasNama').textContent = btn.dataset.kelasNama;
                const count = btn.dataset.muridCount || 0;
                document.getElementById('hapusKelasInfo').textContent =
                    'Kelas ini memiliki ' + count + ' murid terdaftar. Seluruh data sesi dan absensi akan ikut terhapus.';
            }
        });
    }

    // Modal Konfirmasi Keluarkan Murid dari Kelas
    const modalKeluarkanMurid = document.getElementById('modalKonfirmasiKeluarkanMurid');
    if (modalKeluarkanMurid) {
        modalKeluarkanMurid.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (btn) {
                document.getElementById('formKeluarkanMurid').action = btn.dataset.action;
                document.getElementById('keluarkanMuridNama').textContent = btn.dataset.muridNama;
                document.getElementById('keluarkanMuridInfo').textContent =
                    'NIM: ' + btn.dataset.muridNim + ' • Program: ' + btn.dataset.kelasNama;
            }
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    backdrops[backdrops.length - 1].style.zIndex = '1065';
                }
            }, 10);
        });

        modalKeluarkanMurid.addEventListener('hidden.bs.modal', function() {
            if (document.querySelectorAll('.modal.show').length > 0) {
                document.body.classList.add('modal-open');
            }
        });
    }

    // Modal Konfirmasi Hapus Data Murid Secara Permanen
    const modalHapusMurid = document.getElementById('modalKonfirmasiHapusMuridPermanen');
    if (modalHapusMurid) {
        modalHapusMurid.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (btn) {
                document.getElementById('formHapusMuridPermanen').action = btn.dataset.action;
                document.getElementById('hapusMuridNama').textContent = btn.dataset.muridNama;
                document.getElementById('hapusMuridInfo').textContent = 'NIM: ' + btn.dataset.muridNim;
            }
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    backdrops[backdrops.length - 1].style.zIndex = '1065';
                }
            }, 10);
        });

        modalHapusMurid.addEventListener('hidden.bs.modal', function() {
            if (document.querySelectorAll('.modal.show').length > 0) {
                document.body.classList.add('modal-open');
            }
        });
    }

    // Modal Konfirmasi Hapus Guru Secara Permanen
    const modalHapusGuruPermanen = document.getElementById('modalKonfirmasiHapusGuruPermanen');
    if (modalHapusGuruPermanen) {
        modalHapusGuruPermanen.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (btn) {
                document.getElementById('formHapusGuruPermanen').action = btn.dataset.action;
                document.getElementById('hapusGuruNama').textContent = btn.dataset.guruNama;
                document.getElementById('hapusGuruEmail').textContent = btn.dataset.guruEmail || '-';
            }
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    backdrops[backdrops.length - 1].style.zIndex = '1065';
                }
            }, 10);
        });

        modalHapusGuruPermanen.addEventListener('hidden.bs.modal', function() {
            if (document.querySelectorAll('.modal.show').length > 0) {
                document.body.classList.add('modal-open');
            }
        });
    }

    // Modal Konfirmasi Hapus Guru dari Daftar Aktif
    const modalHapusGuruDaftar = document.getElementById('modalKonfirmasiHapusGuruDaftar');
    if (modalHapusGuruDaftar) {
        modalHapusGuruDaftar.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (btn) {
                document.getElementById('formHapusGuruDaftar').action = btn.dataset.action;
                document.getElementById('hapusDaftarGuruNama').textContent = btn.dataset.guruNama;
                document.getElementById('hapusDaftarGuruInfo').textContent = btn.dataset.guruEmail || '-';
            }
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    backdrops[backdrops.length - 1].style.zIndex = '1065';
                }
            }, 10);
        });

        modalHapusGuruDaftar.addEventListener('hidden.bs.modal', function() {
            if (document.querySelectorAll('.modal.show').length > 0) {
                document.body.classList.add('modal-open');
            }
        });
    }
</script>
@endpush
