<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan CSR</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Calibri, Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        td {
            padding: 2px 4px;
            font-weight: normal;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    @php
        // ==== Helper format angka gaya akuntansi (0 -> "-") ====
        $fmt0 = fn($v) => $v == 0 ? '-' : number_format($v, 0, ',', '.');
        $fmt2 = fn($v) => $v == 0 ? '-' : number_format($v, 2, ',', '.');

        // ==== Border shorthand ====
        $bTipis = '1px solid #000';
        $bTebal = '2px solid #000';

        // style dasar tiap sel data (border tipis semua sisi)
        $sData = "border:$bTipis; text-align:center;";
        $sDataKiri = "border:$bTipis; text-align:left;";
        $sLabel = "border:$bTipis; text-align:left;";
        $sHeader = "border:$bTebal; text-align:center; font-weight:bold;";
        $sHeaderSub = "border:$bTipis; border-top:none; text-align:center; font-weight:bold;";
        $sHeaderSubBlank = "border:$bTipis; border-top:none;";
        $sBorderBottomThick = "border-top:none; border-left:$bTebal; border-right:$bTebal; border-bottom:$bTebal; height:2px;";
        $sSpacerDalamBox = "border-top:none; border-bottom:none; border-left:$bTebal; border-right:$bTebal; height:8px;";
    @endphp

    <table>
        <tbody>
            <!-- ======================= BARIS JUDUL ======================= -->
            <tr>
                <td colspan="7" style="border:none; text-align:center; font-weight:bold; font-size:12px;">
                    REALISASI PELAKSANAAN PROGRAM BINA LINGKUNGAN</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="18" style="border:none; text-align:left; font-weight:bold; font-size:12px;">
                    REALISASI PELAKSANAAN PROGRAM BINA LINGKUNGAN</td>
            </tr>
            <tr>
                <td colspan="7" style="border:none; text-align:center; font-weight:bold; font-size:12px;">
                    {{ $namaCabang }}</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="18" style="border:none; text-align:left; font-weight:bold; font-size:12px;">
                    {{ $namaCabang }}</td>
            </tr>
            <tr>
                <td colspan="7" style="border:none; text-align:center; font-weight:bold; font-size:12px;">
                    BULAN {{ $bulanPilihan }} {{ $tahunPilihan }}</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="17" style="border:none; text-align:left; font-weight:bold; font-size:12px;">
                    TAHUN {{ $tahunPilihan }}</td>
                <td style="border:none; text-align:right; font-size:10px;">Lampiran&nbsp;&nbsp;I&nbsp;&nbsp;B&nbsp;L
                </td>
            </tr>

            <!-- Spacer -->
            <tr>
                <td colspan="27" style="border:none; height:14px;">&nbsp;</td>
            </tr>

            <!-- ======================= HEADER TABEL (2 baris) ======================= -->
            <tr>
                <!-- Tabel kiri -->
                <td rowspan="2" style="{{ $sHeader }}">NO</td>
                <td colspan="3" rowspan="2" style="{{ $sHeader }}">URAIAN</td>
                <td style="{{ $sHeader }}">JUMLAH</td>
                <td style="{{ $sHeader }}">REALISASI</td>
                <td style="{{ $sHeader }}">JUMLAH</td>
                <!-- spacer -->
                <td colspan="2" style="border:none;"></td>
                <!-- Tabel kanan -->
                <td rowspan="2" style="{{ $sHeader }}">NO</td>
                <td colspan="3" rowspan="2" style="{{ $sHeader }}">URAIAN</td>
                <td rowspan="2" style="{{ $sHeader }}">KODE</td>
                @foreach ($listBulan as $bln)
                    <td style="{{ $sHeader }}">{{ strtoupper(substr($bln, 0, 3)) }}</td>
                @endforeach
                <td style="{{ $sHeader }}">JUMLAH</td>
            </tr>
            <tr>
                <td style="{{ $sHeaderSub }}">S/D BULAN LALU</td>
                <td style="{{ $sHeaderSub }}">{{ $bulanPilihan }}</td>
                <td style="{{ $sHeaderSub }}">S/D BULAN INI</td>
                <td colspan="2" style="border:none;"></td>
                @foreach ($listBulan as $bln)
                    <td style="{{ $sHeaderSubBlank }}"></td>
                @endforeach
                <td style="{{ $sHeaderSub }}">S/D BULAN INI</td>
            </tr>
            <!-- ======================= A. PENGEMBALIAN / DANA TERSEDIA ======================= -->
            <tr>
                <td style="{{ $sData }} font-weight:bold;">A</td>
                <td colspan="6" style="{{ $sLabel }} font-weight:bold;">PENGEMBALIAN</td>
                <td colspan="2" style="border:none;"></td>
                <td style="{{ $sData }} font-weight:bold;">A</td>
                <td colspan="{{ 3 + 1 + count($listBulan) + 1 }}" style="{{ $sLabel }} font-weight:bold;">DANA
                    TERSEDIA</td>
            </tr>
            <tr>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }}">1</td>
                <td style="{{ $sDataKiri }}">Pengembalian Persekot</td>
                <td style="{{ $sData }}">-</td>
                <td style="{{ $sData }}">-</td>
                <td style="{{ $sData }}">-</td>
                <td colspan="2" style="border:none;"></td>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }}">2</td>
                <td colspan="2" style="{{ $sDataKiri }}">Pengembalian Persekot</td>
                <td style="{{ $sData }}">11.382</td>
                @foreach ($listBulan as $bln)
                    <td style="{{ $sData }}">-</td>
                @endforeach
                <td style="{{ $sData }}">-</td>
            </tr>
            <tr>
                <td colspan="4" style="{{ $sLabel }}"></td>
                <td style="{{ $sData }}">-</td>
                <td style="{{ $sData }}">-</td>
                <td style="{{ $sData }}">-</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="4" style="{{ $sLabel }}">Jumlah Penerimaan Deposito &amp; Persekot (II)</td>
                <td style="{{ $sData }}"></td>
                @foreach ($listBulan as $bln)
                    <td style="{{ $sData }}">-</td>
                @endforeach
                <td style="{{ $sData }}">-</td>
            </tr>

            <!-- Spacer -->
            <tr>
                <td colspan="7" style="{{ $sSpacerDalamBox }}">&nbsp;</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="18" style="{{ $sSpacerDalamBox }}">&nbsp;</td>
            </tr>

            <!-- ======================= B. PENGGUNAAN DANA ======================= -->
            <tr>
                <td style="{{ $sData }} font-weight:bold;">B</td>
                <td colspan="6" style="{{ $sLabel }} font-weight:bold;">PENGGUNAAN DANA</td>
                <td colspan="2" style="border:none;"></td>
                <td style="{{ $sData }} font-weight:bold;">B</td>
                <td colspan="{{ 3 + 1 + count($listBulan) + 1 }}" style="{{ $sLabel }} font-weight:bold;">
                    PENGGUNAAN DANA</td>
            </tr>

            <!-- I. PENYALURAN DANA -->
            <tr>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }} font-weight:bold;">I</td>
                <td colspan="5" style="{{ $sLabel }} font-weight:bold;">PENYALURAN DANA</td>
                <td colspan="2" style="border:none;"></td>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }} font-weight:bold;">I</td>
                <td colspan="{{ 2 + 1 + count($listBulan) + 1 }}" style="{{ $sLabel }} font-weight:bold;">
                    PENYALURAN DANA</td>
            </tr>

            <!-- Baris per pilar (loop dari $ringkasanPilar) -->
            @php $no = 1; @endphp
            @foreach ($ringkasanPilar as $namaPilar => $val)
                <tr>
                    <td style="{{ $sData }}"></td>
                    <td style="{{ $sData }}"></td>
                    <td style="{{ $sData }}">{{ $no }}</td>
                    <td style="{{ $sDataKiri }}">Pilar Pembangunan {{ $namaPilar }}</td>
                    <td style="{{ $sData }}">{{ $fmt2($val['smp_bulan_lalu']) }}</td>
                    <td style="{{ $sData }}">{{ $fmt2($val['bulan_ini']) }}</td>
                    <td style="{{ $sData }}">{{ $fmt2($val['smp_bulan_ini']) }}</td>
                    <td colspan="2" style="border:none;"></td>
                    <td style="{{ $sData }}"></td>
                    <td style="{{ $sData }}">{{ $no }}</td>
                    <td colspan="2" style="{{ $sDataKiri }}">Pilar Pembangunan {{ $namaPilar }}</td>
                    <td style="{{ $sData }}">{{ $val['kode'] }}</td>
                    @foreach ($listBulan as $bln)
                        <td style="{{ $sData }}">{{ $fmt0($tahunanPilar[$namaPilar][$bln]) }}</td>
                    @endforeach
                    <td style="{{ $sData }}">{{ $fmt2($totalPerPilarSetahun[$namaPilar]) }}</td>
                </tr>
                @php $no++; @endphp
            @endforeach
            <!-- Jumlah Penyaluran Dana (I) -->
            <tr>
                <td colspan="3" style="{{ $sLabel }}"></td>
                <td style="{{ $sLabel }} font-weight:bold;">Jumlah Penyaluran Dana (I)</td>
                <td style="{{ $sData }}">{{ $fmt2($totalPenyaluranLalu) }}</td>
                <td style="{{ $sData }}">{{ $fmt2($totalPenyaluranIni) }}</td>
                <td style="{{ $sData }}">{{ $fmt2($totalPenyaluranSmp) }}</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="3" style="{{ $sLabel }}"></td>
                <td colspan="2" style="{{ $sLabel }} font-weight:bold;">Jumlah Penyaluran Dana (I)</td>
                @foreach ($listBulan as $bln)
                    <td style="{{ $sData }}">{{ $fmt0($totalPerBulan[$bln]) }}</td>
                @endforeach
                <td style="{{ $sData }}">{{ $fmt2($totalSetahun) }}</td>
            </tr>

            <!-- Spacer -->
            <tr>
                <td colspan="7" style="{{ $sSpacerDalamBox }}">&nbsp;</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="18" style="{{ $sSpacerDalamBox }}">&nbsp;</td>
            </tr>

            <!-- II. LAIN-LAIN -->
            <tr>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }} font-weight:bold;">II</td>
                <td colspan="5" style="{{ $sLabel }} font-weight:bold;">LAIN-LAIN</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="{{ 1 + 3 + 1 + count($listBulan) + 1 }}" style="border:none;"></td>
            </tr>
            <tr>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }}">1</td>
                <td style="{{ $sDataKiri }}">Pengambilan Persekot</td>
                <td style="{{ $sData }}">-</td>
                <td style="{{ $sData }}">-</td>
                <td style="{{ $sData }}">-</td>
                <td colspan="2" style="border:none;"></td>
                <td style="{{ $sData }}"></td>
                <td style="{{ $sData }}">2</td>
                <td colspan="2" style="{{ $sDataKiri }}">Pengambilan Persekot</td>
                <td style="{{ $sData }}">11.382</td>
                @foreach ($listBulan as $bln)
                    <td style="{{ $sData }}">-</td>
                @endforeach
                <td style="{{ $sData }}">-</td>
            </tr>

            <!-- TOTAL PENGGUNAAN DANA (B = I + II) -->
            <tr>
                <td colspan="3" style="{{ $sLabel }}"></td>
                <td style="{{ $sLabel }} font-weight:bold;">TOTAL PENGGUNAAN DANA ( B = I + II )</td>
                <td style="{{ $sData }}">-</td>
                <td style="{{ $sData }}">{{ $fmt2($totalPenyaluranIni) }}</td>
                <td style="{{ $sData }}">{{ $fmt2($totalPenyaluranSmp) }}</td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="3" style="{{ $sLabel }}"></td>
                <td colspan="2" style="{{ $sLabel }} font-weight:bold;">TOTAL PENGGUNAAN DANA ( B = I + II
                    )</td>
                @foreach ($listBulan as $bln)
                    <td style="{{ $sData }}">{{ $fmt0($totalPerBulan[$bln]) }}</td>
                @endforeach
                <td style="{{ $sData }}">{{ $fmt2($totalSetahun) }}</td>
            </tr>

            <!-- Border bawah tebal penutup tabel -->
            <tr>
                <td colspan="7" style="{{ $sBorderBottomThick }}"></td>
                <td colspan="2" style="border:none;"></td>
                <td colspan="18" style="{{ $sBorderBottomThick }}"></td>
            </tr>
            <!-- Baris SALDO AKHIR (tabel kanan saja) -->
            <tr>
                <td colspan="9" style="border:none; height:16px;"></td>
                <td colspan="4" style="border:none; font-weight:bold;">SALDO AKHIR MENURUT R/C + KAS</td>
                <td colspan="14" style="border:none;"></td>
            </tr>

            <!-- Spacer -->
            <tr>
                <td colspan="27" style="border:none; height:14px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="27" style="border:none; height:14px;">&nbsp;</td>
            </tr>

            <!-- Baris Tanda Tangan 1 -->
            <tr>
                <td colspan="2" style="border:none;"></td>
                <td colspan="3" style="border:none; text-align:center;">Mengetahui</td>
                <td colspan="2" style="border:none; text-align:center;">Pontianak, {{ $tanggalTtd }}</td>
                <td colspan="20" style="border:none;"></td>
            </tr>

            <!-- Spacer -->
            <tr>
                <td colspan="27" style="border:none; height:14px;">&nbsp;</td>
            </tr>

            <!-- Baris Tanda Tangan 2 -->
            <tr>
                <td colspan="2" style="border:none;"></td>
                <td colspan="3" style="border:none; text-align:center;">GENERAL MANAGER</td>
                <td colspan="2" style="border:none; text-align:center;">{{ $jabatanDeptHead }}</td>
                <td colspan="20" style="border:none;"></td>
            </tr>

            <!-- Spacer (3 baris kosong utk tanda tangan) -->
            <tr>
                <td colspan="27" style="border:none; height:14px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="27" style="border:none; height:14px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="27" style="border:none; height:14px;">&nbsp;</td>
            </tr>

            <!-- Baris Nama Tanda Tangan -->
            <tr>
                <td colspan="2" style="border:none;"></td>
                <td colspan="3" style="border:none; text-align:center; text-decoration:underline;">
                    {{ $namaGM }}</td>
                <td colspan="2" style="border:none; text-align:center; text-decoration:underline;">
                    {{ $namaDeptHead }}</td>
                <td colspan="20" style="border:none;"></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
