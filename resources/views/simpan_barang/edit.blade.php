<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('SimpanBarangController@update', [$rak_bar->id]), 'method' => 'PUT', 'id' => 'rak_edit_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Rak Penyimpanan</h4>
      </div>
  
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('name', 'Nama Rak' . ':*') !!}
            {!! Form::text('name', $rak_bar->tempat_simpan, ['class' => 'form-control', 'required', 'placeholder' => 'Nama Rak' ]); !!}
        </div>
  
        <div class="form-group">
          {!! Form::label('keterangan', 'Keterangan' . ':') !!}
            {!! Form::text('keterangan', $rak_bar->keterangan, ['class' => 'form-control','placeholder' => 'Keterangan']); !!}
        </div>
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->