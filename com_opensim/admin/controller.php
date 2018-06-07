<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
jimport('joomla.application.component.controllerform');
jimport('joomla.application.component.helper');

class OpenSimController extends JControllerForm {
	public $model;
	public $opensim;
	public $debugreminded = FALSE;
	/**
	 * Method to display the view
	 *
	 * @access    public
	 */
	public function __construct() {
		parent::__construct();

		$jopensim_debug_reminder	= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_reminder');

		if($jopensim_debug_reminder && $this->debugreminded === FALSE) {
			$this->debugreminder();
			$this->debugreminded = TRUE; // Avoids double reminders
		}

	}

	public function display($cachable = false, $urlparams = array()) {
		jimport('joomla.application.component.helper');
		$addons = JComponentHelper::getParams('com_opensim')->get('addons');
		$view	= JFactory::getApplication()->input->get('view');
		if(!$view) $view = "opensim";
		$this->jopensimmenue($addons,$view);
		parent::display();
	}

	public function getOpensim() {
		return $this->opensim;
	}

	public function jopensimmenue($addon,$vName) {
//		$view				= JFactory::getApplication()->input->get('view');
		$addons_groups		= JComponentHelper::getParams('com_opensim')->get('addons_groups');
		$addons_search		= JComponentHelper::getParams('com_opensim')->get('addons_search');
		$addons_currency	= JComponentHelper::getParams('com_opensim')->get('addons_currency');
		$loginscreen_layout	= JComponentHelper::getParams('com_opensim')->get('loginscreen_layout');

		JHtmlSidebar::addEntry(JText::_('JOPENSIM_OVERVIEW'), 'index.php?option=com_opensim',$vName == 'opensim');
		if($loginscreen_layout == "custom") {
			JHtmlSidebar::addEntry(JText::_('JOPENSIM_LOGINSCREEN'), 'index.php?option=com_opensim&view=loginscreen',$vName == 'loginscreen');
		}
		JHtmlSidebar::addEntry(JText::_('JOPENSIM_MAPS'), 'index.php?option=com_opensim&view=maps',$vName == 'maps');
		JHtmlSidebar::addEntry(JText::_('JOPENSIM_USER'), 'index.php?option=com_opensim&view=user',$vName == 'user');
		if($addons_groups == 1) 	JHtmlSidebar::addEntry(JText::_('JOPENSIM_GROUPS'), 'index.php?option=com_opensim&view=groups',$vName == 'groups');
		if($addons_search == 1) 	JHtmlSidebar::addEntry(JText::_('JOPENSIM_SEARCH'), 'index.php?option=com_opensim&view=search',$vName == 'search');
		if($addons_currency == 1) 	JHtmlSidebar::addEntry(JText::_('JOPENSIM_MONEY'), 'index.php?option=com_opensim&view=money',$vName == 'money');
		JHtmlSidebar::addEntry(JText::_('JOPENSIM_MISC'), 'index.php?option=com_opensim&view=misc',$vName == 'misc');
		JHtmlSidebar::addEntry(JText::_('JOPENSIM_ADDONHELP'), 'index.php?option=com_opensim&view=addons',$vName == 'addons');
	}

	public function payMoneyFromBanker() {
		$data		= JFactory::getApplication()->input->request->getArray();;
		$returnto	= JFactory::getApplication()->input->get('returnto');
		$redirect	= "index.php?option=com_opensim&view=".$returnto;
		if($data['jopensim_money_payment'] == 0) {
			if($returnto == "user") {
				$type = "warning";
				$message = JText::_('JOPENSIM_WARNING_ZEROPAYMENT');
			} else {
				$redirect .= "&task=moneyUser";
				$type = "";
				$message = "";
			}
			$this->setRedirect($redirect,$message,$type);
		} else {
			$parameter['senderID']		= $data['jopensim_money_bankeraccount'];
			$parameter['receiverID']	= $data['jopensim_money_userid'];
			$parameter['amount']		= $data['jopensim_money_payment'];
			$parameter['description']	= $data['jopensim_money_paytext'];

			$model = $this->getModel('money');
			$message = $model->TransferMoney($parameter);
			if(!$model->opensimRelationReverse($data['jopensim_money_userid'])) { // We dont have any user relation yet, raise a warning!
				jimport( 'joomla.error.error' );
				JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_MONEY_NORELATIONWARNING')." (".$data['jopensim_money_userid'].")",'notice');
			}
			if(array_key_exists("success",$message) && $message['success'] == TRUE) {
				$this->setRedirect($redirect,JText::_('JOPENSIM_MONEY_TRANSFER_OK'));
			} else {
				$type		= "warning";
				$message	= JText::_('JOPENSIM_MONEY_TRANSFER_ERROR');
				$this->setRedirect($redirect,$message,$type);
			}
		}
	}

	public function truncatelog() {
		$jinput	= JFactory::getApplication()->input;
		$path	= $jinput->get('path', '');
		$return	= $jinput->get('return', '');
		$log	= $jinput->get('log', '');
		$logfile= "";
		$type	= "";
		switch($log) {
			case "interface":
				$logfile = "interface.log";
			break;
			case "currency":
				$logfile = "currency.log";
			break;
		}
		if($logfile) {
			$logfolder = JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR;
			if(is_file($logfolder.$logfile)) {
				$fh = fopen($logfolder.$logfile,"w");
				$success = ftruncate($fh,0);
				fclose($fh);
				if($success === TRUE) {
					$message = JText::sprintf('JOPENSIM_LOGFILE_TRUNCATED_OK',$logfile);
				} else {
					$type = "warning";
					$message = JText::sprintf('JOPENSIM_LOGFILE_TRUNCATED_ERROR1',$logfolder.$logfile);
				}
			} else {
				$type = "warning";
				$message = JText::sprintf('JOPENSIM_LOGFILE_TRUNCATED_ERROR2',$logfolder.$logfile);
			}
		} else {
			$type = "warning";
			$message = JText::_('JOPENSIM_LOGFILE_TRUNCATED_ERROR');
		}
		$redirect = "index.php?option=com_config&view=component&component=com_opensim&path=".$path."&return=".$return;
		$this->setRedirect($redirect,$message,$type);
	}

	public function debugreminder() {
		$jopensim_debug_access		= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_access');
		$jopensim_debug_input		= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_input');
		$jopensim_debug_profile		= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_profile');
		$jopensim_debug_groups		= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_groups');
		$jopensim_debug_search		= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_search');
		$jopensim_debug_messages	= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_messages');
		$jopensim_debug_currency	= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_currency');
		$jopensim_debug_other		= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_other');
		$jopensim_debug_settings	= JComponentHelper::getParams('com_opensim')->get('jopensim_debug_settings');

		if(($jopensim_debug_access + $jopensim_debug_input + $jopensim_debug_profile + $jopensim_debug_groups + $jopensim_debug_search + $jopensim_debug_messages + $jopensim_debug_currency + $jopensim_debug_other + $jopensim_debug_settings) > 0) {
			JFactory::getApplication()->enqueueMessage(JText::_('JOPENSIM_DEBUG_WARNING'), 'warning');
		}
	}
}
