<?php

namespace Modules\GeoSpatial\Entities;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
	use SpatialTrait;

	/**
	 * The attributes that are mass assignable
	 * @var array
	 */
	protected $fillable = [
		'area_id','name','boundaries'
	];

	/**
	 * The service area where this zone is located.
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function area(){
		return $this->belongsTo(Area::class);
	}

	/**
	 * The attributes that are spatial fields.
	 * @var array
	 */
	protected $spatialFields = [
		'boundaries'
	];

	/**
	 * Parses the geometry to a multipolygon from well-known text.
	 * @param mixed $geometry
	 * @return \Grimzy\LaravelMysqlSpatial\Types\MultiPolygon
	 */
	public function parseGeometryToMultiPolygon($geometry): MultiPolygon
	{
		return $this->boundaries = MultiPolygon::fromWKT($geometry);
	}

	/**
	 * Parse boundaries from frontend
	 *
	 * @param $boundaries
	 */
	public function parseBoundaries($boundaries) {
		if (! empty($boundaries)) {
			$wkt = '';
			$coordinatesCount = count($boundaries);
			foreach ( $boundaries as $k => $polygon ) {
				if (isset($polygon['wktPolygon'])) {
					$wkt .= $polygon['wktPolygon'] . ($k == $coordinatesCount - 1 ? '':',') ;
				}
			}
			$this->parseGeometryToMultiPolygon('POLYGON('.$wkt.')');
		}
	}
}
