<?php
/***********************************************************************

xmlrpc functions for currency handling

 * @component jOpenSim (Communication Interface with the OpenSim Server)
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html

***********************************************************************/

if(!defined("_JOPENSIMCURRENCY")) define("_JOPENSIMCURRENCY",TRUE);

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$xmlrpcserver = new jxmlrpc_server(array(
		// Currency Functions
			"GetBalance"			=> array("function" => "GetBalance"),
			"TransferMoney"			=> array("function" => "TransferMoney"),
			"AmountCovered"			=> array("function" => "AmountCovered"),
			"ApplyCharge"			=> array("function" => "ApplyCharge"),
			"getSettingsData"		=> array("function" => "getSettingsData"),
			"getCurrencyQuote"		=> array("function" => "getCurrencyQuote"),
			"preflightBuyLandPrep"	=> array("function" => "preflightBuyLandPrep"),
			"buyLandPrep"			=> array("function" => "buyLandPrep"),
			"clientInfo"			=> array("function" => "clientInfo"),
			"buyCurrency"			=> array("function" => "buyCurrency")
	), false);


function preflightBuyLandPrep($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter preflightBuyLandPrep");
	}

	$agentid	  = $parameter['agentId'];
	$sessionid	  = $parameter['secureSessionId'];
	$amount		  = $parameter['currencyBuy'];
	$billableArea = $parameter['billableArea'];
	$ipAddress 	  = $_SERVER['REMOTE_ADDR'];

	$confirmvalue = get_confirm_value();
	$membership_levels = array('levels' => array('id' => "00000000-0000-0000-0000-000000000000", 'description' => "some level"));
	$sysurl = "http:/"."/".$_SERVER['HTTP_HOST']."/components/com_opensim/";
	$landUse	= array('upgrade' => False, 'action' => "".$sysurl."");
	$currency   = array('estimatedCost' => convert_to_real($amount));
	$membership = array('upgrade' => False, 'action' => "".$sysurl."", 'levels' => $membership_levels);
	$retval = array('success'	=> True,
					'currency'  => $currency,
					'membership'=> $membership,
					'landUse'	=> $landUse,
					'currency'  => $currency,
					'confirm'	=> $confirmvalue);
	return $retval;
}

function buyLandPrep($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter buyLandPrep");
	}
	return getCurrencyQuote($parameter);
}

function GetBalance($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter GetBalance");
	}
	$startbalance = getSettingsValue("startBalance");
	if($startbalance === FALSE || !$startbalance) { // we did not find any value for the startbalance of new users
		if(_JOPENSIMMONEYDEBUG === TRUE) {
			$debug = varexport($startbalance,TRUE);
			debugzeile($debug,"Parameter GetBalance -> startbalance -> 0");
		}
		$startbalance = 0;
	}
	if(!isset($parameter['clientUUID'])) { // This parameter is obligatory
		$retval['success']	= FALSE;
		$retval['message']	= "No clientUUID provided for function GetBalance() in ".__FILE__." at line ".__LINE__;
		return $retval;
	}
	if(!array_key_exists("AgentHomeURL",$parameter)) $parameter['AgentHomeURL'] = "local"; // just in case still an old module is running, no HG is considered
	$clientSessionID		= (isset($parameter['clientSessionID']))		? $parameter['clientSessionID']:null;
	$clientSecureSessionID	= (isset($parameter['clientSecureSessionID']))	? $parameter['clientSecureSessionID']:null;
	$uuid					= $parameter['clientUUID'];
	$query					= sprintf("SELECT balance FROM #__opensim_moneybalances WHERE `user` = '%s' AND `homeurl` = '%s'",$uuid,$parameter['AgentHomeURL']);
	$db						= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
	$num_rows 				= $db->getNumRows();
