<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerauth extends OpenSimController {
	public function __construct() {
		parent::__construct();
//		$view = $this->getView('auth', 'xml');
	}

	public function confirmHG() {
		$this->hgconfirmresponse	= JFactory::getApplication()->input->get('confirmage');
		if($this->hgconfirmresponse == JText::_('JNO')) {
			$response = "no";
		} else {
			$hguuid = JFactory::getApplication()->input->get('hguser');
			$model = $this->getModel('auth');
			$model->confirmAgeHG($hguuid);
			$response = "yes";
		}
		$redirect = "index.php?option=com_opensim&view=auth&task=confirmresponse&response=".$response;
		$this->setRedirect($redirect);
//		$debug = var_export($_REQUEST,TRUE);
//		echo "<pre>\n";
//		print_r($debug);
//		echo "</pre>\n";
//		exit;
	}
}
?>
