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
            <h3 class="box-title">@lang( 'lang_v1.all_price_quantity_group' )</h3>
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal"
                    data-href="{{action('PriceQuantityGroupController@create')}}" data-container=".view_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="price_quantity_group_table">
                    <thead>
                        <tr>
                            <th>@lang( 'lang_v1.amount' )</th>
                            <th>@lang( 'lang_v1.description' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
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

        //price_quantity_group_table
        var price_quantity_group_table = $('#price_quantity_group_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/price-quantity-group',
            columnDefs: [{
                "targets": 2,
                "orderable": false,
                "searchable": false
            }]
        });

        $(document).on('submit', 'form#price_quantity_group_form', function (e) {
            e.preventDefault();
            var data = $(this).serialize();

            $.ajax({
                method: "POST",
                url: $(this).attr("action"),
                dataType: "json",
                data: data,
                success: function (result) {
                    if (result.success == true) {
                        $('div.view_modal').modal('hide');
                        toastr.success(result.msg);
                        price_quantity_group_table.ajax.reload();
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
                                price_quantity_group_table.ajax.reload();
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