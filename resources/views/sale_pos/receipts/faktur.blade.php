@inject('request', 'Illuminate\Http\Request')
<div class="row">
    <div class="col-xs-12 text-center">
        <table class="" cellpadiing="0" style="font-size:15px; width: 100%;text-align:left;">
            <tr>
                @php
                    // dd($receipt_details->logo);
                    $logo = $receipt_details->logo;
                    $path = Storage::url('invoice_logos/' . $logo);
                    // dd($path);
                @endphp
                {{-- <h2 class="text-center"><img style="width: 10%;" src="{{url($path)}}"></h2> --}}
                @if ($logo == 'no_image')
                    <td rowspan="5" style="padding-right:5px; width: 100px; "></td>
                @else
                    <td rowspan="5" style="padding-right:5px; width: 100px; ">
                        <img style="width: 100%;" src="{{ url($path) }}">
                    </td>
                @endif
                <td style="padding-top:0px; font-size:18px; font-weight: bold ">{!! $receipt_details->display_name !!}</td>
                <td style="padding-top:0px; align-content: right; width:30%;">{{ $receipt_details->kota }},
                    {{ $receipt_details->tgl_trans }}</td>
            </tr>
            <tr>
                <td style="">{{ $receipt_details->address }}</td>
                <td style="align-content: right; width:30%;">Kepada :</td>
            </tr>
            <tr>
                <td>{{ $receipt_details->contact }}</td>
                <td style="align-content:right; font-weight: bold ">{{ strtoupper($receipt_details->customer_name) }}
                </td>
            </tr>

            <tr>
                <td></td>
                <td style="align-content: right;">{{ $receipt_details->customer_almt2 }}
                    Hp.({{ $receipt_details->customer_almt3 }})</td>
            </tr>
        </table>
    </div>
    <div class="col-xs-12">
        <table class="" cellpadiing="0"
            style="margin-top:10px;margin-bottom:10px;font-size:16px; width: 100%;font-size:16px;text-align:left;">
            <tr style="border-top:0.05px dashed black;">
                {{-- <hr style="color: black"> --}}

                @php
                    $nom = $receipt_details->jml_tempo;
                    if ($receipt_details->tipe_tempo == 'days') {
                        // $tgl = \Carbon::today()->addDays($nom)->format('d M Y');
                        $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $receipt_details->tgl_trans2)
                            ->addDays($nom)
                            ->format('d M Y');
                        // $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $receipt_details->tgl_trans2)->addMonths($nom)->format('d M Y');
                    } else {
                        // $tgl = \Carbon::now()->addMonths($nom)->format('d M Y');
                        $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $receipt_details->tgl_trans2)
                            ->addMonths($nom)
                            ->format('d M Y');
                    }

                @endphp
                <td style="padding-top:7px; align-content: right; width:30%; font-size:16px; font-weight: bold ">FAKTUR
                    PENJUALAN</td>
                <td style=" padding-top:7px; padding-left:10px; font-size:16px; padding-bottom:0px;">No :
                    {!! $receipt_details->invoice_no !!}</td>
                <td style="padding-top:7px; padding-left:10px; font-size:16px; padding-bottom:0px;">Type Bayar :
                    {!! $receipt_details->tipe_byr !!}</td>
                <td style="padding-top:7px; padding-left:10px; font-size:16px; padding-bottom:0px;">Jatuh Tempo :
                    {!! $tgl !!}</td>
            </tr>

            <table class="" cellpading="0"
                style="border:0.01px solid black; border-collapse: collapse;font-size:16px; width: 100%;font-size:16px;text-align:left;">
                <thead style="border:0.05px solid black;">
                    <tr>
                        <th style="padding: 0px; border-right:0.05px solid black; text-align:center; width:5%">No</th>
                        <th style="padding: 0px; border-right:0.05px solid black; text-align:center;">Nama Barang</th>
                        <th style="padding: 0px; border-right:0.05px solid black; text-align:center;width:10%">Satuan
                        </th>
                        <th style="padding: 0px; border-right:0.05px solid black; text-align:center;width:15%">Harga
                            Satuan</th>
                        <th style="padding: 0px; border-right:0.05px solid black; text-align:center; width:5%">JML</th>
                        <th style="padding: 0px; border-right:0.05px solid black; text-align:center; width:10%">Diskon
                        </th>
                        <th style="padding: 0px; border-right:0.05px solid black; text-align:center; width:20%">Total
                            Harga</th>
                    </tr>
                </thead>
                @forelse($receipt_details->lines as $line)
                    <tr>
                        <td style="padding: 0px; border-right:0.05px solid black;text-align:center;">
                            {{ $loop->iteration }} </td>
                        <td style="padding-left: 3px; border-right:0.05px solid black;">{{ $line['name'] }}
                            {{ $line['variation'] }}</td>
                        <td style="padding: 0px; border-right:0.05px solid black; text-align:center;">
                            {{ $line['units'] }}</td>
                        <td style="padding-right: 3px; border-right:0.05px solid black;text-align:right;">
                            {{ number_format(round($line['unit_price_inc_tax'])) }}</td>
                        <td style="padding: 0px; border-right:0.05px solid black;text-align:center;">
                            {{ $line['quantity'] }} </td>
                        <td style="padding: 0px; border-right:0.05px solid black;text-align:center;"> </td>
                        <td style="padding-right: 3px; border-right:0.05px solid black;text-align:right;">
                            {{ $line['line_total'] }}</td>
                    </tr>
                @endforeach

            </table>
        </table>
        <table cellpading="0"
            style="border-left:0.01px solid black; border-right:0.01px solid black; border-bottom:0.01px solid black; border-collapse: collapse; font-size:16px; width: 100%; font-size:16px; text-align:right;">
            <tr>
                <td align="left" style="padding-left: 3px;">
                    @if ($receipt_details->discount_type == 'fee')
                        <b>Fee : &nbsp;&nbsp;Rp. {{ $receipt_details->discount }}</b>
                    @endif
                </td>
                <td style=""><b>Sub Total : &nbsp;&nbsp;Rp.</b></td>
                <td style="padding-right: 3px;">
                    {{ $receipt_details->subtotal_unformatted }}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td style="width: 200px;">
                    <b>Diskon : &nbsp;&nbsp;Rp.</b>
                </td>
                <td style="padding-right: 3px;">
                    @if ($receipt_details->discount_type == 'fee')
                        0
                    @else
                        {{ $receipt_details->discount }}
                    @endif
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td style="">
                    <b>Total : &nbsp;&nbsp;Rp.</b>
                </td>
                <td style="padding-right: 3px;">
                    {{ $receipt_details->total }}</td>
            </tr>
        </table>
        <br>
        <table class="" style="font-size:16px; width: 100%;font-size:16px;text-align:center;">
            <thead>
                <th style="text-align:center;">Penerima</th>
                <th style="text-align:center;">Sales/Pengirim</th>
                <th style="text-align:center;">Bag. Gudang</th>
                <th style="text-align:center;">Admin/Kasir</th>
            </thead>
            <tr>
                <td style="padding-top: 10px;"><br>
                </td>
            </tr>
            <tr>
                {{-- <td>()</td> --}}
                <td style="align-content:right; font-weight: bold ">({{ strtoupper($receipt_details->customer_name) }})
                </td>
                <td>(....................)</td>
                <td>(....................)</td>
                {{-- <td>(....................)</td> --}}
                <td style="align-content:right; font-weight: bold ">
                    ({{ auth()->user()->first_name . ' ' . auth()->user()->last_name }})</td>
            </tr>
        </table>
    </div>
</div>
