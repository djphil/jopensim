<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'xmlrpc'.DIRECTORY_SEPARATOR.'xmlrpc.inc'); // get the xmlrpc library from FlotSam
//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'xmlrpc'.DIRECTORY_SEPARATOR.'xmlrpcs.inc');
//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'functions_currency.php');

class jOpenSimPayPalModeljOpenSimPayPal extends JModelList {
	public $_moneySettingsData;
	public $filename		= "jopensimpaypal.php";
	public $view			= "jopensimpaypal";

	public function __construct() {
		parent::__construct();
		$this->getMoneySettings();
	}

	public function getParams() {
		$params = JComponentHelper::getParams('com_jopensimpaypal');
		return $params;
	}

	public function checkParams() {
		$params = $this->getParams();

		$transactionfee		= $params->get('transactionfee');
		$paypaltype			= $params->get('sandboxmode');
		$currencyratebuy	= $params->get('currencyratebuy');
		$currencyratesell	= $params->get('currencyratesell');
		$userlimit_day		= $params->get('userlimit_day');
		$userlimit_week		= $params->get('userlimit_week');
		$userlimit_month	= $params->get('userlimit_month');

		$notifytransaction	= $params->get('notifytransaction');
		$notifyerror		= $params->get('notifyerror');
		$notifywarning		= $params->get('notifywarning');
		$notifyemail		= $params->get('notifyemail');
		$log2file			= $params->get('log2file');

		$logpath			= $params->get('logpath');

		if($paypaltype) {
			$paypalaccount	= $params->get('paypal_sandbox');
		} else {
			$paypalaccount	= $params->get('paypal_account');
		}

		if($this->checkVar($paypalaccount,"email") === FALSE) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_PAYPALACCOUNT_INVALID'),'warning');
		}

