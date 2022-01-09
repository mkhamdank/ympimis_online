<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaOutgoingSerialNumber extends Model
{
    protected $fillable = [
		'date',
		'serial_number',
		'material_number',
		'qty',
		'vendor',
		'vendor_shortname',
		'part_name',
		'status',
		'remark',
		'created_by',

	];
}
