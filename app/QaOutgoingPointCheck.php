<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaOutgoingPointCheck extends Model
{
    protected $fillable = [
		'material_number',
		'material_description',
		'material_alias',
		'vendor',
		'vendor_shortname',
		'part',
		'point_check_level',
		'point_check_type',
		'point_check_index',
		'point_check_name',
		'point_check_standard',
		'point_check_upper',
		'point_check_lower',
		'remark',
		'created_by',
	];
}
