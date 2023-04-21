<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatihanModel extends Model
{
    use HasFactory;
    protected $table = 'penjualan';//nama tabel
    protected $primaryKey = 'id';
    protected $fillable = ['no_invoice', 'nama_pelanggan','tgl_pembelian','jenis_kelamin','saldo',];
    public $timestamps = false;
}