//	echo "Zeile ".__LINE__;
//	echo $query;
//	exit;
	if($num_rows == 0) {
		$query = sprintf("INSERT INTO #__opensim_moneybalances (`user`,`homeurl`,`balance`,`status`) VALUES ('%s','%s','%d','1')",$uuid,$parameter['AgentHomeURL'],0); // Insert a new row for $uuid
		$db->setQuery($query);
		$db->execute();

		$parameter['senderID']		= getSettingsValue("bankerUID");
		$parameter['senderHome']	= "local";
		$parameter['receiverID']	= $uuid;
		$parameter['receiverHome']	= $parameter['AgentHomeURL'];
		$parameter['amount']		= $startbalance;
		$parameter['description']	= JTEXT::_('JOPENSIM_MONEY_STARTBALANCE');
		TransferMoney($parameter);

		$balance = $startbalance;
	} else {
		$userbalance = $db->loadAssoc();
		$balance = $userbalance['balance'];
	}

	$retval['success']				 = TRUE;
	$retval['clientBalance']		 = intval($balance);
	$retval['clientSessionID']		 = $clientSessionID;
	$retval['clientUUID']			 = $uuid;
	$retval['clientSecureSessionID'] = $clientSecureSessionID;
	return $retval;
}

function TransferMoney($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter TransferMoney");
	}
	if(!array_key_exists("senderHome",$parameter) || !$parameter['senderHome']) $parameter['senderHome']		= "local";
	if(!array_key_exists("receiverHome",$parameter) || !$parameter['receiverHome']) $parameter['receiverHome']	= "local";
	$isSender	= checkClient($parameter['senderID'],$parameter['senderHome']);
	$isReceiver	= checkClient($parameter['receiverID'],$parameter['receiverHome']);
	if(!array_key_exists("senderSecureSessionID",$parameter)) $parameter['senderSecureSessionID'] = "00000000-0000-0000-0000-000000000000";

	// Maybe receiver is a group without balance yet?
	if($isReceiver === FALSE) {
		$isGroup = checkGroup($parameter['receiverID']);
		if($isGroup === TRUE) {
			balanceExists($parameter['receiverID'],"2");
			$isReceiver = TRUE;
		}
	}

	if($isSender === FALSE) {
		$retval['success']	= FALSE;
		$retval['message']	= "Could not locate senderID ".$parameter['senderID'];
	} elseif($isReceiver === FALSE) {
		$retval['success']	= FALSE;
		$retval['message']	= "Could not locate receiverID ".$parameter['receiverID'];
	} else {
		$checkbalance['clientUUID']		= $parameter['senderID'];
		$checkbalance['AgentHomeURL']	= $parameter['senderHome'];
		$checkbalance['amount']			= $parameter['amount'];
		$amountcovered = AmountCovered($checkbalance);
		if($amountcovered['success'] !== TRUE) {
			$retval['success']	= FALSE;
			$retval['message']	= "Transaction failed (AmountCovered failed)";
		} else {
			$checksession = checkSession($parameter['senderSecureSessionID']);
			if($checksession == "locked") {
				$retval['success']	= FALSE;
				$retval['message']	= "Transaction failed (session currently in use)";
			} else {
				$parameter['time'] = time();
				$parameter['status'] = 0;
				try {
					$db		= JFactory::getDBO();
					lockSession($parameter['senderSecureSessionID']);
					$db->transactionStart();
		
					insertTransaction($parameter);
		
					setBalance($parameter['receiverID'],$parameter['amount'],$parameter['receiverHome']);
					setBalance($parameter['senderID'],-$parameter['amount'],$parameter['senderHome']);
		
					$db->transactionCommit();
					unlockSession($parameter['senderSecureSessionID']);
		
					$retval['success']				 = TRUE;
					$retval['clientUUID']			 = (isset($parameter['clientUUID']))			? $parameter['clientUUID']:null;
					$retval['clientSessionID']		 = (isset($parameter['clientSessionID']))		? $parameter['clientSessionID']:null;
					$retval['clientSecureSessionID'] = (isset($parameter['clientSecureSessionID']))	? $parameter['clientSecureSessionID']:null;
					$retval['objectID']		 		 = (isset($parameter['objectID']))				? $parameter['objectID']:null;
				}
				catch (Exception $e) {
					$db->transactionRollback();
					$retval['success']	= FALSE;
					$retval['message']	= "Transaction failed with exception: ".$e;
				}
			}
		}
	}
	checkGridBalance("TransferMoney");
	return $retval;
}

