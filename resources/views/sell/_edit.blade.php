<div class="box-body">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('discount_type', __('sale.discount_type') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::select(
                    'discount_type',
                    ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')],
                    $transaction->discount_type,
                    [
                        'class' => 'form-control',
                        'placeholder' => __('messages.please_select'),
                        'required',
                        'data-default' => 'percentage',
                    ],
                ) !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('discount_amount', __('sale.discount_amount') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('discount_amount', @num_format($transaction->discount_amount), [
                    'class' => 'form-control input_number',
                    'data-default' => $business_details->default_sales_discount,
                ]) !!}
            </div>
        </div>
    </div>

    <div class="col-md-4"><br>
        <b>@lang('sale.discount_amount'):</b>(-)
        <span class="display_currency" id="total_discount">0</span>
    </div>

    <div class="clearfix"></div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('tax_rate_id', __('sale.order_tax') . ':*') !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::select(
                    'tax_rate_id',
                    $taxes['tax_rates'],
                    $transaction->tax_id,
                    [
                        'placeholder' => __('messages.please_select'),
                        'class' => 'form-control',
                        'data-default' => $business_details->default_sales_tax,
                    ],
                    $taxes['attributes'],
                ) !!}

                <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount"
                    value="{{ @num_format(optional($transaction->tax)->amount) }}"
                    data-default="{{ $business_details->tax_calculation_amount }}">
                <input type="hidden" name="payment[0][amount]" id="amount_0"
                    value="{{ @num_format(optional($transaction->tax)->amount) }}"
                    data-default="{{ $business_details->tax_calculation_amount }}">

                @php
                    $method = 'cash';
                    $card_number = '';
                    $card_holder_name = '';
                    $card_transaction_number = '';
                    $card_type = '';
                    $card_month = '';
                    $card_year = '';
                    $card_security = '';
                    $cheque_number = '';
                    $bank_account_number = '';
                    $transaction_no_1 = '';
                    $transaction_no_2 = '';
                    $transaction_no_3 = '';
                    $note = '';
                    foreach ($transaction->payment_lines as $payment_lines) {
                        $method = $payment_lines->method;
                        $card_number = $payment_lines->card_number;
                        $card_holder_name = $payment_lines->card_holder_name;
                        $card_transaction_number = $payment_lines->card_transaction_number;
                        $card_type = $payment_lines->card_type;
                        $card_month = $payment_lines->card_month;
                        $card_year = $payment_lines->card_year;
                        $card_security = $payment_lines->card_security;
                        $cheque_number = $payment_lines->cheque_number;
                        $bank_account_number = $payment_lines->bank_account_number;
                        $note = $payment_lines->note;
                    }
                @endphp

                <input type="hidden" name="payment[0][method]" id="method_0" value="{{ $method }}">
                <input type="hidden" name="payment[0][card_number]" id="card_number_0" value="{{ $card_number }}">
                <input type="hidden" name="payment[0][card_holder_name]" id="card_holder_name_0"
                    value="{{ $card_holder_name }}">
                <input type="hidden" name="payment[0][card_transaction_number]" id="card_transaction_number_0"
                    value="{{ $card_transaction_number }}">
                <input type="hidden" name="payment[0][card_type]" id="card_type_0" value="{{ $card_type }}">
                <input type="hidden" name="payment[0][card_month]" id="card_month_0" value="{{ $card_month }}">
                <input type="hidden" name="payment[0][card_year]" id="card_year_0" value="{{ $card_year }}">
                <input type="hidden" name="payment[0][card_security]" id="card_security_0"
                    value="{{ $card_security }}">
                <input type="hidden" name="payment[0][cheque_number]" id="cheque_number_0"
                    value="{{ $cheque_number }}">
                <input type="hidden" name="payment[0][bank_account_number]" id="bank_account_number_0"
                    value="{{ $bank_account_number }}">
                <input type="hidden" name="payment[0][transaction_no_1]" id="transaction_no_1_0"
                    value="{{ $transaction_no_1 }}">
                <input type="hidden" name="payment[0][transaction_no_2]" id="transaction_no_2_0"
                    value="{{ $transaction_no_2 }}">
                <input type="hidden" name="payment[0][transaction_no_3]" id="transaction_no_3_0"
                    value="{{ $transaction_no_3 }}">
                <input type="hidden" name="payment[0][note]" id="note_0" value="{{ $note }}">
            </div>
        </div>
    </div>

    <div class="col-md-4 col-md-offset-4">
        <b>@lang('sale.order_tax'):</b>(+)
        <span class="display_currency" id="order_tax">{{ $transaction->tax_amount }}</span>
    </div>

    <div class="clearfix"></div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('shipping_details', __('sale.shipping_details')) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::textarea('shipping_details', $transaction->shipping_details, [
                    'class' => 'form-control',
                    'placeholder' => __('sale.shipping_details'),
                    'rows' => '1',
                    'cols' => '30',
                ]) !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('shipping_charges', __('sale.shipping_charges')) !!}
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                {!! Form::text('shipping_charges', @num_format($transaction->shipping_charges), [
                    'class' => 'form-control input_number',
                    'placeholder' => __('sale.shipping_charges'),
                ]) !!}
            </div>
        </div>
    </div>
    <div class="col-md-4 col-md-offset-8">
        <div><b>@lang('sale.total_payable'): </b>
            <input type="hidden" name="final_total" id="final_total_input">
            <span id="total_payable">0</span>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('sell_note', __('sale.sell_note') . ':') !!}
            {!! Form::textarea('sale_note', $transaction->additional_notes, ['class' => 'form-control', 'rows' => 3]) !!}
        </div>
    </div>
    <input type="hidden" name="is_direct_sale" value="1">
    <div class="col-md-12">
        <button type="button" class="btn btn-primary pull-right" id="submit-sell">@lang('messages.update')</button>
    </div>
</div>
