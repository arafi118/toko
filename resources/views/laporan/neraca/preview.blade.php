<?php
use App\JenisBuku;
use App\NeracaAwalBulan;
$tgl_pakai = Session::get('business.start_date');
$thn_pakai = substr($tgl_pakai, 0, 4);
$lg_bus = Session::get('business.logo');
$logo = !empty($lg_bus) ? $lg_bus : 'logo.png';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Laporan Neraca</title>
    <style type="text/css">
        body {
            font-size: 12px;
        }
    </style>
</head>

<body>
    @php
        $path = Storage::url('business_logos/' . $logo);
    @endphp
    <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0">
        <tbody>
            <tr>
                <td height="20" colspan="2" class="bottom" width="5%">
                    <div class="style9 text-align-left"><img class="" alt="..." src="{{ url($path) }}"
                            style="float:left; width:70px; margin-right:5px;"></div>
                </td>
                <td class="bottom">
                    <div class="style9">
                        <h3><b>{{ $bl->name }}</b></h3>{{ $bl->city }},{{ $bl->state }},{{ $bl->zip_code }}
                    </div>

                </td>
                <td height="20" colspan="2" class="bottom">
                    <div align="right" class="style9"></div>
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    <table width="96%" border="1" style="border-collapse: collapse;" align="center" cellpadding="3"
        cellspacing="0" class="style9">
        <tbody>
            <tr>
                <?php $bulan = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember']; ?>
                <td height="20" colspan="2" class="style6 bottom align-center"
                    style="text-align: center; vertical-align: middle;">
                    <br>
                    <h3>N E R A C A <br>
                        {{ Request::get('tgl') != null ? Request::get('tgl') : '' }}
                        {{ Request::get('bln') != null ? strtoupper(array_search(Request::get('bln'), array_flip($bulan))) : '' }}
                        {{ Request::get('thn') != null ? Request::get('thn') : '' }}
                    </h3>
                </td>
            </tr>
            <tr>
                <th height="30" class="left top bottom">AKTIVA</th>
                <th class="all">PASSIVA</th>
            </tr>
            <tr>
                <td style="vertical-align: top;" class="left top bottom" width="50%">
                    <table width="100%" cellpadding="0" cellspacing="0" class="style9">
                        <tbody>
                            <?php $ttl_aktiva = 0; ?>
                            @foreach ($rekening_aktiva as $r)
                                <?php if ($r->idrekening != $jr) {
                                    if ($jr != 'all') {
                                        continue;
                                    }
                                } ?>
                                <tr>
                                    <td><strong>{{ $r->nama_rr }}</strong><br>
                                        @foreach ($r->getRekening($r->idrekening) as $ch)
                                            <?php
                                            $jenis_buku = new JenisBuku();
                                            $thn = Request::get('thn');
                                            $bln = Request::get('bln');
                                            
                                            $dt_aktiva = NeracaAwalBulan::where('business_id', $business_id)
                                                ->where('tanggal', "$thn-$bln-01")
                                                ->where('kd_jb', $ch->kd_jb);
                                            if ($dt_aktiva->count() <= 0) {
                                                $saldo_debit = $jenis_buku->getSaldoAwal($ch->kd_jb, 'debit', $thn);
                                                $saldo_kredit = $jenis_buku->getSaldoAwal($ch->kd_jb, 'kredit', $thn);
                                                $awal_debit = $saldo_debit;
                                                $awal_kredit = $saldo_kredit;
                                            
                                                $saldo_awal_tahun = $awal_debit->ttltahunlalu - $awal_kredit->ttltahunlalu;
                                                $saldo_awal_pakai = $awal_debit->ttlawalpakai - $awal_kredit->ttlawalpakai;
                                                $aktiva_bln_lalu = $r->getValueNeracaAwalBulan('aktiva', $ch->kd_jb, Request::get('tgl'), Request::get('bln'), Request::get('thn'), 'sd_bulan_lalu');
                                            
                                                $aktiva_bulan_lalu = $saldo_awal_tahun + $aktiva_bln_lalu;
                                            } else {
                                                $nc = $dt_aktiva->first();
                                                $aktiva_bulan_lalu = $nc->total;
                                                $saldo_awal_pakai = $nc->awal_pakai;
                                            }
                                            
                                            $aktiva = $r->getValueNeraca('aktiva', $ch->kd_jb, Request::get('tgl'), Request::get('bln'), Request::get('thn'));
                                            
                                            if ($thn_pakai == $thn) {
                                                $faktiva = $aktiva_bulan_lalu + $saldo_awal_pakai + $aktiva;
                                            } else {
                                                $faktiva = $aktiva_bulan_lalu + $aktiva;
                                            }
                                            ?>
                                            <table width="100%" class="style9">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">{{ $ch->kd_jb }}.{{ $ch->nama_jb }}</td>
                                                        <td width="30%" class="align-right">
                                                            {{ number_format($faktiva, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php $ttl_aktiva += $faktiva; ?>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
                <td class="all" valign="top" width="50%">
                    <table width="100%" cellpadding="0" cellspacing="0" class="style9">
                        <tbody>
                            <?php $ttl_passiva = 0; ?>
                            @foreach ($rekening_pasiva as $r)
                                <?php if ($r->idrekening != $jr) {
                                    if ($jr != 'all') {
                                        continue;
                                    }
                                } ?>
                                <tr>
                                    <td><strong>{{ $r->nama_rr }}</strong><br>
                                        @foreach ($r->getRekening($r->idrekening) as $ch)
                                            <?php
                                            $jenis_buku = new JenisBuku();
                                            $thn = Request::get('thn');
                                            $bln = Request::get('bln');
                                            
                                            $dt_passiva = NeracaAwalBulan::where('business_id', $business_id)
                                                ->where('tanggal', "$thn-$bln-01")
                                                ->where('kd_jb', $ch->kd_jb);
                                            if ($dt_passiva->count() <= 0) {
                                                $saldo_debit = $jenis_buku->getSaldoAwal($ch->kd_jb, 'debit', $thn);
                                                $saldo_kredit = $jenis_buku->getSaldoAwal($ch->kd_jb, 'kredit', $thn);
                                                $awal_debit = $saldo_debit;
                                                $awal_kredit = $saldo_kredit;
                                            
                                                $saldo_awal_tahun = $awal_kredit->ttltahunlalu - $awal_debit->ttltahunlalu;
                                                $saldo_awal_pakai = $awal_kredit->ttlawalpakai - $awal_debit->ttlawalpakai;
                                                $passiva_bln_lalu = $r->getValueNeracaAwalBulan('passiva', $ch->kd_jb, Request::get('tgl'), Request::get('bln'), Request::get('thn'), 'sd_bulan_lalu');
                                            
                                                $passiva_bulan_lalu = $saldo_awal_tahun + $passiva_bln_lalu;
                                            } else {
                                                $nc = $dt_passiva->first();
                                                $passiva_bulan_lalu = $nc->total;
                                                $saldo_awal_pakai = $nc->awal_pakai;
                                            }
                                            
                                            if ($business_id == '5' && ($ch->kd_jb == '311' || $ch->kd_jb == '312' || $ch->kd_jb == '313' || $ch->kd_jb == '314')) {
                                                $passiva = 0;
                                            } else {
                                                $passiva = $r->getValueNeraca('passiva', $ch->kd_jb, Request::get('tgl'), Request::get('bln'), Request::get('thn'));
                                            }
                                            
                                            if ($thn_pakai == $thn) {
                                                $fpassiva = $passiva_bulan_lalu + $saldo_awal_pakai + $passiva;
                                            } else {
                                                $fpassiva = $passiva_bulan_lalu + $passiva;
                                            }
                                            ?>
                                            <table width="100%" class="style9">
                                                <tbody>
                                                    <tr>
                                                        <td width="70%">{{ $ch->kd_jb }}.{{ $ch->nama_jb }}
                                                        </td>
                                                        <td width="30%" class="align-right">
                                                            {{ number_format($fpassiva, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php $ttl_passiva += $fpassiva; ?>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><strong>Laba / Rugi </strong><br></td>
                                <td>{{ number_format($laba_rugi, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <th height="30" class="left top bottom" style="text-align: right;"><span
                        class="pull-right">{{ number_format($ttl_aktiva, 2) }}</span></th>
                <th class="all" style="text-align: right;"><span
                        class="pull-right">{{ number_format($ttl_passiva + $laba_rugi, 2) }}</span></th>
            </tr>
            <tr>
                <td class="top bottom">&nbsp;</td>
                <th class="top">&nbsp;</th>
            </tr>
            <!-- <tr><td class="left top  right">Modal Awal SPP/UEP<span class="pull-right"></span></td><td>&nbsp;</td></tr>
  <tr><td class="left top  right">Aset Produktif<span class="pull-right"></span></td><td>&nbsp;</td></tr>
  <tr><td class="left top  right">Perkembangan Dana<span class="pull-right"></span></td><td>&nbsp;</td></tr>
  <tr><td class="left top bottom right">Prosentase Perkembangan<span class="pull-right"></span></td><td>&nbsp;</td></tr> -->
        </tbody>
    </table>
    <table width="96%" border="0" align="center" cellpadding="3" cellspacing="0">
        <tbody>
            <tr>
                <td class="style27 align-center" valign="top" width="31%">
                    <br>Diperiksa Oleh:<br>BADAN PENGAWAS<br>

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
                                <td class="style9" width="auto">
                                    <center>Manajer</center>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </td>

                <td class="style27 align-center" valign="top" width="31%"><br>Dilaporkan
                    Oleh:<br>{{ $bl->name }}<br>
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
                                <td class="style9" width="auto">
                                    <center>Bag. Administrasi</center>
                                </td>
                                <td class="style9" width="auto">
                                    <center>Keuangan</center>
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
</body>

</html>