function setBalance($uuid,$amount,$home = "local") {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$arg_list = func_get_args();
		$debug = varexport($arg_list,TRUE);
		debugzeile($debug,"Parameter setBalance");
	}
	$isGroup = checkGroup($uuid);
	if($isGroup === TRUE) balanceExists($uuid,"2"); // $uuid could be a group, see if it exists and if not, create a balance line for it
	$query	= sprintf("UPDATE #__opensim_moneybalances SET balance = balance + %d WHERE `user`= '%s' AND homeurl = '%s'",$amount,$uuid,$home);
	$db		= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
}

function balanceExists($uuid,$status = "1",$home = "local") { // if this $uuid does not exist yet, it will create a 0 Balance for it (different to GetBalance, where $startbalance will be created)
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$arg_list = func_get_args();
		$debug = varexport($arg_list,TRUE);
		debugzeile($debug,"Parameter balanceExists");
	}
	$query	= sprintf("SELECT balance FROM #__opensim_moneybalances WHERE `user` = '%s' AND homeurl = '%s'",$uuid,$home);
	$db		= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	if($num_rows == 0) {
		$query = sprintf("INSERT INTO #__opensim_moneybalances (`user`,`homeurl`,`balance`,`status`) VALUES ('%s','%s',0,$status)",$uuid,$home);
		$db->setQuery($query);
		$db->execute();
	}
}

function checkClient($uuid,$home = "local") {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$arg_list = func_get_args();
		$debug = varexport($arg_list,TRUE);
		debugzeile($debug,"Parameter checkClient");
	}
	$query = sprintf("SELECT * FROM #__opensim_moneybalances WHERE `user`= '%s' AND `homeurl` ='%s'",$uuid,$home);
	$db		= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	if($num_rows == 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function checkGroup($uuid) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$arg_list = func_get_args();
		$debug = varexport($arg_list,TRUE);
		debugzeile($debug,"Parameter checkGroup");
	}
	$query = sprintf("SELECT * FROM #__opensim_group WHERE `GroupID`= '%s'",$uuid);
	$db		= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	if($num_rows == 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function AmountCovered($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter AmountCovered");
	}
	$uuid	= $parameter['clientUUID'];
	$home	= (array_key_exists("AgentHomeURL",$parameter)) ? $parameter['AgentHomeURL']:"local";
	$banker	= getSettingsValue("bankerUID");
	if($banker == $uuid) {
		$retval['success'] = TRUE; // The banker ALLWAYS has sufficient balance ;)
		return $retval;
	}
	$amount	= $parameter['amount'];
	$query	= sprintf("SELECT balance FROM #__opensim_moneybalances WHERE `user` = '%s' AND `homeurl` = '%s'",$uuid,$home);
	$db		= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	if($num_rows == 1) {
		$balance = $db->loadResult();
		if($balance >= $amount) {
			$retval['success'] = TRUE;
		} else {
			$retval['success'] = FALSE;
			$retval['message'] = "Insufficient balance for $amount!";
		}
	} else {
		$retval['success'] = FALSE;
		$retval['message'] = "Error while checking AmountCovered!";
	}
	return $retval;
}

