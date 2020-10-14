<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelOpenSimCp extends OpenSimModelOpenSim {
	var $_settingsData;
	var $filename	= "opensimcp.php";
	var $view		= "opensimcp";
	var $_os_db;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
	}

	public function frontendCSS() {
		return JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."opensim.css";
	}

	public function saveCSS() {
		$cssfile = $this->frontendCSS();
		if(!is_writable($cssfile)) {
			$retval['type']		= "error";
			$retval['message']	= JText::_('JOPENSIM_CSSSAVE_ERROR');
		} else {
			$csscontent = trim(JFactory::getApplication()->input->get('csscontent'));
			file_put_contents($cssfile, $csscontent);
			$retval['type']		= "message";
			$retval['message']	= JText::_('JOPENSIM_CSSSAVE_OK');
		}
		return $retval;
	}
}
?>
