<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('SimpanBarangController@store'), 'method' => 'post', 'id' => $quick_add ? 'quick_add_rak_form' : 'add_rak_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Rak Penyimpanan</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('name', 'Nama Rak' . ':*') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => 'Nama Rak' ]); !!}
        </div>
  
        <div class="form-group">
          {!! Form::label('keterangan', 'Keterangan' . ':') !!}
            {!! Form::text('keterangan', null, ['class' => 'form-control','placeholder' => 'Keterangan' ]); !!}
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->