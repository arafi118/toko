@extends('layouts.app')
@section('title', __('lang_v1.price_quantity_group'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.price_quantity_group' )
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang( 'lang_v1.list_price_quantity_group' ) {{ $product->name }}</h3>
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal"
                    data-href="{{action('VariationGroupQuantityController@create')}}?var_id={{ $variation->id }}"
                    data-container=".view_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="variation_group_quantity">
                    <thead>
                        <tr>
                            <th>Paket Ke</th>
                            <th>@lang( 'lang_v1.amount' )</th>
                            <th>@lang( 'lang_v1.selling_one_price_inc_tax' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>1</td>
                            <td>
                                <span class="display_currency" data-currency_symbol=true>
                                    {{ $variation->sell_price_inc_tax }}
                                </span>
                            </td>
                            <td>Harga normal</td>
                        </tr>
                        @foreach ($variation_group_quantity as $vg)
                        <tr>
                            <td>{{ $loop->iteration+1 }}</td>
                            <td>{{ $vg->amount }}</td>
                            <td>
                                <span class="display_currency" data-currency_symbol=true>
                                    {{ $vg->price_inc_tax }}
                                </span>
                            </td>
                            <td>
                                <button data-href="{{action('VariationGroupQuantityController@edit',[$vg->id])}}"
                                    class="btn btn-xs btn-primary btn-modal" data-container=".view_modal">
                                    <i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")
                                </button>
                                &nbsp;
                                <button data-href="{{action('VariationGroupQuantityController@destroy',[$vg->id])}}"
                                    class="btn btn-xs btn-danger delete_spg_button">
                                    <i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade brands_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function () {

        //variation_group_quantity
        var variation_group_quantity = $('#variation_group_quantity').DataTable({
            // processing: true,
            // serverSide: true,
            // ajax: '/variation-quantity?id={{ $product->id }}',
            columnDefs: [{
                "targets": 2,
                "orderable": false,
                "searchable": false
            }]
        });

        $(document).on('submit', 'form#variation_group_quantity_form', function (e) {
            e.preventDefault();
            var data = $(this).serialize();

            $.ajax({
                method: $(this).find('input[name=_method]').val() || 'POST',
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function (result) {
                    if (result.success == true) {
                        $('div.view_modal').modal('hide');
                        toastr.success(result.msg);
                        window.location.href = location.href
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });

        $(document).on('click', 'button.delete_spg_button', function () {
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function (result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                window.location.href = location.href
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });

    });
</script>
@endsection