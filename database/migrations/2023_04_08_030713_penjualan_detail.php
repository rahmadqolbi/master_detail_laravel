<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PenjualanDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no_invoice')->index();
            $table->string('nama_barang');
            $table->integer('qty');
            $table->integer('harga');
            $table->timestamps();
    
            // $table->foreign('no_invoice')->references('no_invoice')->on('penjualan');
    
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
    }
}
