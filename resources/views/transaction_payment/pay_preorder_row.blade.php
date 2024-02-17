<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('PreOrderController@storePayPreOrder'), 'method' => 'post', 'id' => 'transaction_payment_add_form' ]) !!}
      {!! Form::hidden('transaction_id', $transaction->id); !!}
      
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pelunasan Pre-Order</h4>
      </div>
  
      <div class="modal-body">
        <div class="row">
        @if(!empty($transaction->contact))
          <div class="col-md-4">
            <div class="well">
              <strong>
              @if(in_array($transaction->type, ['purchase', 'purchase_return']))
                @lang('purchase.supplier') 
              @elseif(in_array($transaction->type, ['sell', 'sell_return']))
                @lang('contact.customer') 
              @endif
              
              :</strong><br>{{ $transaction->contact->name }}<br>
              @if($transaction->type == 'purchase')
              <strong>@lang('business.business'): </strong>{{ $transaction->contact->supplier_business_name }}
              @endif
            </div>
          </div>
          @endif
          <div class="col-md-4">
            <div class="well">
            @if(in_array($transaction->type, ['sell', 'sell_return']))
              <strong>Nomor Pre-Order:<br> </strong>{{ $transaction->invoice_no }}
            @else
              <strong>@lang('purchase.ref_no'): </strong>{{ $transaction->ref_no }}
            @endif
              <br>
              <strong>@lang('purchase.location'):<br> </strong>{{ $transaction->location->name }}
            </div>
          </div>
          <div class="col-md-4">
            <div class="well">
              <strong>@lang('sale.total_amount'): <br></strong><span class="display_currency" data-currency_symbol="true">{{ $transaction->final_total }}</span><br>
              <strong>@lang('purchase.payment_note'): </strong><br>
              @if(!empty($transaction->additional_notes))
              {{ $transaction->additional_notes }}
              @else
                --
              @endif
            </div>
          </div>
        </div>
        <div class="row payment_row">
           <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("amount" ,'Jumlah:*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::text("amount", @num_format($payment_line->amount), ['class' => 'form-control input_number', 'required', 'placeholder' => 'Amount', 'data-rule-max-value' => $payment_line->amount, 'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])]); !!}
                {{-- {{dd($shipping_charges)}} --}}
              </div>
            </div>
          </div>
           <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("shipping_charges" ,'Biaya Kirim:*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::text("shipping_charges", @num_format($shipping_charges), ['class' => 'form-control input_number','readonly'=>'', 'required', 'placeholder' => 'Amount', 'data-rule-max-value' => $shipping_charges, 'data-msg-max-value' => __('lang_v1.max_amount_to_be_paid_is', ['amount' => $amount_formated])]); !!}
              </div>
            </div>
          </div>
        </div>
        <div class="row payment_row">
         
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("paid_on" ,'Tanggal:*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </span>
                {!! Form::text('paid_on', date('m/d/Y', strtotime($payment_line->paid_on) ), ['class' => 'form-control', 'readonly', 'required']); !!}
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("method" ,'Metode Pembayaran:*') !!}
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-money"></i>
                </span>
                {!! Form::select("method", $payment_types, $payment_line->method, ['class' => 'form-control select2 payment_types_dropdown', 'required', 'style' => 'width:100%;']); !!}
              </div>
            </div>
          </div>
          @if(!empty($accounts))
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-money"></i>
                  </span>
                  {!! Form::select("account_id", $accounts, !empty($payment_line->account_id) ? $payment_line->account_id : '' , ['class' => 'form-control select2', 'id' => "account_id", 'style' => 'width:100%;']); !!}
                </div>
              </div>
            </div>
          @endif
          <div class="clearfix"></div>
            @include('transaction_payment.payment_type_details')
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label("note",'Catatan Pembayaran:') !!}
              {!! Form::textarea("note", $payment_line->note, ['class' => 'form-control', 'rows' => 3]); !!}
            </div>
          </div>
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->