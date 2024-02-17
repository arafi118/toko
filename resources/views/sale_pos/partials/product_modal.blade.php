<div class="modal fade" tabindex="-1" role="dialog" id="modal_search_product">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('lang_v1.list_product')</h4>
            </div>
            <div class="modal-body">
                @include('sale_pos.partials.product_list_box')
            </div>
            <div class="modal-footer" style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->