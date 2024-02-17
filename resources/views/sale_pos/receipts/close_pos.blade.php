<div class="row">
	<div class="col-xs-12 text-center">
		<table class="" cellpadding="0" style="margin-left:5px; margin-right:5px; margin-bottom:5px;font-size:9px; width: 95%;">
			<tr>
				<td colspan="2" style="font-size:12px; text-align: center; font-weight: bold">@if(!empty($receipt_details->display_name)){{strtoupper($receipt_details->display_name)}}@endif
				</td>
			</tr>
			<tr >
				<td colspan="2"style="font-size:12px; text-align: center;"><small style="margin-bottom: 5px;" class="text-center">
				{!! $receipt_details->address !!}
				</small></td>
			</tr>
			<tr >
				<td colspan="2"style="font-size:12px; text-align: center;"><small style="margin-bottom: 5px;" class="text-center">
				{!! $receipt_details->contact !!}
				</small></td>
			</tr>
			<tr>
				<td style="font-size:12px; font-weight: bold">TUTUP MESIN KASIR</td>
			</tr>
		</table>
	</div>

	<div class="col-xs-12">
		
		<table class="" cellpadding="0" style="margin-right:5px;margin-left:5px;font-size:11px; width: 95%;  border-top: 1px dashed black;">
			<tr>
				<td style="padding : 3px"> Kasir : {{auth()->user()->first_name." ".auth()->user()->last_name}}</td>
				<td style="padding : 3px">Tgl Buka : {{ $dt_cls->tgl_buka }}</td>
				<td style="padding : 3px">Tgl Tutup : {{ $dt_cls->tgl_tutup }}</td>
			</tr>		
			{{-- <tr>
				<td style="padding-bottom: 1px"><p>{{ $dt_cls->closing_note }}</p></td>
			</tr> --}}
		</table>
	</div>

	<div class="col-xs-12">
		@php
			$mt_uang = session('currency');
			// $cek = 22222;
			// dd($mt_uang['symbol']);
		@endphp
		<table class="" cellpadding="0" style="margin-right:5px;margin-left:5px;font-size:11px; width: 95%; border-top: 1px dashed black; border-bottom: 1px dashed black;">
			<tr>
				<td>Uang Tunai Di Tangan (Buka Kasir)</td>
				<td>:</td>
				<td style="align=right">{{$mt_uang['symbol'].'. '. @num_format($dt_cls->cash_in_hand)}}</td>
			</tr>
			<tr>
				<td>Pembayaran Kas</td>
				<td>:</td>
				<td>{{$mt_uang['symbol'].'. '. @num_format($dt_cls->total_cash + $dt_cls->total_cash_refund)}}</td>
			</tr>
			<tr>
				<td>Pembayaran Cek</td>
				<td>:</td>
				<td>{{$mt_uang['symbol'].'. '. @num_format($dt_cls->total_cheque + $dt_cls->total_cheque_refund)}}</td>
			</tr>
			<tr>
				<td>Pembayaran Kartu Debit/Kredit</td>
				<td>:</td>
				{{-- <td>{{number_format(round($dt_cls->total_card + $dt_cls->total_card_refund))}}</td> --}}
				<td>{{$mt_uang['symbol'].'. '.@num_format($dt_cls->total_card + $dt_cls->total_card_refund)}}</td>
			</tr>
			<tr>
				<td style="font-weight: bold">Total Penjualan</td>
				<td style="font-weight: bold">:</td>
				{{-- <td>{{$dt_cls->total_sale + $dt_cls->total_refund}}</td> --}}
				<td style="font-weight: bold"><span>{{$mt_uang['symbol'].'. '.@num_format($dt_cls->total_sale + $dt_cls->total_refund)}}</span></td>
			</tr>
			<tr>
				<td style="font-weight: bold">Total Refund</td>
				<td style="font-weight: bold">:</td>
				<td style="font-weight: bold">{{$mt_uang['symbol'].'. '.@num_format($dt_cls->total_refund)}}</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td style="font-style: italic">
					<small>
					@if($dt_cls->total_cash_refund != 0)
					  Cash: <span>{{$mt_uang['symbol'].'. '. @num_format($dt_cls->total_cash_refund) }}</span><br>
					@endif
					@if($dt_cls->total_cheque_refund != 0) 
					  Cheque: <span>{{$mt_uang['symbol'].'. '. @num_format($dt_cls->total_cheque_refund) }}</span><br>
					@endif
					@if($dt_cls->total_card_refund != 0) 
					  Card: <span>{{$mt_uang['symbol'].'. '. @num_format($dt_cls->total_card_refund) }}</span><br> 
					@endif
					</small>
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold">Total Uang Tunai</td>
				<td style="font-weight: bold">:</td>
				<td style="font-weight: bold">{{$mt_uang['symbol'].'. '.@num_format($dt_cls->cash_in_hand + $dt_cls->total_cash)}}</td>
			</tr>
		</table>
	</div>
	<div class="col-xs-12">
		@php
			$closing_amount = str_replace(',', '', $dt_cls->closing_amount);
		@endphp
		<table class="" cellpadding="0" style="margin-right:5px;margin-left:5px;font-size:11px; width: 95%;  border-bottom: 1px dashed black;">
			<div class="col-sm-4">
				<tr>
					<td style="padding: 3px; font-weight: bold; border-left: 1px dashed black;">Total Uang Tunai Disetor</td>
					{{-- <td style="font-weight: bold">:</td> --}}
					<td style="padding: 3px; font-weight: bold; ">Total Slip Kartu</td>
					<td style="padding: 3px; font-weight: bold; border-right: 1px dashed black;">Total Cek</td>
				</tr>
				<tr>
					<td style="padding: 3px; font-weight: bold; border-left: 1px dashed black;">
						{{ $mt_uang['symbol'].'. '.@num_format($closing_amount) }}
					</td>
					<td style="padding: 3px; font-weight: bold; ">
						{{ $dt_cls->total_card_slips }}
					</td>
					<td style="padding: 3px; font-weight: bold; border-right: 1px dashed black;">
						{{ $dt_cls->total_cheques }}
					</td>
					
				</tr>
				
			</div>
			
		</table>
	</div>
	<div class="col-xs-12">
		
		<table class="" cellpadding="0" style="margin-right:5px;margin-left:5px;font-size:11px; width: 95%;  border-bottom: 1px dashed black;">
			<tr>
				<td style="font-weight: bold; padding-top: 4px;">Catatan Penutup</td>
				
			</tr>		
			<tr>
				<td style="padding-bottom: 1px" align="justify"><p>{{ $dt_cls->closing_note }}</p></td>
			</tr>
		</table>
	</div>
	
	<br>
	<table class="" style="font-size:12px; width: 100%; text-align:center; margin-top: 270px; ">
			<thead>
				<tr>
					<th style="text-align:center; ">Kasir</th>
					<th style="text-align:center; "></th>
					<th style="text-align:center; "></th>
					<th style="text-align:center; ">Kepala Divisi</th>
				</tr>
			</thead>
		<tr>
			<td style="padding-top: 50px;"><br>
			</td>
		</tr>
			<tr>
				<td style="text-align:center; font-weight: bold ">({{auth()->user()->first_name." ".auth()->user()->last_name}})</td>
				<td style="text-align:center; font-weight: bold "></td>
				<td style="text-align:center; font-weight: bold "></td>
				<td style="text-align:center; font-weight: bold ">(...........)</td>
			</tr>
	</table>
	
	{{-- <div class="col-xs-12">
		<table class="" cellpadding="0" style="margin:5px;font-size:11px; width: 95%;">
			<tr>
				<td colspan="3" style="text-align: center;">Terima Kasih</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;">Selamat Berbelanja Kembali</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;">@if(!empty($receipt_details->display_name)){{strtoupper($receipt_details->display_name)}}@endif</td>
			</tr>
		</table>
	</div> --}}
</div>