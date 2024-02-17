<style>
    .flex {
        display: flex;
    }

    .justify-center {
        justify-content: center;
    }

    .justify-between {
        justify-content: space-between;
    }

    .justify-end {
        justify-content: flex-end;
    }

    .align-items-center {
        align-items: center;
    }

    .flex-column {
        flex-direction: column;
    }

    .flex-row {
        flex-direction: row;
    }

    .flex-wrap {
        flex-wrap: wrap;
    }

    .text-xs {
        font-size: 12pxrem;
    }

    .text-sm {
        font-size: 14px;
    }

    .text-base {
        font-size: 16px;
    }

    .text-lg {
        font-size: 20px;
    }

    .text-xl {
        font-size: 20px;
    }

    .text-2xl {
        font-size: 24px;
    }

    .font-medium {
        font-weight: 500;
    }

    .font-bold {
        font-weight: 700;
    }

    .gap-xs {
        gap: 4px;
    }

    .gap-sm {
        gap: 8px;
    }

    .gap {
        gap: 12px;
    }

    .gap_lg {
        gap: 16px;
    }

    .gap_xl {
        gap: 20px;
    }
</style>
<div class="box box-success">
    <div class="box-header with-border">
        <h4>@lang('lang_v1.payment')</h4>
    </div>
    <div class="box-body">
        <div class="row">
            <div id="payment_rows_div">
                @php
                // dd($payment_lines);
                @endphp
                @foreach($payment_lines as $payment_line)

                @if($payment_line['is_return'] == 1)
                @php
                $change_return = $payment_line;
                @endphp

                @continue
                @endif

                @include('sale_pos.partials.payment_row', ['removable' => !$loop->first, 'row_index' =>
                $loop->index, 'payment_line' => $payment_line])
                @endforeach
            </div>
            <input type="hidden" id="payment_row_index" value="{{count($payment_lines)}}">
        </div>
    </div>
    <div class="box-footer flex justify-end gap">
        @if(empty($edit))
        <button type="button" class="btn btn-danger d-block" id="pos-cancel">@lang('sale.cancel')</button>
        <button type="button" class="btn btn-warning d-block" id="pos-draft">Tahan</button>
        @else
        <button type="button" class="btn btn-danger hide" id="pos-delete">@lang('messages.delete')</button>
        @endif
        <button type="submit" class="btn btn-primary" id="pos-save">@lang('sale.finalize_payment')</button>
    </div>
</div>