function ApplyCharge($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter ApplyCharge");
	}
	$amount							= $parameter['amount'];
	$parameter['senderID']			= $parameter['clientUUID'];
	$parameter['senderHome']		= (array_key_exists("AgentHomeURL",$parameter)) ? $parameter['AgentHomeURL']:"local";
	$parameter['receiverID']		= getSettingsValue("bankerUID");
	$parameter['receiverHome']		= "local";
	$parameter['time']				= time();
	switch($parameter['description']) {
		case "Asset upload":
			$parameter['transactionType']	= 1001;
		break;
		case "Group Creation":
			$parameter['transactionType']	= 1002;
		break;
		default:
			$parameter['transactionType']	= 1003;
		break;
	}
	$parameter['status']			= 0;

	$customfee = checkCustomFee($parameter['senderID']);
	if(is_array($customfee) && array_key_exists("uploadfee",$customfee) && array_key_exists("groupfee",$customfee)) {
		switch($parameter['description']) {
			case "Asset upload":
				$amount					= $customfee['uploadfee'];
				$parameter['amount']	= $amount;
			break;
			case "Group Creation":
				$amount	= $customfee['groupfee'];
				$parameter['amount']	= $amount;
			break;
		}
	}

	if($parameter['receiverID'] === FALSE) {
		$retval['success'] = FALSE;
		$retval['message'] = "Banker Account not found";
	} elseif(AmountCovered($parameter) === FALSE) {
		$retval['success'] = FALSE;
		$retval['message'] = "Insufficient balance for $amount!"; // This should actually always have happened before already, but however...
	} else {
		insertTransaction($parameter);
		setBalance($parameter['receiverID'],$amount,$parameter['receiverHome']);
		setBalance($parameter['senderID'],-$amount,$parameter['senderHome']);
		$retval['success'] = TRUE;
	}
	checkGridBalance("ApplyCharge");
	return $retval;
}

function getSettingsValue($field) {
	$settings = jOpenSimSettings();
	$retval = null;
	switch($field) {
		case "bankerUID":
		case "bankerUUID":
			$retval = $settings['jopensimmoneybanker'];
		break;
		case "groupCharge":
			$retval = $settings['jopensimmoney_groupcreation'];
		break;
		case "uploadCharge":
			$retval = $settings['jopensimmoney_upload'];
		break;
		case "startBalance":
			$retval = $settings['jopensimmoney_startbalance'];
		break;
		case "groupMinDividend":
			$retval = $settings['jopensimmoney_groupdividend'];
		break;
		case "currencyName":
		case "name":
			$retval = $settings['jopensimmoney_currencyname'];
		break;
		case "bankerName":
			$retval = $settings['jopensimmoney_bankername'];
		break;
		default:
			$retval = FALSE;
		break;
	}
	return $retval;
}

function getSettingsData($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter getSettingsData");
	}
	$feld		= $parameter['settingsfield'];
	$returntype	= (array_key_exists("returntype", $parameter)) ? $parameter['returntype']:"char";
	$wert		= getSettingsValue($feld);
	if($wert === FALSE) {
		$retval['success']		= FALSE;
		$retval['message']		= "Could not determine value for ".$feld;
	} else {
		switch($returntype) {
			case "int":
				$retval['success']		= TRUE;
				$retval['settingvalue']	= intval($wert);
			break;
			case "string":
			case "char":
				$retval['success']		= TRUE;
				$retval['settingvalue']	= strval($wert);
			break;
			default:
				$retval['success']		= FALSE;
				$retval['message']		= "Unknown Type ".$returntype." for settingsfield ".$feld;
			break;
		}
	}
	return $retval;
}

