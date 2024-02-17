<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('VariationGroupQuantityController@store'), 'method' => 'post', 'id' =>
        'variation_group_quantity_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'lang_v1.add_price_quantity_group' )</h4>
        </div>

        <div class="modal-body">
            {!! Form::hidden('variation_id', $variation_id); !!}

            <div class="form-group">
                {!! Form::label('amount', __( 'lang_v1.amount' ) . ':*') !!}
                {!! Form::text('amount', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                'lang_v1.amount'
                ) ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('price_inc_tax', __( 'lang_v1.selling_one_price_inc_tax' ) . ':*') !!}
                {!! Form::text('price_inc_tax', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                'lang_v1.selling_one_price_inc_tax'
                ) ]); !!}
            </div>

            <div class="text-danger">
                <i>Kami tidak merekomendasikan anda untuk menambah paket penjualan lebih dari 3 (tiga)</i>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->