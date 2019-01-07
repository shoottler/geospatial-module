<?php
/**
 * Created by PhpStorm.
 * User: angelformica
 * Date: 2018-11-29
 * Time: 13:31
 */

namespace Modules\GeoSpatial\Entities;

use App\Models\BaseModel;

abstract class BaseArea extends BaseModel
{
	/**
	 * The table in the DB
	 * @var string
	 */
	protected $table = 'areas';

	/*
	 * The attributes that are mass assignable
	 */
	protected $fillable = [
			'company_id','name','country','city','IATA','ICAO','FAA'
		];

	/**
	 * The persisted attributes
	 * @var array
	 */
	protected static $persisted = [
			'company_id','name','country','city','IATA','ICAO','FAA'
		];

	/**
	 * The attributes for eloquence to search in
	 * @var array
	 */
	public $searchableColumns = [
		'name','country','city','IATA','ICAO','FAA'
	];

	/**
	 * @var bool
	 * Spatie Log Activity options to log changes to the model
	 */
	protected static $logFillable = true;

	/**
	 * Spatie Log Activity options to log only changed fields
	 * @var bool
	 */
	protected static $logOnlyDirty = true;

	/**
	 * The classes that inherit from this class
	 * @var array
	 */
	protected static $singleTableSubclasses = [
		Airport::class,TrainStation::class,Port::class
	];
}