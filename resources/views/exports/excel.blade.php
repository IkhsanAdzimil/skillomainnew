<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <h3>REKAP ABSENSI SKILLO ACADEMY</h3>
    <p>Periode: {{ $tglMulai }} s/d {{ $tglAkhir }} | Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;font-family:Arial;font-size:11px;">
        <thead>
            <tr style="background:#4f46e5;color:#fff;">
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Siswa</th>
                <th>NIM</th>
                <th>Kelas</th>
                <th>Guru Pengajar</th>
                <th>Status Kehadiran</th>
                <th>Catatan / Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                <td><strong>{{ $row->siswa?->nama }}</strong></td>
                <td>{{ $row->siswa?->nim }}</td>
                <td>{{ $row->kelas?->nama_kelas }}</td>
                <td>{{ $row->guru?->name }}</td>
                <td style="font-weight:bold;text-transform:uppercase;">{{ $row->status }}</td>
                <td>{{ $row->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
