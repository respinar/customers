<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package portfolio
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;


/**
 * Class Portfolio
 *
 * Provide methods regarding portfolio archives.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    Portfolio
 */
class Portfolio extends \Frontend
{

	/**
	 * Add portfolio items to the indexer
	 * @param array
	 * @param integer
	 * @param boolean
	 * @return array
	 */
	public function getSearchablePages($arrPages, $intRoot=0, $blnIsSitemap=false)
	{
		$arrRoot = array();

		if ($intRoot > 0)
		{
			$arrRoot = $this->Database->getChildRecords($intRoot, 'tl_page');
		}

		$time = time();
		$arrProcessed = array();

		// Get all client categories
		$objClientCategory = \PortfolioClientCategoryModel::findAll();

		// Walk through each category
		if ($objClientCategory !== null)
		{
			while ($objClientCategory->next())
			{
				// Skip FAQs without target page
				if (!$objClientCategory->jumpTo)
				{
					continue;
				}

				// Skip FAQs outside the root nodes
				if (!empty($arrRoot) && !in_array($objClientCategory->jumpTo, $arrRoot))
				{
					continue;
				}

				// Get the URL of the jumpTo page
				if (!isset($arrProcessed[$objClientCategory->jumpTo]))
				{
					$objParent = \PageModel::findWithDetails($objClientCategory->jumpTo);

					// The target page does not exist
					if ($objParent === null)
					{
						continue;
					}

					// The target page has not been published (see #5520)
					if (!$objParent->published || ($objParent->start != '' && $objParent->start > $time) || ($objParent->stop != '' && $objParent->stop < $time))
					{
						continue;
					}

					// The target page is exempt from the sitemap (see #6418)
					if ($blnIsSitemap && $objParent->sitemap == 'map_never')
					{
						continue;
					}

					// Set the domain (see #6421)
					$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

					// Generate the URL
					$arrProcessed[$objClientCategory->jumpTo] = $domain . $this->generateFrontendUrl($objParent->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);
				}

				$strUrl = $arrProcessed[$objClientCategory->jumpTo];

				// Get the items
				$objClient = \PortfolioClientModel::findPublishedByPid($objClientCategory->id);

				if ($objClient !== null)
				{
					while ($objClient->next())
					{
						$arrPages[] = sprintf($strUrl, (($objClient->alias != '' && !\Config::get('disableAlias')) ? $objClient->alias : $objClient->id));
					}
				}
			}
		}


		// Get all project categories
		$objProjectCategory = \PortfolioProjectCategoryModel::findAll();

		// Walk through each category
		if ($objProjectCategory !== null)
		{
			while ($objProjectCategory->next())
			{
				// Skip FAQs without target page
				if (!$objProjectCategory->jumpTo)
				{
					continue;
				}

				// Skip FAQs outside the root nodes
				if (!empty($arrRoot) && !in_array($objProjectCategory->jumpTo, $arrRoot))
				{
					continue;
				}

				// Get the URL of the jumpTo page
				if (!isset($arrProcessed[$objProjectCategory->jumpTo]))
				{
					$objParent = \PageModel::findWithDetails($objProjectCategory->jumpTo);

					// The target page does not exist
					if ($objParent === null)
					{
						continue;
					}

					// The target page has not been published (see #5520)
					if (!$objParent->published || ($objParent->start != '' && $objParent->start > $time) || ($objParent->stop != '' && $objParent->stop < $time))
					{
						continue;
					}

					// The target page is exempt from the sitemap (see #6418)
					if ($blnIsSitemap && $objParent->sitemap == 'map_never')
					{
						continue;
					}

					// Set the domain (see #6421)
					$domain = ($objParent->rootUseSSL ? 'https://' : 'http://') . ($objParent->domain ?: \Environment::get('host')) . TL_PATH . '/';

					// Generate the URL
					$arrProcessed[$objProjectCategory->jumpTo] = $domain . $this->generateFrontendUrl($objParent->row(), ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/%s' : '/items/%s'), $objParent->language);
				}

				$strUrl = $arrProcessed[$objProjectCategory->jumpTo];

				// Get the items
				$objProject = \PortfolioProjectModel::findPublishedByPid($objProjectCategory->id);

				if ($objProject !== null)
				{
					while ($objProject->next())
					{
						$arrPages[] = sprintf($strUrl, (($objProject->alias != '' && !\Config::get('disableAlias')) ? $objProject->alias : $objProject->id));
					}
				}
			}
		}

		return $arrPages;
	}

}
