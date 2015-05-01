<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   customers
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL
 * @copyright 2014
 */


/**
 * Namespace
 */
namespace portfolio;

/**
 * Class PortfolioProjectCategoryModel
 *
 * @copyright  2014
 * @author     Hamid Abbaszadeh
 * @package    Devtools
 */
class PortfolioProjectCategoryModel extends \Model
{

	/**
	 * Name of the table
	 * @var string
	 */
	protected static $strTable = 'tl_portfolio_project_category';

	public static function findPublishedById($intId)
	{
		$t = static::$strTable;

		$arrColumns = array("$t.id=?");

		return static::findBy($arrColumns, $intId);
	}

}