function insertTransaction($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter insertTransaction");
	}
	$senderID 				= (isset($parameter['senderID']))				? $parameter['senderID']:"";
	$receiverID				= (isset($parameter['receiverID']))				? $parameter['receiverID']:"";
	$amount					= (isset($parameter['amount']))					? $parameter['amount']:0;
	$objectID				= (isset($parameter['objectID']))				? $parameter['objectID']:"";
	$regionHandle			= (isset($parameter['regionHandle']))			? $parameter['regionHandle']:"";
	$transactionType		= (isset($parameter['transactionType']))		? $parameter['transactionType']:"";
	$time					= (isset($parameter['time']))					? $parameter['time']:time();
	$senderSecureSessionID	= (isset($parameter['senderSecureSessionID']))	? $parameter['senderSecureSessionID']:"";
	$status					= (isset($parameter['status']))					? $parameter['status']:0;
	$description			= (isset($parameter['description']))			? $parameter['description']:"";

	$query = sprintf("INSERT INTO #__opensim_moneytransactions (`UUID`,sender,receiver,amount,objectUUID,regionHandle,type,`time`,`secure`,`status`,description)
														VALUES
																(UUID(),'%s','%s','%d','%s','%s','%s','%d','%s','%d','%s')",
								$senderID,
								$receiverID,
								$amount,
								$objectID,
								$regionHandle,
								$transactionType,
								$time,
								$senderSecureSessionID,
								$status,
								$description);
	
	$db		= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
}

function groupMembershipFee($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter groupMembershipFee");
	}
	if(!isset($parameter['groupID']) || !isset($parameter['clientUUID'])) {
		$retval['success'] = FALSE;
		$retval['message'] = "No GroupID or no clientUUID provided for groupMembershipFee in ".__FILE__." at line ".__LINE__;
		return $retval;
	} elseif(!isset($parameter['groupPower']) || intval($parameter['groupPower']) == 0) {
		$retval['success'] = FALSE;
		$retval['message'] = "Missing GroupPower-Bit for groupMembershipFee in ".__FILE__." at line ".__LINE__;
		return $retval;
	}
	$groupID	= $parameter['groupID'];
	$agentID	= $parameter['clientUUID'];
	$groupfee	= sprintf("%d",$parameter['groupFee']);
	$groupPower	= $parameter['groupPower'];
	$sessionID	= (isset($parameter['sessionID'])) ? $parameter['sessionID']:null;

	balanceExists($groupID,"2"); // check if Group already has a balance line (and if not create it)
	$parameter 							= array();
	$parameter['amount']				= $groupfee;
	$parameter['senderID']				= $agentID;
	$parameter['receiverID']			= $groupID;
	$parameter['clientSessionID']		= $sessionID;
	$parameter['clientUUID']			= $agentID;
	$parameter['clientSecureSessionID']	= null;
	$parameter['description']			= "Group Enrollment Fee";
	$retval								= TransferMoney($parameter); // Transfer the groupfee from the new member to the group
	checkGridBalance("groupMembershipFee");
	if($retval['success'] === FALSE) {
		return $retval; // something went wrong?
	} else { // everything ok, we should check if group pays dividend now
		$parameter['groupID']		= $groupID;
		$parameter['groupPower']	= $groupPower;
		$retval = groupDividend($parameter);
		return $retval;
	}
}

