@inject('request', 'Illuminate\Http\Request')

<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    {!! Form::open(['url' => action('CashRegisterController@getCloseRegister'), 'method' => 'post' ]) !!}
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h3 class="modal-title">
        <center>@lang( 'cash_register.close_register' )</center>
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
                <span class="display_currency"
                  data-currency_symbol="true">{{ $transaction_sell + $register_details->total_cash_refund }}</span>
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
                <span class="display_currency" data-currency_symbol="true">0</span>
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

      @include('cash_register.register_product_details')

      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('closing_amount', __( 'cash_register.total_uang_disetor' ) . ':*') !!}
            {!! Form::text('closing_amount', @num_format($transaction_sell), ['class' => 'form-control
            input_number', 'required', 'placeholder' => __( 'cash_register.total_uang_disetor' ), 'readonly' => 'true'
            ]); !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('total_card_slips', __( 'cash_register.total_card_slips' ) . ':*') !!}
            @show_tooltip(__('tooltip.total_card_slips'))
            {!! Form::number('total_card_slips', $register_details->total_card_slips_close, ['class' => 'form-control',
            'required', 'placeholder' => __( 'cash_register.total_card_slips' ), 'min' => 0, 'readonly' => 'true' ]);
            !!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('total_cheques', __( 'cash_register.total_cheques' ) . ':*') !!}
            @show_tooltip(__('tooltip.total_cheques'))
            {!! Form::number('total_cheques', $register_details->total_cheques_close, ['class' => 'form-control',
            'required', 'placeholder' => __( 'cash_register.total_cheques' ), 'min' => 0, 'readonly' => 'true' ]); !!}
          </div>
        </div>
        <div class="col-sm-12">
          <div class="form-group">
            {!! Form::label('closing_note', __( 'cash_register.closing_note' ) . ':') !!}
            {!! Form::textarea('closing_note', $register_details->closing_note_close, ['class' => 'form-control',
            'placeholder' => __( 'cash_register.closing_note' ), 'rows' => 3, 'readonly' => 'true' ]); !!}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <b>@lang('report.user'):</b> {{ $register_details->user_name}}<br>
          <b>Email:</b> {{ $register_details->email}}
        </div>
      </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-primary no-print" aria-label="Print"
        onclick="$(this).closest('div.modal').printThis();">
        <i class="fa fa-print"></i> @lang( 'messages.print' )
      </button>

      <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.cancel' )
      </button>
    </div>
    {!! Form::close() !!}
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->