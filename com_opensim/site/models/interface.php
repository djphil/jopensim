<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

// require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

if(!function_exists("mysqlsafestring")) {
	function mysqlsafestring($string) {
		$db = JFactory::getDBO();
		return $db->escape($string);
	}
}

class opensimModelInterface extends OpenSimModelOpenSim {

	public $settings;
	public $filename		= "interface.php";
	public $view			= "interface";
	public $_os_db;
	public $_osgrid_db;
	public $_db;
	public $xmlrpcfunctions = array();
	public $debug;
	public $debuglogfile;
	public $paypalcall		= FALSE;
	public $uuidZero		= "00000000-0000-0000-0000-000000000000";

	public $groupWriteKey;
	public $groupReadKey;

	// will be filled with helper classes when addon is enabled
	public $groups			= null;
	public $search			= null;
	public $profiles		= null;

	public function __construct() {
		parent::__construct();
		$this->settings = $this->getSettingsData();
		if(!is_writable($this->settings['jopensim_debug_path'])) $this->settings['jopensim_debug_path'] = JPATH_SITE."/components/com_opensim/";
		$this->initMethods();
		$this->initAddons();
		$this->setDebug();
//		$this->debuglog($this->debug,"debug");
	}

	public function setPayPalCall() {
		$this->paypalcall	= TRUE;
	}

