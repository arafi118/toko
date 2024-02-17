<!-- <script type="text/javascript">
		
	$(document).ready(function() {
		
		//apabila terjadi event onchange terhadap object <select id=id_barang>
		  $("#id_barang").change(function(){
			var id_barang = $("#id_barang").val();
			$.ajax({
				url: "modul/jurnal/ambilunit.php",
				data: "id_barang="+id_barang,
				cache: false,
				success: function(msg){
					//jika data sukses diambil dari server kita tampilkan
					//di <select id=unit>
					$("#unit").html(msg);
				}
			});
		  });
		
				//SIMPAN JURNAL	
				$("#SimpanPenghapusanInventaris").click(function() {
                    var tgl_transaksi		= $('#tgl_transaksi').val();
                    var id_barang			= $('#id_barang').val();
                    var tgl_pakai			= $('#tgl_pakai').val();
                    var rekening_debit		= $('#rekening_debit').val();
                    var rekening_kredit		= $('#rekening_kredit').val();
                    var status				= $('#status').val();
                    var unit				= $('#unit').val();
                    var nominal				= $('#nominal').val();
                    var unitm				= $('#unitm').val();
                    var nominalm			= $('#nominalm').val();
                    var nominaljual			= $('#nominaljual').val();
					
					if(tgl_transaksi < tgl_pakai){
						$("#DilarangTransaksi").load('modul/jurnal_angsuran/dilarang_angsuran.php');
						return false;
							}
					
					if(unit.length==0 || unit==0){
						$("#Dilarang").load('modul/jurnal/dilarang.php');
						document.getElementById("SimpanPenghapusanInventaris").disabled = false;
						$("#unit").focus();
						return false;
							}
												
                    $.ajax({
                        type: "POST",
                        url: "modul/jurnal/simpan_hapus_inventaris.php",
                        data: 	"tgl_transaksi="+tgl_transaksi+
								"&id_barang="+id_barang+
								"&rekening_debit="+rekening_debit+
								"&rekening_kredit="+rekening_kredit+
								"&status="+status+
								"&unit="+unit+
								"&nominal="+nominal+
								"&unitm="+unitm+
								"&nominalm="+nominalm+
								"&nominaljual="+nominaljual,
						success: function(data) {
							$("#TampilJurnal").load('modul/jurnal/tampil_jurnal.php');
							$("#TampilBuku").load('modul/jurnal/tampil_buku.php');
							document.getElementById("SimpanPenghapusanInventaris").disabled = true;

                        }
                    });

                });
				
				
            });
			
        </script> 

