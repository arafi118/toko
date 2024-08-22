@extends('master.layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-4">
            {!! Form::open(['url' => action('Master\MasterController@register'), 'method' => 'post']) !!}
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Register</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('nama_usaha', 'Nama Usaha') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-home"></i>
                                    </span>
                                    {!! Form::text('nama_usaha', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('provinsi', 'Provinsi') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::select('provinsi', $prov, null, [
                                        'class' => 'form-control select2',
                                        'id' => 'prov',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('kabupaten', 'Kabupaten') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    {!! Form::select('kabupaten', [], null, [
                                        'class' => 'form-control select2',
                                        'id' => 'kode_kab',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('kode_pos', 'Kode Pos') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-address-card"></i>
                                    </span>
                                    {!! Form::text('kode_pos', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">
                        Simpan
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-md-4">
            {!! Form::open(['url' => action('Master\MasterController@jenisBuku'), 'method' => 'post']) !!}
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Jenis Buku</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('posisi', 'Posisi') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-archive"></i>
                                    </span>
                                    {!! Form::select(
                                        'posisi',
                                        [
                                            '1' => 'Aktiva',
                                            '2' => 'Hutang',
                                            '3' => 'Modal',
                                            '4' => 'Pendapatan',
                                            '5' => 'Biaya',
                                        ],
                                        '1',
                                        [
                                            'class' => 'form-control select2',
                                            'id' => 'posisi',
                                            'required',
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('jenis_rekening', 'Jenis Rekening') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                    {!! Form::select('jenis_rekening', $rek, '1', [
                                        'class' => 'form-control select2',
                                        'id' => 'jenis_rekening',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('kode', 'Kode') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                    {!! Form::text('kode', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('nama', 'Nama') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                    {!! Form::text('nama', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('inisial', 'Inisial') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                    {!! Form::text('inisial', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">
                        Simpan
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-md-4">
            {!! Form::open(['url' => action('Master\MasterController@rekening'), 'method' => 'post']) !!}
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Rekening</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('jenis_buku', 'Jenis Buku Rekening') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-book"></i>
                                    </span>
                                    {!! Form::select('jenis_buku', $jb, null, [
                                        'class' => 'form-control select2',
                                        'id' => 'jenis_buku',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('kode_rekening', 'Kode') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                    {!! Form::text('kode_rekening', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('jenis_buku_pasangan', 'Jenis Buku Pasangan') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-book"></i>
                                    </span>
                                    {!! Form::select('jenis_buku_pasangan', $jb, null, [
                                        'class' => 'form-control select2',
                                        'id' => 'jenis_buku_pasangan',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('kode_rekening_pasangan', 'Kode') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                    {!! Form::text('kode_rekening_pasangan', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('nama_rekening', 'Nama Rekening') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                    {!! Form::text('nama_rekening', '', [
                                        'class' => 'form-control ',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">
                        Simpan
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).on('change', '#prov', function(e) {
            e.preventDefault();

            var prov = $(this).val();
            $.get('/master/wilayah/kab/' + prov, function(result) {
                setSelectValue('kode_kab', result.data)
            })
        })

        function setSelectValue(id, data) {
            $('#' + id).empty()
            data.forEach((val, index) => {
                $('#' + id).append('<option value="' + val.kode + '">' + val.nama + '</option>')
            })

            $('#' + id).trigger('change')
        }

        var prov = $('#prov').val();
        $.get('/master/wilayah/kab/' + prov, function(result) {
            setSelectValue('kode_kab', result.data)
        })
    </script>

    @if (Session::get('success'))
        <script>
            swal({
                icon: 'success',
                text: '{{ Session::get('success') }}',
                title: 'Berhasil'
            })
        </script>
    @endif
@endsection
