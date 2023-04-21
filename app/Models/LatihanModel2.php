<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatihanModel2 extends Model
{
    use HasFactory;
    protected $table = 'penjualan_detail';//nama tabel
    protected $primaryKey = 'id';
    protected $fillable = ['no_invoice', 'nama_barang','qty','harga'];
    public $timestamps = false;
}
