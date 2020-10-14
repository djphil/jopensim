<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class jOpenSimPayPalModeljOpenSimPayPal extends JModelLegacy {
	public		$_moneySettingsData;
	protected	$params			= array();
	public		$filename		= "jopensimpaypal.php";
	public		$view			= "jopensimpaypal";
	public		$paypalhost;

	public function __construct() {
		parent::__construct();
		$this->getMoneySettings();
		$this->getParams();
	}

	public function getParams() {
		$params = JComponentHelper::getParams('com_jopensimpaypal');

		//paypal params
		$this->params['sandboxmode']		= $params->get('sandboxmode');
		if($this->params['sandboxmode']) {
			$this->params['paypal_account']	= $params->get('paypal_sandbox');
			$this->params['paypal_action']	= "https://www.sandbox.paypal.com/cgi-bin/webscr";
			$this->paypalhost				= "Host: www.sandbox.paypal.com";
			$this->paypalmode				= "sandbox";
		} else {
			$this->params['paypal_account']	= $params->get('paypal_account');
			$this->params['paypal_action']	= "https://www.paypal.com/cgi-bin/webscr";
			$this->paypalhost				= "Host: www.paypal.com";
			$this->paypalmode				= "production";
		}
		$this->params['paypal_nossl']	= $params->get('paypal_nossl');

		// currency params
		$this->params['currency']			= $params->get('currency');
		$this->params['currencyratebuy']	= $params->get('currencyratebuy');
		$this->params['currencyratesell']	= $params->get('currencyratesell');
		$this->params['transactionfee']		= $params->get('transactionfee');
		if($this->params['transactionfee']) {
			$this->params['hasfee'] 		= TRUE;
		} else {
			$this->params['hasfee'] 		= FALSE;
		}
		$this->params['transactionfeetype']	= $params->get('transactionfeetype');
		$this->params['minbuy']				= $params->get('minbuy');
		$this->params['maxbuy']				= $params->get('maxbuy');
		$this->params['minsell']			= $params->get('minsell');
		$this->params['maxsell']			= $params->get('maxsell');
		$this->params['transactiontext']	= $params->get('transactiontext');

		if(!is_numeric($this->params['minbuy']))	$this->params['minbuy']		= 0;
		if(!is_numeric($this->params['maxbuy']))	$this->params['maxbuy']		= 0;
		if(!is_numeric($this->params['minsell']))	$this->params['minsell']	= 0;
		if(!is_numeric($this->params['maxsell']))	$this->params['maxsell']	= 0;

		// text params purchase form
		$this->params['pre_message']		= $params->get('pre_message');
		$this->params['post_message']		= $params->get('post_message');
		$this->params['success_message']	= $params->get('success_message');
		$this->params['cancel_message']		= $params->get('cancel_message');

		// text params payout form
		$this->params['sell_pre_message']	= $params->get('sell_pre_message');
		$this->params['sell_post_message']	= $params->get('sell_post_message');

		// userlimit params
		$this->params['userlimit_day']		= $params->get('userlimit_day');
		$this->params['userlimit_week']		= $params->get('userlimit_week');
		$this->params['userlimit_month']	= $params->get('userlimit_month');

		// notify params
		$this->params['notifytransaction']	= $params->get('notifytransaction');
		$this->params['notifyerror']		= $params->get('notifyerror');
		$this->params['notifywarning']		= $params->get('notifywarning');
		$this->params['notifypayout']		= $params->get('notifypayout');
		$this->params['notifyemail']		= $params->get('notifyemail');
		$this->params['log2file']			= $params->get('log2file');
		$this->params['logpath']			= $params->get('logpath');
	}

	public function getParam($paramname) {
		if($paramname == "all") return $this->params;
		elseif(array_key_exists($paramname,$this->params)) return $this->params[$paramname];
		else return null;
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
			$settings['currencydebug']		= $params->get('jopensim_debug_currency');

			if(!defined("_JOPENSIMMONEYDEBUG")) {
				if($settings['currencydebug'] == "1") define("_JOPENSIMMONEYDEBUG",TRUE);
				else define("_JOPENSIMMONEYDEBUG",FALSE);
			}

			$this->_moneySettingsData = $settings;
		}
		return $this->_moneySettingsData;
	}

	public function getMoneySetting($name) {
		if (empty($this->_moneySettingsData)) $this->getMoneySettings();
		if(array_key_exists($name,$this->_moneySettingsData)) return $this->_moneySettingsData[$name];
		else return null;
	}

	public function jOpenSimSettings() {
		$params = JComponentHelper::getParams('com_opensim');

		$settings['jopensimmoneybanker']			= $params->get('jopensimmoneybanker');
		$settings['jopensimmoney_groupcreation']	= $params->get('jopensimmoney_groupcreation');
		$settings['jopensimmoney_upload']			= $params->get('jopensimmoney_upload');
		$settings['jopensimmoney_startbalance']		= $params->get('jopensimmoney_startbalance');
		$settings['jopensimmoney_groupdividend']	= $params->get('jopensimmoney_groupdividend');
		$settings['jopensimmoney_currencyname']		= $params->get('jopensimmoney_currencyname');
		$settings['jopensimmoney_bankername']		= $params->get('jopensimmoney_bankername');

		$settings['jopensimmoney_sendgridbalancewarning']	= $params->get('jopensimmoney_sendgridbalancewarning');
		$settings['jopensimmoney_warningrecipient']			= $params->get('jopensimmoney_warningrecipient');
		$settings['jopensimmoney_warningsubject']			= $params->get('jopensimmoney_warningsubject');

		return $settings;
	}

	public function getUUID($userid) {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT #__opensim_userrelation.opensimID FROM #__opensim_userrelation WHERE #__opensim_userrelation.joomlaID = '%s'",$userid);
		$db->setQuery($query);
		$uuid = $db->loadResult();
		return $uuid;
	}

	public function getBalance($uuid) {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT #__opensim_moneybalances.balance FROM #__opensim_moneybalances WHERE #__opensim_moneybalances.user = '%s'",$uuid);
		$db->setQuery($query);
		$balance = $db->loadResult();
		return $balance;
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

	public function sendnotify($type,$handler,$msg) {
		$notifyemail	= $this->getParam("notifyemail");
		$failure2file	= $this->getParam("log2file");
		$mailer			= JFactory::getMailer();
		$config			= JFactory::getConfig();
		$sender			= array( 
	    	$config->get('config.mailfrom'),
	    	$config->get('config.fromname'));
	
		$mailer->setSender($sender);
		$mailer->addRecipient($notifyemail);

		switch($type) {
			case 2:
				$mailer->setSubject('jOpenSimPayPal Warning Notification');
			break;
			case 3:
				$mailer->setSubject('jOpenSimPayPal Transaction Notification');
			break;
			case 4:
				$mailer->setSubject('jOpenSimPayPal Payout request Notification');
			break;
			default:
				$mailer->setSubject('jOpenSimPayPal Error Notification');
			break;
		}
		switch($handler) {
			case 1:
				$body = "This is a notification about a successful transaction:\n\n".$msg;
			break;
			case 2:
				$body = "This is a notification about a transaction with warnings:\n\n".$msg;
			break;
			case 3:
				$body = "This is a notification about an error during a transaction:\n\n".$msg;
			break;
			case 4:
				$body = "This is a notification about a payout request:\n\n".$msg;
			break;
			default:
				$body = "Unknown message handler: ".$handler."\n\nmsg: ".$msg;
			break;
		}
		$mailer->setBody($body);
		$send = $mailer->Send();
		if ($send !== TRUE) {
		    if(($failure2file & 1) == 1) $this->simpledebugzeile('Error sending email: '.$send->get('message'));
		}
	}

	public function simpledebugzeile($zeile) {
		$zeit = date("Y-m-d H:i:s");
		$logfile = $this->getlogfile();
		$handle = fopen($logfile,"a+");
		$logzeile = $zeit."\t".$zeile."\n";
		fputs($handle,$logzeile);
		fclose($handle);
	}

	public function debugzeile($zeile,$function = "") {
		if(!$function) $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##########\n";
		else $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##### ".$function." ##########\n";
		$zeile = var_export($zeile,TRUE);
		$logfile = $this->getlogfile();
		$handle = fopen($logfile,"a+");
		$logzeile = $zeit.$zeile."\n\n";
		fputs($handle,$logzeile);
		fclose($handle);
	}

	public function getlogfile() {
		$logpath	= $this->getParam('logpath');
		if(substr($logpath,-1) != DIRECTORY_SEPARATOR) $logpath .= DIRECTORY_SEPARATOR;
		$logfile	= "jopensimpaypal.log";
		return $logpath.$logfile;
	}

	public function __destruct() {
	}
}
?>