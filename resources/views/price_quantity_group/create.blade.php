<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('PriceQuantityGroupController@store'), 'method' => 'post', 'id' =>
        'price_quantity_group_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'lang_v1.add_price_quantity_group' )</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('amount', __( 'lang_v1.amount' ) . ':*') !!}
                {!! Form::text('amount', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                'lang_v1.amount'
                ) ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('description', __( 'lang_v1.description' ) . ':') !!}
                {!! Form::textarea('description', null, ['class' => 'form-control','placeholder' => __(
                'lang_v1.description' ), 'rows' => 3]); !!}
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->