<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Perpustakaan</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 11px;
            margin-bottom: 80px;
        }

        .kop {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .kop-text h2 {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .kop-text h3 {
            font-size: 14px;
            margin-bottom: 3px;
        }

        .kop-text p {
            font-size: 10px;
            margin: 1px 0;
        }

        .content {
            margin-top: 10px;
        }

        .header {
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 14px;
            text-align: center;
            margin-bottom: 5px;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        table {
            margin: 0;
            border-collapse: collapse;
            width: 100%;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        .footer {
            position: fixed;
            bottom: 40px;
            left: 0;
            width: 100%;
            padding: 10px 0;
            border-top: 1px solid #000;
            background-color: white;
        }

        .page-info {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            padding: 0 20px;
            margin: 5px 0;
        }

        .page-info-left {
            text-align: left;
        }

        .page-info-right {
            text-align: right;
        }

        .signature-container {
            position: absolute;
            bottom: 100px;
            right: 0;
            width: 45%;
            text-align: center;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .signature-name {
            margin-top: 40px;
            font-weight: bold;
            text-decoration: underline;
            font-size: 10px;
        }

        .signature-nip {
            font-size: 9px;
            margin-top: 3px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .content-wrapper {
            margin: 0;
            padding: 0;
            position: relative;
            height: auto;
            min-height: 0;
        }

        @media print {
            .content-wrapper {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    @php
        $perPage = 20;
        $chunks = $transaksi->chunk($perPage);
        $totalPages = $chunks->count();
    @endphp

    @foreach($chunks as $pageNumber => $chunk)
        <div class="content-wrapper">
            <div class="kop">
                <div class="kop-text">
                    <h2>PERPUSTAKAAN SMK TELEKOMUNIKASI TELSEDAI BEKASI</h2>
                    <h3>KABUPATEN BEKASI</h3>
                    <p>Desa, Mekarsari, Kec. Tambun Sel., Kabupaten Bekasi, Jawa Barat 17510</p>
                    <p>Telp: 0813-2525-0554 | Email: perpustakaan@smkntelekomunikasi.sch.id</p>
                </div>
            </div>

            <div class="content">
                <div class="header">
                    <h1>LAPORAN TRANSAKSI PEMINJAMAN BUKU</h1>
                    <p style="text-align: center;">Periode: {{ now()->format('d F Y') }}</p>
                    <p style="text-align: center;">Total Transaksi: {{ $transaksi->count() }} peminjaman</p>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>NIS</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Tanggal Pengembalian</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chunk as $index => $item)
                            <tr>
                                <td>{{ ($pageNumber * $perPage) + $index + 1 }}</td>
                                <td>{{ $item['nama_peminjam'] }}</td>
                                <td>{{ $item['nis'] }}</td>
                                <td>{{ $item['judul_buku'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['tanggal_pinjam'])->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['tanggal_kembali'])->format('d/m/Y') }}</td>
                                <td>
                                    @if($item['tanggal_pengembalian'])
                                        {{ \Carbon\Carbon::parse($item['tanggal_pengembalian'])->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ ucfirst($item['status']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($loop->last)
                <div class="signature-container">
                    <p>Bekasi, {{ now()->format('d F Y') }}</p>
                    <p class="signature-title">Kepala Perpustakaan</p>
                    <p class="signature-name">Guruh Wijarnako</p>
                    <p class="signature-nip">NIP. 19640815 198903 1 017</p>
                </div>
            @endif

            <div class="footer">
                <div class="page-info">
                    <div class="page-info-left">
                        <p>Petugas Perpustakaan</p>
                    </div>
                    <div class="page-info-right">
                        Halaman {{ $pageNumber + 1 }} dari {{ $totalPages }}
                    </div>
                </div>
            </div>
        </div>

        @unless($loop->last)
            <div style="height: 0px; page-break-after: always;"></div>
        @endunless
    @endforeach
</body>

</html>