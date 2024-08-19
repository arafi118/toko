<div class="row">
    <div class="col-xs-12 text-center">
        <table class="" cellpadding="0"
            style="margin-left:5px; margin-right:5px; margin-bottom:5px;font-size:9px; width: 95%;">
            <tr>
                <td colspan="2" style="text-align: center;">
                    @if (!empty($receipt_details->display_name))
                        {{ strtoupper($receipt_details->display_name) }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2"style="text-align: center;"><small style="margin-bottom: 5px;" class="text-center">
                        {!! $receipt_details->address !!}
                    </small></td>
            </tr>
            <tr>
                <td colspan="2"style="text-align: center;"><small style="margin-bottom: 5px;" class="text-center">
                        {!! $receipt_details->contact !!}
                    </small></td>
            </tr>
        </table>
    </div>
    <div class="col-xs-12">
        <table class="" cellpadding="0"
            style="margin-right:5px;margin-left:5px;font-size:9px; width: 95%; border-top: 1px dashed black; border-bottom: 1px dashed black;">
            <tr>
                <td>Nota</td>
                <td>:</td>
                <td>{{ $receipt_details->invoice_no }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ $receipt_details->invoice_date }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td>{{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}</td>
            </tr>
        </table>
    </div>
    <div class="col-xs-12">
        <table class="" cellpadding="0" style="margin:5px;width:85%;font-size:9px; ">
            @forelse($receipt_details->lines as $line)
                <tr>
                    <td>
                        {{ $line['name'] }} {{ $line['variation'] }}
                        <!-- @if (!empty($line['sub_sku']))
, {{ $line['sub_sku'] }}
@endif @if (!empty($line['brand']))
, {{ $line['brand'] }}
@endif @if (!empty($line['cat_code']))
, {{ $line['cat_code'] }}
@endif
                            @if (!empty($line['sell_line_note']))
({{ $line['sell_line_note'] }})
@endif
                            @if (!empty($line['lot_number']))
<br> {{ $line['lot_number_label'] }}:  {{ $line['lot_number'] }}
@endif
                            @if (!empty($line['product_expiry']))
, {{ $line['product_expiry_label'] }}:  {{ $line['product_expiry'] }}
@endif -->
                    </td>
                    <td>{{ $line['quantity'] }} {{ $line['units'] }} x </td>
                    <td style="text-align: right;">{{ number_format(round($line['unit_price_inc_tax'])) }}</td>
                    <td class="text-center">:</td>
                    <td style="text-align: right;">{{ number_format(round($line['line_total'])) }}</td>
                </tr>
                @if (!empty($line['modifiers']))
                    @foreach ($line['modifiers'] as $modifier)
                        <tr>
                            <td colspan="4">
                                {{ $modifier['name'] }} {{ $modifier['variation'] }}
                                <!-- @if (!empty($modifier['sub_sku']))
, {{ $modifier['sub_sku'] }}
@endif @if (!empty($modifier['cat_code']))
, {{ $modifier['cat_code'] }}
@endif
  @if (!empty($modifier['sell_line_note']))
({{ $modifier['sell_line_note'] }})
@endif  -->
                            </td>
                        </tr>
                        <td>
                        <td>{{ $modifier['quantity'] }} {{ $modifier['units'] }} x </td>
                        <td>{{ $modifier['unit_price_inc_tax'] }}</td>
                        <td class="text-center">:</td>
                        <td>{{ $modifier['line_total'] }}</td>
                        </tr>
                    @endforeach
                @endif

            @empty
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="4"><br></td>
            </tr>
            @if (!empty($receipt_details->total_due))
                <tr>
                    <td></td>
                    <td>Total Hutang</td>
                    <td>:</td>
                    <td class="text-right">{{ number_format(round($receipt_details->total_due)) }}</td>
                </tr>
            @endif
            @if (!empty($receipt_details->shipping_charges))
                <tr>
                    <td></td>
                    <td>Biaya Kirim</td>
                    <td>:</td>
                    <td class="text-right">{{ number_format(round($receipt_details->shipping_charges)) }}</td>
                </tr>
            @endif
            @if (!empty($receipt_details->discount))
                @if ($receipt_details->discount_type != 'fee')
                    <tr>
                        <td></td>
                        <td>
                            Diskon
                        </td>
                        <td>:</td>
                        <td class="text-right">
                            {{ number_format(round($receipt_details->discount)) }}
                        </td>
                    </tr>
                @endif
            @endif
            @if (!empty($receipt_details->tax))
                <tr>
                    <td></td>
                    <td>Pajak</td>
                    <td>:</td>
                    <td class="text-right">{{ number_format(round($receipt_details->tax)) }}</td>
                </tr>
            @endif
            @if (!empty($receipt_details->total))
                <tr>
                    <td></td>
                    <td>Total</td>
                    <td>:</td>
                    <td class="text-right">{{ number_format(round($receipt_details->total)) }}</td>
                </tr>
            @endif
            <tr>
                <td></td>
                <td>Bayar</td>
                <td>:</td>
                <td class="text-right">{{ number_format((float) $receipt_details->bayar) }}</td>
            </tr>
            <tr>
                <td></td>
                <td>Kembali</td>
                <td>:</td>
                <td class="text-right">{{ number_format((float) $receipt_details->kembali) }}</td>
            </tr>
            @if (!empty($receipt_details->cashback))
                <tr>
                    <td></td>
                    <td>
                        Cashback
                    </td>
                    <td>:</td>
                    <td class="text-right">
                        {{ number_format(round($receipt_details->cashback)) }}
                    </td>
                </tr>
            @endif
            <tr>
                <td></td>
                <td>Catatan</td>
                <td>:</td>
                <td class="text-right">{{ $receipt_details->additional_notes }}</td>
            </tr>
        </table>
    </div>
    <div class="col-xs-12">
        <table class="" cellpadding="0"
            style="margin:5px;font-size:9px; width: 95%;border-top: 1px dashed black; border-bottom: 1px dashed black;">
            <tr>
                <td>Pelanggan</td>
                <td>:</td>
                <td>{{ $receipt_details->customer_name }}</td>
            </tr>
        </table>
        <div style="margin:5px;font-size:8px; width: 95%; font-weight: bold">
            *Barang yang sudah dibeli tidak dapat dikembalikan.
            @if ($receipt_details->discount_type == 'fee')
                <div>
                    *Fee : &nbsp;&nbsp;Rp. {{ number_format(round($receipt_details->discount)) }}
                </div>
            @endif
        </div>
    </div>
    <div class="col-xs-12">
        <table class="" cellpadding="0" style="margin:5px;font-size:9px; width: 95%;">
            <tr>
                <td colspan="3" style="text-align: center;">Terima Kasih</td>
            </tr>
            @php
                $business_id = auth()->user()->business_id;
            @endphp
            @if ($business_id == 2)
                <tr>
                    <td colspan="3" style="text-align: center;">Layanan Pesan Hubungi 0831 9588 9116</td>
                </tr>
            @endif
            <tr>
                <td colspan="3" style="text-align: center;">Selamat Berbelanja Kembali</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;">
                    @if (!empty($receipt_details->display_name))
                        {{ strtoupper($receipt_details->display_name) }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
