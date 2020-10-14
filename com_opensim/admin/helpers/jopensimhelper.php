<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access

defined('_JEXEC') or die('Restricted access');

class jOpenSimHelper {
	public static function getActions($categoryId = 0) {
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($categoryId)) {
			$assetName = 'com_opensim';
			$level = 'component';
		} else {
			$assetName = 'com_opensim.category.' . (int)$categoryId;
			$level = 'category';
		}

		$actions = JAccess::getActions('com_opensim', $level);

		foreach ($actions as $action) {
			$result -> set($action -> name, $user -> authorise($action -> name, $assetName));
		}

		return $result;
	}
}