function groupDividend($parameter) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter groupDividend");
	}
	if(!isset($parameter['groupID'])) {
		$retval['success'] = FALSE;
		$retval['message'] = "No GroupID provided for groupDividend in ".__FILE__." at line ".__LINE__;
		return $retval;
	} elseif(!isset($parameter['groupPower']) || intval($parameter['groupPower']) == 0) {
		$retval['success'] = FALSE;
		$retval['message'] = "Missing GroupPower-Bit for groupMembershipFee in ".__FILE__." at line ".__LINE__;
		return $retval;
	}

	$groupMinDividend	= getSettingsValue('groupMinDividend');
	if($groupMinDividend == 0) { // nothing set for groupMinDividend, we dont pay anything
		$retval['success'] = TRUE;
		$retval['message'] = "groupMinDividend = 0 ... payment done manually? -> in ".__FILE__." at line ".__LINE__;
		return $retval;
	}
	$groupID			= $parameter['groupID'];
	$query				= sprintf("SELECT balance FROM #__opensim_moneybalances WHERE #__opensim_moneybalances.`user` = '%s'",$groupID);
	$db					= JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
	$num_rows			= $db->getNumRows();
	$groupBalance		= $db->loadResult();

	if($num_rows == 0) { // Group does not have any balance line, nothing to pay
		$retval['success'] = FALSE;
		$retval['message'] = "Missing balance for function groupDividend in ".__FILE__." at line ".__LINE__;
		return $retval;
	} elseif(intval($groupBalance) == 0) { // Group does have balance 0, that's rather ok but we still dont pay anything
		$retval['success'] = TRUE;
		return $retval;
	}

	$grouppower	= sprintf("%d",$parameter['groupPower']);
	$query = sprintf("SELECT
							#__opensim_grouprolemembership.AgentID
						FROM
							#__opensim_grouprolemembership,
							#__opensim_grouprole,
							#__opensim_moneybalances
						WHERE
							#__opensim_grouprolemembership.RoleID = #__opensim_grouprole.RoleID
						AND
							#__opensim_grouprolemembership.GroupID = #__opensim_grouprole.GroupID
						AND
							#__opensim_grouprole.GroupID = '%s'
						AND
							#__opensim_grouprole.Powers & %d
						AND
							#__opensim_grouprolemembership.AgentID = #__opensim_moneybalances.`user`
						GROUP BY
							#__opensim_grouprolemembership.AgentID",
		$groupID,
		$grouppower);
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	if($num_rows == 0) { // no member??? nobody to pay for
		$retval['success'] = FALSE;
		$retval['message'] = "Nobody in this group?";
	}
	$groupdividend		= intval(floor($groupBalance / $num_rows));
	if($groupMinDividend > $groupdividend) { // The minimum for paying dividend is not reached yet
		$retval['success'] = TRUE;
		$retval['message'] = "Minimum group dividend not reached yet";
		$retval['groupMinDividend'] = $groupMinDividend;
		$retval['groupdividend'] = $groupdividend;
		return $retval;
	}

	// Everything seems to be ok, lets pay the dividend
	$receivers			= $db->loadAssocList();
	foreach($receivers AS $key => $receiver) {
		$parameter 							= array();
		$parameter['amount']				= $groupdividend;
		$parameter['senderID']				= $groupID;
		$parameter['receiverID']			= $receivers[$key]['AgentID'];
		$parameter['receiverHome']			= "local";
		$parameter['clientSessionID']		= null;
		$parameter['clientUUID']			= null;
		$parameter['clientSecureSessionID']	= null;
		$parameter['transactionType']		= "6003";
		$parameter['description']			= "Group Dividend";
		$retval								= TransferMoney($parameter); // Transfer the groupfee from the group to the ones who should get
		if($retval['success'] === FALSE) {
			checkGridBalance("groupDividend1");
			return $retval; // something went wrong?
		}
	}
	checkGridBalance("groupDividend2");
	$retval['success']	= TRUE;
	return $retval;
}

// Todo: "echte" Currency Funktionen

function convert_to_real($wert) {
	return intval($wert);
}

function get_confirm_value() {
	$ipAddress = $_SERVER['REMOTE_ADDR'];
	$key = "1234567883789";
	$confirmvalue = md5($key."_".$ipAddress);
	return $confirmvalue;
}

function buyCurrency($parameter) {
	$test = var_export($parameter,TRUE);
	return getCurrencyQuote($parameter);
}

function getCurrencyQuote($parameter) {
	$test = var_export($parameter,TRUE);
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($parameter,TRUE);
		debugzeile($debug,"Parameter getCurrencyQuote");
	}

//	$amount	   = $parameter['currencyBuy'];
//	$cost = convert_to_real($amount);
//	$currency = array('estimatedCost'=> $cost, 'currencyBuy'=> $amount);

