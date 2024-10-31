<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_pembelian_detail';
    protected $guarded = [];

    public function produk(){
        return $this->hasOne(Produk::class, 'id_produk','id_produk');
    }
}
