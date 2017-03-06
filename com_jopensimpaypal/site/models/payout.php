<?php
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html, see LICENSE.php
 *
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2013 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

require_once(JPATH_COMPONENT.DS.'models'.DS.'jopensimpaypal.php');

class jOpenSimPayPalModelpayout extends jOpenSimPayPalModeljOpenSimPayPal {
	public $filename		= "payout.php";
	public $view			= "payout";

	public function __construct() {
		parent::__construct();
	}

	public function savePayoutRequest() {
		$user = JFactory::getUser();
		$opensimid = $this->getUUID($user->id);
		if(!$opensimid) return $this->returnError(null,__LINE__);
		$requestedPayout	= intval(trim(JRequest::getVar('jopensimpaypal_amount')));
		$receiverPayPal		= trim(JRequest::getVar('paypaluser'));
		$payoutrequest		= floatval(trim(JRequest::getVar('payoutvalue')));
		$amount_iwc			= JRequest::getVar('jopensimpaypal_amount');
		$currency_rlc		= JRequest::getVar('currency_code');
		$xchangerate		= JRequest::getVar('jopensimpaypal_xchangerate');
		$transactionfee		= JRequest::getVar('transactionfee');
		$transactionfeetype	= JRequest::getVar('transactionfeetype');
		$requestip			= $_SERVER['REMOTE_ADDR'];
		$payoutValue = $this->getPayoutValue($requestedPayout);

		// lets first check a few things
		if($requestedPayout == 0)								return $this->returnError(null,__LINE__);
		$sufficient = $this->sufficientBalance($opensimid,$requestedPayout);
		if($sufficient !== TRUE)								return $this->returnError(1);
		if($this->checkVar($receiverPayPal,"email") !== TRUE)	return $this->returnError(2);
		if($payoutValue < 0)									return $this->returnError(3);
		if($payoutValue != $payoutrequest)						return $this->returnError(4);

		$date		= JFactory::getDate();
		$db			= JFactory::getDbo();
		$datum		= $db->quote($date->toSQL(),false);
		$query		= $db->getQuery(true);
		$columns	= array('joomlaid','opensimid','amount_iwc','amount_rlc','currency_rlc','xchangerate','transactionfee','transactionfeetype','paypalaccount','requesttime','requestip','status');
		$values		= array($user->id,$db->quote($opensimid),$db->quote($amount_iwc),$payoutValue,$db->quote($currency_rlc),$db->quote($xchangerate),$db->quote($transactionfee),$db->quote($transactionfeetype),$db->quote($receiverPayPal),$datum,$db->quote($requestip),0);
		$query
		    ->insert($db->quoteName('#__jopensimpaypal_payoutrequests'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));
		$db->setQuery($query);
		$result = $db->query();
		if($result === TRUE) {
			$payoutnotify = $this->getParam("notifypayout");
			if($payoutnotify == 1) {
				$message = "Name: ".$user->name."\nUsername: ".$user->username."\nJoomlaID: ".$user->id."\nOpenSimUID: ".$opensimid."\n".$currency_rlc.": ".$payoutValue."\n".$this->getMoneySetting('name').": ".$amount_iwc;
				$this->sendnotify(4,4,$message);
			}
		}
		return $result;
	}

	private function sufficientBalance($opensimid,$request) {
		$balance = $this->getBalance($opensimid);
		if($balance < $request) return FALSE;
		else return TRUE;
	}

	public function returnError($errnum = 0,$line = null) {
		$retval['success'] = FALSE;
		$retval['error'] = $errnum;
		switch($errnum) {
			case 1:
				$retval['errormsg'] = JText::sprintf('COM_JOPENSIMPAYPAL_BALANCE_SELLERROR',COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME);
			break;
			case 2:
				$retval['errormsg'] = JText::_('COM_JOPENSIMPAYPAL_PAYPALERROR');
			break;
			case 3:
				$retval['errormsg'] = JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_NEGATIVE',COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME);
			break;
			case 4:
				$retval['errormsg'] = JText::_('COM_JOPENSIMPAYPAL_UNKNOWNERROR')." (!=)";
			break;
			default:
				$retval['errormsg'] = JText::_('COM_JOPENSIMPAYPAL_UNKNOWNERROR')." (Line: ".$line.")";
			break;
		}
		return $retval;
	}

	public function getPayoutValue($requestvalue) {
		$rlc = $requestvalue / $this->params['currencyratesell'];
		if($this->params['hasfee'] === TRUE) {
			$feetype = $this->params['transactionfeetype'];
			switch($feetype) {
				case "percent":
					$fee = $rlc / 100 * $this->params['transactionfee'];
				break;
				case "amount":
					$fee = $this->params['transactionfee'];
				break;
				default:
					return FALSE;
				break;
			}
		} else {
			$fee = 0;
		}
		$payoutvalue = $rlc - $fee;
		return round($payoutvalue,2);
	}

	public function __destruct() {
	}
}
?>