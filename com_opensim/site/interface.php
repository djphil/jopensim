<?php
/*
 * @component jOpenSim (Communication Interface with the OpenSim Server)
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
/* Initialize Joomla framework */
if(!defined('_JEXEC')) define( '_JEXEC', 1 );
if(!defined('JPATH_BASE')) define('JPATH_BASE', realpath(dirname("../../index.php")));
if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

// For JED
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$xmlrpcerrorlevel = 0;
error_reporting($xmlrpcerrorlevel);

/* Required Files */
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'configuration.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
/* To use Joomla's Database Class */
require_once ( JPATH_ROOT .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'factory.php' );
/* Create the Application */
$mainframe =& JFactory::getApplication('site');

/* Load the language file from the component opensim */

$lang =& JFactory::getLanguage();
$extension = 'com_opensim';
$base_dir = JPATH_SITE;

// $language_tag = 'en-GB';
// $lang->load($extension, $base_dir, $language_tag, true);
$lang->load($extension, $base_dir, null, true);

/**************************************************/
// My code starts here...
/**************************************************/
require_once('includes/opensim.class.php');
require_once(JPATH_BASE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');
$opensim_model = new OpenSimModelOpenSim();

if(is_file(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'currency.php') && is_file(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'functions_currency.php')) {
	define('_JOPENSIMCURRENCY',TRUE);
} else {
	define('_JOPENSIMCURRENCY',FALSE);
}

function getXMLrpcFunctions() {
	$retval['searchfunctions'] = array(
								"init_SearchDataUpdate",
								"searchDataUpdate",
								"dir_places_query",
								"dir_popular_query",
								"dir_land_query",
								"dir_events_query",
								"dir_classified_query",
								"event_info_query",
								"classifieds_info_query"); // all methods for handling inworld search
	$retval['profilefunctions'] = array(
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
	$retval['groupfunctions'] = array(
								"groups.createGroup",
								"groups.updateGroup",
								"groups.getGroup",
								"groups.findGroups",
								"groups.getGroupRoles",
								"groups.addRoleToGroup",
								"groups.removeRoleFromGroup",
								"groups.updateGroupRole",
								"groups.getGroupRoleMembers",
								"groups.setAgentGroupSelectedRole",
								"groups.addAgentToGroupRole",
								"groups.removeAgentFromGroupRole",
								"groups.getGroupMembers",
								"groups.addAgentToGroup",
								"groups.addAgentToGroup2",
								"groups.removeAgentFromGroup",
								"groups.setAgentGroupInfo",
								"groups.addAgentToGroupInvite",
								"groups.getAgentToGroupInvite",
								"groups.removeAgentToGroupInvite",
								"groups.setAgentActiveGroup",
								"groups.getAgentGroupMembership",
								"groups.getAgentGroupMemberships",
								"groups.getAgentActiveMembership",
								"groups.getAgentRoles",
								"groups.getGroupNotices",
								"groups.getGroupNotice",
								"groups.addGroupNotice"); // all (?) methods for group handling
	return $retval;
}



$input = file_get_contents("php://input");
$responsefunction = ""; // temporary debug switch to know, if groups or profile (soon search) triggered

function simpledebugzeile($zeile) {
	$zeit = date("Y-m-d H:i:s");
	$logfile = "./interface.log";
	$handle = fopen($logfile,"a+");
	$logzeile = $zeit."\t".$zeile."\n";
	fputs($handle,$logzeile);
	fclose($handle);
}

function debugzeile($zeile,$function = "") {
	if(!$function) $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##########\n";
	else $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##### ".$function." ##########\n";
	$zeile = var_export($zeile,TRUE);
	$logfile = "./interface.log";
	$handle = fopen($logfile,"a+");
	$logzeile = $zeit.$zeile."\n\n";
	fputs($handle,$logzeile);
	fclose($handle);
}

if(!function_exists("varexport")) {
	function varexport($var) {
		$retval = var_export($var,TRUE);
		return $retval;
	}
}

function mysqlsafestring($string) {
	$db = JFactory::getDBO();
	return $db->escape($string);
//	return mysql_real_escape_string(stripslashes($string));
}

function jOpenSimSettings() {
	global $opensim_model;
	$settings = $opensim_model->getSettingsData();
	return $settings;
}

function getRegionInfo($regionUUID) {
	global $opensim;
	$regioninfo = $opensim->getRegionData($regionUUID);
	return $regioninfo;
}

function addonSettings() {
	$settings = jOpenSimSettings();
	$addon = $settings['addons'];
	$retval['messages']		= $addon & 1;
	$retval['profile']		= $addon & 2;
	$retval['groups']		= $addon & 4;
	$retval['inworldident']	= $addon & 8;
	$retval['search']		= $addon & 16;
	return $retval;
}


function roundoff($v, $d) {
	$r = pow(10, $d);
	$v *= $r;
	if ($v - floor($v) >= 0.5) {
		return(ceil($v)/$r);
	} else {
		return (floor($v)/$r);
	}
}

$params = &JComponentHelper::getParams('com_opensim');

$osgriddbhost	= $params->get('opensimgrid_dbhost');
$osgriddbuser	= $params->get('opensimgrid_dbuser');
$osgriddbpasswd	= $params->get('opensimgrid_dbpasswd');
$osgriddbname	= $params->get('opensimgrid_dbname');
$osgriddbport	= $params->get('opensimgrid_dbport');

// get Debugging levels
$debug['access']	= $params->get('jopensim_debug_access');
$debug['input']		= $params->get('jopensim_debug_input');
$debug['profile']	= $params->get('jopensim_debug_profile');
$debug['groups']	= $params->get('jopensim_debug_groups');
$debug['search']	= $params->get('jopensim_debug_search');
$debug['terminal']	= $params->get('jopensim_debug_terminal');
$debug['messages']	= $params->get('jopensim_debug_messages');
$debug['other']		= $params->get('jopensim_debug_other');
$debug['currency']	= $params->get('jopensim_debug_currency');

if(!defined("_JOPENSIMGROUPDEBUG")) {
	if($debug['groups'] == "1") define("_JOPENSIMGROUPDEBUG",TRUE);
	else define("_JOPENSIMGROUPDEBUG",FALSE);
}

if(!defined("_JOPENSIMMONEYDEBUG")) {
	if($debug['currency'] == "1") define("_JOPENSIMMONEYDEBUG",TRUE);
	else define("_JOPENSIMMONEYDEBUG",FALSE);
}

// debugzeile($debug,"debug");

$opensim = new opensim($osgriddbhost,$osgriddbuser,$osgriddbpasswd,$osgriddbname,$osgriddbport);

$grp_readkey	= $params->get('grp_readkey');
$grp_writekey	= $params->get('grp_writekey');

// $offlinemsg		= $params->get('offlinemsg');

$addons = addonSettings();

if($addons['messages'] > 0) $addon_offlinemsg		=  1;
else  $addon_offlinemsg		=  0;
if($addons['profile'] > 0) $addon_profiles			=  1;
else $addon_profiles		=  0;
if($addons['groups'] > 0) $addon_groups				=  1;
else $addon_groups			=  0;
if($addons['search'] > 0) $addon_search				=  1;
else $addon_search			=  0;
if($addons['inworldident'] > 0) $addon_inworldident	=  1;
else $addon_inworldident	=  0;

$remoteip = $_SERVER['REMOTE_ADDR']; // who is talking with us?

ob_start();

// some debug to test directly width the browser:
if(isset($_REQUEST['test'])) {
    require_once("xmlrpc/xmlrpc.inc"); // get the xmlrpc library from FlotSam
    require_once("xmlrpc/xmlrpcs.inc");
    if(isset($_REQUEST['test'])) {
		switch($_REQUEST['test']) {
			case "test":
				echo "test!";
				exit;
			break;
			default:
				$test = $_REQUEST['test'];
				$retval = null;
				$functionblock = "unknown";

				$xmlrpcfunctions	= getXMLrpcFunctions();
				$profilefunctions	= $xmlrpcfunctions['profilefunctions'];
				$groupfunctions		= $xmlrpcfunctions['groupfunctions'];
				$searchfunctions	= $xmlrpcfunctions['searchfunctions'];

			    require_once("xmlrpc/xmlrpc.inc"); // get the xmlrpc library from FlotSam
			    require_once("xmlrpc/xmlrpcs.inc");

				if(in_array($test,$profilefunctions)) {
					$functionblock = "profile";
					require_once('includes/functions_profile.php'); // get the functions for profiles
					$params = $_REQUEST;
					unset($params['test']);
					$retval = call_user_func($test,$params);
				} elseif(in_array($test,$groupfunctions)) {
					$functionblock = "groups";
					require_once('includes/functions_groups.php'); // get the functions for groups
					$params = $_REQUEST;
					unset($params['test']);
					$function = $xmlrpcserver->dmap[$test]['function'];
					$retval = call_user_func($function,$params);
				} elseif(in_array($test,$searchfunctions)) {
					$functionblock = "search";
					require_once('includes/functions_search.php'); // get the functions for groups
					$params = $_REQUEST;
					unset($params['test']);
					$function = $xmlrpcserver->dmap[$test]['function'];
					$retval = call_user_func($function,$params);
				} else {
					require_once('includes/functions_groups.php'); // get the functions for groups
					echo "function not found :(<br />";
				}
				if($retval) debugprint($retval,"retval");
				else echo "no response for ".$test." :(<br />";
				echo "Functionblock: ".$functionblock."<br />";
//				$response = $xmlrpcserver->service(null,TRUE);
//				debugprint($response,"response");
				exit;
			break;
		}
	}
}

if($debug['access']	== "1") simpledebugzeile("Request coming from ".$remoteip);
if($debug['input']	== "1") debugzeile($input,$function = "input");



if($opensim->checkRegionIP($remoteip) === TRUE) { // only registered regions (or better their server) should access this
	if($debug['access'] == "1") simpledebugzeile("Access granted for ".$remoteip." at line ".__LINE__." in ".__FILE__);
	if(!isset($_REQUEST['action'])) $_REQUEST['action'] = null; // Avoid Notices
	switch($_REQUEST['action']) { // this is the inworld terminal communication
		case "identify":
			$responsefunction = "terminal";
			if($addon_inworldident == 0) {
				if($debug['terminal'] == "1") simpledebugzeile("Terminal identify (but disabled in config) fired from ".$remoteip." at line ".__LINE__." in ".__FILE__);
				exit; // nothing to do if the addon is disabled
			} else {
				if($debug['terminal'] == "1") simpledebugzeile("Terminal identify fired from ".$remoteip." at line ".__LINE__." in ".__FILE__);
			}
			$db =& JFactory::getDBO();
			// first clean up old ident requests
			$settings = jOpenSimSettings();
			if($settings['addons_identminutes'] > 0) {
				$query = sprintf("DELETE FROM #__opensim_inworldident WHERE created < DATE_SUB(NOW(), INTERVAL %d MINUTE)",$settings['addons_identminutes']);
				$db->setQuery($query);
				$db->query();
			}
			$identString	= mysqlsafestring(JFactory::getApplication()->input->get('identString'));
			$identKey		= mysqlsafestring(JFactory::getApplication()->input->get('identKey'));
			// first check if uuid has already a joomla relation
			$query = sprintf("SELECT joomlaID FROM #__opensim_userrelation WHERE opensimID = '%s'",$identKey);
			$db->setQuery($query);
			$db->query();
			$num_rows = $db->getNumRows();
			if($num_rows > 0) {
				echo JText::_('INWORLDALREADYIDENTIFIED');
			} else {
				// no relation existing, check if some inworld ident is prepared
				$query = sprintf("SELECT joomlaID FROM #__opensim_inworldident WHERE inworldIdent = '%s'",$identString);
				$db->setQuery($query);
				$db->query();
				$num_rows = $db->getNumRows();
				if($num_rows > 0) {
					$joomlaId = $db->loadResult();
					// this should actually never happen, but better check this as well:
					$query = sprintf("SELECT opensimID FROM #__opensim_userrelation WHERE joomlaID = '%d'",$joomlaId);
					$db->setQuery($query);
					$db->query();
					$num_rows = $db->getNumRows();
					if($num_rows > 0) { // something went completely wrong here, hope this causes ppl to report a bug
						echo "Error while double check joomlaID ".$joomlaId." (could be a bug)! Please contact the Gridmanager and/or FoTo50 at the support forum at http://www.jopensim.com";
					} else { // Everything ok, inworld account identified
						// Update the relation table
						$query = sprintf("INSERT INTO #__opensim_userrelation (joomlaID,opensimID) VALUES ('%1\$d','%2\$s')",$joomlaId,$identKey);
						$db->setQuery($query);
						$db->query();
						// and delete from the ident table
						$query = sprintf("DELETE FROM #__opensim_inworldident WHERE inworldIdent = '%s'",$identString);
						$db->setQuery($query);
						$db->query();
						echo JText::_('IDENTIFYINWORLDSUCCESS');
					}
				} else { // no inworld ident found, give a proper message
					echo JText::_('IDENTIFYINWORLDFAILED');
				}
			}
			exit; // we have done already everything, dont check if there could be more
		break;
		case "register":
			$responsefunction = "terminal";
			if($addon_inworldident == 0) {
				if($debug['terminal'] == "1") simpledebugzeile("Terminal register (but disabled in config) fired from ".$remoteip." at line ".__LINE__." in ".__FILE__);
				echo JText::_('IDENTIFYINWORLDDISABLED');
				exit; // nothing to do if the addon is disabled
			} else {
				if($debug['terminal'] == "1") simpledebugzeile("Terminal register fired from ".$remoteip." at line ".__LINE__." in ".__FILE__);
			}
			$terminalDescription = mysqlsafestring(JFactory::getApplication()->input->get('terminalDescription'));
			$terminalUrl = mysqlsafestring(JFactory::getApplication()->input->get('myurl'));
			$regionString = $_SERVER['HTTP_X_SECONDLIFE_REGION'];
			$locationString = $_SERVER['HTTP_X_SECONDLIFE_LOCAL_POSITION'];
			$terminalKey = mysqlsafestring($_SERVER['HTTP_X_SECONDLIFE_OBJECT_KEY']);
			$terminalName = $_SERVER['HTTP_X_SECONDLIFE_OBJECT_NAME'];
			$region_suchmuster = "/([^\(])*/";
			preg_match($region_suchmuster,$regionString,$treffer);
			$region = trim($treffer[0]);
			$location_suchmuster = "/[^\d]*([\d\.]*)[^\d]*([\d\.]*)[^\d]*([\d\.]*)/";
			preg_match_all($location_suchmuster,$locationString,$treffer,PREG_SET_ORDER);
			$location_x = roundoff($treffer[0][1],0);
			$location_y = roundoff($treffer[0][2],0);
			$location_z = roundoff($treffer[0][3],0);
			$db =& JFactory::getDBO();
			$query = sprintf("SELECT staticLocation FROM #__opensim_terminals WHERE terminalKey = '%s'",$terminalKey);
			$db->setQuery($query);
			$db->query();
			$num_rows = $db->getNumRows();
			if($num_rows > 0) {
				$terminalstatic = $db->loadResult();
				if($terminalstatic == "1") {
					echo "Terminal found, but not updated due to static setting!";
				} else {
					$query = sprintf("UPDATE #__opensim_terminals SET
										terminalName = '%s',
										terminalDescription = '%s',
										terminalUrl = '%s',
										region = '%s',
										location_x = '%d',
										location_y = '%d',
										location_z = '%d'
									WHERE
										terminalKey = '%s'",
						$terminalName,
						$terminalDescription,
						$terminalUrl,
						$region,
						$location_x,
						$location_y,
						$location_z,
						$terminalKey);
					$db->setQuery($query);
					$db->query();
					echo "Terminal found and sucessfully updated!";
				}
			} else {
				$query = sprintf("INSERT INTO #__opensim_terminals
										(terminalName,terminalDescription,terminalKey,terminalUrl,region,location_x,location_y,location_z)
									VALUES
										('%s','%s','%s','%s','%s','%d','%d','%d')",
						$terminalName,
						$terminalDescription,
						$terminalKey,
						$terminalUrl,
						$region,
						$location_x,
						$location_y,
						$location_z);
				$db->setQuery($query);
				$db->query();
				echo "Terminal sucessfully registered!";
			}
			exit; // we have done already everything, dont check if there could be more
		break;
		case "setState":
			if($debug['terminal'] == "1") simpledebugzeile("Terminal setState fired from ".$remoteip." at line ".__LINE__." in ".__FILE__);
			$responsefunction = "terminal";
			$terminalState = mysqlsafestring(JFactory::getApplication()->input->get('state'));
			$terminalKey = mysqlsafestring($_SERVER['HTTP_X_SECONDLIFE_OBJECT_KEY']);
			$query = sprintf("UPDATE #__opensim_terminals SET active = '%d' WHERE terminalKey = '%s'",$terminalState,$terminalKey);
			$db =& JFactory::getDBO();
			$db->setQuery($query);
			$db->query();
			if($terminalState == 1) echo JText::_('TERMINALSETVISIBLE');
			else echo JText::_('TERMINALSETINVISIBLE');
			exit; // we have done already everything, dont check if there could be more
		break;
	}
	$method = (isset($_SERVER["PATH_INFO"])) ? $_SERVER["PATH_INFO"]:null;
	switch($method) { // so far, only the Offline-Messages are sending the method as path_info
		case "/SaveMessage/":
			if($debug['messages'] == "1") simpledebugzeile("Offline Messages [SaveMessage] fired from ".$remoteip." at line ".__LINE__." in ".__FILE__);
			$responsefunction = "messages";
			if($addon_offlinemsg == 0) {
				if($debug['messages'] == "1") simpledebugzeile("Offline Messages disabled!");
				echo "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><boolean>false</boolean>";
				exit;
			}
			$message = $opensim->parseOSxml($input);
			if($debug['messages'] == "1") debugzeile($message,"message");
			$searchstr = "?".">";
			$start = strpos($input, $searchstr);
			if($debug['messages'] == "1") simpledebugzeile("Offline Messages:\nstart:\n".$start);
			if ($start != -1) {
				$start+=2;
				$msg = substr($input, $start);
				if($debug['messages'] == "1") simpledebugzeile("Offline Messages:\nmsg:\n".$msg);
				$db =& JFactory::getDBO();
				$query = sprintf("INSERT INTO #__opensim_offlinemessages (imSessionID,fromAgentID,fromAgentName,toAgentID,fromGroup,message,remoteip,sent) VALUES ('%s','%s','%s','%s','%s','%s','%s',NOW())",
							$message['imSessionID'],
							$message['fromAgentID'],
							$message['fromAgentName'],
							$message['toAgentID'],
							$message['fromGroup'],
							addslashes($msg),
							$remoteip);
				$db->setQuery($query);
				$db->execute();
				if($db->getErrorNum() == 0) {
					if($debug['messages'] == "1") simpledebugzeile("Offline Messages:\n\$db->getErrorNum():\n".$db->getErrorNum());
					echo "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><boolean>true</boolean>";

					// no need to check in components settings for offline messages since we would not be here if disabled
					$usersettings = $opensim->getUserSettings($message['toAgentID']);
					if($usersettings['im2email'] == 1) { // Send Email to "toAgentID" only if set in user settings
						$userdata	= $opensim->getUserData($message['toAgentID']);
						$mailer		= JFactory::getMailer();
						$config		= JFactory::getConfig();
						$sender		= array($config->get('mailfrom'),$config->get('fromname'));
						$body		= JText::_('IMFROM').": ".$message['fromAgentName']."\n\n".$message['message'];
						$subject	= JText::_('IM2MAILSUBJECT')." ".$config->get('fromname')." (".$message['fromAgentName'].")";
						$mailer->setSender($sender);
						$mailer->addRecipient($userdata['email']);
						$mailer->setSubject($subject);
						$mailer->setBody($body);
						$mailer->Send();
					}
				} else {
					if($debug['messages'] == "1") simpledebugzeile("Offline Messages:\n\$db->getErrorNum():\n".$db->getErrorNum());
					echo "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><boolean>false</boolean>";
				}
			} else {
				echo "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><boolean>false</boolean>";
			}
		break;
		case "/RetrieveMessages/":
			if($debug['messages'] == "1") simpledebugzeile("Offline Messages [RetrieveMessages] fired from ".$remoteip." at line ".__LINE__." in ".__FILE__);
			$responsefunction = "messages";
			$message = $opensim->parseOSxml($input);
			$guid = $message['Guid'];
			$db =& JFactory::getDBO();
			$query = sprintf("SELECT message FROM #__opensim_offlinemessages WHERE toAgentID = '%s' ORDER BY sent",
					$guid);
			$db->setQuery($query);
			$messages = $db->loadRowList();
			$output = "<?x"."ml version=\"1.0\" encoding=\"utf-8\"?><ArrayOfGridInstantMessage xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">";
			foreach($messages AS $message) {
				$output .= $message[0];
			}
			$output .= "</ArrayOfGridInstantMessage>";
			echo $output;
			$query = sprintf("DELETE FROM #__opensim_offlinemessages WHERE toAgentID = '%s'",$guid);
			$db->setQuery($query);
			$db->query();
		break;
		default: // This must be some xml-rpc request from "Profile", "Search" or "Groups"
			/*ob_start();*/
			$method = $opensim->parseOSxml($input,"method"); // get name of method
			$debugtest = var_export($method,TRUE);
			if(!$method) {
				break; // no method? nothing we can do then ... :(
			}

			$xmlrpcfunctions	= getXMLrpcFunctions();
			$profilefunctions	= $xmlrpcfunctions['profilefunctions'];
			$groupfunctions		= $xmlrpcfunctions['groupfunctions'];
			$searchfunctions	= $xmlrpcfunctions['searchfunctions'];

		    require_once("xmlrpc/xmlrpc.inc"); // get the xmlrpc library from FlotSam
		    require_once("xmlrpc/xmlrpcs.inc");

			if(in_array($method,$profilefunctions)) {
				if($addon_profiles > 0) {
					require_once('includes/functions_profile.php'); // get the functions for profiles
					$responsefunction = "profile";
					$response2opensim = TRUE;
				} else {
					$response2opensim = FALSE;
					$noresponsereason = "profile";
				}
			} elseif(in_array($method,$groupfunctions)) {
				if($addon_groups > 0) {
					require_once('includes/functions_groups.php'); // get the functions for groups
					$response2opensim = TRUE;
					$responsefunction = "groups";
				} else {
					$response2opensim = FALSE;
					$noresponsereason = "groups";
				}
			} elseif(in_array($method,$searchfunctions)) {
				require_once('includes/functions_search.php'); // get the functions for search
				$responsefunction = "search";
				$response2opensim = TRUE;
			} else {
				if($debug['profile'] == "1" || $debug['groups'] == "1" || $debug['search'] == "1" || $debug['other'] == "1") debugzeile("\nmethod not found:\n\n".var_export($method,TRUE),"general error");
				exit;
			}

			$xmlrpcserver->functions_parameters_type = 'phpvals';
			/*$xmlrpcserver->service();*/
			if($response2opensim === TRUE) {
				$response = $xmlrpcserver->service(null,TRUE);
			} else {
				// can somebody tell me how to return a "fake" response without f*** up the login after disabling groups?
				// $response = $xmlrpcserver->service("addon_disabled",TRUE); <--- that didnt work with groups disabled
				$response = $xmlrpcserver->service(null,TRUE);
			}
			echo $response;

/*

			if($responsefunction == "groups") groupdebug("\nresponse for ".$method.":\n\n".$response,"response for groups");
			elseif($responsefunction == "profile") profiledebug("\nresponse for ".$method.":\n\n".$response,"response for profile");
			elseif($responsefunction == "search") debugzeile("\nresponse for ".$method.":\n\n".$response,"response for search");
			else debugzeile("\nresponse for ".$method.":\n\n".$response,"response for unbekannt");
*/
		break;
	}
} else {
	if(isset($_REQUEST['action'])) {
		switch($_REQUEST['action']) {
			case "identify": case "register": case "setState": // just answer for wrong configured terminals to give a small hint ;)
				echo "No access to ".$_SERVER['HTTP_HOST']." for this terminal!\nPlease be sure to enter the correct url in your script for \"targetUrl\"!";
			break;
		}
	}
	//	no access? Just dont answer at all ;)
	if($debug['access'] == "1") simpledebugzeile("No access for ".$remoteip." at line ".__LINE__." in ".__FILE__);
}

$output = ob_get_contents();
ob_end_clean();

// debugzeile("\noutput -> ".$method.": \n".$output);

$response = var_export($output,TRUE);

if($debug['groups']			== "1" && $responsefunction == "groups") debugzeile("\nresponse for ".$method.":\n\n".$response,"response for groups");
elseif($debug['profile']	== "1" && $responsefunction == "profile") debugzeile("\nresponse for ".$method.":\n\n".$response,"response for profile");
elseif($debug['search']		== "1" && $responsefunction == "search") debugzeile("\nresponse for ".$method.":\n\n".$response,"response for search");
elseif($debug['terminal']	== "1" && $responsefunction == "terminal") debugzeile("\nresponse for ".$method.":\n\n".$response,"response for terminal");
elseif($debug['messages']	== "1" && $responsefunction == "messages") debugzeile("\nresponse for ".$method.":\n\n".$response,"response for messages");
elseif(($debug['groups']	== "1" ||
		$debug['profile']	== "1" ||
		$debug['search']	== "1" ||
		$debug['terminal']	== "1" ||
		$debug['messages']	== "1" ||
		$debug['other']		== "1")		&&
		$responsefunction	!= "groups"		&&
		$responsefunction	!= "profile"	&&
		$responsefunction	!= "search"		&&
		$responsefunction	!= "terminal"	&&
		$responsefunction	!= "messages")	debugzeile("\nresponse for unknown method ".$method.":\n\n".$response,"response for unknown");


echo $output;
/*ob_end_clean();*/
?>