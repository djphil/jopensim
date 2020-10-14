<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
/*jimport('joomla.application.component.model');*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelAddons extends OpenSimModelOpenSim {
	var $_settingsData;
	var $filename = "addons.php";
	var $view = "addons";
	var $_os_db;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
	}
}
?>
