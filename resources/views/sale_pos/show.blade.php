<?php
use App\Transaction;

$trans = new Transaction();
// dd($sell->invoice_no);
?>

<div class="modal-dialog modal-xl no-print" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle"> @lang('sale.sell_details') (<b>@lang('sale.invoice_no'):</b>
                {{ $sell->invoice_no }})
            </h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-sm-4">
                    <p class="pull-left"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <b>{{ __('sale.invoice_no') }}:</b> #{{ $sell->invoice_no }}<br>
                    <b>{{ __('sale.status') }}:</b>
                    @if ($sell->status == 'draft' && $sell->is_quotation == 1)
                        {{ __('lang_v1.quotation') }}
                    @else
                        {{ ucfirst($sell->status) }}
                    @endif
                    <br>
                    <b>{{ __('sale.payment_status') }}:</b>
                    @if ($sell->payment_status == 'due')
                        {{ 'Tempo' }}
                    @elseif($sell->payment_status == 'paid')
                        {{ 'Dibayar' }}
                    @elseif($sell->payment_status == 'partial')
                        {{ 'Dibayar Sebagian' }}
                    @endif
                    {{-- {{ ucfirst( $sell->payment_status ) }} --}}
                    <br>
                </div>
                <div class="col-sm-4">
                    <b>{{ __('sale.customer_name') }}:</b> {{ $sell->contact->name }}<br>
                    <b>{{ __('business.address') }}:</b><br>
                    @if ($sell->contact->landmark)
                        {{ $sell->contact->landmark }}
                    @endif

                    {{ ', ' . $sell->contact->city }}

                    @if ($sell->contact->state)
                        {{ ', ' . $sell->contact->state }}
                    @endif
                    <br>
                    @if ($sell->contact->country)
                        {{ $sell->contact->country }}
                    @endif
                </div>
                <div class="col-sm-4">
                    <b>{{ 'Jatuh Tempo' }}:</b>
                    {{-- {{$sell->pay_term_number}} --}}
                    @php
                        $nom = $sell->pay_term_number;
                        if ($sell->pay_term_type == 'days') {
                            // $cek = \Carbon::createFromFormat('Y-m-d H:i:s', $sell->transaction_date)->addDays($nom)->format('d M Y');
                            // dd($cek);
                            $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $sell->transaction_date)
                                ->addDays($nom)
                                ->format('d M Y');
                            // $tgl = \Carbon::today()->addDays($nom)->format('d M Y');
                        } else {
                            // $bulan = \Carbon::today()->addDays($nom)->toDateString();
                            $tgl = \Carbon::createFromFormat('Y-m-d H:i:s', $sell->transaction_date)
                                ->addMonths($nom)
                                ->format('d M Y');
                            // $tgl = \Carbon::now()->addMonths($nom)->format('d M Y');
                        }

                    @endphp
                    {{ $tgl }}
                    {{-- @if ($sell->pay_term_type == 'days')
            {{'Hari'}}
        @elseif($sell->pay_term_type == 'months')
            {{'Bulan'}}
        @endif  --}}
                    <br>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <h4>{{ __('sale.products') }}:</h4>
                </div>

                <div class="col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table bg-gray">
                            <tr class="bg-green">
                                <th>#</th>
                                <th>{{ __('sale.product') }}</th>
                                @if (session()->get('business.enable_lot_number') == 1)
                                    <th>{{ __('lang_v1.lot_n_expiry') }}</th>
                                @endif
                                <th>{{ __('sale.qty') }}</th>
                                <th>{{ __('sale.unit_price') }}</th>
                                <th>{{ __('sale.discount') }}</th>
                                <th>{{ __('sale.tax') }}</th>
                                <th>{{ __('sale.price_inc_tax') }}</th>
                                <th>{{ __('sale.subtotal') }}</th>
                            </tr>

                            @php
                                // $group_name = $trans->check_group($sell->contact_id);
                                // dd($sell->contact_id);
                            @endphp
                            @foreach ($sell->sell_lines as $sell_line)
                                @php
                                    // $real_price = $trans->check_harga($sell->contact_id,$sell_line->variation_id);
                                    // $real_price = $sell->total_before_tax
                                    // dd($sell_line);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>

                                        {{ $sell_line->product->name }}
                                        @if ($sell_line->product->type == 'variable')
                                            - {{ $sell_line->variations->product_variation->name or '' }}
                                            - {{ $sell_line->variations->name or '' }},
                                        @endif
                                        {{ $sell_line->variations->sub_sku or '' }}
                                        @php
                                            $brand = $sell_line->product->brand;
                                        @endphp
                                        @if (!empty($brand->name))
                                            {{ $brand->name }}
                                        @endif

                                        @if (!empty($sell_line->sell_line_note))
                                            <br> {{ $sell_line->sell_line_note }}
                                        @endif
                                        {{-- {{$group_name}} --}}
                                    </td>
                                    @if (session()->get('business.enable_lot_number') == 1)
                                        <td>{{ $sell_line->lot_details->lot_number or '--' }}
                                            @if (session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                                                ({{ @format_date($sell_line->lot_details->exp_date) }})
                                            @endif
                                        </td>
                                    @endif
                                    <td>{{ $sell_line->quantity }}</td>
                                    <td>
                                        {{-- <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price }}</span> --}}
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $sell_line->unit_price }}</span>
                                    </td>
                                    <td>
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $sell_line->get_discount_amount() }}</span>
                                        @if ($sell_line->line_discount_type == 'percentage' || $sell_line->line_discount_type == 'fee')
                                            ({{ $sell_line->line_discount_amount }}%)
                                        @endif
                                    </td>
                                    <td>
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $sell_line->item_tax }}</span>
                                        @if (!empty($taxes[$sell_line->tax_id]))
                                            ( {{ $taxes[$sell_line->tax_id] }} )
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price_inc_tax }}</span> --}}
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $sell_line->unit_price_inc_tax }}</span>
                                    </td>
                                    <td>
                                        {{-- <span class="display_currency" data-currency_symbol="true">{{ $sell_line->quantity * $sell_line->unit_price_inc_tax }}</span> --}}
                                        <span class="display_currency"
                                            data-currency_symbol="true">{{ $sell_line->quantity * $sell_line->unit_price_inc_tax }}</span>
                                    </td>
                                </tr>

                                @if (!empty($sell_line->modifiers))
                                    @foreach ($sell_line->modifiers as $modifier)
                                        @php
                                            //  $real_price = $trans->check_harga($sell->contact_id,$sell_line->variation_id);
                                            $real_price = $sell_line->unit_price_inc_tax;
                                        @endphp
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                {{ $modifier->product->name }} -
                                                {{ $modifier->variations->name or '' }},
                                                {{ $modifier->variations->sub_sku or '' }}
                                            </td>
                                            @if (session()->get('business.enable_lot_number') == 1)
                                                <td>&nbsp;</td>
                                            @endif
                                            <td>{{ $modifier->quantity }}</td>
                                            <td>
                                                <span class="display_currency"
                                                    data-currency_symbol="true">{{ $real_price }}</span>
                                            </td>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <td>
                                                <span class="display_currency"
                                                    data-currency_symbol="true">{{ $modifier->item_tax }}</span>
                                                @if (!empty($taxes[$modifier->tax_id]))
                                                    ({{ $taxes[$modifier->tax_id] }})
                                                @endif
                                            </td>
                                            <td>
                                                <span class="display_currency"
                                                    data-currency_symbol="true">{{ $real_price }}</span>
                                            </td>
                                            <td>
                                                <span class="display_currency"
                                                    data-currency_symbol="true">{{ $modifier->quantity * $real_price }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <h4>{{ __('sale.payment_info') }}:</h4>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table bg-gray">
                            <tr class="bg-green">
                                <th>#</th>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('purchase.ref_no') }}</th>
                                <th>{{ __('sale.amount') }}</th>
                                <th>{{ __('sale.payment_mode') }}</th>
                                <th>{{ __('sale.payment_note') }}</th>
                            </tr>
                            @php
                                $total_paid = 0;
                                $discount = $sell->discount_amount;
                                if ($sell->discount_type == 'percentage') {
                                    $discount .= '%';
                                }

                                if ($sell->discount_type == 'fee') {
                                    $discount = 0;
                                }
                            @endphp
                            @if (count($sell->payment_lines) > 0)
                                @foreach ($sell->payment_lines as $payment_line)
                                    @php
                                        if ($payment_line->is_return == 1) {
                                            $total_paid -= $payment_line->amount;
                                        } else {
                                            $total_paid += $payment_line->amount;
                                        }

                                        if ($payment_line->id_rekening_debit == '111.04') {
                                            $discount = ($sell->discount_amount * $payment_line->amount) / 100;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ @format_date($payment_line->paid_on) }}</td>
                                        <td>{{ $payment_line->payment_ref_no }}</td>
                                        <td><span class="display_currency"
                                                data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                                        <td>
                                            {{ $payment_types[$payment_line->method] or $payment_line->method }}
                                            @if ($payment_line->is_return == 1)
                                                <br />
                                                ({{ __('lang_v1.change_return') }})
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment_line->note)
                                                {{ ucfirst($payment_line->note) }}
                                            @else
                                                {{ $payment_line->nama_rekening }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">- Belum ada data pembayaran -</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table bg-gray">
                            <tr>
                                <th>{{ __('sale.total') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->total_before_tax }}</span></td>
                            </tr>
                            <tr>
                                <th>
                                    @if ($sell->discount_type == 'fee')
                                        {{ __('sale.fee') }}:
                                    @else
                                        {{ __('sale.discount') }}:
                                    @endif
                                </th>
                                <td><b>(-)</b></td>
                                <td>
                                    @if ($sell->discount_type != 'percentage')
                                        <span class="display_currency pull-right" data-currency_symbol="true">
                                            {{ $discount }}
                                        </span>
                                    @else
                                        <span class="pull-right">
                                            {{ $discount }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.order_tax') }}:</th>
                                <td><b>(+)</b></td>
                                <td class="text-right">
                                    @if (!empty($order_taxes))
                                        @foreach ($order_taxes as $k => $v)
                                            <strong><small>{{ $k }}</small></strong> - <span
                                                class="display_currency pull-right"
                                                data-currency_symbol="true">{{ $v }}</span><br>
                                        @endforeach
                                    @else
                                        0.00
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.shipping') }}: @if ($sell->shipping_details)
                                        ({{ $sell->shipping_details }})
                                    @endif
                                </th>
                                <td><b>(+)</b></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->shipping_charges }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.total_payable') }}: </th>
                                <td></td>
                                <td><span class="display_currency pull-right">{{ $sell->final_total }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.total_paid') }}:</th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $total_paid }}</span></td>
                            </tr>
                            <tr>
                                <th>{{ __('sale.total_remaining') }}:</th>
                                <td></td>
                                <td><span class="display_currency pull-right"
                                        data-currency_symbol="true">{{ $sell->final_total - $total_paid }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <strong>{{ __('sale.sell_note') }}:</strong><br>
                    <p class="well well-sm no-shadow bg-gray">
                        @if ($sell->additional_notes)
                            {{ $sell->additional_notes }}
                        @else
                            --
                        @endif
                    </p>
                </div>
                <div class="col-sm-6">
                    <strong>{{ __('sale.staff_note') }}:</strong><br>
                    <p class="well well-sm no-shadow bg-gray">
                        @if ($sell->staff_note)
                            {{ $sell->staff_note }}
                        @else
                            --
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="print-invoice btn btn-primary"
                data-href="{{ route('sell.printInvoice', [$sell->id]) }}"><i class="fa fa-print"
                    aria-hidden="true"></i> @lang('messages.print')</a>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var element = $('div.modal-xl');
        __currency_convert_recursively(element);
    });
</script>