	public function initAddons() {
		$addon	= $this->settings['addons'];

		$addons['messages']		= $addon & 1;
		$addons['profile']		= $addon & 2;
		$addons['groups']		= $addon & 4;
		$addons['inworldident']	= $addon & 8;
		$addons['search']		= $addon & 16;
		$addons['currency']		= $addon & 32;

		if($addons['messages'] > 0) {
			$this->addon_offlinemsg		=  1;
//			require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.messages.php');
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.messages.php');
			$this->addon_offlinemsg		=  1;
			$this->messages				= new opensimModelInterfaceMessages();
		} else {
			$this->addon_offlinemsg		=  0;
		}
		if($addons['profile'] > 0) {
//			require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.profiles.php');
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.profiles.php');
			$this->profiles				= new opensimModelInterfaceProfiles();
			$this->addon_profiles		=  1;
		} else {
			$this->addon_profiles		=  0;
			unset($this->xmlrpcfunctions['profilefunctions']);
		}
		if($addons['groups'] > 0) {
//			require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.groups.php');
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.groups.php');
			$this->groups				= new opensimModelInterfaceGroups();
			$this->addon_groups			=  1;
		} else {
			$this->addon_groups			=  0;
			unset($this->xmlrpcfunctions['groupfunctions']);
		}
		if($addons['search'] > 0) {
//			require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.search.php');
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.search.php');
			$this->search				= new opensimModelInterfaceSearch();
			$this->addon_search			=  1;
		} else {
			$this->addon_search			=  0;
			unset($this->xmlrpcfunctions['searchfunctions']);
		}
		if($addons['inworldident'] > 0) {
//			require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.terminals.php');
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.terminals.php');
			$this->terminal				= new opensimModelInterfaceTerminals();
			$this->addon_inworldident	=  1;
		} else {
			$this->addon_inworldident	=  0;
		}
		if($addons['currency'] > 0) {
//			require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.currency.php');
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'interface.currency.php');
			$this->money				= new opensimModelInterfaceCurrency();
			$this->addon_currency		=  1;
		} else {
			$this->addon_currency		=  0;
		}
	}

	public function initMethods() {
		$this->xmlrpcfunctions['searchfunctions'] = array(
				"registerSearch",
				"init_SearchDataUpdate",
				"searchDataUpdate",
				"dir_places_query",
				"dir_popular_query",
				"dir_land_query",
				"dir_events_query",
				"dir_classified_query",
				"event_info_query",
				"classifieds_info_query"); // all methods for handling inworld search
		$this->xmlrpcfunctions['profilefunctions'] = array(
				"avatar_properties_request",
				"avatar_properties_update",
				"avatar_interests_update",
				"avatarnotesrequest",
				"avatar_notes_update",
				"avatarpicksrequest",
				"pickinforequest",
				"picks_update",
				"picks_delete",
				"avatarclassifiedsrequest",
				"classifiedinforequest",
				"classified_update",
				"classified_delete",
				"user_preferences_request",
				"user_preferences_update",
				"clientInfo"); // all methods for the profile handling
		$this->xmlrpcfunctions['groupfunctions'] = array(
				"groups.createGroup"				=> array("function" => "createGroup"),
				"groups.updateGroup"				=> array("function" => "updateGroup"),
				"groups.getGroup"					=> array("function" => "getGroup"),
				"groups.findGroups"					=> array("function" => "findGroups"),
				"groups.getGroupRoles"				=> array("function" => "getGroupRoles"),
				"groups.addRoleToGroup"				=> array("function" => "addRoleToGroup"),
				"groups.removeRoleFromGroup"		=> array("function" => "removeRoleFromGroup"),
				"groups.updateGroupRole"			=> array("function" => "updateGroupRole"),
				"groups.getGroupRoleMembers"		=> array("function" => "getGroupRoleMembers"),
				"groups.setAgentGroupSelectedRole"	=> array("function" => "setAgentGroupSelectedRole"),
				"groups.addAgentToGroupRole"		=> array("function" => "addAgentToGroupRole"),
				"groups.removeAgentFromGroupRole"	=> array("function" => "removeAgentFromGroupRole"),
				"groups.getGroupMembers"			=> array("function" => "getGroupMembers"),
				"groups.addAgentToGroup"			=> array("function" => "addAgentToGroup"),
				"groups.removeAgentFromGroup"		=> array("function" => "removeAgentFromGroup"),
				"groups.setAgentGroupInfo"			=> array("function" => "setAgentGroupInfo"),
				"groups.addAgentToGroupInvite"		=> array("function" => "addAgentToGroupInvite"),
				"groups.getAgentToGroupInvite"		=> array("function" => "getAgentToGroupInvite"),
				"groups.removeAgentToGroupInvite"	=> array("function" => "removeAgentToGroupInvite"),
				"groups.setAgentActiveGroup"		=> array("function" => "setAgentActiveGroup"),
				"groups.getAgentGroupMembership"	=> array("function" => "getAgentGroupMembership"),
				"groups.getAgentGroupMemberships"	=> array("function" => "getAgentGroupMemberships"),
				"groups.getAgentActiveMembership"	=> array("function" => "getAgentActiveMembership"),
				"groups.getAgentRoles"				=> array("function" => "getAgentRoles"),
				"groups.getGroupNotices"			=> array("function" => "getGroupNotices"),
				"groups.getGroupNotice"				=> array("function" => "getGroupNotice"),
				"groups.addGroupNotice"				=> array("function" => "addGroupNotice")); // all (?) methods for group handling
		$this->xmlrpcfunctions['currencyfunctions'] = array(
				"GetBalance"			=> array("function" => "GetMoneyBalance", "public" => FALSE),
				"TransferMoney"			=> array("function" => "TransferMoney", "public" => FALSE),
				"AmountCovered"			=> array("function" => "AmountCovered", "public" => FALSE),
				"ApplyCharge"			=> array("function" => "ApplyCharge", "public" => FALSE),
				"getSettingsData"		=> array("function" => "getMoneySettingsData", "public" => TRUE),
				"buy_land_prep"			=> array("function" => "buyLandPrep", "public" => TRUE),
				"buy_land"				=> array("function" => "buy_land", "public" => FALSE),
				"getCurrencyQuote"		=> array("function" => "getCurrencyQuote", "public" => FALSE),
				"preflightBuyLandPrep"	=> array("function" => "preflightBuyLandPrep", "public" => TRUE),
				"buyLandPrep"			=> array("function" => "buyLandPrep", "public" => TRUE),
				"buyCurrency"			=> array("function" => "buyCurrency", "public" => FALSE),
				"clientInfo"			=> array("function" => "clientInfo", "public" => FALSE)
				); // all methods for handling inworld currency (TRUE = public method)
		$this->xmlrpcfunctions['messagefunctions'] = array(
				"SaveMessage",
				"RetrieveMessages"
				);
		$this->xmlrpcfunctions['terminalfunctions'] = array(
				"identifyTerminal",
				"registerTerminal",
				"setStateTerminal"
		);
	}

	public function subCall($method,$params) {
		$remoteip	= $_SERVER['REMOTE_ADDR']; // who is talking with us?
		$access		= $this->opensim->checkRegionIP($remoteip);
		if(in_array($method,$this->xmlrpcfunctions['terminalfunctions'])) {
			if($access === FALSE) return $this->accessfailed("terminals",$method,$remoteip);
			elseif($this->jdebug['access'] && $this->jdebug['terminal']) $this->debuglog("Access granted for ".$remoteip,"terminals");
			if(method_exists($this->terminal,$method)) {
				return $this->terminal->$method($params);
			} else {
				return $this->norepsonse($method,"terminals");
			}
		} elseif(in_array($method,$this->xmlrpcfunctions['messagefunctions'])) {
			if($access === FALSE) return $this->accessfailed("offlinemessages",$method,$remoteip);
			elseif($this->jdebug['access'] && $this->jdebug['messages']) $this->debuglog("Access granted for ".$remoteip,"messages");
			if(method_exists($this->messages,$method)) {
				return $this->messages->$method($params);
			} else {
				return $this->norepsonse($method,"messages");
			}
		} elseif(in_array($method,$this->xmlrpcfunctions['searchfunctions'])) {
			if($access === FALSE) return $this->accessfailed("search",$method,$remoteip);
			elseif($this->jdebug['access'] && $this->jdebug['search']) $this->debuglog("Access granted for ".$remoteip,"search");
			if(method_exists($this->search,$method)) {
				return $this->search->$method($params);
			} else {
				return $this->norepsonse($method,"search");
			}
		} elseif(in_array($method,$this->xmlrpcfunctions['profilefunctions'])) {
			if($access === FALSE) return $this->accessfailed("profiles",$method,$remoteip);
			elseif($this->jdebug['access'] && $this->jdebug['profile']) $this->debuglog("Access granted for ".$remoteip,"profiles");
			if(method_exists($this->profiles,$method)) {
				return $this->profiles->$method($params);
			} else {
				return $this->norepsonse($method,"profiles");
			}
		} elseif(array_key_exists($method,$this->xmlrpcfunctions['groupfunctions'])) {
			if($access === FALSE) return $this->accessfailed("groups",$method,$remoteip);
			elseif($this->jdebug['access'] && $this->jdebug['groups']) $this->debuglog("Access granted for ".$remoteip,"groups");
			if(method_exists($this->groups,$this->xmlrpcfunctions['groupfunctions'][$method]['function'])) {
				if(array_key_exists("requestingAgent",$params)) $this->groups->setRequestingAgent($params['requestingAgent']);
				else $this->groups->setRequestingAgent(null);
				$subcallmethod = $this->xmlrpcfunctions['groupfunctions'][$method]['function'];
				return $this->groups->$subcallmethod($params);
			} else {
				return $this->norepsonse($method,"groups");
			}
		} elseif(array_key_exists($method,$this->xmlrpcfunctions['currencyfunctions'])) {
			if($this->paypalcall === FALSE && $access === FALSE && $this->xmlrpcfunctions['currencyfunctions'][$method]['public'] === FALSE) return $this->accessfailed("currency",$method,$remoteip);
			elseif($this->jdebug['access'] && $this->jdebug['currency']) $this->debuglog("Access granted for ".$remoteip,"currency");
			if(method_exists($this->money,$this->xmlrpcfunctions['currencyfunctions'][$method]['function'])) {
//				if(array_key_exists("requestingAgent",$params)) $this->money->setRequestingAgent($params['requestingAgent']);
//				else $this->money->setRequestingAgent(null);
				$subcallmethod = $this->xmlrpcfunctions['currencyfunctions'][$method]['function'];
				return $this->money->$subcallmethod($params);
			} else {
				return $this->norepsonse($method,"currency");
			}
		} else {
			if($this->jdebug['any'] > 0) $this->debuglog("No hit in subCall found for method ".$method);
		}
	}

	public function setDebug() {
		$this->jdebug['access']		= $this->settings['jopensim_debug_access'];
		$this->jdebug['input']		= $this->settings['jopensim_debug_input'];
		$this->jdebug['profile']	= $this->settings['jopensim_debug_profile'];
		$this->jdebug['groups']		= $this->settings['jopensim_debug_groups'];
		$this->jdebug['search']		= $this->settings['jopensim_debug_search'];
		$this->jdebug['messages']	= $this->settings['jopensim_debug_messages'];
		$this->jdebug['currency']	= $this->settings['jopensim_debug_currency'];
		$this->jdebug['terminal']	= $this->settings['jopensim_debug_terminal'];
		$this->jdebug['other']		= $this->settings['jopensim_debug_other'];
		$this->jdebug['any']		= $this->jdebug['access']
									+ $this->jdebug['input']
									+ $this->jdebug['profile']
									+ $this->jdebug['groups']
									+ $this->jdebug['search']
									+ $this->jdebug['messages']
									+ $this->jdebug['currency']
									+ $this->jdebug['other'];
		$this->setDebugLogfile();
	}

	public function setDebugLogfile() {
		$this->debuglogfile = $this->settings['jopensim_debug_path'].'interface.log';
	}

	public function norepsonse($methodname,$block = null) {
		$this->debuglog("noresponse for method ".$methodname." (Block ".$block.")");
		if($block) $methodname .= " for block ".$block;
		$this->retval	= array('success'		=> False,
								'errorMessage'	=> 'Error handling (missing?) method '.$methodname,
								'data'			=> array());
		return $this->retval;
	}

	public function getdebugsettings() {
		return $this->jdebug;
	}

	public function debuglog($zeile,$function = "") {
		$logfile	= $this->debuglogfile;

		if(!$function) $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##########\n";
		else $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ########## ".$function." ##########\n";
		$zeile = var_export($zeile,TRUE);
		$handle = fopen($logfile,"a+");
		$logzeile = $zeit.$zeile."\n\n";
		fputs($handle,$logzeile);
		fclose($handle);
	}

	public function clientInfo($parameter, $source = "?") {
		if($this->debug === TRUE) $this->debuglog($parameter,"Parameter for ".__FUNCTION__);
		if(!array_key_exists("agentName",$parameter) && !array_key_exists("agentIP",$parameter) && !array_key_exists("agentID",$parameter)) return; // no params, what should we save?
		$agent	= (array_key_exists("agentName",$parameter)) ? $parameter['agentName']:"unknown";
		$userip	= (array_key_exists("agentIP",$parameter)) ? $parameter['agentIP']:"127.0.0.3";
		$uuid	= (array_key_exists("agentID",$parameter)) ? $parameter['agentID']:$this->uuidZero;
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
		$db = JFactory::getDBO();
		$query = sprintf("INSERT INTO #__opensim_clientinfo (PrincipalID,userName,grid,remoteip,lastseen,`from`) VALUES (%1\$s,%2\$s,%3\$s,%4\$s,NOW(),%5\$s)
							ON DUPLICATE KEY UPDATE userName = %2\$s, grid = %3\$s, remoteip = %4\$s, lastseen = NOW(), `from`= %5\$s",
			$db->quote($uuid),
			$db->quote(trim($username)),
			$db->quote($host),
			$db->quote($userip),
			$db->quote($source));
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();
	}

	public function roundoff($v, $d) {
		$r = pow(10, $d);
		$v *= $r;
		if ($v - floor($v) >= 0.5) {
			return(ceil($v)/$r);
		} else {
			return (floor($v)/$r);
		}
	}

	public function varexport($var) {
		$retval = var_export($var,TRUE);
		return $retval;
	}

	public function accessfailed($block,$method,$remoteip) {
		if($this->jdebug['access']) $this->debuglog("No access granted for IP ".$remoteip." for method ".$method." in ".$block,"NO ACCESS!!");
		$retval['success']	= FALSE;
		$retval['message']		= 
		$retval['errorMessage']	=
		$retval['error']		= "No access to ".$block."!"; // Trying to be compatible with all modules ;)
		return $retval;
	}

}
?>
