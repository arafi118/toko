@inject('request', 'Illuminate\Http\Request')

<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action('CashRegisterController@postCloseRegister'), 'method' => 'post' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">
        <center>@lang( 'cash_register.current_register' )</center>
      </h3>
      @php
      // $get = $request->session()->all();
      // dd($get);
      $tgl_buka = \Carbon::createFromFormat('Y-m-d H:i:s', $register_details->open_time)->format('d M, Y H:i');
      $tgl_tutup = \Carbon::now()->format('d M, Y H:i');
      @endphp
    </div>
    <div class="modal-body">
      <p>@lang('cash_register.open')&nbsp;&nbsp;&nbsp;&nbsp;:
        {{ \Carbon::createFromFormat('Y-m-d H:i:s', $register_details->open_time)->format('d M, Y H:i') }}</p>
      <p>@lang('cash_register.close') &nbsp;: {{ \Carbon::now()->format('d M, Y H:i') }}</p>
      <input type="hidden" id="location_close" name="location_close">
      <input type="hidden" id="cash_in_hand" name="cash_in_hand" value="{{ $register_details->cash_in_hand }}">
      <input type="hidden" id="total_cash" name="total_cash" value="{{ $register_details->total_cash }}">
      <input type="hidden" id="total_cheque" name="total_cheque" value="{{ $register_details->total_cheque }}">
      <input type="hidden" id="total_card" name="total_card" value="{{ $register_details->total_card }}">
      <input type="hidden" id="total_sale" name="total_sale" value="{{ $register_details->total_sale }}">
      <input type="hidden" id="total_refund" name="total_refund" value="{{ $register_details->total_refund }}">
      <input type="hidden" id="total_cash_refund" name="total_cash_refund"
        value="{{ $register_details->total_cash_refund }}">
      <input type="hidden" id="total_cheque_refund" name="total_cheque_refund"
        value="{{ $register_details->total_cheque_refund }}">
      <input type="hidden" id="total_card_refund" name="total_card_refund"
        value="{{ $register_details->total_card_refund }}">
      <input type="hidden" id="uang_tunai" name="uang_tunai"
        value="{{ $register_details->cash_in_hand + $register_details->total_cash }}">
      <input type="hidden" id="tgl_buka" name="tgl_buka" value="{{ $tgl_buka }}">
      <input type="hidden" id="tgl_tutup" name="tgl_tutup" value="{{ $tgl_tutup }}">
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="col-sm-12">
          <table class="table">
            <tr>
              <td>
                @lang('cash_register.uang_tunai_buka'):
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $register_details->cash_in_hand }}</span>
              </td>
            </tr>
            <tr>
              <td>
                @lang('cash_register.cash_payment'):
              </td>
              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $transaction_sell }}</span>
              </td>
            </tr>
            <tr>
              <td>
                @lang('cash_register.checque_payment'):
              </td>
              <td>
                <span class="display_currency"
                  data-currency_symbol="true">{{ $register_details->total_cheque + $register_details->total_cheque_refund }}</span>
              </td>
            </tr>
            <tr>
              <td>
                @lang('cash_register.card_payment'):
              </td>
              <td>
                <span class="display_currency"
                  data-currency_symbol="true">{{ $register_details->total_card + $register_details->total_card_refund}}</span>
              </td>
            </tr>
            <tr>
              <td>
                @lang('cash_register.total_sales'):
              </td>
              <td>
                <span class="display_currency"
                  data-currency_symbol="true">{{ $register_details->total_sale + $register_details->total_refund}}</span>
              </td>
            </tr>
            <tr class="success">
              <th>
                @lang('cash_register.tot_ref')
              </th>
              <td>
                <b><span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_refund }}</span></b><br>
                <small>
                  @if($register_details->total_cash_refund != 0)
                  Cash: <span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_cash_refund }}</span><br>
                  @endif
                  @if($register_details->total_cheque_refund != 0)
                  Cheque: <span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_cheque_refund }}</span><br>
                  @endif
                  @if($register_details->total_card_refund != 0)
                  Card: <span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_card_refund }}</span><br>
                  @endif
                  {{-- @if($register_details->total_bank_transfer_refund != 0)
                  Bank Transfer: <span class="display_currency" data-currency_symbol="true">{{ $register_details->total_bank_transfer_refund }}</span><br>
                  @endif
                  @if(config('constants.enable_custom_payment_1') && $register_details->total_custom_pay_1_refund != 0)
                  @lang('lang_v1.custom_payment_1'): <span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_custom_pay_1_refund }}</span>
                  @endif
                  @if(config('constants.enable_custom_payment_2') && $register_details->total_custom_pay_2_refund != 0)
                  @lang('lang_v1.custom_payment_2'): <span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_custom_pay_2_refund }}</span>
                  @endif
                  @if(config('constants.enable_custom_payment_3') && $register_details->total_custom_pay_3_refund != 0)
                  @lang('lang_v1.custom_payment_3'): <span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_custom_pay_3_refund }}</span>
                  @endif
                  @if($register_details->total_other_refund != 0)
                  Other: <span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->total_other_refund }}</span>
                  @endif --}}
                </small>
              </td>
            </tr>
            <tr class="success">
              <th>
                @lang('cash_register.total_cash')
              </th>
              <td>
                <b><span class="display_currency"
                    data-currency_symbol="true">{{ $register_details->cash_in_hand + $transaction_sell }}</span></b>
              </td>
            </tr>
          </table>
        </div>
      </div>

      {{-- @include('cash_register.register_product_details') --}}

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('closing_amount', __( 'cash_register.total_uang_disetor' ) . ':*') !!}
            {!! Form::text('closing_amount', @num_format($register_details->cash_in_hand +
            $transaction_sell), ['class' => 'form-control input_number', 'required', 'placeholder' => __(
            'cash_register.total_uang_disetor' ) ]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('total_card_slips', __( 'cash_register.total_card_slips' ) . ':*') !!}
            @show_tooltip(__('tooltip.total_card_slips'))
            {!! Form::number('total_card_slips', $register_details->total_card_slips, ['class' => 'form-control',
            'required', 'placeholder' => __( 'cash_register.total_card_slips' ), 'min' => 0 ]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('total_cheques', __( 'cash_register.total_cheques' ) . ':*') !!}
            @show_tooltip(__('tooltip.total_cheques'))
            {!! Form::number('total_cheques', $register_details->total_cheques, ['class' => 'form-control', 'required',
            'placeholder' => __( 'cash_register.total_cheques' ), 'min' => 0 ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('closing_note', __( 'cash_register.closing_note' ) . ':') !!}
            {!! Form::textarea('closing_note', null, ['class' => 'form-control', 'placeholder' => __(
            'cash_register.closing_note' ), 'rows' => 3 ]); !!}
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.cancel' )</button>
      <button type="submit" class="btn btn-primary" id="tutup_kasir">@lang( 'cash_register.close_register' )</button>
    </div>
    {!! Form::close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->