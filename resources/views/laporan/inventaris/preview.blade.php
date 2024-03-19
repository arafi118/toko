<!DOCTYPE html>
<html>
<style type="text/css">
    .style6 {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 16px;
    }

    .style9 {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    .style10 {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    .top {
        border-top: 1px solid #000000;
    }

    .bottom {
        border-bottom: 1px solid #000000;
    }

    .left {
        border-left: 1px solid #000000;
    }

    .right {
        border-right: 1px solid #000000;
    }

    .all {
        border: 1px solid #000000;
    }

    .style26 {
        font-family: Verdana, Arial, Helvetica, sans-serif
    }

    .style27 {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 11px;
        font-weight: bold;
    }

    .align-justify {
        text-align: justify;
    }

    .align-center {
        text-align: center;
    }

    .align-right {
        text-align: right;
    }

    .align-left {
        text-align: left;
    }
</style>

<body>
    @php
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $lg_bus = Session::get('business.logo');
        $logo = !empty($lg_bus) ? $lg_bus : 'logo.png';
        $path = Storage::url('business_logos/' . $logo);
    @endphp
    <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
        <tbody>
            <tr>
                <td height="20" colspan="2" class="bottom" width="5%">
                    <div class="style9 text-align-left"><img class="" alt="..." src="{{ url($path) }}"
                            style="float:left; margin-right:5px;" width="70"></div>
                </td>
                <td class="bottom">
                    <div class="style9" style="margin-bottom: -10px;">
                        <div style="margin-bottom: 2px;">
                            <b style="text-transform: uppercase;">
                                {{ $bl->name }}
                            </b>
                        </div>
                        {{ $bl->city }} {{ $bl->state }} {{ $bl->zip_code }}
                        {{ $bl->mobile ? 'Telp. ' . $bl->mobile : '' }}<br>
                    </div>
                    <div class="style9" style="float: right; margin-top: -15px;">Laporan Bulan:
                        {{ $bln != null ? strtoupper(array_search($bln, array_flip($bulan))) : '' }}
                        {{ $thn != null ? $thn : '' }}</div><br>
                    <div class="style9" style="float: right; margin-top: -20px">Kd.Doc. INVENTARIS Lembar-1</div>
                </td>
            </tr>
        </tbody>
    </table>

    <br>
    <div class="style6" style="text-align: center;">
        LAPORAN {{ strtoupper($jenis_laporan) }}<br>
        BULAN
        {{ $bln != null ? strtoupper(array_search($bln, array_flip($bulan))) : '' }}
        {{ $thn != null ? $thn : '' }}
    </div>
    <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
        <thead>
            <tr>
                <th width="2%" height="30" class="style9 all" rowspan="2">NO.</th>
                <th width="7%" class="style9 top bottom" rowspan="2">TANGGAL BELI</th>
                <th width="24%" class="style9 all" rowspan="2">NAMA BARANG</th>
                <th width="3%" class="style9 top bottom right" rowspan="2">ID</th>
                <th width="3%" class="style9 top bottom right" rowspan="2">KON<br>DISI</th>
                <th width="3%" class="style9 top bottom" rowspan="2">UNIT</th>
                <th width="7%" class="style9 all" rowspan="2">HARGA SATUAN</th>
                <th width="8%" class="style9 top bottom" rowspan="2">HARGA PEROLEHAN</th>
                <th width="4%" class="style9 all" rowspan="2">UMUR<br>EKO.</th>
                <th width="5%" class="style9 top bottom right" rowspan="2">SATUAN SUSUT<BR></th>
                <th class="style9 top bottom right" colspan="2">TAHUN INI</th>
                <th class="style9 top bottom right" colspan="2">S.D. TAHUN INI</th>
                <th width="10%" class="style9 top bottom right" rowspan="2">NILAI BUKU</th>
            </tr>

            <tr>
                <th width="2%" class="style9 bottom right">UMUR PAKAI</th>
                <th width="5%" class="style9 bottom right">BIAYA SUSUT</th>
                <th width="2%" class="style9 bottom right">UMUR PAKAI</th>
                <th width="7%" class="style9 bottom right">AKUM. SUSUT<BR></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jurnal as $data)
                <tr>
                    <td height="18" class="style9 left right bottom">
                        <div align="center">{{ $loop->iteration }}</div>
                    </td>
                    <td class="style9 bottom align-center">{{ $data['tgl_beli'] }}</td>
                    <td class="style9 left right bottom">
                        {{ $data['nama_barang'] }}
                        @if ($data['status'] != 'baik')
                            <span style="color: #df4759;">({{ $data['status'] . ' ' . $data['tgl_validasi'] }})</span>
                        @endif
                    </td>
                    <td class="style9 right bottom align-center">{{ $data['id'] }}</td>
                    <td class="style9 right bottom align-center" style="text-transform: capitalize;">
                        {{ $data['status'] }}
                    </td>
                    <td class="style9 bottom align-center">{{ $data['unit'] }}</td>
                    <td class="style9 left bottom right align-right">{{ number_format($data['harsat']) }}</td>
                    <td class="style9 bottom align-right">{{ number_format($data['nominal']) }}</td>
                    <td class="style9 left bottom right align-center">{{ $data['umur_ekonomis'] }}</td>
                    <td class="style9 bottom right align-right">{{ $data['satuan'] }}</td>
                    <td class="style9 bottom right align-center">{{ $data['umur_pakai'] }}</td>
                    <td class="style9 bottom right align-right">{{ $data['biaya'] }}</td>
                    <td class="style9 bottom right align-center">{{ $data['umurpakai'] }}</td>
                    <td class="style9 bottom right align-right">{{ $data['akum'] }}</td>
                    <td class="style9 bottom right align-right">{{ number_format($data['nilai']) }}
                    </td>
                </tr>
            @endforeach
            </br>

            <tr>
                <th height="18" class="style9 left right bottom align-left" colspan="5">
                    <div>Jumlah</div>
                </th>
                <th class="style9 bottom align-center">
                    {{ $jmltotal - $t_unit }}
                </th>
                <th class="style9 left bottom right align-right"></th>
                <th class="style9 bottom align-right">{{ number_format($jml_total - $hr) }} </th>
                <th class="style9 left bottom right align-center"></th>
                <th class="style9 bottom right align-right"></th>
                <th class="style9 bottom right align-center"></th>
                <th class="style9 bottom right align-right">{{ number_format($biaya) }}</th>
                <th class="style9 bottom right align-center"></th>
                <th class="style9 bottom right align-right">{{ number_format($akum) }}</th>
                <th class="style9 bottom right align-right">{{ number_format($jml_nilai) }}</th>
            </tr>
            <tr>
                <th height="18" class="style9 left right bottom align-left" colspan="5">
                    <div>Jumlah (Hapus, Jual, Hilang) s.d. Tahun {{ $thn - 1 }}</div>
                </th>
                <th class="style9 bottom align-center">
                    {{ $t_unit != 0 ? $t_unit : '' }}
                </th>
                <th class="style9 left bottom right align-right"></th>
                <th class="style9 bottom align-right">{{ number_format($hr) }}</th>
                <th class="style9 left bottom right align-center"></th>
                <th class="style9 bottom right align-right"></th>
                <th class="style9 bottom right align-center"></th>
                <th class="style9 bottom right align-right"></th>
                <th class="style9 bottom right align-center"></th>
                <th class="style9 bottom right align-right">{{ number_format($hr) }}</th>
                <th class="style9 bottom right align-right">0</th>
            </tr>
            <tr>
                <th height="18" class="style9 left right bottom" colspan="5">
                    <div align="center">JUMLAH</div>
                </th>
                <th class="style9 bottom align-center">
                    {{ $jmltotal }}
                </th>
                <th class="style9 left bottom right align-right"></th>
                <th class="style9 bottom align-right">{{ number_format($jml_total) }}</th>
                <th class="style9 left bottom right align-center"></th>
                <th class="style9 bottom right align-right"></th>
                <th class="style9 bottom right align-center"></th>
                <th class="style9 bottom right align-right"></th>
                <th class="style9 bottom right align-center"></th>
                <th class="style9 bottom right align-right">{{ number_format($akum) }}</th>
                <th class="style9 bottom right align-right">{{ number_format($jml_nilai) }}</th>
            </tr>
        </tbody>
    </table>
    <table width="96%" border="0" align="center" cellpadding="3" cellspacing="0">
        <tbody>
            <tr>
                <td class="style27 align-center" valign="top" width="31%">
                    <br>Diperiksa Oleh<br>

                    <table width="100%">
                        <tbody>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="style9" width="auto">
                                    @php
                                        $name = isset($pengawas)
                                            ? $pengawas['first_name'] . ' ' . $pengawas['last_name']
                                            : '';
                                        $name =
                                            !$name || $name == '' || $name == null || $name == ' '
                                                ? '...................................'
                                                : $name;
                                    @endphp
                                    <center>( {{ $name }} )</center>
                                </td>
                            </tr>
                            <tr>
                                <td class="style9" width="auto">
                                    <center>Pengawas</center>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                @php
                    $date = $jumlah_hari . ' ' . array_search(date('m'), array_flip($bulan)) . ' ' . date('Y');
                @endphp
                <td class="style27 align-center" valign="top" width="31%"><br>{{ $bl->city }},
                    {{ $date }}<br>Dilaporkan Oleh<br>
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>
                                    @if (isset($direktur))
                                        {{ $direktur->first_name . ' ' . $direktur->last_name }}
                                    @else
                                        ( ................................... )
                                    @endif
                                </td>
                                <td>
                                    @if (isset($keuangan))
                                        {{ $keuangan->first_name . ' ' . $keuangan->last_name }}
                                    @else
                                        ( ................................... )
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="style9" width="auto">
                                    @if (isset($direktur))
                                        <center>Direktur</center>
                                    @else
                                        ...................................
                                    @endif
                                </td>
                                <td class="style9" width="auto">
                                    @if (isset($keuangan))
                                        <center>Bag. Keuangan</center>
                                    @else
                                        ...................................
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
        </tbody>
    </table>
    <hr style="margin-top: 120px;">
    <div class="style9" style="float: right; margin-right: 5px;">Dicetak oleh :{{ $ttd }},
        {{ $bl->city }} ,
        {{ $date }} , {{ date('h:i:s') }}</div>
</body>
<title>LAPORAN INVENTARIS BULAN {{ $bln != null ? strtoupper(array_search($bln, array_flip($bulan))) : '' }}
    {{ $thn != null ? $thn : '' }}</title>

</html>
