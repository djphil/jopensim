<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
use Joomla\CMS\Factory;


require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'jopensimpaypal.php');

class jOpenSimPayPalModelnotify extends jOpenSimPayPalModeljOpenSimPayPal {
	public $filename		= "notify.php";
	public $view			= "notify";
	public $postvals		= array();
	public $imodel			= null;
	public $log2file		= FALSE;
	public $failure2file	= FALSE;
	public $paypaldebug		= TRUE;

	public $curlopt_ssl_verifypeer;
	public $curlopt_ssl_verifyhost;


	public function __construct() {
		parent::__construct();
		if($this->params['paypal_nossl'] == 1) {
			$this->curlopt_ssl_verifypeer = FALSE;
			$this->curlopt_ssl_verifyhost = FALSE;
		} else {
			$this->curlopt_ssl_verifypeer = 1;
			$this->curlopt_ssl_verifyhost = 2;
		}
		$logsetting		= $this->getParam('log2file');
		if(($logsetting & 2) == 2) $this->log2file		= TRUE; // log everything
		if(($logsetting & 1) == 1) $this->failure2file	= TRUE; // log errors and warnings
	}

	public function validate() {
		$jinput			= Factory::getApplication()->input;
		$this->postvals	= $jinput->post->getArray();

		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=' . urlencode('_notify-validate');

		foreach ($this->postvals as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->params['paypal_action']);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->curlopt_ssl_verifypeer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->curlopt_ssl_verifyhost);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($this->paypalhost));
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}

	public function handleResponse($res,$debug = FALSE) {
		if($debug === TRUE) $paymentstatus = "Completed";
		else $paymentstatus	= $this->postvals['payment_status'];
		// check the payment_status is Completed
		if($paymentstatus == "Completed") {
			// check that receiver_email is your Primary PayPal email
			if($debug === TRUE) $business = "paypaltest@jopensim.com";
			else $business	= $this->postvals['business'];
			if($business == $this->params['paypal_account']) {
				$db			= JFactory::getDBO();
				// check that txn_id has not been previously processed
				if($debug === TRUE) $txn_id = "97417185GE1381330";
				else $txn_id		= $db->escape($this->postvals['txn_id']);
				$query		= sprintf("SELECT #__jopensimpaypal_transactions.* FROM #__jopensimpaypal_transactions WHERE #__jopensimpaypal_transactions.txn_id = '%s'",$txn_id);
				$db->setQuery($query);
				$db->query();
				$num_rows	= $db->getNumRows();
				if($num_rows > 0) { // txn_id already existing???
					$result = $db->loadAssocList();
					if($this->failure2file === TRUE) $this->debugzeile($this->postvals,"ERROR: txn_id already existing");
					if($this->params['notifyerror'] == 1) {
						$msg = "ERROR: txn_id already existing!!\n\n".var_export($this->postvals,TRUE);
						$this->sendnotify(1,3,$msg);
					}
				} else {
					// check that payment_amount/payment_currency are correct
					$currency = $this->getParam('currency');
					if($debug === TRUE) $mc_currency = "EUR";
					else $mc_currency = $this->postvals['mc_currency'];
					if($currency != $mc_currency) {
						if($this->failure2file === TRUE) $this->debugzeile($this->postvals,"ERROR: wrong currency?");
						if($this->params['notifyerror'] == 1) {
							$msg = "ERROR: wrong currency?!!\n\n".var_export($this->postvals,TRUE);
							$this->sendnotify(1,3,$msg);
						}
					} else {
						$minpay		= $this->getParam('minbuy');
						$maxpay		= $this->getParam('maxbuy');
						if($debug === TRUE) $payment	= "10.00";
						else $payment	= $db->escape($this->postvals['mc_gross']);
						if(is_numeric($minpay) && $minpay > 0 && $payment < $minpay) { // less than the minimum payment
							if($this->failure2file === TRUE) $this->simpledebugzeile("WARNING: minpay not reached");
							if($this->params['notifywarning'] == 1) $this->sendnotify(2,2,"Payment of ".$payment." did not reach the minimum payment of ".$minpay);
						} elseif(is_numeric($maxpay) && $maxpay > 0 && $payment > $maxpay) { // more than maximum payment
							if($this->failure2file === TRUE) $this->simpledebugzeile("WARNING: maxpay exceeded");
							if($this->params['notifywarning'] == 1) $this->sendnotify(2,2,"Payment of ".$payment." exceeded the maximum payment of ".$maxpay);
						}
						// check if custom value can be identified
						if($debug === TRUE) {
							$userdata = explode(":","0e88c38e-9e09-4c8d-8ee4-5b22accfc2b4:62");
						} else {
							$paypalcustom	= $this->postvals['custom'];
							$userdata = explode(":",$db->escape($paypalcustom));
						}
						if(count($userdata) != 2) { // what the hell did we get back here from PayPal?
							if($this->failure2file === TRUE) $this->debugzeile($this->postvals,"ERROR: wrong custom value from PayPal! (".$db->escape($this->postvals['custom']).")");
							if($this->params['notifyerror'] == 1) {
								$msg = "ERROR: wrong custom value from PayPal!!\n\n".var_export($this->postvals,TRUE);
								$this->sendnotify(1,3,$msg);
							}
						} else {
							$existing = $this->imodel->subCall("checkClient",$userdata[0]); // check if the UUID already has a balance row
							if($existing === FALSE) {
								$this->imodel->subCall("balanceExists",$userdata[0]); // if not existing, create it initially with 0
								if($this->params['notifywarning'] == 1) {
									$msg = "WARNING: user balance not found with PayPals custom value (newbie transaction - ".$paypalcustom." <-> ".$userdata[0]."?)!!\nBalance row instantly created\n".var_export($this->postvals,TRUE);
									$this->sendnotify(2,3,$msg);
								}
							}
							$query = sprintf("SELECT #__opensim_userrelation.* FROM #__opensim_userrelation WHERE #__opensim_userrelation.opensimID = '%s' AND #__opensim_userrelation.joomlaID = '%d'",
													$userdata[0],
													$userdata[1]);
							$db->setQuery($query);
							$db->query();
							$num_rows	= $db->getNumRows();
							if($num_rows == 0) {
								$debug['query'] = $query;
								$debug['_POST'] = $this->postvals;
								if($this->failure2file === TRUE) $this->debugzeile($debug,"ERROR: user relation not found with PayPals custom value!");
								if($this->params['notifyerror'] == 1) {
									$msg = "ERROR: user relation not found with PayPals custom value!!\n\n".var_export($this->postvals,TRUE);
									$this->sendnotify(1,3,$msg);
								}
							}
							// From here everything should be OK (process even if min or max failure ... payment has been done anyway already)
							$rate			= $this->getParam('currencyratebuy');
							$fee			= $this->getParam('transactionfee');
							$feetype		= $this->getParam('transactionfeetype');

							if(is_numeric($fee) && $fee > 0) {
								if($feetype == "percent") {
									$basepayment = $payment / (100+$fee) * 100;
								} else {
									$basepayment = $payment - $fee;
								}
							} else {
								$basepayment = $payment;
							}
							$iwcurrency = floor($basepayment * $rate);

							// finally process payment
							$banker = $this->getMoneySetting("bankerUID");
							$parameter['senderID']		= $banker;
							$parameter['receiverID']	= $userdata[0];
							$parameter['amount']		= $iwcurrency;
							$parameter['description']	= $this->getParam('transactiontext');
							// now we call the function from jOpenSim
							$retval = $this->imodel->subcall("TransferMoney",$parameter);
							if(!is_array($retval) || !array_key_exists("success",$retval) || $retval['success'] !== TRUE) { // something went wrong :(
								if($this->failure2file === TRUE) $this->debugzeile($retval,"ERROR: TransferMoney failed!");
								if($this->params['notifyerror'] == 1) {
									$msg = "ERROR: TransferMoney failed!!\n\n".var_export($retval,TRUE);
									$this->sendnotify(1,3,$msg);
								}
							}

							// lets get the new balance
							$parameter					= array();
							$parameter['clientUUID']	= $userdata[0];
							$retval = $this->imodel->subCall("GetBalance",$parameter);
							if(!is_array($retval) || !array_key_exists("success",$retval) || $retval['success'] !== TRUE) { // something went wrong :(
								if($this->failure2file === TRUE) $this->debugzeile($retval,"ERROR: GetBalance failed!");
								if($this->params['notifyerror'] == 1) {
									$msg = "ERROR: GetBalance failed!!\n\n".var_export($retval,TRUE);
									$this->sendnotify(1,3,$msg);
								}
								$balance = 0;
							} else {
								$balance = $retval['clientBalance'];
							}

							if($this->paypaldebug === TRUE) $pp_request = serialize($_REQUEST);
							else $pp_request = "";

							// now we insert this transaction into the components table (even if transfer failed)
							$query = sprintf("INSERT INTO #__jopensimpaypal_transactions
											   (txn_id,
												verify_sign,
												payer_email,
												payer_id,
												payer_firstname,
												payer_lastname,
												payment_status,
												payment_type,
												currencyname,
												item_name,
												mc_fee,
												opensimid,
												joomlaid,
												amount_rlc,
												amount_iwc,
												iwbalance,
												fee,
												feetype,
												transactiontime,
												mode,
												paypaldebug)
											VALUES
											   ('%s',
												'%s',
												'%s',
												'%s',
												'%s',
												'%s',
												'%s',
												'%s',
												'%s',
												'%s',
												'%01.2f',
												'%s',
												'%d',
												'%01.2f',
												'%d',
												'%d',
												'%s',
												'%s',
												NOW(),
												'%s',
												'%s')",
									$txn_id,
									$db->escape($this->postvals['verify_sign']),
									$db->escape($this->postvals['payer_email']),
									$db->escape($this->postvals['payer_id']),
									utf8_encode($db->escape($this->postvals['first_name'])),
									utf8_encode($db->escape($this->postvals['last_name'])),
									$db->escape($this->postvals['payment_status']),
									$db->escape($this->postvals['payment_type']),
									$currency,
									$db->escape($this->postvals['item_name1']),
									$db->escape($this->postvals['mc_fee']),
									$userdata[0],
									$userdata[1],
									$payment,
									$iwcurrency,
									$balance,
									$fee,
									$feetype,
									$this->paypalmode,
									$pp_request);
							$db->setQuery($query);
							$db->query();
							$transactionid = $db->insertid();
							// now log/notify the transaction if necessary
							$processdetails = "Processed payment:\nPayment: ".$payment."\nFee: ".$fee."\nFeetype: ".$feetype."\nBasepayment: ".$basepayment."\nInworldcurrency: ".$iwcurrency."\nPayment Type: ".$this->postvals['payment_type']."\nUserdata: ".$this->postvals['custom']."\nLocal transaction id: ".$transactionid."\n\n";
							if($this->log2file === TRUE) $this->simpledebugzeile($processdetails);
							if($this->params['notifytransaction'] == 1) $this->sendnotify(3,1,$processdetails);
						}
					}
				}
			} else { // $business != $paypalaccount ... ???
				if($this->failure2file === TRUE) $this->debugzeile($this->postvals,"ERROR: \$business != \$paypalaccount!");
				if($this->params['notifyerror'] == 1) {
					$msg = "ERROR: \$business != \$paypalaccount!!\n\n".var_export($this->postvals,TRUE);
					$this->sendnotify(1,3,$msg);
				}
			}
		} else { // $paymentstatus != "Completed" ... ???
			if($this->failure2file === TRUE) $this->debugzeile($this->postvals,"ERROR: \$paymentstatus != \"Completed\"!");
			if($this->params['notifyerror'] == 1) {
				$msg = "ERROR: \$paymentstatus != \"Completed\"!!\n\n".var_export($this->postvals,TRUE);
				$this->sendnotify(1,3,$msg);
			}
		}
	}

	public function setMoneyModel($imodel) {
		$this->imodel	= $imodel;
	}

	public function __destruct() {
	}
}
?>