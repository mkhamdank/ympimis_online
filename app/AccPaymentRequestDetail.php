<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccPaymentRequestDetail extends Model
{
    protected $fillable = [
		'id_payment','invoice','amount','ppn','typepph','amount_service','pph','net_payment','created_by','created_name'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'created_by')->withTrashed();
	}
}
