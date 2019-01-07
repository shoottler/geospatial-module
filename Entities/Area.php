<?php

namespace Modules\GeoSpatial\Entities;

use App\Models\Company\Company;

class Area extends BaseArea
{
	/**
	 * This area's company
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function company(){
		return $this->belongsTo(Company::class);
	}

	/**
	 * This area's zones
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function zones(){
		return $this->hasMany(Zone::class,'area_id');
	}
}
