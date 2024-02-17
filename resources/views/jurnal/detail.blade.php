
<div class="modal-dialog" role="document">
  <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Detail Jurnal Umum</h4>
        </div>

        <div class="modal-body">
            <table class="table table-hover">
                <tr>
                    <th>ID</th>
                    <th>Tanggal Beli</th>
                    <th>Nama Barang</th>
                    <th>Unit</th>
                    <th>Umur Ekonomis</th>
                    <th>Harga Satuan</th>
                    <th>Harga Perolehan</th>
                    <th>Keterangan</th>
                </tr>
                @foreach($tbl_inventaris as $in)
                <tr>
                    <td>{{$in->id}}</td>
                    <td>{{$in->tgl_beli}}</td>
                    <td>{{$in->nama_barang}} ({{$in->status}})</td>
                    <td>{{$in->unit}}</td>
                    <td>{{$in->umur_ekonomis}}</td>
                    <td>Rp<?php echo number_format($in->harsat)?></td>
                    <td>Rp<?php echo number_format($in->nominal)?></td>
                    <td>{{$in->keterangan}}</td>
                </tr>
                @endforeach
            </table>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
    </div>
</div>