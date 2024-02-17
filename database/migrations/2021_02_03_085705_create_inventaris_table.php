<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventarisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->index();
            $table->string('nama_barang', 200);
            $table->date('tgl_beli');
            $table->integer('unit');
            $table->integer('harsat');
            $table->integer('umur_ekonomis');
            $table->integer('jenis')->default(1);
            $table->string('status')->default('Baik');
            $table->date('tgl_validasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventaris');
    }
}
