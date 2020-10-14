<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

class opensimModelInterfaceCurrency extends opensimModelInterface {

	public $debug	= FALSE;

	public function __construct() {
		parent::__construct();
		if($this->jdebug['currency']) $this->debug	= TRUE;
	}

	public function initMethods() { // empty this this method to avoid endless loops
		return;
	}

	public function initAddons() { // empty this this method to avoid endless loops
		return;
	}

	public function getMoneySettingsData($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$feld		= $parameter['settingsfield'];
		$returntype	= (array_key_exists("returntype", $parameter)) ? $parameter['returntype']:"char";
		$wert		= $this->getSettingsValue($feld);
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

	public function getSettingsValue($field) {
		$retval = null;
		switch($field) {
			case "bankerUID":
			case "bankerUUID":
				$retval = $this->settings['jopensimmoneybanker'];
			break;
			case "groupCharge":
				$retval = $this->settings['jopensimmoney_groupcreation'];
			break;
			case "uploadCharge":
				$retval = $this->settings['jopensimmoney_upload'];
			break;
			case "startBalance":
				$retval = $this->settings['jopensimmoney_startbalance'];
			break;
			case "groupMinDividend":
				$retval = $this->settings['jopensimmoney_groupdividend'];
			break;
			case "currencyName":
			case "name":
				$retval = $this->settings['jopensimmoney_currencyname'];
			break;
			case "bankerName":
				$retval = $this->settings['jopensimmoney_bankername'];
			break;
			default:
				$retval = FALSE;
			break;
		}
		return $retval;
	}

	public function GetMoneyBalance($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$startbalance	= $this->getSettingsValue("startBalance");
		$startbalanceHG	= $this->getSettingsValue("startBalanceHG");
		if($startbalance === FALSE || !$startbalance) { // we did not find any value for the startbalance of new users
			$startbalance = 0;
		}
		if(!isset($parameter['clientUUID'])) { // This parameter is obligatory
			$retval['success']	= FALSE;
			$retval['message']	= "No clientUUID provided for function GetBalance() in ".__FILE__." at line ".__LINE__;
			return $retval;
		}

		if(!array_key_exists("AgentHomeURL",$parameter)) $parameter['AgentHomeURL'] = "local"; // just in case still an old module is running, no HG is considered
		if($parameter['AgentHomeURL'] != "local") $startbalance = $startbalanceHG;

		$clientSessionID		= (isset($parameter['clientSessionID']))		? $parameter['clientSessionID']:null;
		$clientSecureSessionID	= (isset($parameter['clientSecureSessionID']))	? $parameter['clientSecureSessionID']:null;
		$uuid					= $parameter['clientUUID'];
		$db						= JFactory::getDBO();
		$query					= $db->getQuery(true);

		$query->select($db->quoteName('#__opensim_moneybalances.balance'));
		$query->from($db->quoteName('#__opensim_moneybalances'));
		$query->where($db->quoteName('#__opensim_moneybalances.user')." = ".$db->quote($parameter['clientUUID']));
		$query->where($db->quoteName('#__opensim_moneybalances.homeurl')." = ".$db->quote($parameter['AgentHomeURL']));

		$db->setQuery($query);
		$db->execute();
		$num_rows 				= $db->getNumRows();

		if($num_rows == 0) {
			$newBalance				= new stdClass();
			$newBalance->user		= $parameter['clientUUID'];
			$newBalance->homeurl	= $parameter['AgentHomeURL'];
			$newBalance->balance	= 0;
			$newBalance->status		= "1";
			$result = $db->insertObject('#__opensim_moneybalances', $newBalance);

			$parameter['senderID']		= $this->getSettingsValue("bankerUID");
			$parameter['senderHome']	= "local";
			$parameter['receiverID']	= $uuid;
			$parameter['receiverHome']	= $parameter['AgentHomeURL'];
			$parameter['amount']		= $startbalance;
			$parameter['description']	= JTEXT::_('JOPENSIM_MONEY_STARTBALANCE');
			$this->TransferMoney($parameter);

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

	public function TransferMoney($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!array_key_exists("senderHome",$parameter) || !$parameter['senderHome']) $parameter['senderHome']		= "local";
		if(!array_key_exists("receiverHome",$parameter) || !$parameter['receiverHome']) $parameter['receiverHome']	= "local";
		$isSender	= $this->checkClient($parameter['senderID'],$parameter['senderHome']);
		$isReceiver	= $this->checkClient($parameter['receiverID'],$parameter['receiverHome']);
		if(!array_key_exists("senderSecureSessionID",$parameter)) $parameter['senderSecureSessionID'] = $this->uuidZero;

		// Maybe receiver is a group without balance yet?
		if($isReceiver === FALSE) {
			$isGroup = $this->checkGroup($parameter['receiverID']);
			if($isGroup === TRUE) {
				$this->balanceExists($parameter['receiverID'],"2");
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
			$amountcovered = $this->AmountCovered($checkbalance);
			if($amountcovered['success'] !== TRUE) {
				$retval['success']	= FALSE;
				$retval['message']	= "Transaction failed (AmountCovered failed)";
			} else {
				$checksession = $this->checkSession($parameter['senderSecureSessionID']);
				if($checksession == "locked") {
					$retval['success']	= FALSE;
					$retval['message']	= "Transaction failed (session currently in use)";
				} else {
					$parameter['time'] = time();
					$parameter['status'] = 0;
					try {
						$db		= JFactory::getDBO();
						$this->lockSession($parameter['senderSecureSessionID']);
						$db->transactionStart();
			
						$this->insertTransaction($parameter);
			
						$this->setBalance($parameter['receiverID'],$parameter['amount'],$parameter['receiverHome']);
						$this->setBalance($parameter['senderID'],-$parameter['amount'],$parameter['senderHome']);
			
						$db->transactionCommit();
						$this->unlockSession($parameter['senderSecureSessionID']);
			
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
		$this->checkGridBalance("TransferMoney");
		return $retval;
	}

	public function insertTransaction($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
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

		$randomUUID				= $this->getUUID();

		$newTransaction					= new stdClass();
		$newTransaction->UUID			= $randomUUID;
		$newTransaction->sender			= $senderID;
		$newTransaction->receiver		= $receiverID;
		$newTransaction->amount			= $amount;
		$newTransaction->objectUUID		= $objectID;
		$newTransaction->regionHandle	= $regionHandle;
		$newTransaction->type			= $transactionType;
		$newTransaction->time			= $time;
		$newTransaction->secure			= $senderSecureSessionID;
		$newTransaction->status			= $status;
		$newTransaction->description	= $description;

		$db		= JFactory::getDBO();
		$result = $db->insertObject('#__opensim_moneytransactions', $newTransaction);
	}

	public function setBalance($uuid,$amount,$home = "local") {
		if($this->debug === TRUE) {
			$arg_list = func_get_args();
			$debug = $this->varexport($arg_list,TRUE);
			$this->debuglog($debug,"Parameter for ".__FUNCTION__);
		}
		$db			= JFactory::getDBO();
		$isGroup	= $this->checkGroup($uuid);
		if($isGroup === TRUE) $this->balanceExists($uuid,"2"); // $uuid could be a group, see if it exists and if not, create a balance line for it
		$query		= sprintf("UPDATE #__opensim_moneybalances SET balance = balance + %d WHERE `user`= %s AND homeurl = %s",$amount,$db->quote($uuid),$db->quote($home));
		$db->setQuery($query);
		$db->execute();
	}

	public function checkClient($uuid,$home = "local") {
//		if($this->debug === TRUE) {
			$arg_list = func_get_args();
			$this->debuglog($arg_list,"Parameter for ".__FUNCTION__);
//		}
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query
			->select($db->quoteName('#__opensim_moneybalances').".*")
			->from($db->quoteName('#__opensim_moneybalances'))
			->where($db->quoteName('#__opensim_moneybalances.user')." = ".$db->quote($uuid))
			->where($db->quoteName('#__opensim_moneybalances.homeurl')." = ".$db->quote($home));
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 1) {
			$this->debuglog($uuid." gefunden!",__FUNCTION__);
			return TRUE;
		} else {
			$this->debuglog($uuid." NICHT gefunden!",__FUNCTION__);
			return FALSE;
		}
	}

	public function checkGroup($uuid) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query
			->select($db->quoteName('#__opensim_group').".*")
			->from($db->quoteName('#__opensim_group'))
			->where($db->quoteName('#__opensim_group.GroupID')." = ".$db->quote($uuid));
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function AmountCovered($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$uuid	= $parameter['clientUUID'];
		$home	= (array_key_exists("AgentHomeURL",$parameter)) ? $parameter['AgentHomeURL']:"local";
		$banker	= $this->getSettingsValue("bankerUID");
		if($banker == $uuid) {
			$retval['success'] = TRUE; // The banker ALLWAYS has sufficient balance ;)
			return $retval;
		}
		$amount	= $parameter['amount'];
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query
			->select($db->quoteName('#__opensim_moneybalances.balance'))
			->from($db->quoteName('#__opensim_moneybalances'))
			->where($db->quoteName('#__opensim_moneybalances.user')." = ".$db->quote($uuid))
			->where($db->quoteName('#__opensim_moneybalances.homeurl')." = ".$db->quote($home));
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

	public function ApplyCharge($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$amount							= $parameter['amount'];
		$parameter['senderID']			= $parameter['clientUUID'];
		$parameter['senderHome']		= (array_key_exists("AgentHomeURL",$parameter)) ? $parameter['AgentHomeURL']:"local";
		$parameter['receiverID']		= $this->getSettingsValue("bankerUID");
		$parameter['receiverHome']		= "local";
		$parameter['time']				= time();
		if(!array_key_exists("description",$parameter)) $parameter['description'] = null; // avoid notices
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

		$customfee = $this->checkCustomFee($parameter['senderID']);
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
		} elseif($this->AmountCovered($parameter) === FALSE) {
			$retval['success'] = FALSE;
			$retval['message'] = "Insufficient balance for $amount!"; // This should actually always have happened before already, but however...
		} else {
			$this->insertTransaction($parameter);
			$this->setBalance($parameter['receiverID'],$amount,$parameter['receiverHome']);
			$this->setBalance($parameter['senderID'],-$amount,$parameter['senderHome']);
			$retval['success'] = TRUE;
		}
		$this->checkGridBalance("ApplyCharge");
		return $retval;
	}

	public function balanceExists($uuid,$status = "1",$home = "local") { // if this $uuid does not exist yet, it will create a 0 Balance for it (different to GetBalance, where $startbalance will be created)
		$parameter = func_get_args();
		if($this->debug === TRUE) {
			$this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		}
		$this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query
			->select($db->quoteName('#__opensim_moneybalances.balance'))
			->from($db->quoteName('#__opensim_moneybalances'))
			->where($db->quoteName('#__opensim_moneybalances.user')." = ".$db->quote($uuid))
			->where($db->quoteName('#__opensim_moneybalances.homeurl')." = ".$db->quote($home));

		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 0) {
			$balanceLine				= new stdClass();
			$balanceLine->user		= $uuid;
			$balanceLine->homeurl	= $home;
			$balanceLine->balance	= 0;
			$balanceLine->status	= $status;

			$result	= $db->insertObject('#__opensim_moneybalances', $balanceLine);
		}
	}

	public function buyCurrency($parameter) {
		return $this->getCurrencyQuote($parameter);
	}

	public function getCurrencyQuote($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		// get a default value for emergency
		$joomlauri			= JURI::root();
		$retval['success']	= FALSE;

		if($this->settings['jopensimmoney_buycurrency'] == 0) {
			$retval['errorMessage'] = JText::_('JOPENSIM_MONEY_BUYCURRENCY_DISABLED_MSG');
			$joomlauri = $this->settings['jopensimmoney_buycurrency_url'];
		} else {
			if($this->settings['jopensimmoney_buycurrency_customized'] == 0) {
				$retval['errorMessage'] = JText::_('JOPENSIM_MONEY_BUYCURRENCY_MSG');
				if($this->settings['jopensimmoney_buycurrency_url']) $joomlauri = $this->settings['jopensimmoney_buycurrency_url'];
			} else {
				if($this->settings['jopensimmoney_buycurrency_custom_msg']) $retval['errorMessage'] = $this->settings['jopensimmoney_buycurrency_custom_msg'];
				else $retval['errorMessage'] = JText::_('JOPENSIM_MONEY_BUYCURRENCY_MSG');
				if($this->settings['jopensimmoney_buycurrency_custom_url']) {
					$joomlauri = $this->settings['jopensimmoney_buycurrency_custom_url'];
				} elseif($this->settings['jopensimmoney_buycurrency_url']) {
					$joomlauri = $this->settings['jopensimmoney_buycurrency_url'];
				}
			}
		}
		$retval['errorURI'] = $joomlauri;
		return $retval;
	}

	public function preflightBuyLandPrep($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		$agentid	  = $parameter['agentId'];
		$sessionid	  = $parameter['secureSessionId'];
		$amount		  = $parameter['currencyBuy'];
		$billableArea = $parameter['billableArea'];

		$confirmvalue = $this->get_confirm_value();
		$membership_levels = array('levels' => array('id' => $this->uuidZero, 'description' => "some level"));
//		$sysurl = "http:/"."/".$_SERVER['HTTP_HOST']."/components/com_opensim/";
		$sysurl		= JURI::root()."index.php?option=com_opensim&view=interface";
		$landUse	= array('upgrade' => False, 'action' => "".$sysurl."");
		$currency   = array('estimatedCost' => $this->convert_to_real($amount));
		$membership = array('upgrade' => False, 'action' => "".$sysurl."", 'levels' => $membership_levels);
		$retval = array('success'	=> True,
						'currency'  => $currency,
						'membership'=> $membership,
						'landUse'	=> $landUse,
						'currency'  => $currency,
						'confirm'	=> $confirmvalue);
		return $retval;
	}

	public function buyLandPrep($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		return $this->getCurrencyQuote($parameter);
	}

	public function checkCustomFee($uuid) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query
			->select($db->quoteName('#__opensim_money_customfees.uploadfee'))
			->select($db->quoteName('#__opensim_money_customfees.groupfee'))
			->from($db->quoteName('#__opensim_money_customfees'))
			->where($db->quoteName('#__opensim_money_customfees.PrincipalID')." = ".$db->quote($uuid));
		$db->setQuery($query);
		$customfees = $db->loadAssoc();
		return $customfees;
	}

	public function checkGridBalance($functionname = "n/a") {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query
				->select("SUM(".$db->quoteName('#__opensim_moneybalances.balance').") AS gridbalance")
				->from($db->quoteName('#__opensim_moneybalances'));
		$db->setQuery($query);
		$row	= $db->loadAssoc();

		if($row['gridbalance'] != 0) {
			$query	= $db->getQuery(true);
			$query
				->select($db->quoteName('#__opensim_moneygridbalance').".*")
				->from($db->quoteName('#__opensim_moneygridbalance'))
				->order($db->quoteName('#__opensim_moneygridbalance.timestamp')." DESC")
				->setLimit('1');

			$db->setQuery($query);
			$row2 = $db->loadAssoc();
			if(!is_array($row2) || $row2['gridbalance'] != $row['gridbalance']) {

			$query	= $db->getQuery(true);
			$query
				->select($db->quoteName('#__opensim_moneytransactions').".*")
				->from($db->quoteName('#__opensim_moneytransactions'))
				->order($db->quoteName('#__opensim_moneytransactions.time')." DESC")
				->setLimit('1');

				$db->setQuery($query);
				$row3 = $db->loadAssoc();

				$gridBalanceLine				= new stdClass();
				$gridBalanceLine->timestamp		= $row3['time'];
				$gridBalanceLine->gridbalance	= $row['gridbalance'];

				$result	= $db->insertObject('#__opensim_moneygridbalance', $gridBalanceLine);

				$debug	= $this->varexport($row3,TRUE);
				if($this->settings['jopensimmoney_sendgridbalancewarning'] == 1 && $this->settings['jopensimmoney_warningrecipient']) {
					$message = "Since ".date("Y-m-d H:i:s",$row3['time'])." (timestamp: ".$row3['time'].") the grid balance is ".$row['gridbalance']."\n\nFunction: ".$functionname."\n\nLast row in #__opensim_moneytransactions:\n".$debug;
					mail($this->settings['jopensimmoney_warningrecipient'],$this->settings['jopensimmoney_warningsubject'],$message);
				}
			}
		}
	}

	public function groupDividend($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!isset($parameter['groupID'])) {
			$retval['success'] = FALSE;
			$retval['message'] = "No GroupID provided for groupDividend in ".__FILE__." at line ".__LINE__;
			return $retval;
		} elseif(!isset($parameter['groupPower']) || intval($parameter['groupPower']) == 0) {
			$retval['success'] = FALSE;
			$retval['message'] = "Missing GroupPower-Bit for groupMembershipFee in ".__FILE__." at line ".__LINE__;
			return $retval;
		}

		$groupMinDividend	= $this->getSettingsValue('groupMinDividend');
		if($groupMinDividend == 0) { // nothing set for groupMinDividend, we dont pay anything
			$retval['success'] = TRUE;
			$retval['message'] = "groupMinDividend = 0 ... payment done manually? -> in ".__FILE__." at line ".__LINE__;
			return $retval;
		}
		$groupID	= $parameter['groupID'];
		$db			= JFactory::getDBO();
		$query		= $db->getQuery(true);
		$query
			->select($db->quoteName('#__opensim_moneybalances.balance'))
			->from($db->quoteName('#__opensim_moneybalances'))
			->where($db->quoteName('#__opensim_moneybalances.user')." = ".$db->quote($parameter['groupID']));
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

		$query		= $db->getQuery(true);
		$query
			->select($db->quoteName('#__opensim_grouprolemembership.AgentID'))
			->from($db->quoteName('#__opensim_grouprolemembership'))
			->from($db->quoteName('#__opensim_grouprole'))
			->from($db->quoteName('#__opensim_moneybalances'))
			->where($db->quoteName('#__opensim_grouprolemembership.RoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID'))
			->where($db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID'))
			->where($db->quoteName('#__opensim_grouprolemembership.AgentID')." = ".$db->quoteName('#__opensim_moneybalances.user'))
			->where($db->quoteName('#__opensim_grouprole.GroupID')." = ".$db->quote($parameter['groupID']))
			->where($db->quoteName('#__opensim_grouprole.Powers')." = ".$db->quote($parameter['groupPower']))
			->group($db->quoteName('#__opensim_grouprolemembership.AgentID'));

		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		if($num_rows == 0) { // no member??? nobody to pay for
			$retval['success'] = FALSE;
			$retval['message'] = "Nobody in this group?";
		}
		$groupdividend		= intval(floor($groupBalance / $num_rows));
		if($groupMinDividend > $groupdividend) { // The minimum for paying dividend is not reached yet
			$retval['success']			= TRUE;
			$retval['message']			= "Minimum group dividend not reached yet";
			$retval['groupMinDividend']	= $groupMinDividend;
			$retval['groupdividend']	= $groupdividend;
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
			$retval								= $this->TransferMoney($parameter); // Transfer the groupfee from the group to the ones who should get
			if($retval['success'] === FALSE) {
				$this->checkGridBalance("groupDividend1");
				return $retval; // something went wrong?
			}
		}
		$this->checkGridBalance("groupDividend2");
		$retval['success']	= TRUE;
		return $retval;
	}

	public function groupMembershipFee($parameter) {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
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

		$this->balanceExists($groupID,"2"); // check if Group already has a balance line (and if not create it)
		$parameter 							= array();
		$parameter['amount']				= $groupfee;
		$parameter['senderID']				= $agentID;
		$parameter['receiverID']			= $groupID;
		$parameter['clientSessionID']		= $sessionID;
		$parameter['clientUUID']			= $agentID;
		$parameter['clientSecureSessionID']	= null;
		$parameter['description']			= "Group Enrollment Fee";
		$retval								= $this->TransferMoney($parameter); // Transfer the groupfee from the new member to the group
		$this->checkGridBalance("groupMembershipFee");
		if($retval['success'] === FALSE) {
			return $retval; // something went wrong?
		} else { // everything ok, we should check if group pays dividend now
			$parameter['groupID']		= $groupID;
			$parameter['groupPower']	= $groupPower;
			$retval = $this->groupDividend($parameter);
			return $retval;
		}
	}

	public function checkSession($sessionid) {
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
		return $retval;
	}

	public function lockSession($sessionid) {
		if($this->debug === TRUE) $this->debuglog($sessionid,"sessionid for ".__FUNCTION__);
		$db		= JFactory::getDbo();

		$newSession	= new stdClass();
		$newSession->sessionid			= $sessionid;

		$result = $db->insertObject('#__opensim_moneysessions', $newSession);
	}

	public function unlockSession($sessionid) {
		if($this->debug === TRUE) $this->debuglog($sessionid,"sessionid for ".__FUNCTION__);
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

	public function clientInfo($parameter, $source = "M") {
		parent::clientInfo($parameter, $source);
	}

	// Todo: "echte" Currency Funktionen
	public function convert_to_real($wert) {
		return intval($wert);
	}

	public function get_confirm_value() {
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$key = "1234567883789";
		$confirmvalue = md5($key."_".$ipAddress);
		return $confirmvalue;
	}

}
?>