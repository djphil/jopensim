<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class OpenSimController extends JControllerLegacy {
	var $model;
	public $opensim;
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	public function __construct() {
		parent::__construct();
	}

	public function display($cachable = false, $urlparams = false) {
		$view	= JFactory::getApplication()->input->get('view');
//		if($view == "auth") {
//			$format = JFactory::getApplication()->input->get('format');
//			$tmpl	= JFactory::getApplication()->input->get('tmpl');
//			if($format != "xml" || $tmpl != "component") {
//				$this->setRedirect('index.php?option=com_opensim&view=auth&format=xml&tmpl=component');
//				return false;
//			} else {
//				parent::display($view);
//			}
//		} else {
//			parent::display($view);
//		}
		parent::display($view);
	}

	public function getOpensim() {
		return $this->opensim;
	}
}
