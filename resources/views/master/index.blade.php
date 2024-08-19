@extends('master.layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-4">
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
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('provinsi', 'Provinsi') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    {!! Form::select('provinsi', [], null, [
                                        'class' => 'form-control mousetrap',
                                        'id' => 'kode_kab',
                                        'placeholder' => 'Nama Provinsi',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('kabupaten', 'Kabupaten') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    {!! Form::select('kabupaten', [], null, [
                                        'class' => 'form-control mousetrap',
                                        'id' => 'kode_kab',
                                        'placeholder' => 'Nama Kabupaten',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('kecamatan', 'Kecamatan') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    {!! Form::select('kecamatan', [], null, [
                                        'class' => 'form-control mousetrap',
                                        'id' => 'kode_kab',
                                        'placeholder' => 'Nama Kecamatan',
                                        'required',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('kode_pos', 'Kode Pos') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-home"></i>
                                    </span>
                                    {!! Form::text('kode_pos', '', [
                                        'class' => 'form-control ',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script></script>
@endsection