		if(!$currencyratebuy) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_XCHANGEBUY_MISSING'),'warning');
		} elseif($this->checkVar($currencyratebuy,"number") === FALSE) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_XCHANGEBUY_INVALID'),'warning');
		}

		if($currencyratesell && $this->checkVar($currencyratesell,"number") === FALSE) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_XCHANGESELL_INVALID'),'warning');
		}

		if($transactionfee && $this->checkVar($transactionfee,"number") === FALSE) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_TRANSACTIONFEE_INVALID'),'warning');
		}

		if($userlimit_day && $this->checkVar($userlimit_day,"number") === FALSE) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_USERLIMITDAY_INVALID'),'warning');
		}

		if($userlimit_week && $this->checkVar($userlimit_week,"number") === FALSE) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_USERLIMITWEEK_INVALID'),'warning');
		}

		if($userlimit_month && $this->checkVar($userlimit_month,"number") === FALSE) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_USERLIMITMONTH_INVALID'),'warning');
		}

		if($notifytransaction == 1 || $notifyerror == 1 || $notifywarning == 1) {
			if($this->checkVar($notifyemail,"email") === FALSE) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JOPENSIMPAYPAL_ERROR_NOTIFYEMAIL_INVALID'),'warning');
			}
		}
		if($log2file > 0 && !$logpath) {
			$recommendpath = dirname($_SERVER["DOCUMENT_ROOT"]."../")."/joslogs/";
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_NOLOGPATH'),$recommendpath,'warning');
		} elseif($log2file > 0 && !is_dir($logpath)) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_INVALIDLOGPATH',$logpath),'warning');
		} elseif($log2file > 0 && !is_writable($logpath)) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_NOWRITELOGPATH',$logpath),'warning');
		}
	}

	public function getMoneySettings() {
		// Lets load the data if it doesn't already exist
		if (empty($this->_moneySettingsData)) {
			$settings = array();

			$params = JComponentHelper::getParams('com_opensim');

			$settings['name']				= $params->get('jopensimmoney_currencyname');
			$settings['bankerUID']			= $params->get('jopensimmoneybanker');
			$settings['groupCharge']		= $params->get('jopensimmoney_groupcreation');
			$settings['uploadCharge']		= $params->get('jopensimmoney_upload');
			$settings['groupMinDividend']	= $params->get('jopensimmoney_groupdividend');
			$settings['startBalance']		= $params->get('jopensimmoney_startbalance');

			$this->_moneySettingsData = $settings;
		}
		return $this->_moneySettingsData;
	}

	public function checkVar($var,$type) {
		switch($type) {
			case "email":
				if(!filter_var($var, FILTER_VALIDATE_EMAIL)) return FALSE;
				else return TRUE;
			break;
			case "number":
				if(!is_numeric($var)) return FALSE;
				else return TRUE;
			break;
			default:
				return FALSE;
			break;
		}
	}

	public function transactionpaypal($joomlaid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('DISTINCT(#__jopensimpaypal_transactions.payer_email)');
		$query->from('#__jopensimpaypal_transactions');
		$query->order('#__jopensimpaypal_transactions.payer_email');
		$query->where('#__jopensimpaypal_transactions.joomlaid = '.(int)$joomlaid);
		$db->setQuery((string)$query);
		$paypal = $db->loadColumn();
		return $paypal;
	}

	public function getOpenSimNames($items) {
		if(!is_array($items)) return FALSE;
		foreach($items AS $key => $item) {
			$items[$key]->opensimname = $this->getOpenSimName($item->opensimid);
		}
		return $items;
	}

	public function getOpenSimName($uuid) {
		$opensim	= $this->opensimclass();
		$name		= $opensim->getUserName($uuid,'full');
//		error_log($name);
		return $name;
	}

	public function getOpenSimData($uuid) {
		$opensim = $this->opensimclass();
		$data = $opensim->getUserData($uuid);
		return $data;
	}

	public function opensimclass() {
//		$app = JFactory::getApplication('site');
//		$params =  & $app->getParams('com_opensim');
		$params = JComponentHelper::getParams('com_opensim');

		$osdbhost		= $params->get('opensim_dbhost');
		$osdbuser		= $params->get('opensim_dbuser');
		$osdbpasswd		= $params->get('opensim_dbpasswd');
		$osdbname		= $params->get('opensim_dbname');
		$osdbport		= $params->get('opensim_dbport');
		$osgriddbhost	= $params->get('opensimgrid_dbhost');
		$osgriddbuser	= $params->get('opensimgrid_dbuser');
		$osgriddbpasswd	= $params->get('opensimgrid_dbpasswd');
		$osgriddbname	= $params->get('opensimgrid_dbname');
		$osgriddbport	= $params->get('opensimgrid_dbport');
		$opensim = new opensim($osgriddbhost,$osgriddbuser,$osgriddbpasswd,$osgriddbname,$osgriddbport);
		return $opensim;
	}

	public function newTransactions($sincedate) {
		$date	= JFactory::getDate($sincedate);
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__jopensimpaypal_transactions');
		$query->where('#__jopensimpaypal_transactions.transactiontime > '.$db->quote($date->toSQL(),FALSE));
		$db->setQuery((string)$query);
		$newtransactions = $db->loadResult();
		return $newtransactions;
	}

	public function newPayouts($sincedate) {
		$date	= JFactory::getDate($sincedate);
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__jopensimpaypal_payoutrequests');
		$query->where('#__jopensimpaypal_payoutrequests.requesttime > '.$db->quote($date->toSQL(),FALSE));
		$db->setQuery((string)$query);
		$newpayouts = $db->loadResult();
		return $newpayouts;
	}

	public function unsolvedPayouts() {
		$date	= JFactory::getDate();
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('COUNT(#__jopensimpaypal_payoutrequests.id) AS anzahl');
		$query->select('#__jopensimpaypal_payoutrequests.`status` AS filter');
		$query->select('ELT(#__jopensimpaypal_payoutrequests.`status`+2,"COM_JOPENSIMPAYPAL_PAYOUTSTATUS_PENDING","COM_JOPENSIMPAYPAL_PAYOUTSTATUS_NEW","COM_JOPENSIMPAYPAL_PAYOUTSTATUS_APPROVED") AS payoutstatus');
		$query->from('#__jopensimpaypal_payoutrequests');
		$query->where('FIND_IN_SET(#__jopensimpaypal_payoutrequests.`status`,"-1,0,1")');
		$query->group('#__jopensimpaypal_payoutrequests.`status`');
		$db->setQuery((string)$query);
		$unsolvedpayouts = $db->loadObjectList();
		return $unsolvedpayouts;
	}

	public function changePayout() {
		$payoutid			= JFactory::getApplication()->input->get('payoutid');
		$newstatus			= JFactory::getApplication()->input->get('newstatus');
		$remarks			= JFactory::getApplication()->input->get('remarks');
		$transfer			= FALSE;

		$object				= new stdClass();
		$object->id			= $payoutid;
		$object->status		= $newstatus;
		$object->remarks	= $remarks;
		$object->lastchange	= date("Y-m-d H:i:s");

		$db					= JFactory::getDbo();

		if($newstatus == 2) { // status to finished ... lets check if we need to transfer money or it was done already
			$query = $db->getQuery(true);
			$query->select(array('opensimid','amount_iwc','transferred'));
			$query->from('#__jopensimpaypal_payoutrequests');
			$query->where(sprintf("id = '%d'",$payoutid));
			$db->setQuery($query);
			$result = $db->loadObject();
			if($result->transferred == 0) { // we need to transfer the money back to bank
				$banker = $this->_moneySettingsData['bankerUID'];
				$parameter['senderID']		= $result->opensimid;
				$parameter['receiverID']	= $banker;
				$parameter['amount']		= $result->amount_iwc;
				$parameter['description']	= JText::_('COM_JOPENSIMPAYPAL_PAYOUT_TRANSACTIONTEXT');
				$transferresult = TransferMoney($parameter);
				if(!is_array($transferresult) || !array_key_exists("success",$transferresult) || $transferresult['success'] !== TRUE) { // something went wrong :(
					$retval['type']		= "error";
					$retval['message']	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTCHANGE_ERROR');
					return $retval;
				}
				$object->transferred	= 1;
			}
		}

		$retval	= array();

		try {
		    // Update their details in the users table using id as the primary key.
			$result = $db->updateObject('#__jopensimpaypal_payoutrequests', $object, 'id');
			$retval['type']		= "message";
			$retval['message']	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTCHANGE_OK');
		} catch (Exception $e) {
			// catch the error.
			$retval['type']		= "error";
			$retval['message']	= JText::_('COM_JOPENSIMPAYPAL_PAYOUTCHANGE_ERROR');
		}
		return $retval;
	}

	public function __destruct() {
	}
}
?>