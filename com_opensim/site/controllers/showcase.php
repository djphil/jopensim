<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllerShowcase extends OpenSimController {
	public function __construct() {
		parent::__construct();
		$this->model = $this->getModel('showcase');
		$view			= $this->getView( 'showcase', 'html' );
		$view->setModel($this->getModel('maps'),true);
		$view->setModel($this->getModel('regions'),true);
		$view->setModel($this->model);
	}
}
?>
