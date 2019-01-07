<?php
/**
 * Created by PhpStorm.
 * User: angelformica
 * Date: 2018-11-29
 * Time: 13:32
 */

namespace Modules\GeoSpatial\Entities;


class Airport extends Area
{
	/**
	 * The type of class
	 * @var string
	 */
	protected static $singleTableType = Airport::class;
}