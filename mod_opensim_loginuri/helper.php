<?php
/**
 * @module      OpenSim LoginURI (mod_opensim_loginuri)
 * @copyright   Copyright (C) djphil 2016, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');


class ModOpensimLoginuri
{
	public static function getReturnUrl($params, $type)
	{
		$app  = JFactory::getApplication();
		$item = $app->getMenu()->getItem($params->get($type));

		if ($item)
		{
			$url = 'index.php?Itemid=' . $item->id;
		}
		else
		{
			// Stay on the same page
			$url = JUri::getInstance()->toString();
		}

		return base64_encode($url);
	}
}
?>