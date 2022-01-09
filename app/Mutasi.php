<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $table = "mutasi_depts";

    protected $fillable = [
		'status', 'mutasi_nama', 'mutasi_nik','mutasi_bagian','mutasi_jabatan1','mutasi_rekomendasi','mutasi_ke_bagian', 'ke_section', 'mutasi_jabatan', 'mutasi_tanggal', 'mutasi_alasan', 'chief_or_foreman', 'manager', 'gm', 'director'
	];

    public function mutasi()
	{
		return $this->belongsTo('App\Mutasi', 'mutasi_nik', 'mutasi_nik')->withTrashed();
	}
}
