<?php
/**
 * @plugin      jOpenSimRegister (plg_jopensimregister)
 * @copyright   Copyright (C) 2016 FoTo50 http://www.jopensim.com
 * @license     GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');

class plgUserJopensimregisterInstallerScript {
	public function preflight($type,$parent) {
		// abort if jOpenSim is not installed
		$jopensim = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim";
		if($type == "install" && !is_dir($jopensim)) {
			$application = JFactory::getApplication();
			$application->enqueueMessage("This plugin requires jOpenSim! Please download and install the latest version at <a href='http:/"."/www.jopensim.com' target='_blank'>www.jopensim.com</a>!", 'error');
			return FALSE;
		}
	}
}
?>