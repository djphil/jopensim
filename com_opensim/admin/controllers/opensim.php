<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerOpenSim extends OpenSimController {

	public $model;

	public function __construct() {
		parent::__construct();
		$this->model	= $this->getModel('opensim');
	}

	public function savecss() {
		$retval	= $this->model->saveCSS();
		$redirect	= "index.php?option=com_opensim&view=opensim";
		$type		= $retval['type'];
		$message	= $retval['message'];
		$this->setRedirect($redirect,$message,$type);
	}

	public function applycss() {
		$retval	= $this->model->saveCSS();
		$redirect	= "index.php?option=com_opensim&view=opensim&task=editcss";
		$type		= $retval['type'];
		$message	= $retval['message'];
		$this->setRedirect($redirect,$message,$type);
	}

	public function cancel() {
		$redirect	= "index.php?option=com_opensim&view=opensim";
		$this->setRedirect($redirect);
	}
}
?>