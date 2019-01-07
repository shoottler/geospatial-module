<?php
/**
 * Created by PhpStorm.
 * User: angelformica
 * Date: 2018-11-29
 * Time: 15:10
 */

namespace Modules\GeoSpatial\Http\Controllers\Zone;

use App\Http\Controllers\Controller;
use App\Http\Resources\ZoneCollection;
use App\Http\Resources\ZoneResource;
use App\Models\Company\Company;
use Illuminate\Http\Request;
use Modules\GeoSpatial\Entities\Area;
use Modules\GeoSpatial\Entities\Zone;

class ZoneController extends Controller {

	/**
	 * List the zones of the given company
	 * @param Request $request
	 *
	 * @return ZoneCollection
	 */
	public function index(Request $request){
		$company = Company::findOrFail($request->query('company_id'));
		return new ZoneCollection($company->zones);
	}

	/**
	 * Create the zone for the given service area
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store(Request $request){
		$request->validate([
			'area_id' => 'required',
			'name' => 'required|max:255',
			'boundaries' => 'required'
		]);
		$data = $request->all();
		$wkt = '';
		$coordinatesCount = count($data['boundaries']);
		foreach ( $data['boundaries'] as $k => $polygon ) {
			if (isset($polygon['wktPolygon'])) {
				$wkt .= $polygon['wktPolygon'] . ($k == $coordinatesCount - 1 ? '':',') ;
			}
		}
		$zone = new Zone($data);
		$zone->parseGeometryToMultiPolygon('POLYGON('.$wkt.')');
		$area = Area::findOrFail($request->get('area_id'));
		$area->zones()->save($zone);
		return response()->json([
			'zone' => $zone,
			'message' => 'Zone created successfully'
		],200);
	}

	/**
	 * Update the zone with new data
	 * @param Request $request
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(Request $request, $id) {
		$request->validate([
			'area_id' => 'required',
			'name' => 'required|max:255',
		]);
		$data = $request->all();
		$zone = Zone::findOrFail($id);
		$zone->name = $request->get('name');
		$zone->parseBoundaries($data['boundaries']);
		$area = Area::findOrFail($request->get('area_id'));
		$area->zones()->save($zone);
		return response()->json([
			'data' => $zone,
			'message' => 'Zone updated successfully',
		],200);
	}

	/**
	 * Show the requested zone
	 * @param $id
	 *
	 * @return ZoneResource
	 */
	public function show($id){
		return new ZoneResource(Zone::findOrFail($id));
	}

	/**
	 * Delete a zone
	 * @param $id
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy($id){
		$zone = Zone::findOrFail($id);
		$zone->delete();
		return response()->json(null,204);
	}
}