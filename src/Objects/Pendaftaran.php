<?php

namespace App\Objects;

use App\Objects\BaseModel;

class Pendaftaran extends BaseModel
{
	protected $table = 'pendaftaran';
	protected $with = ['tahun_ajaran', 'pmb'];

	public static $date_fields = ['tanggal_mulai', 'tanggal_berakhir'];
	protected $casts = [
	    'tanggal_mulai' => 'datetime:d/m/Y',
	    'tanggal_berakhir' => 'datetime:d/m/Y',
	];

	public $status_enum = [
		"process" => "Process",
		"open" => "Open",
		"closed" => "Closed",
	];
	
	public $selection_fields = ['status'];
	protected $appends = ['status_label'];

	public static $relation = [
		['name' => 'tahun_ajaran', 'is_selection' => true, 'skip' => true]
	];

	public function getStatusLabelAttribute() {
		$status_enum = $this->status_enum;
		$label = null;

		if ($this->status) {
			$label = $status_enum[$this->status];
		}

		return $label;
	}

	public function tahun_ajaran()
	{
		return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
	}

	public function pmb()
	{
		return $this->hasMany(Pmb::class, 'pendaftaran_id', 'id');
	}
	
	// public function update(array $attributes = [], array $options = [])
	// {
	// 	if (array_key_exists('status', $attributes)) {
	// 		if ($attributes['status'] == 'open') {
	// 			$attributes['tanggal_mulai'] = date('d/m/Y');
	// 		}

	// 		if ($attributes['status'] == 'closed') {
	// 			$attributes['tanggal_berakhir'] = date('d/m/Y');
	// 		}
	// 	}

	// 	return parent::update($attributes, $options);
	// }

	public function terbitkanNIM()
	{
		$pmbData = $this->pmb->sortBy('orang.nama');
		foreach ($pmbData as $key => $value) {
            $penerbitan_nim_obj = self::getModelByName('penerbitan_nim');
            $penerbitan_nim = $penerbitan_nim_obj->where('pmb_id', $value->id)->first();

            if (!empty($penerbitan_nim) && $penerbitan_nim->status != 'terbit') {
                $penerbitan_nim->update(['status' => 'terbit']);
            }
        }
	}
}