<div class="form-group namabarang2">
                                <label class="control-label col-sm-4">Nama Barang</label>
                                <div class="col-sm-8"><select id="id_barang"  name="id_barang" onchange="changeValue(this.value)"
                                                data-placeholder="-- Pilih Nama Inventaris yang akan dihapus --"
                                                data-width="auto"
                                                data-minimum-results-for-search="10"
                                                tabindex="-1"
												class="select2 form-control">
												<option value="">-- Pilih Inventaris Terhapus --</option>
												<?php 
													$jsArray = "var prdName = new Array();\n"; 
													$view=mysql_query("select * from inventaris_$_SESSION[kd_kab] WHERE lokasi='$_SESSION[lokasi]' AND status='Baik' "); //List Inventaris
														while($row=mysql_fetch_array($view)){
														echo "<option value='$row[id]'>$row[nama_barang] ($row[unit] unit x ".number_format($row['harsat']).") | NB: ".number_format(nilaibuku("$tgl_kondisi-$row[id]"))."</option>";
															$jsArray .= "prdName['" . $row['id'] . "'] = {unit:'" . addslashes($row['unit']) . "',nominalm:'" . addslashes(nilaibuku("$tgl_kondisi-$row[id]")) . "',nominal:'".addslashes(number_format(nilaibuku("$tgl_kondisi-$row[id]"),0,',','.'))."'};\n";
														}
														?>
										</select> </div>
                            </div>
							<div class="form-group">
                                <label class="control-label col-sm-4">Alasan Hapus</label>
                                <div class="col-sm-8"><select id="status"  name="status" onchange="changeStatus(this.value)"
                                                data-placeholder="-- Pilih Alasan Penghapusan --"
                                                data-width="auto"
                                                data-minimum-results-for-search="10"
                                                tabindex="-1"
												class="select2 form-control">
												<option value="Rusak">Rusak</option>
												<option value="Hilang">Hilang</option>
												<option value="Hapus">Hapus</option>
												<option value="Dijual">Dijual</option>
										</select> </div>
                            </div>
							<div class="form-group">
                                    <label class="col-sm-4 control-label" for="default-select">&Sigma; Unit Terhapus</label>
                                    <div class="col-sm-8">
                                        <select id="unit"  name="unit" onchange="changeUnit(this.value)"
                                                data-placeholder="-- Unit Barang Terhapus --"
                                                data-width="auto"
                                                data-minimum-results-for-search="10"
                                                tabindex="-1"
												class="select2 form-control">
												<option value=""></option>
										</select>
									</div>
                                </div>
							<div class="form-group">
                                <label class="control-label col-sm-4">Nilai Buku <span class="pull-right">Rp.</span></label>
                                <div class="col-sm-8"><input type="text" name="nominal" id="nominal" class="form-control input-lg" 
									required="required" autocomplete="off" placeholder="-- Nominal / Nilai Buku --" onkeydown="return numbersonly(this, event);" onkeyup="javascript:tandaPemisahTitik(this);"> </div>
                            </div>
							<div class="form-group" id="jual">
                                <label class="control-label col-sm-4">Harga Jual <span class="pull-right">Rp.</span></label>
                                <div class="col-sm-8"><input type="text" name="nominaljual" id="nominaljual" class="form-control input-lg" 
									required="required" autocomplete="off" placeholder="-- Harga Jual --" onkeyup="javascript:tandaPemisahTitik(this);"> </div>
                            </div>
							
							<input type="hidden" name="unitm" id="unitm"/>
							<input type="hidden" name="nominalm" id="nominalm"/>
							
							<script type="text/javascript">  
							document.getElementById("jual").style.visibility ="hidden";
								<?php echo $jsArray; ?>
								function changeValue(id){
									document.getElementById('unitm').value = prdName[id].unit;
									document.getElementById('nominalm').value = prdName[id].nominalm; //Nilai Buku Total
									document.getElementById('nominal').value = prdName[id].nominal; //Nilai Buku unit terhapus
									document.getElementById('nominaljual').value = prdName[id].nominal; //Harga Jual
									document.getElementById("SimpanPenghapusanInventaris").disabled = false;
									document.getElementById("nominal").disabled = false;
								};
								
								function changeUnit(id) {
									var unitm = $("#unitm").val();
									var unit = document.getElementById("unit").value;
									var nominalm = $("#nominalm").val();
									var sumnominal=unit/unitm * nominalm;
										document.getElementById("nominal").value=tandaPemisahTitik(sumnominal); //Nilai Buku unit terhapus
										document.getElementById("nominaljual").value=tandaPemisahTitik(sumnominal); //Harga Jual
										document.getElementById("nominal").disabled = true;
										$("#nominaljual").focus();
								};
								
								function changeStatus(id) {
									var status = document.getElementById("status").value;
										if(status=="Dijual"){
											document.getElementById("jual").style.visibility ="visible";
											$("#nominaljual").focus();
										}else{
											document.getElementById("jual").style.visibility ="hidden"; 
										}
								};
							</script>

							
							<input type="hidden" name="tgl_transaksi" id="tgl_transaksi" value="<?php echo $tgl_transaksi;?>"/>
							<input type="hidden" name="rekening_debit" id="rekening_debit" value="<?php echo $rekening_debit;?>"/>
							<input type="hidden" name="rekening_kredit" id="rekening_kredit" value="<?php echo $rekening_kredit;?>"/>
							<input type="hidden" name="tgl_pakai" id="tgl_pakai" value="<?php echo $tgl_pakai;?>"/>
							<div id="Dilarang"></div>
							<div id="DilarangTransaksi"></div>
							<button class="btn btn-primary btn-rounded pull-right mb" id="SimpanPenghapusanInventaris"><i class="fa fa-paper-plane"></i> <br>POSTING PENGHAPUSAN</button>
                            </div>							 -->