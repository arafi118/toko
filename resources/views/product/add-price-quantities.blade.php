@extends('layouts.app')
@section('title', __('lang_v1.update_n_add_quantity'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.update_n_add_quantity')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('ProductController@savePriceQuantity'), 'method' => 'post', 'id' =>
    'selling_price_form' ]) !!}
    {!! Form::hidden('product_id', $product->id); !!}
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <div class="box-header">
                    <h3 class="box-title">@lang('sale.product'): {{$product->name}} ({{$product->sku}})</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-condensed table-bordered table-th-green text-center table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                @lang('lang_v1.amount')
                                            </th>
                                            <th>@lang('lang_v1.selling_price_inc_tax')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variations as $variation)
                                        <tr>
                                            <td>
                                                <span>
                                                    1
                                                </span>
                                            </td>
                                            <td>
                                                <span class="display_currency" data-currency_symbol="true">
                                                    {{$variation->sell_price_inc_tax}}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            {!! Form::hidden('submit_type', 'save', ['id' => 'submit_type']); !!}
            <div class="text-center">
                <div class="btn-group">
                    <button id="opening_stock_button" @if($product->enable_stock == 0) disabled @endif type="submit"
                        value="submit_n_add_opening_stock" class="btn bg-purple
                        submit_form">@lang('lang_v1.save_n_add_opening_stock')</button>
                    <button type="submit" value="save_n_add_another"
                        class="btn bg-maroon submit_form">@lang('lang_v1.save_n_add_another')</button>
                    <button type="submit" value="submit"
                        class="btn btn-primary submit_form">@lang('messages.save')</button>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
</section>
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function () {
        $('button.submit_form').click(function (e) {
            e.preventDefault();
            $('input#submit_type').val($(this).attr('value'));

            if ($("form#selling_price_form").valid()) {
                $("form#selling_price_form").submit();
            }
        });
    });
</script>
@endsection