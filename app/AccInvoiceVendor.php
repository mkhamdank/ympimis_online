<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccInvoiceVendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tanggal','supplier_code','supplier_name', 'pic', 'kwitansi', 'tagihan', 'surat_jalan', 'faktur_pajak','purchase_order','note','currency','amount','ppn','amount_total','file','status','created_by','updated_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by')->withTrashed();
    }

}
