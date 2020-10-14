<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Hello World Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */

class jOpenSimPayPalController extends JControllerLegacy {
	var $model;
	public $opensim;
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	public function __construct() {
		parent::__construct();
		if(!defined('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME')) {
			$model			= $this->getModel('jopensimpaypal');
			$money			= $model->getMoneySettings();
			$sandboxmode	= $model->getParam("sandboxmode");
			$noadd			= JFactory::getApplication()->input->get('noadd');
			if($sandboxmode && $noadd == 0) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_SANDBOXMODE'),'warning');
			}
			if($money['name']) define('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME',$money['name']);
		}
	}

	public function removerequest() {
		$model		= $this->getModel('transactionlist');
		$result		= $model->revokePayOut();
		if($result) {
			$message	= JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REVOKE_OK');
		} else {
			$message	= JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REVOKE_ERROR');
		}
		$type		= "message";
		$itemid		= JFactory::getApplication()->input->get('Itemid');
		$redirect	= "index.php?option=com_jopensimpaypal&view=transactionlist&noadd=1&Itemid=".$itemid;
		$this->setRedirect($redirect,$message,$type);
	}

	public function display($cachable = false, $urlparams = false) {
		$view	= JFactory::getApplication()->input->get('view');
		parent::display($view);
	}
}
