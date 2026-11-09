@extends('layouts.app')

@section('title', 'Jurnal Harian Guru')

@section('content')

{{-- Header Kelas --}}
@if($kelas)
<div class="mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="skillo-tag">
            <i class="bi bi-journal-bookmark-fill"></i> Jurnal Mengajar
        </span>
        <span class="text-secondary small fw-semibold" id="currentDate"></span>
    </div>
    <h2 class="fw-black mb-1 font-monsserat" style="font-size: 1.85rem; letter-spacing: -0.03em;">
        {{ $kelas->nama_kelas }}
    </h2>
    <p class="text-secondary small mb-0 fw-bold">
        {{ auth()->user()->name }}
    </p>
</div>
@else
<div class="card p-4 text-center my-4 border-warning" style="background: #fff9e8; border-radius: 20px;">
    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
         style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.15);">
        <i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>
    </div>
    <h5 class="fw-black font-monsserat text-dark">Belum Ada Penugasan Kelas</h5>
    <p class="text-secondary small mb-0">Anda belum ditugaskan ke kelas manapun. Silakan hubungi Administrator untuk mengatur jadwal kelas Anda.</p>
</div>
@endif

@if($kelas)
{{-- Form Absensi & Jurnal --}}
<div class="card">
    <div class="card-header">
        <h5 class="fw-black mb-0 font-monsserat" style="font-size: 1.15rem;">
            <i class="bi bi-card-checklist me-2" style="color: var(--skillo-primary);"></i>
            Presensi & Log Perkembangan Siswa
        </h5>
        <small class="text-muted">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</small>
    </div>
    <div class="card-body p-0">
        @if($kelas->siswa->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 text-secondary opacity-50"></i>
                <p class="mt-2 fw-semibold">Belum ada siswa di kelas ini.<br>Hubungi Administrator untuk mendaftarkan siswa secara massal.</p>
            </div>
        @else
        <form action="{{ route('guru.absensi.simpan') }}" method="POST">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            {{-- Input Topik / Materi Pembelajaran Hari Ini --}}
            <div class="p-3 border-bottom" style="background: #fff9e8;">
                <div class="row align-items-center g-2">
                    <label class="col-md-3 col-form-label fw-bold small text-dark">
                        <i class="bi bi-bookmark-star-fill text-warning me-1"></i>Materi / Topik Pembelajaran:
                    </label>
                    <div class="col-md-9">
                        <input type="text" name="materi" value="{{ $sesi?->materi ?? '' }}"
                               class="form-control form-control-sm bg-white rounded-pill px-3 fw-medium"
                               placeholder="Contoh: Pengenalan dasar syntax loop dan logic project game (opsional)">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light" style="border-bottom: 2px solid #f1f5f9;">
                        <tr class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            <th class="px-4 py-3" style="width:4%">No</th>
                            <th class="py-3" style="width:13%">NIM Siswa</th>
                            <th class="py-3" style="width:22%">Nama Siswa</th>
                            <th class="py-3" style="width:31%">Status Kehadiran</th>
                            <th class="py-3 pe-4" style="width:30%">Catatan Progres (Enter untuk Simpan)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelas->siswa as $i => $siswa)
                        @php
                            $absen  = $absensiHari[$siswa->id] ?? null;
                            $status = $absen?->status ?? 'hadir';
                            $notes  = $absen?->notes ?? '';
                        @endphp
                        <tr>
                            <td class="px-4 fw-bold text-muted">{{ $i + 1 }}</td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border font-monospace px-2.5 py-1 fw-bold">{{ $siswa->nim }}</span>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-bold font-monsserat text-dark" style="font-size: 0.95rem;">{{ $siswa->nama }}</span>
                                    @if(!empty($siswa->no_hp))
                                        <div class="mt-0.5">
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siswa->no_hp) }}" target="_blank"
                                               class="badge rounded-pill bg-light text-success border text-decoration-none" style="font-size: 0.7rem;" title="Hubungi via WhatsApp">
                                                <i class="bi bi-whatsapp me-1"></i>{{ $siswa->no_hp }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2 gap-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="absensi[{{ $siswa->id }}]"
                                               id="hadir_{{ $siswa->id }}" value="hadir"
                                               {{ $status === 'hadir' ? 'checked' : '' }} style="cursor: pointer;">
                                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="hadir_{{ $siswa->id }}" style="cursor: pointer; color: #000000 !important;">
                                            Hadir
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="absensi[{{ $siswa->id }}]"
                                               id="sakit_{{ $siswa->id }}" value="sakit"
                                               {{ $status === 'sakit' ? 'checked' : '' }} style="cursor: pointer;">
                                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="sakit_{{ $siswa->id }}" style="cursor: pointer; color: #000000 !important;">
                                            Sakit
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="absensi[{{ $siswa->id }}]"
                                               id="izin_{{ $siswa->id }}" value="izin"
                                               {{ $status === 'izin' ? 'checked' : '' }} style="cursor: pointer;">
                                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="izin_{{ $siswa->id }}" style="cursor: pointer; color: #000000 !important;">
                                            Izin
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio"
                                               name="absensi[{{ $siswa->id }}]"
                                               id="alfa_{{ $siswa->id }}" value="alfa"
                                               {{ $status === 'alfa' ? 'checked' : '' }} style="cursor: pointer;">
                                        <label class="form-check-label fw-bold text-dark small cursor-pointer" for="alfa_{{ $siswa->id }}" style="cursor: pointer; color: #000000 !important;">
                                            Alpa
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 pe-4">
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-sm rounded-pill px-3 bg-light note-input"
                                           data-siswa-id="{{ $siswa->id }}"
                                           name="notes[{{ $siswa->id }}]"
                                           value="{{ $notes }}"
                                           placeholder="Ketik catatan progres lalu tekan ENTER...">
                                    <span class="note-saved-badge text-success small position-absolute end-0 top-50 translate-middle-y me-3 d-none" style="pointer-events: none;">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top text-end py-3 px-4">
                <button type="submit" class="btn btn-skillo-primary w-100 w-sm-auto px-4 py-2">
                    <i class="bi bi-check2-all me-1"></i>Simpan Seluruh Jurnal & Absensi
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    const now = new Date();
    const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    const formatted = now.toLocaleDateString('id-ID', opts);
    const el1 = document.getElementById('currentDate');
    if (el1) el1.textContent = formatted;

    // Simpan Catatan Progres per murid hanya dengan ENTER (tanpa submit semua & tanpa reload)
    document.querySelectorAll('.note-input').forEach(function(input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Mencegah form utama ter-submit

                const siswaId = this.dataset.siswaId;
                const notesValue = this.value;
                const kelasId = document.querySelector('input[name="kelas_id"]').value;
                const tanggal = document.querySelector('input[name="tanggal"]').value;
                const statusRadio = document.querySelector(`input[name="absensi[${siswaId}]"]:checked`);
                const status = statusRadio ? statusRadio.value : 'hadir';
                const csrfToken = document.querySelector('input[name="_token"]').value;

                const inputElem = this;
                const badgeElem = this.parentElement.querySelector('.note-saved-badge');

                fetch("{{ route('guru.absensi.catatan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        kelas_id: kelasId,
                        siswa_id: siswaId,
                        tanggal: tanggal,
                        notes: notesValue,
                        status: status
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Tanda visual halus di kolom input murid tersebut
                        inputElem.classList.remove('bg-light');
                        inputElem.classList.add('border-success');
                        if (badgeElem) badgeElem.classList.remove('d-none');

                        setTimeout(() => {
                            inputElem.classList.remove('border-success');
                            inputElem.classList.add('bg-light');
                            if (badgeElem) badgeElem.classList.add('d-none');
                        }, 1200);
                    }
                })
                .catch(err => {
                    console.error('Gagal menyimpan catatan:', err);
                });
            }
        });
    });
</script>
@endpush
