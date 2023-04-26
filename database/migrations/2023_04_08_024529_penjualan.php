<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Penjualan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('penjualan', function(Blueprint $table){
        $table->increments('id');
        $table->string('no_invoice')->unique();
        $table->string('nama_pelanggan');
        $table->date('tgl_pembelian');
        $table->string('jenis_kelamin');
        $table->float('saldo');
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
        //
        Schema::dropIfExists('penjualan');
    }
}
