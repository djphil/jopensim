<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');


class opensimModelinworld extends OpenSimModelOpenSim {

	var $_settingsData;
	var $filename = "inworld.php";
	var $view = "inworld";
	var $_os_db = null;
	var $_osgrid_db = null;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
		$this->getOpenSimDB();
		$this->_os_db		= $this->getOpenSimDB();
		$this->_osgrid_db	= $this->getOpenSimGridDB();
	}

	public function opensimGetInworldIdent() { // returns the ident string if user has applied for inworld identification or FALSE if not
		$user	= JFactory::getUser();
		$db		= JFactory::getDBO();
		$query	= sprintf("SELECT inworldIdent FROM #__opensim_inworldident WHERE joomlaID = '%d'",$user->id);
		$db->setQuery($query);
		$ident = $db->loadResult();
		if(!$ident) return FALSE;
		else return $ident;
	}

	public function opensimSetInworldIdent() { // creates an inworld ident string for the user
		$identstring = $this->getUUID();
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$query = sprintf("INSERT INTO #__opensim_inworldident (joomlaID,inworldIdent,created) VALUES ('%d','%s',NOW())",$user->id,$identstring);
		$db->setQuery($query);
		$db->execute();
	}

	public function opensimCancelInworldIdent() { // creates an inworld ident string for the user
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$query = sprintf("DELETE FROM #__opensim_inworldident WHERE joomlaID = '%d'",$user->id);
		$db->setQuery($query);
		$db->execute();
	}

	public function setUserRelation($opensimid) {
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$query = sprintf("INSERT INTO #__opensim_userrelation (joomlaID,opensimID) VALUES ('%d','%s')",$user->id,$opensimid);
		$db->setQuery($query);
		$db->execute();
	}

	public function checkUserExists($firstname,$lastname,$uid = null) {
		if(empty($this->_osgrid_db)) $this->getOpenSimDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$checkquery = $opensim->getCheckQuery($firstname,$lastname,$uid);
		$this->_osgrid_db->setQuery($checkquery);
		$this->_osgrid_db->execute();
		$existing = $this->_osgrid_db->getNumRows();
		if($existing > 0) return TRUE;
		else return FALSE;
	}

	public function insertuser($newuser) {
		if(empty($this->_osgrid_db)) $this->getOpenSimDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$newuser['passwordSalt'] = md5($newuser['password']);
		$newuser['passwordHash'] = md5(md5($newuser['password']).":".$newuser['passwordSalt']);

		$newuser['homeregion'] = $this->_settingsData['defaulthome'];
		$newuser['homeposition'] = sprintf("<"."%f,%f,%f".">",$this->_settingsData['mapstartX'],$this->_settingsData['mapstartY'],$this->_settingsData['mapstartZ']);
		$newuser['homelookat'] = "<0,0,0>"; // have to figure out once how to set that exact

		$insertquery = $opensim->getInsertUserQuery($newuser);
		$this->_osgrid_db->setQuery($insertquery['user']);
		$retval = $this->_osgrid_db->execute();
		$this->_osgrid_db->setQuery($insertquery['auth']);
		$retval = $this->_osgrid_db->execute();
		if($newuser['homeregion'] && $this->regionExists($newuser['homeregion'])) { // only add home region if set already
			$this->_osgrid_db->setQuery($insertquery['grid']);
			$retval = $this->_osgrid_db->execute();
		}
		$inventoryqueries = $opensim->getinventoryqueries($newuser['uuid']);
		if(is_array($inventoryqueries)) {
			foreach($inventoryqueries AS $query) {
				$this->_osgrid_db->setQuery($query);
				$this->_osgrid_db->execute();
			}
		}
		$this->createJuserData($newuser['uuid']);
		return $retval;
	}

	public function createJuserData($uuid) {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT COUNT(*) FROM #__opensim_usersettings WHERE uuid = '%s'",$uuid);
		$db->setQuery($query);
		$count = $db->loadResult();
		if($count == 0) {
			$query = sprintf("INSERT INTO #__opensim_usersettings (uuid,im2email,visible) VALUES ('%s',0,0)",$uuid);
			$db->setQuery($query);
			$db->execute();
		}
	}

	public function getUUID() {
		$db = JFactory::getDBO();
		$query = "SELECT UUID()";
		$db->setQuery($query);
		$uuid = $db->loadResult();
		return $uuid;
	}

	public function updateuser($data) {
		if(empty($this->_osgrid_db)) $this->getOpenSimDB();
		if(!$this->_osgrid_db) return FALSE;
		$osuid = $this->opensimIsCreated();
		if(!$osuid) return FALSE; // not logged in or no opensim account attached?
		$pregmail = "/^.{1,}@.{2,}\..{2,63}\$/";
		$retval = array();
		preg_match($pregmail, $data['email'], $treffer); // Emailadresse auf Gültigkeit prüfen
		if(empty($this->_settingsData)) $this->getSettingsData();
		if(!$data['firstname'] && $this->_settingsData['userchange'] & 1) $retval[] = JText::_('FIRST_NAME')." ".JText::_('ISREQUIRED');
		if(!$data['lastname'] && $this->_settingsData['userchange'] & 2) $retval[] = JText::_('LAST_NAME')." ".JText::_('ISREQUIRED');
		elseif($data['lastname'] && $data['lastname'] != $data['oldlastname']) { // last name has changed, check if it is allowed (means already existing "not allowed" last names will not be checked)
			$checkLastnameAllowed = $this->checkLastnameAllowed($data['lastname']); // check if Last Name is allowed
			if(!$checkLastnameAllowed) $retval[] = JText::_('ERROR_LASTNAMENOTALLOWED');
		}
		if(!$data['email'] && $this->_settingsData['userchange'] & 4) $retval[] = JText::_('EMAIL')." ".JText::_('ISREQUIRED');
		elseif($treffer[0] != $data['email'] || !isset($treffer[0]) && $this->_settingsData['userchange'] & 4) $retval[] = JText::_('ERROR_INVALIDEMAIL');
		if($data['pwd1'] && $data['pwd1'] != $data['pwd2'] && $this->_settingsData['userchange'] & 8) $retval[] = JText::_('ERROR_PWDMISMATCH');
		if(($data['firstname'] || $data['lastname']) && (($this->_settingsData['userchange_firstname'] == 1 || $this->_settingsData['userchange_lastname'] == 1))) { // names could be changed, check if already existing
			$osdata = $this->getUserData($osuid);
			$firstname = ($data['firstname']) ? $data['firstname']:$osdata['firstname'];
			$lastname  = ($data['lastname']) ? $data['lastname']:$osdata['lastname'];
			$existing = $this->checkUserExists($firstname,$lastname,$osuid);
			if($existing) $retval[] = JText::_('ERROR_USEREXISTS');
		}
		if(count($retval) > 0) { // some error occured, lets go back
			array_unshift($retval,"error");
			return $retval;
		} else { // no errors, lets save the stuff
			if($data['firstname'] && $this->_settingsData['userchange_firstname'] == 1) $this->updateOsField('firstname',$data['firstname'],$osuid);
			if($data['lastname'] && $this->_settingsData['userchange_lastname'] == 1) $this->updateOsField('lastname',$data['lastname'],$osuid);
			if($data['email'] && $this->_settingsData['userchange_email'] == 1) $this->updateOsField('email',$data['email'],$osuid);
			if($data['pwd1'] && $this->_settingsData['userchange_password'] == 1) $test = $this->updateOsPwd($data['pwd1'],$osuid);

			// Update users settings in Joomla DB
			$this->updateJuserData($data['uuid'],"im2email",$data['im2email'],"default");
			$this->updateJuserData($data['uuid'],"timezone",$data['timezone'],"default");

			$retval[] = "ok";
			$retval[] = $test;
			return $retval;
		}
	}

	public function updateJuserData($uuid,$name,$value,$type = "default") { // Update settings from Joomlas DB
		$db = JFactory::getDBO();
		$this->createJuserData($uuid); // ensure that the line already exists
		switch($type) {
			case "boolean_true":
				$query = sprintf("UPDATE #__opensim_usersettings SET %s = %s | %d WHERE uuid = '%s'",$name,$name,$value,$uuid);
			break;
			case "boolean_false":
				$query = sprintf("UPDATE #__opensim_usersettings SET %s = %s & ~%d WHERE uuid = '%s'",$name,$name,$value,$uuid);
			break;
			default:
				$query = sprintf("UPDATE #__opensim_usersettings SET %s = '%s' WHERE uuid = '%s'",$name,$value,$uuid);
			break;
		}
		$db->setQuery($query);
		$db->execute();
	}


	public function regionExists($regionID) {
		$opensim = $this->opensim;
		$query = $opensim->regionExistsQuery($regionID);
		$this->_osgrid_db->setQuery($query);
		$existing = $this->_osgrid_db->loadResult();
		if($existing == $regionID) return TRUE;
		else return FALSE;
	}

	public function addonSettings() {
		$retval['messages']		= $this->_settingsData['addons'] & 1;
		$retval['profile']		= $this->_settingsData['addons'] & 2;
		$retval['groups']		= $this->_settingsData['addons'] & 4;
		$retval['inworldauth']	= $this->_settingsData['addons'] & 8;
		$retval['search']		= $this->_settingsData['addons'] & 16;
		$retval['money']		= $this->_settingsData['addons'] & 32;
		$retval['addon']		= $this->_settingsData['addons'];
		$retval['settings'] 	= $this->_settingsData;
		return $retval;
	}

	public function messagelist($uuid) {
		$opensim = $this->opensim;
		$db = JFactory::getDBO();
		$query = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));";
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("SELECT sorteddata.* FROM (
							SELECT
								#__opensim_offlinemessages.imSessionID,
								#__opensim_offlinemessages.fromAgentID,
								#__opensim_offlinemessages.fromAgentName,
								#__opensim_offlinemessages.message AS rawmessage,
								#__opensim_offlinemessages.sent
							FROM
								#__opensim_offlinemessages
							WHERE
								#__opensim_offlinemessages.toAgentID = '%s'
							ORDER BY
								#__opensim_offlinemessages.sent DESC
							) AS sorteddata
						 GROUP BY
							sorteddata.imSessionID,sorteddata.fromAgentID",$db->escape($uuid));
		$db->setQuery($query);
		$messages = $db->loadAssocList();
		foreach($messages AS $key => $message) {
			$values = $opensim->parseOSxml($message['rawmessage'],"messaging");
			$messages[$key]['timestamp']	= $values['timestamp'];
			$messages[$key]['plainmsg']		= $values['message'];
		}
		return $messages;
	}

	public function messagedetail($imSessionID,$fromAgentID) {
		$opensim = $this->opensim;
		$db = JFactory::getDBO();
		$query = sprintf("SELECT
						imSessionID,
						fromAgentID,
						fromAgentName,
						message AS rawmessage
					FROM
						#__opensim_offlinemessages
					WHERE
						imSessionID = '%s'
					AND
						fromAgentID = '%s'",$imSessionID,$fromAgentID);
		$db->setQuery($query);
		$messages = $db->loadAssocList();
		foreach($messages AS $key => $message) {
			$values = $opensim->parseOSxml($message['rawmessage'],"messaging");
			$retval[$values['timestamp']] = $values['message'];
		}
		ksort($retval);
		$retval['fromAgentName'] = $messages[0]['fromAgentName'];
		return $retval;
	}

	public function updateprofile($data) {
		$userid = $this->opensimIsCreated();
		if(!$userid) return FALSE; // we dont have an opensim relation
		$db = JFactory::getDBO();

		$wantmask = 0;
		if(is_array($data['wantmask'])) {
			foreach($data['wantmask'] AS $wantbit) $wantmask += $wantbit;
		}
		$skillsmask = 0;
		if(is_array($data['skillsmask'])) {
			foreach($data['skillsmask'] AS $skillsbit) $skillsmask += $skillsbit;
		}
		$query = sprintf("INSERT INTO #__opensim_userprofile
											(avatar_id,aboutText,maturePublish,url,wantmask,wanttext,skillsmask,skillstext,languages,firstLifeText)
										VALUES
											('%1\$s','%2\$s','%3\$s','%4\$d','%5\$s','%6\$d','%7\$s','%8\$s','%9\$s','%10\$s')
									ON DUPLICATE KEY UPDATE
											aboutText		= '%2\$s',
											maturePublish	= '%3\$s',
											url				= '%4\$s',
											wantmask		= '%5\$d',
											wanttext		= '%6\$s',
											skillsmask		= '%7\$d',
											skillstext		= '%8\$s',
											languages		= '%9\$s',
											firstLifeText	= '%10\$s'",
					$userid,
					$data['aboutText'],
					$data['maturePublish'],
					$data['profile_url'],
					$wantmask,
					$data['wanttext'],
					$skillsmask,
					$data['skillstext'],
					$data['languages'],
					$data['firstLifeText']);
		$db->setQuery($query);
		$db->execute();
	}

	public function groupmemberships($userid,$groupid = null) {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT
								#__opensim_group.GroupID AS groupid,
								#__opensim_group.Charter AS charter,
								#__opensim_group.Name AS groupname,
								#__opensim_group.OwnerRoleID AS ownerroleid,
								#__opensim_groupmembership.AcceptNotices AS acceptnotices
							FROM
								#__opensim_groupmembership JOIN #__opensim_group ON #__opensim_groupmembership.GroupID = #__opensim_group.GroupID
							WHERE
								#__opensim_groupmembership.AgentID = '%s'",$userid);
		if($groupid) $query .= sprintf("\nAND #__opensim_group.GroupID = '%s'",$groupid);
		$db->setQuery($query);
		$groupmemberships = $db->loadAssocList();
		foreach($groupmemberships AS $key => $groupmembership) {
			$groupnotices = $this->getnotices($groupmembership['groupid']);
			$groupmemberships[$key]['hasnotices'] = count($groupnotices);
			$group_power = $this->group_power($userid,$groupmembership['groupid']);
			$groupmemberships[$key]['power'] = $group_power;
			$groupmemberships[$key]['power']['isowner'] = $this->isowner($userid,$groupmembership['groupid']);
		}
		return $groupmemberships;
	}

	public function getnotices($groupid) {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT * FROM #__opensim_groupnotice WHERE GroupID = '%s' ORDER BY Timestamp DESC",$groupid);
		$db->setQuery($query);
		$groupnotices = $db->loadAssocList();
		if(is_array($groupnotices)) return $groupnotices;
		else return array(); // just return an empty array if no notices found
	}

	public function grouppowerbits() { // we dont need ALL power Bits on a webinterface...
		$power['Eject']				= "4";				// for emergency to eject users
		$power['SendNotices']		= "4398046511104";	// who can send notices should also be able to DELETE notices
		$power['ReceiveNotices']	= "8796093022208";	// regular viewing notices
		$power['RoleMembersVisible']= "140737488355328";// to eject, you need to see members first
		return $power;
	}

	public function group_power($userid,$groupid) {
		$grouppowers = $this->grouppowerbits();
		$db = JFactory::getDBO();
		$query = sprintf("SELECT
								BIT_OR(#__opensim_grouprole.Powers) AS allpowers,
								IF((BIT_OR(#__opensim_grouprole.Powers) & %3\$d)=%3\$d,1,0) AS power_eject,
								IF((BIT_OR(#__opensim_grouprole.Powers) & %4\$d)=%4\$d,1,0) AS power_sendnotice,
								IF((BIT_OR(#__opensim_grouprole.Powers) & %5\$d)=%5\$d,1,0) AS power_receivenotice,
								IF((BIT_OR(#__opensim_grouprole.Powers) & %6\$d)=%6\$d,1,0) AS power_rolemembersvisible
							FROM
								#__opensim_grouprole,
								#__opensim_grouprolemembership
							WHERE
								#__opensim_grouprolemembership.GroupID = #__opensim_grouprole.GroupID
							AND
								#__opensim_grouprolemembership.RoleID = #__opensim_grouprole.RoleID
							AND
								#__opensim_grouprole.GroupID = '%1\$s'
							AND
								#__opensim_grouprolemembership.AgentID = '%2\$s'",
					$groupid,
					$userid,
					$grouppowers['Eject'],
					$grouppowers['SendNotices'],
					$grouppowers['ReceiveNotices'],
					$grouppowers['RoleMembersVisible']);
		$db->setQuery($query);
		$grouppower = $db->loadAssoc();
		return $grouppower;
	}

	public function isowner($userid,$groupid) {
		$membersVisibleTo = "Owner";

		$db = JFactory::getDBO();
		$query = sprintf("SELECT
							CASE WHEN MIN(#__opensim_grouprolemembership.AgentID) IS NOT NULL THEN 1 ELSE 0 END AS IsOwner
						FROM
							#__opensim_group
								JOIN #__opensim_groupmembership ON (#__opensim_group.GroupID = #__opensim_groupmembership.GroupID AND #__opensim_groupmembership.AgentID = '%1\$s')
								LEFT JOIN #__opensim_grouprolemembership ON (#__opensim_grouprolemembership.GroupID = #__opensim_group.GroupID AND #__opensim_grouprolemembership.RoleID  = #__opensim_group.OwnerRoleID AND #__opensim_grouprolemembership.AgentID = '%1\$s')
						WHERE
							#__opensim_group.GroupID = '%2\$s'
						GROUP BY
							#__opensim_group.GroupID",
					$userid,
					$groupid);

		$db->setQuery($query);
		$viewMemberInfo = $db->loadAssocList();


		if(count($viewMemberInfo) == 0) {
			return false;
		} else {
			return $viewMemberInfo[0]['IsOwner'];
		}
	}

	public function leaveGroup($groupid) {
		$userid = $this->opensimIsCreated();
		if(!$userid) return FALSE; // we dont have an opensim relation
		return $this->removeFromGroup($groupid,$userid);
	}

	public function removeFromGroup($groupid,$userid) {
		$db = JFactory::getDBO();
		$query = sprintf("DELETE FROM #__opensim_grouprolemembership WHERE GroupID = '%s' AND AgentID = '%s'",$groupid,$userid);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("DELETE FROM #__opensim_groupmembership WHERE GroupID = '%s' AND AgentID = '%s'",$groupid,$userid);
		$db->setQuery($query);
		$db->execute();
		$query = sprintf("UPDATE #__opensim_groupactive SET ActiveGroupID = '00000000-0000-0000-0000-000000000000' WHERE ActiveGroupID = '%s' AND AgentID = '%s'",$groupid,$userid);
		$db->setQuery($query);
		$db->execute();
		return TRUE;
	}

	public function ejectFromGroup($groupid,$ejectid) {
		$userid = $this->opensimIsCreated();
		if(!$userid) return FALSE; // we dont have an opensim relation, no ejecting possible
		if($this->isowner($ejectid,$groupid)) return FALSE; // we dont eject owners from groups here ... fight on this inworld ;)
		$grouppower = $this->group_power($userid,$groupid); // check if this user is allowed to eject
		if($this->isowner($userid,$groupid) || $grouppower['power_eject']) { // Owners and Role assigned are allowed
			return $this->removeFromGroup($groupid,$ejectid);
		} else { // not allowed to eject
			return FALSE;
		}
	}

	public function memberlist($groupid) {
		$userid = $this->opensimIsCreated();
		if(!$userid) return FALSE; // we dont have an opensim relation, no access to memberlists
		$grouppowers = $this->group_power($userid,$groupid); // check if permission to view memberlist is there
		if(!isset($grouppowers['RoleMembersVisible'])) $grouppowers['RoleMembersVisible'] = FALSE;
		if(!$grouppowers['RoleMembersVisible'] && !$this->isowner($userid,$groupid)) return FALSE; // no permission to view memberlist
		$opensim = $this->opensim;
		$db = JFactory::getDBO();
		$query = sprintf("SELECT
							#__opensim_grouprolemembership.AgentID,
							GROUP_CONCAT(DISTINCT(#__opensim_grouprole.`Name`)) AS roles,
							IF(FIND_IN_SET('owners',GROUP_CONCAT(DISTINCT(#__opensim_grouprole.`Name`))),1,0) AS isowner
						FROM
							#__opensim_groupmembership,
							#__opensim_grouprolemembership,
							#__opensim_grouprole
						WHERE
							#__opensim_groupmembership.AgentID = #__opensim_grouprolemembership.AgentID
						AND
							#__opensim_groupmembership.GroupID = #__opensim_grouprolemembership.GroupID
						AND
							#__opensim_grouprolemembership.GroupID = #__opensim_grouprole.GroupID
						AND
							#__opensim_grouprolemembership.RoleID = #__opensim_grouprole.RoleID
						AND
							#__opensim_groupmembership.GroupID = '%s'
						GROUP BY
							CONCAT(#__opensim_grouprolemembership.GroupID,#__opensim_grouprolemembership.AgentID)",$groupid);
		$db->setQuery($query);
		$memberList = $db->loadAssocList();
		foreach($memberList AS $key => $member) {
			$userdetails = $this->getUserData($member['AgentID']);
			$memberList[$key]['name'] = $userdetails['firstname']." ".$userdetails['lastname'];
		}
		return $memberList;
	}

	public function divorcefrom($partneruuid) {
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			$db		= JFactory::getDBO();
			$query	= sprintf("UPDATE #__opensim_userprofile SET #__opensim_userprofile.partner = '' WHERE #__opensim_userprofile.avatar_id = '%s'",$opensimUUID);
			$db->setQuery($query);
			$db->execute();
			$rows = $db->getAffectedRows();
			if($rows != 1) return FALSE;
			$query = sprintf("UPDATE #__opensim_userprofile SET #__opensim_userprofile.partner = '' WHERE #__opensim_userprofile.avatar_id = '%s'",$partneruuid);
			$db->setQuery($query);
			$db->execute();
			$rows = $db->getAffectedRows();
			if($rows != 1) return FALSE;
			else return TRUE;
		} else {
			return FALSE;
		}
	}

	public function sendpartnerrequest($partneruuid) {
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			if($this->opensimCreated($partneruuid) === TRUE) {
				$db		= JFactory::getDBO();
				$query	= sprintf("INSERT INTO #__opensim_partnerrequests (`from`,`to`,`requesttime`,`status`) VALUES (%s,%s,NOW(),0)",
									$db->quote($opensimUUID),
									$db->quote($partneruuid));
				$db->setQuery($query);
				$db->execute();
				$rows = $db->getAffectedRows();
				if($rows == 1) return TRUE;
				else return FALSE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function partnerrequeststatus($status = 0) {
		$retval					= array();
		$retval['status']		= null;
		$retval['partneruuid']	= null;
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			$db		= JFactory::getDBO();
			$query	= sprintf("SELECT #__opensim_partnerrequests.`to` FROM #__opensim_partnerrequests WHERE #__opensim_partnerrequests.`from` = %s AND #__opensim_partnerrequests.`status` = %d",
									$db->quote($opensimUUID),
									$status);
			$db->setQuery($query);
			$db->execute();
			$numrows	= $db->getNumRows();
			if($numrows == 1) {
				$retval['status']		= "sent";
				$retval['partneruuid']	= $db->loadResult();
			} else {
				$query = sprintf("SELECT #__opensim_partnerrequests.`from` FROM #__opensim_partnerrequests WHERE #__opensim_partnerrequests.`to` = %s AND #__opensim_partnerrequests.`status` = %d",
										$db->quote($opensimUUID),
										$status);
				$db->setQuery($query);
				$db->execute();
				$numrows	= $db->getNumRows();
				if($numrows == 1) {
					$retval['status']		= "received";
					$retval['partneruuid']	= $db->loadResult();
				}
			}
		}
		return $retval;
	}

	public function deletepartnerrequest($partneruuid) {
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			$db = JFactory::getDBO();
			$query = sprintf("DELETE FROM #__opensim_partnerrequests WHERE (#__opensim_partnerrequests.`from` = %1\$s AND #__opensim_partnerrequests.`to` = %2\$s) OR (#__opensim_partnerrequests.`from` = %2\$s AND #__opensim_partnerrequests.`to` = %1\$s)",
								$db->quote($opensimUUID),
								$db->quote($partneruuid));
			$db->setQuery($query);
			$db->execute();
			$rows = $db->getAffectedRows();
			if($rows == 1) return TRUE;
			else return FALSE;
		} else {
			return FALSE;
		}
	}

	public function denypartnerrequest($partneruuid) {
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			$db = JFactory::getDBO();
			$query = sprintf("DELETE FROM #__opensim_partnerrequests WHERE #__opensim_partnerrequests.`to` = %s AND #__opensim_partnerrequests.`from` = %s",
								$db->quote($opensimUUID),
								$db->quote($partneruuid));
			$db->setQuery($query);
			$db->execute();
			$rows = $db->getAffectedRows();
			if($rows == 1) return TRUE;
			else return FALSE;
		} else {
			return FALSE;
		}
	}

	public function ignorepartnerrequest($partneruuid) {
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			$db		= JFactory::getDBO();
			$query	= sprintf("UPDATE #__opensim_partnerrequests SET #__opensim_partnerrequests.`status` = -1, #__opensim_partnerrequests.`updated` = NOW() WHERE #__opensim_partnerrequests.`to` = %s AND #__opensim_partnerrequests.`from` = %s",
								$db->quote($opensimUUID),
								$db->quote($partneruuid));
			$db->setQuery($query);
			$db->execute();
			$rows = $db->getAffectedRows();
			if($rows == 1) return TRUE;
			else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function getIgnoreStatus($partneruuid) {
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			$db = JFactory::getDBO();
			$query = sprintf("SELECT #__opensim_partnerrequests.* FROM #__opensim_partnerrequests WHERE #__opensim_partnerrequests.`from` = %s AND #__opensim_partnerrequests.`to` = %s AND #__opensim_partnerrequests.`status`= -1",
								$db->quote($opensimUUID),
								$db->quote($partneruuid));
			$db->setQuery($query);
			$db->execute();
			$numrows	= $db->getNumRows();
			if($numrows == 1) {
				return -1; // user is ignored by $partneruuid
			} else {
				$query = sprintf("SELECT #__opensim_partnerrequests.* FROM #__opensim_partnerrequests WHERE #__opensim_partnerrequests.`to` = %s AND #__opensim_partnerrequests.`from` = %s AND #__opensim_partnerrequests.`status`= -1",
									$db->quote($opensimUUID),
									$db->quote($partneruuid));
				$db->setQuery($query);
				$db->execute();
				$numrows	= $db->getNumRows();
				if($numrows == 1) {
					return 1; // user is ignoring $partneruuid
				} else {
					return 0; // no ignore detected
				}
			}
		} else {
			return 0; // no ignore detected
		}
	}

	public function partnership($partneruuid) {
		$user = JFactory::getUser();
		$opensimUUID = $this->opensimRelation($user->id);
		if($opensimUUID) {
			if($this->opensimCreated($partneruuid) === TRUE) {
				$db = JFactory::getDBO();
				$query1 = sprintf("INSERT INTO #__opensim_userprofile
											(avatar_id,partner)
										VALUES
											(%1\$s,%2\$s)
									ON DUPLICATE KEY UPDATE
											partner		= %2\$s",
						$db->quote($opensimUUID),
						$db->quote($partneruuid));

				$query2 = sprintf("INSERT INTO #__opensim_userprofile
											(avatar_id,partner)
										VALUES
											(%1\$s,%2\$s)
									ON DUPLICATE KEY UPDATE
											partner		= %2\$s",
						$db->quote($partneruuid),
						$db->quote($opensimUUID));

				$db->setQuery($query1);
				$db->execute();

				$db->setQuery($query2);
				$db->execute();

				return $this->deletepartnerrequest($partneruuid);
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}
?>