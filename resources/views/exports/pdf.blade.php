<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1a1a1a; }
        h2 { color: #4f46e5; font-size: 14px; margin-bottom: 4px; }
        .subtitle { color: #6b7280; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        thead th { background: #4f46e5; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
        tbody tr:nth-child(even) { background: #f8faff; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; }
        .status-hadir  { color: #16a34a; font-weight: bold; text-transform: uppercase; }
        .status-sakit  { color: #d97706; font-weight: bold; text-transform: uppercase; }
        .status-izin   { color: #2563eb; font-weight: bold; text-transform: uppercase; }
        .status-alfa   { color: #dc2626; font-weight: bold; text-transform: uppercase; }
        .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; }
        .badge { background: #eef2ff; color: #4f46e5; padding: 2px 6px; border-radius: 4px; font-size: 9px; }
    </style>
</head>
<body>
    <h2>REKAP ABSENSI — SKILLO ACADEMY</h2>
    <div class="subtitle">Periode: {{ $tglMulai }} s/d {{ $tglAkhir }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</div>

    @if($rows->isEmpty())
        <p style="color:#6b7280;font-style:italic;">Tidak ada data absensi pada periode ini.</p>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th style="width:11%">Tanggal</th>
                <th style="width:20%">Nama Siswa</th>
                <th style="width:13%">NIM</th>
                <th style="width:18%">Kelas</th>
                <th style="width:12%">Guru</th>
                <th style="width:8%">Status</th>
                <th>Catatan/Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td><strong>{{ $row->siswa?->nama }}</strong></td>
                <td><span class="badge">{{ $row->siswa?->nim }}</span></td>
                <td>{{ $row->kelas?->nama_kelas }}</td>
                <td>{{ $row->guru?->name }}</td>
                <td class="status-{{ $row->status }}">{{ strtoupper($row->status) }}</td>
                <td style="color:#374151">{{ $row->notes ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Portal Staff — Skillo Academy &nbsp;|&nbsp; {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
