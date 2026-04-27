<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .hadir {
            background: #22c55e;
            color: #fff;
        }

        .terlambat {
            background: #eab308;
        }

        .absen {
            background: #ef4444;
            color: #fff;
        }
    </style>
</head>

<body>

    <h2>REKAP ABSENSI</h2>

    @if ($mode === 'hari')
        <p style="text-align:center;">Tanggal: {{ $date }}</p>

        <table>
            <tr>
                <th>Nama</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Status</th>
            </tr>

            @foreach ($users as $user)
                @php $row = $data[$user->id] ?? null; @endphp
                <tr>
                    <td>{{ $user->nama }}</td>
                    <td>{{ $row->check_in_time ?? '-' }}</td>
                    <td>{{ $row->check_out_time ?? '-' }}</td>
                    <td class="{{ $row->status ?? 'absen' }}">
                        {{ $row->status ?? 'absen' }}
                    </td>
                </tr>
            @endforeach
        </table>

    @endif


    @if ($mode === 'bulan')
        <p style="text-align:center;">Bulan: {{ $month }}</p>

        <table>
            <tr>
                <th>Nama</th>
                @for ($i = 1; $i <= $daysInMonth; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>

            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->nama }}</td>

                    @for ($i = 1; $i <= $daysInMonth; $i++)
                        @php
                            $d = $absensiMap[$user->id][$i] ?? null;
                            $status = $d->status ?? 'absen';
                        @endphp

                        <td class="{{ $status }}">
                            {{ $d ? substr($status, 0, 1) : '-' }}
                        </td>
                    @endfor
                </tr>
            @endforeach
        </table>

    @endif

</body>

</html>
