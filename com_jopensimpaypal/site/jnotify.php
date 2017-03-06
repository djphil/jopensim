<?php
error_reporting(0);
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html, see LICENSE.php
 *
 * @component jOpenSimPayPal (Communication Interface with PayPal)
 * @copyright Copyright (C) 2013 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
/* Initialize Joomla framework */
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(realpath("../../index.php")));
define( 'DS', DIRECTORY_SEPARATOR );

/*
 * optional 
 * bool $paypaldebug
 *
 * FALSE = disabled
 * TRUE  = entire request values from paypal will be stored serialized in the transaction table
 *
*/
$paypaldebug = TRUE;

/* Required Files */
require_once ( JPATH_BASE .DS.'configuration.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

/* To use Joomla's Database Class */
require_once ( JPATH_ROOT .DS.'libraries'.DS.'joomla'.DS.'factory.php' );

/* Create the Application */
$mainframe =& JFactory::getApplication('site');

/* Load the language file from the component opensim */

$lang		=& JFactory::getLanguage();
$extension	= 'com_jopensimpaypal';
$base_dir	= JPATH_SITE;

// $language_tag = 'en-GB';
// $lang->load($extension, $base_dir, $language_tag, true);
$lang->load($extension, $base_dir, null, true);

// My code starts here
// just create a dummy class for the currency functions
if(!class_exists("jxmlrpc_server")) {
	class jxmlrpc_server {
		public $constructarray = array();
		public function __construct($params) {
			$this->constructarray = $params;
		}
	}
}
// we need the model of jOpenSimPayPal
require_once(JPATH_BASE .DS.'components'.DS.$extension.DS.'models'.DS.'jopensimpaypal.php');
$model = new jOpenSimPayPalModeljOpenSimPayPal();
// and we need the currency functions from jOpenSim
require_once(JPATH_BASE .DS.'components'.DS.'com_opensim'.DS.'includes'.DS.'functions_currency.php');


// get the components parameters
$paypaltype		= $model->getParam('sandboxmode');
$paypalaccount	= $model->getParam('paypal_account');
$sslverify		= $model->getParam('paypal_nossl');

if($sslverify == 1) {
	$curlopt_ssl_verifypeer = FALSE;
	$curlopt_ssl_verifyhost = FALSE;
} else {
	$curlopt_ssl_verifypeer = 1;
	$curlopt_ssl_verifyhost = 2;
}

if($paypaltype) {
	$paypalurl		= "https://www.sandbox.paypal.com/cgi-bin/webscr";
	$paypalhost		= "Host: www.sandbox.paypal.com";
	$paypalmode		= "sandbox";
} else {
	$paypalurl		= "https://www.paypal.com/cgi-bin/webscr";
	$paypalhost		= "Host: www.paypal.com";
	$paypalmode		= "production";
}

$notifytransaction	= $model->getParam('notifytransaction');
$notifyerror		= $model->getParam('notifyerror');
$notifywarning		= $model->getParam('notifywarning');
$notifyemail		= $model->getParam('notifyemail');


// some required values
$logpath		= $model->getParam('logpath');
if(substr($logpath,-1) != DS) $logpath .= DS;
$paypallogfile	= "jopensimpaypal.log";
$logsetting		= $model->getParam('log2file');
if(($logsetting & 2) == 2) $log2file		= TRUE; // log everything
else $log2file		= FALSE;
if(($logsetting & 1) == 1) $failure2file	= TRUE; // log errors and warnings
else $failure2file	= FALSE;

function simpledebugzeile($zeile) {
	global $logpath,$paypallogfile;
	$zeit = date("Y-m-d H:i:s");
	$logfile = $logpath.$paypallogfile;
	$handle = fopen($logfile,"a+");
	$logzeile = $zeit."\t".$zeile."\n";
	fputs($handle,$logzeile);
	fclose($handle);
}

function debugzeile($zeile,$function = "") {
	global $logpath,$paypallogfile;
	if(!$function) $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##########\n";
	else $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##### ".$function." ##########\n";
	$zeile = var_export($zeile,TRUE);
	$logfile = $logpath.$paypallogfile;
	$handle = fopen($logfile,"a+");
	$logzeile = $zeit.$zeile."\n\n";
	fputs($handle,$logzeile);
	fclose($handle);
}

function sendnotify($type,$handler,$msg) {
	global $notifyemail;
	$mailer = JFactory::getMailer();
	$config = JFactory::getConfig();
	$sender = array( 
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
		default:
			$body = "Unknown message handler: ".$handler."\n\nmsg: ".$msg;
		break;
	}
	$mailer->setBody($body);
	$send =& $mailer->Send();
	if ($send !== TRUE) {
	    if($failure2file === TRUE) simpledebugzeile('Error sending email: '.$send->message);
	}
}

if(!function_exists("jOpenSimSettings")) {
	function jOpenSimSettings() {
		global $model;
		$settings = $model->jOpenSimSettings();
		return $settings;
	}
}

if(array_key_exists("test",$_GET) && $_GET['test'] == "test") {
//	simpledebugzeile(var_export($_GET,TRUE));
	echo "this is only a test!<br />";
	echo "paypalaccount: ".$paypalaccount."<br />\n";
	echo "logpath: ".$logpath.$paypallogfile."<br />\n";
	$db			= JFactory::getDBO();
	echo "<pre>\n";
	$test = $db->escape($_GET['custom']);
	echo "custom:\n\n";
	var_dump($_GET['custom']);
	echo "test:\n\n";
	var_dump($test);

	$minpay		= $model->getParam('minbuy');
	$maxpay		= $model->getParam('maxbuy');
	echo "minpay:\n\n";
	var_dump($minpay);
	echo "maxpay:\n\n";
	var_dump($maxpay);

	echo "\n\n\ndb:\n\n";
	var_dump($db);
	echo "</pre>\n";
	exit;
}

$input = file_get_contents("php://input");
if($log2file === TRUE) simpledebugzeile($input);

if($log2file === TRUE) debugzeile($_REQUEST,"\$_REQUEST");
if($log2file === TRUE) debugzeile($_SERVER,"\$_SERVER");

// read the post from PayPal system and add 'cmd'
$req = 'cmd=' . urlencode('_notify-validate');

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paypalurl);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $curlopt_ssl_verifypeer);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $curlopt_ssl_verifyhost);
curl_setopt($ch, CURLOPT_HTTPHEADER, array($paypalhost));
$res = curl_exec($ch);
curl_close($ch);

