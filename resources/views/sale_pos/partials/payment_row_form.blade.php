<div class="row">
    <input type="hidden" class="payment_row_index" value="{{ $row_index }}">
    @php
        $col_class = 'col-md-6';
        if (!empty($accounts)) {
            $col_class = 'col-md-4';
        }
        // dd($payment_line['amount']);
    @endphp
    {!! Form::hidden("payment[$row_index][amount]", @num_format($payment_line['amount']), [
        'class' => 'form-control payment-amount input_number text-2xl font-medium',
        'required',
        'readonly',
        'id' => "amount_$row_index",
        'style' => 'border:0px; text-align: right; margin-bottom:0;',
        'placeholder' => __('sale.amount'),
    ]) !!}
    <div class="col-md-12">
        <div class="row">
            @if (isset($transaction_date))
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="transaction_date">Tanggal Penjualan:*</label>
                        <span class="hidden">
                            {!! $transaction->transaction_date !!}
                        </span>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {!! Form::text('transaction_date', $transaction_date, [
                                'class' => 'form-control',
                                'required',
                                'id' => 'transaction_date',
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif

            <input type="hidden" name="bayar" id="bayar" class="duit form-control payment-amount input_number">
            <div class="col-md-7">
                <div class="form-group">
                    <label>Bayar</label>
                    <input type="text" name="bayar" placehoder="Bayar" value=""
                        style="font-size: 32px; text-align: right;" id="bayar"
                        class="duit form-control payment-amount input_number">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Kembali</label>
                    <input type="text" name="kembali" readonly="" id="kembali"
                        style="font-size: 32px; text-align: right;" class="form-control payment-amount input_number">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label("method_$row_index", __('lang_v1.payment_method') . ':*') !!}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-money"></i>
                        </span>
                        {!! Form::select("payment[$row_index][method]", $payment_types, $payment_line['method'], [
                            'class' => 'form-control col-md-12 payment_types_dropdown',
                            'required',
                            'id' => "method_$row_index",
                            'style' => 'width:100%;',
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (!empty($accounts))
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label("account_$row_index", __('lang_v1.payment_account') . ':') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-money"></i>
                            </span>
                            {!! Form::select(
                                "payment[$row_index][account_id]",
                                $accounts,
                                !empty($payment_line['account_id']) ? $payment_line['account_id'] : '',
                                ['class' => 'form-control select2', 'id' => "account_$row_index", 'style' => 'width:100%;'],
                            ) !!}
                        </div>
                    </div>
                </div>
            @endif
            @include('sale_pos.partials.payment_type_details')
        </div>
    </div>
    <div class="col-md-5 hidden">
        <div class="form-group">
            {!! Form::label('sale_note', __('sale.sell_note') . ':') !!}
            {!! Form::textarea('sale_note', !empty($transaction) ? $transaction->additional_notes : null, [
                'class' => 'form-control',
                'rows' => 3,
                'placeholder' => __('sale.sell_note'),
                'style' => 'height: 110px;',
            ]) !!}
        </div>
        <div class="form-group" style="display: none;">
            {!! Form::label("note_$row_index", __('sale.payment_note') . ':') !!}
            {!! Form::textarea("payment[$row_index][note]", $payment_line['note'], [
                'class' => 'form-control',
                'rows' => 3,
                'id' => "note_$row_index",
            ]) !!}
        </div>
    </div>
</div>
