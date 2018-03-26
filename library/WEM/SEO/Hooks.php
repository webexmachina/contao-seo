<?php

/**
 * Module SEO for Contao Open Source CMS
 *
 * Copyright (c) 2018 Web ex Machina
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */

namespace WEM\SEO;

use Exception;

use Contao\Environment;
use Contao\FilesModel;

/**
 * Class with functions to handle SEO configuration
 *
 * @author Web ex Machina <https://www.webexmachina.fr>
 */
class Hooks
{
	/**
	 * Handle page rules
	 * @param Object
	 * @param Object
	 * @param Object
	 */
	public function applySEORules($objPage, $objLayout, $objPageRegular)
	{
		try
		{
			$strDomain = Environment::get('base');

			// Get the page title
			if($objPage->pageTitle)
				$strPageTitle = $objPage->pageTitle;
			else
				$strPageTitle = $objPage->title;

			// Get the page description
			if($objPage->description)
				$strPageDescription = str_replace(array("\n", "\r", '"'), array(' ', '', ''), $objPage->description);
			else
				$strPageDescription = '';

			// Check if we have a canonical to apply
			if($objPage->metaCanonical)
				$strCanonical = $objPage->metaCanonical;
			else
				$strCanonical = Environment::get('request');

			// Adjust canonical url depends on few rules
			if($strCanonical == "/")
				$strCanonical = $strDomain;
			else if(strpos($strCanonical, $strDomain) === false)
				$strCanonical = $strDomain.$strCanonical;

			// Check if we have a meta image
			if($objPage->metaImage && $objFile = FilesModel::findByUuid($objPage->metaImage))
				$strMetaImage = $strDomain.$objFile->path;
			elseif($objPage->alias == 'article' && $objNews = \NewsModel::findByIdOrAlias(\Input::get('auto_item'))){
				if($objFile = FilesModel::findByUuid($objNews->singleSRC))
					$strMetaImage = $strDomain.$objFile->path;
			}
			else
				$strMetaImage = $strDomain."files/app/img/banner_1.jpg";

			// Apply Rules
			if($strCanonical)
				$objLayout->head .= sprintf('<link rel="canonical" href="%s" />', $strCanonical);

			// og:title
			if($objPage->overrideOGTags && $objPage->metaOGTitle)
				$objLayout->head .= sprintf('<meta property="og:title" content="%s" />', $objPage->metaOGTitle);
			else
				$objLayout->head .= sprintf('<meta property="og:title" content="%s" />', $strPageTitle);

			// og:type
			if($objPage->overrideOGTags && $objPage->metaOGType)
				$objLayout->head .= sprintf('<meta property="og:type" content="%s" />', $objPage->metaOGType);
			else
				$objLayout->head .= sprintf('<meta property="og:type" content="%s" />', 'website');

			// og:url
			if($objPage->overrideOGTags && $objPage->metaOGUrl)
				$objLayout->head .= sprintf('<meta property="og:url" content="%s" />', $objPage->metaOGUrl);
			else
				$objLayout->head .= sprintf('<meta property="og:url" content="%s" />', $strCanonical);

			// og:description
			if($objPage->overrideOGTags && $objPage->metaOGDescription)
				$objLayout->head .= sprintf('<meta property="og:description" content="%s" />', str_replace(array("\n", "\r", '"'), array(' ', '', ''), $objPage->metaOGDescription));
			else
				$objLayout->head .= sprintf('<meta property="og:description" content="%s" />', $strPageDescription);

			// og:image
			if($objPage->overrideOGTags && $objPage->metaOGImage && $objFile = FilesModel::findByUuid($objPage->metaOGImage))
				$objLayout->head .= sprintf('<meta property="og:image" content="%s" />', $strDomain.$objFile->path);
			else if($strMetaImage)
				$objLayout->head .= sprintf('<meta property="og:image" content="%s" />', $strMetaImage);

			// twitter:card
			if($objPage->overrideTwitterTags && $objPage->metaTwitterCard)
				$objLayout->head .= sprintf('<meta name="twitter:card" content="%s" />', $objPage->metaTwitterCard);
			else
				$objLayout->head .= sprintf('<meta name="twitter:card" content="%s" />', 'summary');

			// twitter:site
			if($objPage->overrideTwitterTags && $objPage->metaTwitterSite)
				$objLayout->head .= sprintf('<meta name="twitter:site" content="%s" />', $objPage->metaTwitterSite);

			// twitter:title
			if($objPage->overrideTwitterTags && $objPage->metaTwitterTitle)
				$objLayout->head .= sprintf('<meta name="twitter:title" content="%s" />', $objPage->metaTwitterTitle);
			else
				$objLayout->head .= sprintf('<meta name="twitter:title" content="%s" />', $strPageTitle);

			// twitter:description
			if($objPage->overrideTwitterTags && $objPage->metaTwitterDescription)
				$objLayout->head .= sprintf('<meta name="twitter:description" content="%s" />', str_replace(array("\n", "\r", '"'), array(' ', '', ''), $objPage->metaTwitterDescription));
			else
				$objLayout->head .= sprintf('<meta name="twitter:description" content="%s" />', $strPageDescription);

			// twitter:image
			if($objPage->overrideOGTags && $objPage->metaTwitterImage && $objFile = FilesModel::findByUuid($objPage->metaTwitterImage))
				$objLayout->head .= sprintf('<meta name="twitter:image" content="%s" />', $strDomain.$objFile->path);
			else if($strMetaImage)
				$objLayout->head .= sprintf('<meta name="twitter:image" content="%s" />', $strMetaImage);

			// twitter:image:alt
			if($objPage->overrideTwitterTags && $objPage->metaTwitterImageAlt)
				$objLayout->head .= sprintf('<meta name="twitter:image:alt" content="%s" />', $objPage->metaTwitterImageAlt);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}
}