if($log2file === TRUE) debugzeile($res,"\$res");

if (strcmp ($res, "VERIFIED") == 0) {
	$paymentstatus	= $_POST['payment_status'];
	// check the payment_status is Completed
	if($paymentstatus == "Completed") {
		// check that receiver_email is your Primary PayPal email
		$business	= $_POST['business'];
		if($business == $paypalaccount) {
			$db			= JFactory::getDBO();
			// check that txn_id has not been previously processed
			$txn_id		= $db->escape($_POST['txn_id']);
			$query		= sprintf("SELECT #__jopensimpaypal_transactions.* FROM #__jopensimpaypal_transactions WHERE #__jopensimpaypal_transactions.txn_id = '%s'",$txn_id);
			$db->setQuery($query);
			$db->query();
			$num_rows	= $db->getNumRows();
			if($num_rows > 0) { // txn_id already existing???
				$result = $db->loadAssocList();
				if($failure2file === TRUE) debugzeile($_POST,"ERROR: txn_id already existing");
				if($notifyerror == 1) {
					$msg = "ERROR: txn_id already existing!!\n\n".var_export($_POST,TRUE);
					sendnotify(1,3,$msg);
				}
			} else {
				// check that payment_amount/payment_currency are correct
				$currency = $model->getParam('currency');
				if($currency != $_POST['mc_currency']) {
					if($failure2file === TRUE) debugzeile($_POST,"ERROR: wrong currency?");
					if($notifyerror == 1) {
						$msg = "ERROR: wrong currency?!!\n\n".var_export($_POST,TRUE);
						sendnotify(1,3,$msg);
					}
				} else {
					$minpay		= $model->getParam('minbuy');
					$maxpay		= $model->getParam('maxbuy');
					$payment	= $db->escape($_POST['mc_gross']);
					if(is_numeric($minpay) && $minpay > 0 && $payment < $minpay) { // less than the minimum payment
						if($failure2file === TRUE) simpledebugzeile("WARNING: minpay not reached");
						if($notifywarning == 1) sendnotify(2,2,"Payment of ".$payment." did not reach the minimum payment of ".$minpay);
					} elseif(is_numeric($maxpay) && $maxpay > 0 && $payment > $maxpay) { // more than maximum payment
						if($failure2file === TRUE) simpledebugzeile("WARNING: maxpay exceeded");
						if($notifywarning == 1) sendnotify(2,2,"Payment of ".$payment." exceeded the maximum payment of ".$maxpay);
					}
					// check if custom value can be identified
					$userdata = explode(":",$db->escape($_POST['custom']));
					if(count($userdata) != 2) { // what the hell did we get back here from PayPal?
						if($failure2file === TRUE) debugzeile($_POST,"ERROR: wrong custom value from PayPal! (".$db->escape($_POST['custom']).")");
						if($notifyerror == 1) {
							$msg = "ERROR: wrong custom value from PayPal!!\n\n".var_export($_POST,TRUE);
							sendnotify(1,3,$msg);
						}
					} else {
						$existing = checkClient($userdata[0]); // check if the UUID already has a balance row
						if($existing !== TRUE) {
							balanceExists($userdata[0]); // if not existing, create it initially with 0
							if($notifywarning == 1) {
								$msg = "WARNING: user balance not found with PayPals custom value (newbie transaction?)!!\nBalance row instantly created\n".var_export($_POST,TRUE);
								sendnotify(2,3,$msg);
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
							$debug['_POST'] = $_POST;
							if($failure2file === TRUE) debugzeile($debug,"ERROR: user relation not found with PayPals custom value!");
							if($notifyerror == 1) {
								$msg = "ERROR: user relation not found with PayPals custom value!!\n\n".var_export($_POST,TRUE);
								sendnotify(1,3,$msg);
							}
						}
						// From here everything should be OK (process even if min or max failure ... payment has been done anyway already)
						$rate			= $model->getParam('currencyratebuy');
						$fee			= $model->getParam('transactionfee');
						$feetype		= $model->getParam('transactionfeetype');

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
						$banker = $model->getMoneySetting("bankerUID");
						$parameter['senderID']		= $banker;
						$parameter['receiverID']	= $userdata[0];
						$parameter['amount']		= $iwcurrency;
						$parameter['description']	= $model->getParam('transactiontext');
						// now we call the function from jOpenSim
						$retval = TransferMoney($parameter);
						if(!is_array($retval) || !array_key_exists("success",$retval) || $retval['success'] !== TRUE) { // something went wrong :(
							if($failure2file === TRUE) debugzeile($retval,"ERROR: TransferMoney failed!");
							if($notifyerror == 1) {
								$msg = "ERROR: TransferMoney failed!!\n\n".var_export($retval,TRUE);
								sendnotify(1,3,$msg);
							}
						}

						// lets get the new balance
						$parameter					= array();
						$parameter['clientUUID']	= $userdata[0];
						$retval = GetBalance($parameter);
						if(!is_array($retval) || !array_key_exists("success",$retval) || $retval['success'] !== TRUE) { // something went wrong :(
							if($failure2file === TRUE) debugzeile($retval,"ERROR: GetBalance failed!");
							if($notifyerror == 1) {
								$msg = "ERROR: GetBalance failed!!\n\n".var_export($retval,TRUE);
								sendnotify(1,3,$msg);
							}
							$balance = 0;
						} else {
							$balance = $retval['clientBalance'];
						}

						if($paypaldebug === TRUE) $pp_request = serialize($_REQUEST);
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
								$db->escape($_POST['verify_sign']),
								$db->escape($_POST['payer_email']),
								$db->escape($_POST['payer_id']),
								utf8_encode($db->escape($_POST['first_name'])),
								utf8_encode($db->escape($_POST['last_name'])),
								$db->escape($_POST['payment_status']),
								$db->escape($_POST['payment_type']),
								$currency,
								$db->escape($_POST['item_name1']),
								$db->escape($_POST['mc_fee']),
								$userdata[0],
								$userdata[1],
								$payment,
								$iwcurrency,
								$balance,
								$fee,
								$feetype,
								$paypalmode,
								$pp_request);
						$db->setQuery($query);
						$db->query();
						$transactionid = $db->insertid();
						// now log/notify the transaction if necessary
						$processdetails = "Processed payment:\nPayment: ".$payment."\nFee: ".$fee."\nFeetype: ".$feetype."\nBasepayment: ".$basepayment."\nInworldcurrency: ".$iwcurrency."\nPayment Type: ".$_POST['payment_type']."\nUserdata: ".$_POST['custom']."\nLocal transaction id: ".$transactionid."\n\n";
						if($log2file === TRUE) simpledebugzeile($processdetails);
						if($notifytransaction == 1) sendnotify(3,1,$processdetails);
					}
				}
			}
		} else { // $business != $paypalaccount ... ???
			if($failure2file === TRUE) debugzeile($_POST,"ERROR: \$business != \$paypalaccount!");
			if($notifyerror == 1) {
				$msg = "ERROR: \$business != \$paypalaccount!!\n\n".var_export($_POST,TRUE);
				sendnotify(1,3,$msg);
			}
		}
	} else { // $paymentstatus != "Completed" ... ???
		if($failure2file === TRUE) debugzeile($_POST,"ERROR: \$paymentstatus != \"Completed\"!");
		if($notifyerror == 1) {
			$msg = "ERROR: \$paymentstatus != \"Completed\"!!\n\n".var_export($_POST,TRUE);
			sendnotify(1,3,$msg);
		}
	}
} else if (strcmp ($res, "INVALID") == 0) { // answer from _notify-validate was "INVALID" ... ???
	$report['response_notify-validate']	= $res;
	$report['post']	= $_POST;
	if($failure2file === TRUE) debugzeile($report,"ERROR: _notify-validate was \"INVALID\"!");
	if($notifyerror == 1) {
		$msg = "ERROR: _notify-validate was \"INVALID\"!!\n\n".var_export($report,TRUE);
		sendnotify(1,3,$msg);
	}
} else { // answer from _notify-validate was not "VERIFIED" and not "INVALID" ... ???
	$report['response_notify-validate']	= $res;
	$report['post']	= $_POST;
	if($failure2file === TRUE) debugzeile($report,"ERROR: _notify-validate was not \"VERIFIED\" and not \"INVALID\"!");
	if($notifyerror == 1) {
		$msg = "ERROR: _notify-validate was not \"VERIFIED\" and not \"INVALID\"!!\n\n".var_export($report,TRUE);
		sendnotify(1,3,$msg);
	}
}
?>