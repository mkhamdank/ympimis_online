<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaOutgoingVendorFinal extends Model
{
    protected $fillable = [
    	'so_number',
    	'final_serial_number',
		'serial_number',
		'material_number',
		'material_description',
		'vendor',
		'vendor_shortname',
		'hpl',
		'point_check_id',
		'inspector',
		'qty_check',
		'total_ok',
		'total_ng',
		'ng_ratio',
		'ng_name',
		'ng_qty',
		'product_index',
		'product_result',
		'lot_status',
		'created_by',
	];
}
