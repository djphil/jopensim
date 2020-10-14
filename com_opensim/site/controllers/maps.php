<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class OpenSimControllermaps extends OpenSimController {
	public function __construct() {
		parent::__construct();
		$this->model	= $this->getModel('maps');
		$layout			= JFactory::getApplication()->input->get('layout','','method','string');
		$view			= $this->getView( 'maps', 'html' );
		$view->setModel($this->getModel('regions'),true);
		$view->setModel($this->getModel('showcase'),true);	// to get data for classified markers
		$view->setModel($this->getModel('events'),true);	// to get data for event markers
		$view->setModel($this->model);
		$view->setLayout($layout);
	}
}
?>
