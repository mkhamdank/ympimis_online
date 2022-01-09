<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QaInspectionLevel extends Model
{
    protected $fillable = [
		'inspection_levels', 'lot_size_lower','lot_size_upper','sample_code','sample_size','lot_ok','lot_out','remark','created_by'
	];
}