//	$confirmvalue = get_confirm_value();

	// get a default value for emergency
	if(strlen(JPATH_BASE) > strlen($_SERVER['DOCUMENT_ROOT'])) {
		$joomlauri	= $_SERVER['HTTP_HOST'].substr(JPATH_BASE,strlen($_SERVER['DOCUMENT_ROOT']));
	} else {
		$joomlauri	= $_SERVER['HTTP_HOST'];
	}

	$settings	= jOpenSimSettings();
	
	$retval['success'] = FALSE;
	if($settings['jopensimmoney_buycurrency'] == 0) {
		$retval['errorMessage'] = JText::_('JOPENSIM_MONEY_BUYCURRENCY_DISABLED_MSG');
		$joomlauri = $settings['jopensimmoney_buycurrency_url'];
	} else {
		if($settings['jopensimmoney_buycurrency_customized'] == 0) {
			$retval['errorMessage'] = JText::_('JOPENSIM_MONEY_BUYCURRENCY_MSG');
			if($settings['jopensimmoney_buycurrency_url']) $joomlauri = $settings['jopensimmoney_buycurrency_url'];
		} else {
			if($settings['jopensimmoney_buycurrency_custom_msg']) $retval['errorMessage'] = $settings['jopensimmoney_buycurrency_custom_msg'];
			else $retval['errorMessage'] = JText::_('JOPENSIM_MONEY_BUYCURRENCY_MSG');
			if($settings['jopensimmoney_buycurrency_custom_url']) {
				$joomlauri = $settings['jopensimmoney_buycurrency_custom_url'];
			} elseif($settings['jopensimmoney_buycurrency_url']) {
				$joomlauri = $settings['jopensimmoney_buycurrency_url'];
			}
		}
	}
	$retval['errorURI'] = $joomlauri;
	return $retval;
}

function checkCustomFee($uuid) {
		$query	= sprintf("SELECT #__opensim_money_customfees.uploadfee, #__opensim_money_customfees.groupfee FROM #__opensim_money_customfees WHERE #__opensim_money_customfees.PrincipalID = '%s'",$uuid);
		$db		= JFactory::getDBO();
		$db->setQuery($query);
		$customfees = $db->loadAssoc();
		return $customfees;
}

// added this function temporary to find out why sometimes the grid balance is not zero
function checkGridBalance($functionname = "n/a") {
	$settings = jOpenSimSettings();
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug['args'] = varexport(func_get_args(),TRUE);
		debugzeile($debug,"checkGridBalance");
	}
	$query	= "SELECT
					SUM(#__opensim_moneybalances.balance) AS gridbalance
				FROM
					#__opensim_moneybalances";
	$db		= JFactory::getDBO();
	$db->setQuery($query);
	$row	= $db->loadAssoc();

	if($row['gridbalance'] != 0) {
		$query = "SELECT #__opensim_moneygridbalance.* FROM #__opensim_moneygridbalance ORDER BY #__opensim_moneygridbalance.timestamp DESC LIMIT 0,1";
		$db->setQuery($query);
		$row2 = $db->loadAssoc();
		if(!is_array($row2) || $row2['gridbalance'] != $row['gridbalance']) {
			$query = "SELECT
							#__opensim_moneytransactions.*
						FROM
							#__opensim_moneytransactions
						ORDER BY
							#__opensim_moneytransactions.`time` DESC
						LIMIT 0,1";
			$db->setQuery($query);
			$row3 = $db->loadAssoc();
			$query = sprintf("INSERT INTO #__opensim_moneygridbalance (`timestamp`,`gridbalance`) VALUES ('%s','%d')",$row3['time'],$row['gridbalance']);
			$db->setQuery($query);
			$db->execute();
			$debug = varexport($row3,TRUE);
			if($settings['jopensimmoney_sendgridbalancewarning'] == 1 && $settings['jopensimmoney_warningrecipient']) {
				$message = "Since ".date("Y-m-d H:i:s",$row3['time'])." (timestamp: ".$row3['time'].") the grid balance is ".$row['gridbalance']."\n\nFunction: ".$functionname."\n\nLast row in #__opensim_moneytransactions:\n".$debug;
				mail($settings['jopensimmoney_warningrecipient'],$settings['jopensimmoney_warningsubject'],$message);
			}
		}
	}
}

