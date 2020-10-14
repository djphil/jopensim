<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
 


class jOpenSimPayPalController extends JControllerLegacy {
	var $model;
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	public function __construct() {
		parent::__construct();
		if(!defined('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME')) {
			$this->model	= $this->getModel('jopensimpaypal');
			$money			= $this->model->getMoneySettings();
			$params			= $this->model->getParams();
			$noadd			= JFactory::getApplication()->input->get('noadd');
			if($params->get('sandboxmode') && $noadd == 0) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_SANDBOXMODE'),'notice');
			}
			if(array_key_exists("name",$money) && $money['name']) define('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME',$money['name']);
		}
	}

	public function display($cachable = false, $urlparams = array()) {
		$view	= JFactory::getApplication()->input->get('view');
		$this->jopensimpaypalmenue($view);
		parent::display();
	}

	public function changepayout() {
		$result		= $this->model->changePayout();
		$redirect	= "index.php?option=com_jopensimpaypal&view=payout&noadd=1";
		$this->setRedirect($redirect,$result['message'],$result['type']);
	}

	public function jopensimpaypalmenue($vName) {
		$view				= JFactory::getApplication()->input->get('view');
		JHtmlSidebar::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_OVERVIEW'), 'index.php?option=com_jopensimpaypal',$vName == 'jopensimpaypal');
		JHtmlSidebar::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_TRANSACTIONS'), 'index.php?option=com_jopensimpaypal&view=transactions',$vName == 'transactions');
		JHtmlSidebar::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_PAYOUT'), 'index.php?option=com_jopensimpaypal&view=payout',$vName == 'payout');
	}

	public function jopensimmenue() {
		$view	= JFactory::getApplication()->input->get('view');
		
		switch($view) {
			case "transactions":
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_OVERVIEW'), 'index.php?option=com_jopensimpaypal&view=jopensimpaypal');
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_TRANSACTIONS'), 'index.php?option=com_jopensimpaypal&view=transactions',true);
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_PAYOUT'), 'index.php?option=com_jopensimpaypal&view=payout');
//				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_REGIONRENT'), 'index.php?option=com_jopensimpaypal&view=regionrent');
			break;
			case "payout":
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_OVERVIEW'), 'index.php?option=com_jopensimpaypal&view=jopensimpaypal');
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_TRANSACTIONS'), 'index.php?option=com_jopensimpaypal&view=transactions');
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_PAYOUT'), 'index.php?option=com_jopensimpaypal&view=payout',true);
//				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_REGIONRENT'), 'index.php?option=com_jopensimpaypal&view=regionrent');
			break;
			case "regionrent":
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_OVERVIEW'), 'index.php?option=com_jopensimpaypal&view=jopensimpaypal');
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_TRANSACTIONS'), 'index.php?option=com_jopensimpaypal&view=transactions');
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_PAYOUT'), 'index.php?option=com_jopensimpaypal&view=payout');
//				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_REGIONRENT'), 'index.php?option=com_jopensimpaypal&view=regionrent',true);
			break;
			default:
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_OVERVIEW'), 'index.php?option=com_jopensimpaypal&view=jopensimpaypal',true);
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_TRANSACTIONS'), 'index.php?option=com_jopensimpaypal&view=transactions');
				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_PAYOUT'), 'index.php?option=com_jopensimpaypal&view=payout');
//				JSubMenuHelper::addEntry(JText::_('COM_JOPENSIMPAYPAL_MENU_REGIONRENT'), 'index.php?option=com_jopensimpaypal&view=regionrent');
			break;
		}
	}

}