function clientInfo($parameter) {
	global $debug;
	if(is_array($debug) && array_key_exists("profile",$debug) && $debug['profile'] == "1") debugzeile($parameter,"\$parameter for ".__FUNCTION__);
	if(!array_key_exists("agentName",$parameter) && !array_key_exists("agentIP",$parameter) && !array_key_exists("agentID",$parameter)) return; // no params, what should we save?
	$agent	= (array_key_exists("agentName",$parameter)) ? $parameter['agentName']:"unknown";
	$userip	= (array_key_exists("agentIP",$parameter)) ? $parameter['agentIP']:"127.0.0.3";
	$uuid	= (array_key_exists("agentID",$parameter)) ? $parameter['agentID']:"00000000-0000-0000-0000-000000000000";
	if(strstr($agent,"@") !== FALSE) {
		$lastpos	= strlen($agent) - strlen(strrchr($agent,"@"));
		$hoststring	= "http://".substr(strrchr($agent,"@"),1);
		$hostarray	= parse_url($hoststring);
		if(array_key_exists("host",$hostarray) && $hostarray['host']) {
			// den Hostnamen aus URL holen
			preg_match('@^(?:http://)?([^/]+)@i',$hostarray['host'], $treffer);
			$host = $treffer[1];
			// die letzten beiden Segmente aus Hostnamen holen
			preg_match('/[^.]+\.[^.]+$/', $host, $treffer);
			if(is_array($treffer) && count($treffer) > 0 && $treffer[0]) {
				$username	= substr($agent,0,$lastpos);
				$host		= $hostarray['host'];
			} else {
				$username	= $agent;
				$host		= "local";
			}
		} else {
			$username	= $agent;
			$host		= "local";
		}
	} else {
		$username	= $agent;
		$host		= "local";
	}
	$query = sprintf("INSERT INTO #__opensim_clientinfo (PrincipalID,userName,grid,remoteip,lastseen,`from`) VALUES ('%1\$s','%2\$s','%3\$s','%4\$s',NOW(),'P')
						ON DUPLICATE KEY UPDATE userName = '%2\$s', grid = '%3\$s', remoteip = '%4\$s', lastseen = NOW(), `from`= 'P'",
		$uuid,
		mysqlsafestring(trim($username)),
		$host,
		$userip);
	$db = JFactory::getDBO();
	$db->setQuery($query);
	$db->execute();
}


function lockSession($sessionid) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($sessionid,TRUE);
		debugzeile($debug,"Parameter lockSession");
	}
	$db		= JFactory::getDbo();

	$newSession	= new stdClass();
	$newSession->sessionid			= $sessionid;

	$result = $db->insertObject('#__opensim_moneysessions', $newSession);
}

function unlockSession($sessionid) {
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($sessionid,TRUE);
		debugzeile($debug,"Parameter unlockSession");
	}
	$db		= JFactory::getDbo();
	$query = $db->getQuery(true);

	$conditions = array(
		$db->quoteName('sessionid').' = '.$db->quote($sessionid)
	);
	$query->delete($db->quoteName('#__opensim_moneysessions'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();
}

function checkSession($sessionid) {
	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query->select($db->quoteName('#__opensim_moneysessions.sessionid'));
	$query->from($db->quoteName('#__opensim_moneysessions'));
	$query->where($db->quoteName('#__opensim_moneysessions.sessionid').' = '.$db->quote($sessionid));

	$db->setQuery($query);
	$db->execute();
	$foundsession = $db->getNumRows();
	if($foundsession > 0) {
		$retval = "locked";
	} else {
		$retval = "free";
	}
	if(_JOPENSIMMONEYDEBUG === TRUE) {
		$debug = varexport($sessionid,TRUE);
		debugzeile($debug,"Parameter checkSession");
		debugzeile($retval,"retval checkSession");
	}
	return $retval;
}

?>