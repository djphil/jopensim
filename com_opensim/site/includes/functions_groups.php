<?php
/***********************************************************************

xmlrpc functions for group handling

adopted flotsam groups

 * @component jOpenSim (Communication Interface with the OpenSim Server)
 * @Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html

***********************************************************************/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$groupWriteKey = $grp_writekey;
$groupReadKey  = $grp_readkey;

$membersVisibleTo = 'Group'; // Anyone in the group can see members
// $membersVisibleTo = 'Owners'; // Only members of the owners role can see members
// $membersVisibleTo = 'All'; // Anyone can see members

$groupPowers = array(
	'None' => '0',
	/// <summary>Can send invitations to groups default role</summary>
	'Invite' => '2',
	/// <summary>Can eject members from group</summary>
	'Eject' => '4',
	/// <summary>Can toggle 'Open Enrollment' and change 'Signup fee'</summary>
	'ChangeOptions' => '8',
	/// <summary>Can create new roles</summary>
	'CreateRole' => '16',
	/// <summary>Can delete existing roles</summary>
	'DeleteRole' => '32',
	/// <summary>Can change Role names, titles and descriptions</summary>
	'RoleProperties' => '64',
	/// <summary>Can assign other members to assigners role</summary>
	'AssignMemberLimited' => '128',
	/// <summary>Can assign other members to any role</summary>
	'AssignMember' => '256',
	/// <summary>Can remove members from roles</summary>
	'RemoveMember' => '512',
	/// <summary>Can assign and remove abilities in roles</summary>
	'ChangeActions' => '1024',
	/// <summary>Can change group Charter, Insignia, 'Publish on the web' and which
	/// members are publicly visible in group member listings</summary>
	'ChangeIdentity' => '2048',
	/// <summary>Can buy land or deed land to group</summary>
	'LandDeed' => '4096',
	/// <summary>Can abandon group owned land to Governor Linden on mainland, or Estate owner for
	/// private estates</summary>
	'LandRelease' => '8192',
	/// <summary>Can set land for-sale information on group owned parcels</summary>
	'LandSetSale' => '16384',
	/// <summary>Can subdivide and join parcels</summary>
	'LandDivideJoin' => '32768',
	/// <summary>Can join group chat sessions</summary>
	'JoinChat' => '65536',
	/// <summary>Can toggle "Show in Find Places" and set search category</summary>
	'FindPlaces' => '131072',
	/// <summary>Can change parcel name, description, and 'Publish on web' settings</summary>
	'LandChangeIdentity' => '262144',
	/// <summary>Can set the landing point and teleport routing on group land</summary>
	'SetLandingPoint' => '524288',
	/// <summary>Can change music and media settings</summary>
	'ChangeMedia' => '1048576',
	/// <summary>Can toggle 'Edit Terrain' option in Land settings</summary>
	'LandEdit' => '2097152',
	/// <summary>Can toggle various About Land > Options settings</summary>
	'LandOptions' => '4194304',
	/// <summary>Can always terraform land, even if parcel settings have it turned off</summary>
	'AllowEditLand' => '8388608',
	/// <summary>Can always fly while over group owned land</summary>
	'AllowFly' => '16777216',
	/// <summary>Can always rez objects on group owned land</summary>
	'AllowRez' => '33554432',
	/// <summary>Can always create landmarks for group owned parcels</summary>
	'AllowLandmark' => '67108864',
	/// <summary>Can use voice chat in Group Chat sessions</summary>
	'AllowVoiceChat' => '134217728',
	/// <summary>Can set home location on any group owned parcel</summary>
	'AllowSetHome' => '268435456',
	/// <summary>Can host events on group owned parcels</summary>
	'EventManager'	=> '2199023255552',
	/// <summary>Can modify public access settings for group owned parcels</summary>
	'LandManageAllowed' => '536870912',
	/// <summary>Can manager parcel ban lists on group owned land</summary>
	'LandManageBanned' => '1073741824',
	/// <summary>Can manage pass list sales information</summary>
	'LandManagePasses' => '2147483648',
	/// <summary>Can eject and freeze other avatars on group owned land</summary>
	'LandEjectAndFreeze' => '4294967296',
	/// <summary>Can return objects set to group</summary>
	'ReturnGroupSet' => '8589934592',
	/// <summary>Can return non-group owned/set objects</summary>
	'ReturnNonGroup' => '17179869184',
	/// <summary>Can landscape using Linden plants</summary>
	'LandGardening' => '34359738368',
	/// <summary>Can deed objects to group</summary>
	'DeedObject' => '68719476736',
	/// <summary>Can moderate group chat sessions</summary>
	'ModerateChat' => '137438953472',
	/// <summary>Can move group owned objects</summary>
	'ObjectManipulate' => '274877906944',
	/// <summary>Can set group owned objects for-sale</summary>
	'ObjectSetForSale' => '549755813888',
	/// <summary>Pay group liabilities and receive group dividends</summary>
	'Accountable' => '1099511627776',
	/// <summary>Can send group notices</summary>
	'SendNotices'    => '4398046511104',
	/// <summary>Can receive group notices</summary>
	'ReceiveNotices' => '8796093022208',
	/// <summary>Can create group proposals</summary>
	'StartProposal' => '17592186044416',
	/// <summary>Can vote on group proposals</summary>
	'VoteOnProposal' => '35184372088832',
	/// <summary>Can return group owned objects</summary>
	'ReturnGroupOwned' => '281474976710656',

	/// <summary>Members are visible to non-owners</summary>
	'RoleMembersVisible' => '140737488355328',

	// changed to here for easier change later
	'everyonePowers' => '203410053857280',
//	'everyonePowers' => '281474976710655'
//	'ownerPowers'	=> '281474976710655'
	'ownerPowers'	=> '4503599627370495'
);

// This is filled in by secure()
$uuidZero			= "00000000-0000-0000-0000-000000000000";
$requestingAgent	= $uuidZero;
$common_sig			= array(array($xmlrpcStruct, $xmlrpcStruct));

$xmlrpcserver = new jxmlrpc_server(array(
		// Group Functions
			"groups.createGroup"				=> array("function" => "createGroup",				"signature" => $common_sig),
			"groups.updateGroup"				=> array("function" => "updateGroup",				"signature" => $common_sig),
			"groups.getGroup"					=> array("function" => "getGroup",					"signature" => $common_sig),
			"groups.findGroups"					=> array("function" => "findGroups",				"signature" => $common_sig),
			"groups.getGroupRoles"				=> array("function" => "getGroupRoles",				"signature" => $common_sig),
			"groups.addRoleToGroup"				=> array("function" => "addRoleToGroup",			"signature" => $common_sig),
			"groups.removeRoleFromGroup"		=> array("function" => "removeRoleFromGroup",		"signature" => $common_sig),
			"groups.updateGroupRole"			=> array("function" => "updateGroupRole",			"signature" => $common_sig),
			"groups.getGroupRoleMembers"		=> array("function" => "getGroupRoleMembers",		"signature" => $common_sig),
			"groups.setAgentGroupSelectedRole"	=> array("function" => "setAgentGroupSelectedRole",	"signature" => $common_sig),
			"groups.addAgentToGroupRole"		=> array("function" => "addAgentToGroupRole",		"signature" => $common_sig),
			"groups.removeAgentFromGroupRole"	=> array("function" => "removeAgentFromGroupRole",	"signature" => $common_sig),
			"groups.getGroupMembers"			=> array("function" => "getGroupMembers",			"signature" => $common_sig),
			"groups.addAgentToGroup"			=> array("function" => "addAgentToGroup",			"signature" => $common_sig),
			"groups.addAgentToGroup2"			=> array("function" => "_addAgentToGroup",			"signature" => $common_sig),
			"groups.removeAgentFromGroup"		=> array("function" => "removeAgentFromGroup",		"signature" => $common_sig),
			"groups.setAgentGroupInfo"			=> array("function" => "setAgentGroupInfo",			"signature" => $common_sig),
			"groups.addAgentToGroupInvite"		=> array("function" => "addAgentToGroupInvite",		"signature" => $common_sig),
			"groups.getAgentToGroupInvite"		=> array("function" => "getAgentToGroupInvite",		"signature" => $common_sig),
			"groups.removeAgentToGroupInvite"	=> array("function" => "removeAgentToGroupInvite",	"signature" => $common_sig),
			"groups.setAgentActiveGroup"		=> array("function" => "setAgentActiveGroup",		"signature" => $common_sig),
			"groups.getAgentGroupMembership"	=> array("function" => "getAgentGroupMembership",	"signature" => $common_sig),
			"groups.getAgentGroupMemberships"	=> array("function" => "getAgentGroupMemberships",	"signature" => $common_sig),
			"groups.getAgentActiveMembership"	=> array("function" => "getAgentActiveMembership",	"signature" => $common_sig),
			"groups.getAgentRoles"				=> array("function" => "getAgentRoles",				"signature" => $common_sig),
			"groups.getGroupNotices"			=> array("function" => "getGroupNotices",			"signature" => $common_sig),
			"groups.getGroupNotice"				=> array("function" => "getGroupNotice",			"signature" => $common_sig),
			"groups.addGroupNotice"				=> array("function" => "addGroupNotice",			"signature" => $common_sig),

			"groups.test"						=> array("function" => "test"),
	), false);

function createGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter createGroup");
	}

	if(is_array($error = secureRequest($params, TRUE))) {
		return $error;
	}

	global $groupPowers,$uuidZero;

	$everyonePowers	= (intval($params["EveryonePowers"]) | intval($groupPowers['RoleMembersVisible']));
	$ownersPowers	= intval($groupPowers['ownerPowers']);

	$db				= JFactory::getDbo();

	$newGroup	= new stdClass();
	$newGroup->GroupID			= $params["GroupID"];
	$newGroup->Name				= $params["Name"];
	$newGroup->Charter			= $params["Charter"];
	$newGroup->InsigniaID		= $params["InsigniaID"];
	$newGroup->FounderID		= $params["FounderID"];
	$newGroup->MembershipFee	= $params["MembershipFee"];
	$newGroup->OpenEnrollment	= $params["OpenEnrollment"];
	$newGroup->ShowInList		= $params["ShowInList"];
	$newGroup->AllowPublish		= $params["AllowPublish"];
	$newGroup->MaturePublish	= $params["MaturePublish"];
	$newGroup->OwnerRoleID		= $params["OwnerRoleID"];

	$result = $db->insertObject('#__opensim_group', $newGroup);

	if($result !== TRUE) {
		return array('error' => "Error while creating group ".$params["GroupID"]);
	}

	// Create the "everyone" role
	$result = _addRoleToGroup(array('GroupID'		=> $params["GroupID"],
									'RoleID'		=> $uuidZero,
									'Name'			=> 'Everyone',
									'Description'	=> 'Everyone in the group is in the everyone role.',
									'Title'			=> "Member of ".$params["Name"],
									'Powers'		=> $everyonePowers));
	if(isset($result['error'])) {
		return $result;
	}

	// Create Owner Role
	$result = _addRoleToGroup(array('GroupID'		=> $params["GroupID"],
									'RoleID'		=> $params["OwnerRoleID"],
									'Name'			=> 'Owners',
									'Description'	=> "Owners of ".$params["Name"],
									'Title'			=> "Owner of ".$params["Name"],
									'Powers'		=> $ownersPowers));
	if(isset($result['error'])) {
		return $result;
	}

	// Add founder to group, will automatically place them in the Everyone Role, also places them in specified Owner Role
	$result = _addAgentToGroup(array('AgentID'		=> $params["FounderID"],
									 'GroupID'		=> $params["GroupID"],
									 'RoleID'		=> $params["OwnerRoleID"]));
	if(isset($result['error'])) {
		return $result;
	}

	// Select the owner's role for the founder
	$result = _setAgentGroupSelectedRole(array('AgentID'	=> $params["FounderID"],
											   'RoleID'		=> $params["OwnerRoleID"],
											   'GroupID'	=> $params["GroupID"]));
	if(isset($result['error'])) {
		return $result;
	}

	// Set the new group as the founder's active group
	$result = _setAgentActiveGroup(array('AgentID'	=> $params["FounderID"],
										 'GroupID'	=> $params["GroupID"]));
	if(isset($result['error'])) {
		return $result;
	}

	$retval = _getGroup(array("GroupID"	=> $params["GroupID"]));
	return $retval;
}

// Private method, does not include security, to only be called from places that have already verified security
function _addRoleToGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter _addRoleToGroup");
	}
//	groupdebug($params);
	global $groupPowers;

	if(!isset($params['Powers']) || ($params['Powers'] == 0) || ($params['Powers'] == '')) {
		$powers	= $groupPowers['everyonePowers'];
	} else {
		$powers	= $params['Powers'];
	}

	$db			= JFactory::getDbo();

	$newRole	= new stdClass();
	$newRole->GroupID		= $params['GroupID'];
	$newRole->RoleID		= $params['RoleID'];
	$newRole->Name			= $params['Name'];
	$newRole->Description	= $params['Description'];
	$newRole->Title			= $params['Title'];
	$newRole->Powers		= $powers;

	$result = $db->insertObject('#__opensim_grouprole', $newRole);

	if($result !== TRUE) {
		return array('error' => "Error while creating role ".$params['RoleID']." for group ".$params['GroupID']);
	} else {
		return array("success" => "true");
	}
}

function addRoleToGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter addRoleToGroup");
	}
	if(is_array($error = secureRequest($params, TRUE))) {
		return $error;
	}

	global $groupPowers;

	// Verify the requesting agent has permission
	if(is_array($error = checkGroupPermission($params['GroupID'], $groupPowers['CreateRole']))) {
		return $error;
	}

	return _addRoleToGroup($params);
}

function updateGroupRole($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter updateGroupRole");
	}
	if(is_array($error = secureRequest($params, TRUE))) {
		return $error;
	}

	global $groupPowers;

	// Verify the requesting agent has permission
	if(is_array($error = checkGroupPermission($params['GroupID'],$groupPowers['RoleProperties']))) {
		return $error;
	}

	$db				= JFactory::getDbo();

	$updateRole	= new stdClass();

	// GroupID and RoleID are required
	$updateRole->GroupID			= $params["GroupID"];
	$updateRole->RoleID				= $params["RoleID"];

	if(array_key_exists("Name",$params))		$updateRole->Name			= $params["Name"];
	if(array_key_exists("Description",$params))	$updateRole->Description	= $params["Description"];
	if(array_key_exists("Title",$params))		$updateRole->Title			= $params["Title"];
	if(array_key_exists("Powers",$params))		$updateRole->Powers			= $params["Powers"];

	$condition	= array("GroupID",
						"RoleID");

	$result = $db->updateObject('#__opensim_grouprole', $updateRole, $condition);

	if($result === TRUE) {
		return array('success' => 'true');
	} else {
		return array('error' => "Error updating role ".$params["RoleID"]." for group ".$params['GroupID']);
	}
}

function removeRoleFromGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter removeRoleFromGroup");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	global $uuidZero, $groupPowers;

	if(is_array($error = checkGroupPermission($params['GroupID'],$groupPowers['RoleProperties']))) {
		return $error;
	}

	$db				= JFactory::getDbo();

	/// 1. Remove all members from Role
	$query = $db->getQuery(true);
	$conditions = array(
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID']), 
		$db->quoteName('RoleID').' = '.$db->quote($params['RoleID'])
	);
	$query->delete($db->quoteName('#__opensim_grouprolemembership'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();
	/// 2. Set selected Role to uuidZero for anyone that had the role selected
	$query	= $db->getQuery(true);
	$fields	= array(
				$db->quoteName('SelectedRoleID').' = '.$db->quote($uuidZero)
			);
	$conditions = array(
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID']), 
		$db->quoteName('SelectedRoleID').' = '.$db->quote($params['RoleID'])
	);
	$query->update($db->quoteName('#__opensim_groupmembership'))->set($fields)->where($conditions);
	$db->setQuery($query);
	$db->execute();
	/// 3. Delete roll
	$query = $db->getQuery(true);
	$conditions = array(
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID']), 
		$db->quoteName('RoleID').' = '.$db->quote($params['RoleID'])
	);
	$query->delete($db->quoteName('#__opensim_group'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();
	return array("success" => "true");
}

function getGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getGroup");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}

	return _getGroup($params);
}

function _getGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter _getGroup");
	}

	// GroupID or Name is required
	if((!array_key_exists("GroupID",$params) || !$params['GroupID']) && (!array_key_exists("Name",$params) || !$params['Name'])) {
		return array("error" => "Must specify GroupID or Name");
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query->select($db->quoteName('#__opensim_group.GroupID'));
	$query->select($db->quoteName('#__opensim_group.Name'));
	$query->select($db->quoteName('#__opensim_group.Charter'));
	$query->select($db->quoteName('#__opensim_group.InsigniaID'));
	$query->select($db->quoteName('#__opensim_group.FounderID'));
	$query->select($db->quoteName('#__opensim_group.MembershipFee'));
	$query->select($db->quoteName('#__opensim_group.OpenEnrollment'));
	$query->select($db->quoteName('#__opensim_group.ShowInList'));
	$query->select($db->quoteName('#__opensim_group.AllowPublish'));
	$query->select($db->quoteName('#__opensim_group.MaturePublish'));
	$query->select($db->quoteName('#__opensim_group.OwnerRoleID'));
	$query->select('COUNT('.$db->quoteName('#__opensim_grouprole.RoleID').') AS GroupRolesCount');
	$query->select('COUNT('.$db->quoteName('#__opensim_groupmembership.AgentID').') AS GroupMembershipCount');

	$query->from($db->quoteName('#__opensim_group'));

	$query->join("LEFT",$db->quoteName('#__opensim_grouprole').' ON ('.$db->quoteName('#__opensim_group.GroupID').' = '.$db->quoteName('#__opensim_grouprole.GroupID').')');
	$query->join("LEFT",$db->quoteName('#__opensim_groupmembership').' ON ('.$db->quoteName('#__opensim_group.GroupID').' = '.$db->quoteName('#__opensim_groupmembership.GroupID').')');

	if(array_key_exists("GroupID",$params) && $params['GroupID'])	$query->where($db->quoteName('#__opensim_group.GroupID').' = '.$db->quote($params['GroupID']));
	if(array_key_exists("Name",$params) && $params['Name'])			$query->where($db->quoteName('#__opensim_group.Name').' = '.$db->quote($params['Name']));

	$query->group($db->quoteName('#__opensim_group.GroupID'));

	$db->setQuery($query);
	$db->execute();
	$foundgroup = $db->getNumRows();
	if($foundgroup == 1) {
		$groupdata = $db->loadAssoc();
		return $groupdata;
	} else {
		if(array_key_exists("GroupID",$params) && $params['GroupID'])	return array('succeed' => 'false', 'error' => 'Group with ID '.$params['GroupID'].' not found');
		elseif(array_key_exists("Name",$params) && $params['Name'])		return array('succeed' => 'false', 'error' => 'Group with name '.$params['Name'].' not found');
		else															return array('succeed' => 'false', 'error' => 'Group not found');
	}
}

function updateGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter updateGroup");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	global $groupPowers;

	if(is_array($error = checkGroupPermission($params["GroupID"],$groupPowers['ChangeOptions']))) {
		return $error;
	}

	$db				= JFactory::getDbo();

	$updateGroup	= new stdClass();

	// GroupID is required
	$updateGroup->GroupID			= $params["GroupID"];

	if(array_key_exists("Charter",$params))			$updateGroup->Charter			= $params["Charter"];
	if(array_key_exists("InsigniaID",$params))		$updateGroup->InsigniaID		= $params["InsigniaID"];
	if(array_key_exists("MembershipFee",$params))	$updateGroup->MembershipFee		= $params["MembershipFee"];
	if(array_key_exists("OpenEnrollment",$params))	$updateGroup->OpenEnrollment	= $params["OpenEnrollment"];
	if(array_key_exists("ShowInList",$params))		$updateGroup->ShowInList		= $params["ShowInList"];
	if(array_key_exists("AllowPublish",$params))	$updateGroup->AllowPublish		= $params["AllowPublish"];
	if(array_key_exists("MaturePublish",$params))	$updateGroup->MaturePublish		= $params["MaturePublish"];

	$result = $db->updateObject('#__opensim_group', $updateGroup, 'GroupID');

	if($result === TRUE) {
		return array('success' => 'true');
	} else {
		return array('error' => "Error updating Group ".$params['GroupID']);
	}
}


function findGroups($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter findGroups");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}
	if(!isset($params['Search']) || !$params['Search']) {
		$retval['success'] = FALSE;
		$retval['message'] = "no search term provided!";
		return $retval;
	}

	$db		= JFactory::getDbo();
	$search = '%'.$db->escape( $params['Search'],true).'%';
	$query	= $db->getQuery(true);
	$query->select($db->quoteName('#__opensim_group.GroupID'));
	$query->select($db->quoteName('#__opensim_group.Name'));
	$query->select('COUNT('.$db->quoteName('#__opensim_groupmembership.AgentID').') AS Members');
	$query->from($db->quoteName('#__opensim_group'));
	$query->join("LEFT",$db->quoteName('#__opensim_groupmembership').' ON ('.$db->quoteName('#__opensim_group.GroupID').' = '.$db->quoteName('#__opensim_groupmembership.GroupID').')');
	$query->group($db->quoteName('#__opensim_group.GroupID'));
	$conditions = array(
					"MATCH (".$db->quoteName('#__opensim_group.Name').") AGAINST (".$db->quote($params['Search'])." IN BOOLEAN MODE)",
					$db->quoteName('#__opensim_group.Name')." LIKE ".$db->quote($search,false),
					$db->quoteName('#__opensim_group.Name')." REGEXP ".$db->quote($params['Search']));
	$query->where($conditions, 'OR');
	$db->setQuery($query);
	$db->execute();
	$foundgroup = $db->getNumRows();
	if($foundgroup > 0) {
		$results = array();
		$groupdata = $db->loadAssocList();
		foreach($groupdata AS $groupresult) {
			$results[$groupresult['GroupID']] = $groupresult;
		}
		return array('results' => $results, 'success' => TRUE);
	} else {
		return array('succeed' => 'false', 'error' => 'No groups found.');
	}
}

function _setAgentActiveGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter _setAgentActiveGroup");
	}

	$db		= JFactory::getDbo();
	$query	= "INSERT INTO ".$db->quoteName('#__opensim_groupactive')." (".$db->quoteName('ActiveGroupID').", ".$db->quoteName('AgentID').") VALUES (".$db->quote($params['GroupID']).", ".$db->quote($params['AgentID']).")
					ON DUPLICATE KEY UPDATE ".$db->quoteName('ActiveGroupID')." = ".$db->quote($params['GroupID']).", ".$db->quoteName('AgentID')." = ".$db->quote($params['AgentID']);

	$db->setQuery($query);
	$db->execute();
	return array("success" => "true");
}

function setAgentActiveGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter setAgentActiveGroup");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	global $groupEnforceGroupPerms, $requestingAgent, $uuidZero;

	if(isset($requestingAgent) && ($requestingAgent != $uuidZero) && ($requestingAgent != $params['AgentID'])) {
		return array('error' => "Agent can only change their own Selected Group Role", 'params' => varexport($params, TRUE));
	}

	return _setAgentActiveGroup($params);
}

function addAgentToGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter addAgentToGroup");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	global $groupPowers;

	if(is_array($error = checkGroupPermission($params["GroupID"],$groupPowers['AssignMember']))) {
		// If they don't have direct permission, check to see if the group is marked for open enrollment
		$groupInfo = _getGroup(array('GroupID' => $params["GroupID"]));

		if(isset($groupInfo['error'])) {
			return $groupInfo;
		}

		if($groupInfo['OpenEnrollment'] != 1) { // Group is not open enrollment, check if the specified agentid has an invite

			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->quoteName('#__opensim_groupinvite.GroupID'));
			$query->select($db->quoteName('#__opensim_groupinvite.RoleID'));
			$query->select($db->quoteName('#__opensim_groupinvite.AgentID'));
			$query->from($db->quoteName('#__opensim_groupinvite'));
			$query->where($db->quoteName('#__opensim_groupinvite.GroupID')." = ".$db->quote($params["GroupID"]));
			$query->where($db->quoteName('#__opensim_groupinvite.AgentID')." = ".$db->quote($params["AgentID"]));

			$db->setQuery($query);
			$db->execute();
			$foundinvite = $db->getNumRows();
			if($foundinvite == 1) {
				$invite = $db->loadAssoc();
				$params['RoleID'] = $invite['RoleID'];
			} else {
				return array('error' => "No access to join group ".$params["GroupID"]);
			}
		}
	}

	return _addAgentToGroup($params);
}

// Private method, does not include security, to only be called from places that have already verified security
function _addAgentToGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		debugzeile(varexport($params,TRUE),"Parameter _addAgentToGroup in Zeile ".__LINE__);
	}
	global $uuidZero, $groupPowers;

	if(!array_key_exists("RoleID",$params) || !$params['RoleID']) $params['RoleID'] = $uuidZero;

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	// Check if agent already a member
	$query->select("COUNT(".$db->quoteName('#__opensim_groupmembership.AgentID').") AS isMember");
	$query->from($db->quoteName('#__opensim_groupmembership'));
	$query->where($db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quote($params["AgentID"]));
	$query->where($db->quoteName('#__opensim_groupmembership.GroupID')." = ".$db->quote($params["GroupID"]));

	$db->setQuery($query);
	$db->execute();
	$foundmembership = $db->loadResult();

	// If not a member, add membership, select role (defaults to uuidZero, or everyone role)
	if($foundmembership == 0) {
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('#__opensim_group.MembershipFee'));
		$query->from($db->quoteName('#__opensim_group'));
		$query->where($db->quoteName('#__opensim_group.GroupID')." = ".$db->quote($params["GroupID"]));
		$db->setQuery($query);
		$groupfee	= $db->loadResult();
		if($groupfee > 0) { // There is a fee set
			$currencyfunctionfile = JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'functions_currency.php'; // for jOpenSimMoney this file needs to be present
			if(is_file($currencyfunctionfile)) { // lets check if currency is enabled
				require_once($currencyfunctionfile); // get the functions
				if(defined('_JOPENSIMCURRENCY') && _JOPENSIMCURRENCY === TRUE) { // this jOpenSim has currency enabled, we need to check who gets the Enrollment fee
					$parameter['clientUUID']		= $agentID;
					$parameter['amount']			= $groupfee;
					$covered = AmountCovered($parameter);
					if($covered['success'] == TRUE) {
						$parameter['clientUUID']	= $params["AgentID"];
						$parameter['groupID']		= $params["GroupID"];
						$parameter['groupPower']	= $groupPowers['Accountable'];
						$parameter['groupFee']		= $groupfee;
						$parameter['sessionID']		= (array_key_exists("sessionID",$params)) ? $params["sessionID"]:$uuidZero;

						$retval = groupMembershipFee($parameter);
						if($retval['success'] === FALSE) { // Something went wrong during paying groupMembershipFee (or checking groupDividend)?
							return $retval;
						}
					} else {
						$retval = $covered;
						$retval1['covered'] = FALSE;
//						return $covered;
					}
				} else {
					$retval1['defined'] = _JOPENSIMCURRENCY;
				}
			} else {
				$retval1['script_filename'] = $_SERVER['SCRIPT_FILENAME'];
				$retval1['filenotfound'] = "Line ".__LINE__.": Currency enabled and Group has MembershipFee, but currency.php not found";
//				return $retval1;
			}
		}

		$newMember	= new stdClass();
		$newMember->GroupID			= $params['GroupID'];
		$newMember->AgentID			= $params['AgentID'];
		$newMember->Contribution	= 0;
		$newMember->ListInProfile	= 1;
		$newMember->AcceptNotices	= 1;
		$newMember->SelectedRoleID	= $params['RoleID'];

		$result = $db->insertObject('#__opensim_groupmembership', $newMember);
	}

	// Make sure they're in the Everyone role
	$result = _addAgentToGroupRole(array("GroupID" => $params['GroupID'], "RoleID" => $uuidZero, "AgentID" => $params['AgentID']));
	if(isset($result['error'])) {
		return $result;
	}

	// Make sure they're in specified role, if they were invited
	if($params['RoleID'] != $uuidZero) {
		$result = _addAgentToGroupRole(array("GroupID" => $params['GroupID'], "RoleID" => $params['RoleID'], "AgentID" => $params['AgentID']));
		if(isset($result['error'])) {
			return $result;
		}
	}

	//Set the role they were invited to as their selected role
	_setAgentGroupSelectedRole(array('AgentID' => $params['AgentID'], 'RoleID' => $params['RoleID'], 'GroupID' => $params['GroupID']));

	// Set the group as their active group.
	// _setAgentActiveGroup(array("GroupID" => $groupID, "AgentID" => $agentID));

	$retval['success'] = TRUE;
	if(isset($retval1)) $retval['sub_returnvalue'] = $retval1;
	return $retval;
}

function removeAgentFromGroup($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter removeAgentFromGroup");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	global $groupPowers,$requestingAgent,$uuidZero;

	// An agent is always allowed to remove themselves from a group -- so only check if the requesting agent is different then the agent being removed.
	if($params["AgentID"] != $requestingAgent) {
		if(is_array($error = checkGroupPermission($params["GroupID"],$groupPowers['RemoveMember']))) {
			return $error;
		}
	}

	$db				= JFactory::getDbo();

	// 1. If group is agent's active group, change active group to uuidZero
	$query = $db->getQuery(true);
	$fields	= array(
				$db->quoteName('ActiveGroupID').' = '.$db->quote($uuidZero)
			);
	$conditions = array(
		$db->quoteName('AgentID').' = '.$db->quote($params['AgentID']), 
		$db->quoteName('ActiveGroupID').' = '.$db->quote($params['GroupID'])
	);
	$query->update($db->quoteName('#__opensim_groupactive'))->set($fields)->where($conditions);
	$db->setQuery($query);
	$db->execute();
	// 2. Remove Agent from group (osgroupmembership)
	$query = $db->getQuery(true);
	$conditions = array(
		$db->quoteName('AgentID').' = '.$db->quote($params['AgentID']), 
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID'])
	);
	$query->delete($db->quoteName('#__opensim_groupmembership'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();
	// 3. Remove Agent from all of the groups roles (osgrouprolemembership)
	$query = $db->getQuery(true);
	$query->delete($db->quoteName('#__opensim_grouprolemembership'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();

	return array("success" => "true");
}

function _addAgentToGroupRole($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter _addAgentToGroupRole");
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);
	$query->select("COUNT(".$db->quoteName('#__opensim_grouprolemembership.AgentID').") AS isMember");
	$query->from($db->quoteName('#__opensim_grouprolemembership'));
	$query->where($db->quoteName('#__opensim_grouprolemembership.AgentID')." = ".$db->quote($params["AgentID"]));
	$query->where($db->quoteName('#__opensim_grouprolemembership.RoleID')." = ".$db->quote($params["RoleID"]));
	$query->where($db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quote($params["GroupID"]));

	$db->setQuery($query);
	$db->execute();
	$foundmemberrole = $db->loadResult();
	if($foundmemberrole == 0) {
		$newMemberRole	= new stdClass();
		$newMemberRole->GroupID	= $params['GroupID'];
		$newMemberRole->RoleID	= $params['RoleID'];
		$newMemberRole->AgentID	= $params['AgentID'];
		$result = $db->insertObject('#__opensim_grouprolemembership', $newMemberRole);
	}
	return array("success" => "true");
}

function addAgentToGroupRole($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter addAgentToGroupRole");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	global $groupPowers, $requestingAgent;

	if(is_array($error = checkGroupPermission($params["GroupID"],$groupPowers['AssignMember']))) {
		return $error;
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	// Check if being assigned to Owners role, assignments to an owners role can only be requested by owners.
	$query->select($db->quoteName('#__opensim_group.OwnerRoleID'));
	$query->select($db->quoteName('#__opensim_grouprolemembership.AgentID'));
	$query->from($db->quoteName('#__opensim_group'));
	$query->join("LEFT",$db->quoteName('#__opensim_grouprolemembership')." ON (".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." AND ".$db->quoteName('#__opensim_group.OwnerRoleID')." = ".$db->quoteName('#__opensim_grouprolemembership.RoleID').")");
	$query->where($db->quoteName('#__opensim_grouprolemembership.AgentID')." = ".$db->quote($params["RequestingAgentID"]));
	$query->where($db->quoteName('#__opensim_group.GroupID')." = ".$db->quote($params["GroupID"]));

	$db->setQuery($query);
	$db->execute();
	$foundowner = $db->getNumRows();
	if($foundowner == 0) {
		return array('error' => "Group (".$params["GroupID"].") not found or Agent (".$params["RequestingAgentID"].") is not in the owner's role");
	}

	$ownerRoleInfo = $db->loadAssoc();

	if(($ownerRoleInfo['OwnerRoleID'] == $params["RoleID"]) && ($ownerRoleInfo['AgentID'] != $requestingAgent)) {
		return array('error' => "Requesting agent ".$requestingAgent." is not a member of the Owners Role and cannot add members to the owners role.");
	}

	return _addAgentToGroupRole($params);
}

function removeAgentFromGroupRole($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter removeAgentFromGroupRole");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}
	if(!array_key_exists("AgentID",$params) || !array_key_exists("GroupID",$params) || !array_key_exists("RoleID",$params)) {
	}

	global $uuidZero, $groupPowers;

	if(is_array($error = checkGroupPermission($params["GroupID"],$groupPowers['AssignMember']))) {
		return $error;
	}

	$db				= JFactory::getDbo();

	// 1. If agent has this role selected, change their selection to everyone (uuidZero) role
	$query = $db->getQuery(true);
	$fields	= array(
				$db->quoteName('SelectedRoleID').' = '.$db->quote($uuidZero)
			);
	$conditions = array(
		$db->quoteName('AgentID').' = '.$db->quote($params['AgentID']), 
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID']), 
		$db->quoteName('SelectedRoleID').' = '.$db->quote($params['RoleID'])
	);
	$query->update($db->quoteName('#__opensim_groupmembership'))->set($fields)->where($conditions);
	$db->setQuery($query);
	$db->execute();

	// 2. Delete Agent from group role
	$query = $db->getQuery(true);
	$conditions = array(
		$db->quoteName('AgentID').' = '.$db->quote($params['AgentID']), 
		$db->quoteName('RoleID').' = '.$db->quote($params['RoleID']),
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID'])
	);
	$query->delete($db->quoteName('#__opensim_grouprolemembership'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();

	return array("success" => "true");
}

function _setAgentGroupSelectedRole($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter _setAgentGroupSelectedRole");
	}

	$db		= JFactory::getDbo();

	$query	= $db->getQuery(true);
	$fields	= array(
				$db->quoteName('SelectedRoleID').' = '.$db->quote($params['RoleID'])
			);
	$conditions = array(
		$db->quoteName('AgentID').' = '.$db->quote($params['AgentID']), 
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID'])
	);
	$query->update($db->quoteName('#__opensim_groupmembership'))->set($fields)->where($conditions);
	$db->setQuery($query);
	$db->execute();
	return array('success' => 'true');
}

function setAgentGroupSelectedRole($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter setAgentGroupSelectedRole");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	global $requestingAgent, $uuidZero;

	if(isset($requestingAgent) && ($requestingAgent != $uuidZero) && ($requestingAgent != $params["AgentID"])) {
		return array('error' => "Agent can only change their own Selected Group Role");
	}

	return _setAgentGroupSelectedRole($params);
}

function getAgentGroupMembership($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getAgentGroupMembership");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}
	return _getAgentGroupMembership($params);
}

function _getAgentGroupMembership($params) {
	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query->select($db->quoteName('#__opensim_group.GroupID'));
	$query->select($db->quoteName('#__opensim_group.Name')." AS GroupName");
	$query->select($db->quoteName('#__opensim_group.Charter'));
	$query->select($db->quoteName('#__opensim_group.InsigniaID'));
	$query->select($db->quoteName('#__opensim_group.FounderID'));
	$query->select($db->quoteName('#__opensim_group.MembershipFee'));
	$query->select($db->quoteName('#__opensim_group.OpenEnrollment'));
	$query->select($db->quoteName('#__opensim_group.ShowInList'));
	$query->select($db->quoteName('#__opensim_group.AllowPublish'));
	$query->select($db->quoteName('#__opensim_group.MaturePublish'));
	$query->select($db->quoteName('#__opensim_groupmembership.Contribution'));
	$query->select($db->quoteName('#__opensim_groupmembership.ListInProfile'));
	$query->select($db->quoteName('#__opensim_groupmembership.AcceptNotices'));
	$query->select($db->quoteName('#__opensim_groupmembership.SelectedRoleID'));
	$query->select($db->quoteName('#__opensim_grouprole.Title'));
	$query->select("IFNULL(".$db->quoteName('#__opensim_groupactive.ActiveGroupID').",'00000000-0000-0000-0000-000000000000') AS ActiveGroupID");
	$query->from($db->quoteName('#__opensim_group'));
	$query->join("INNER",$db->quoteName('#__opensim_groupmembership')." ON (".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_groupmembership.GroupID').")");
	$query->join("INNER",$db->quoteName('#__opensim_grouprole')." ON (".$db->quoteName('#__opensim_groupmembership.SelectedRoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID')." AND ".$db->quoteName('#__opensim_grouprole.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID').")");
	$query->join("LEFT",$db->quoteName('#__opensim_groupactive')." ON (".$db->quoteName('#__opensim_groupactive.AgentID')." = ".$db->quoteName('#__opensim_groupmembership.AgentID').")");
	$query->where($db->quoteName('#__opensim_group.GroupID')." = ".$db->quote($params['GroupID']));
	$query->where($db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quote($params['AgentID']));



	$db->setQuery($query);
	$db->execute();
	$foundgroupmembership = $db->getNumRows();
	if($foundgroupmembership == 0) {
		return array('succeed' => 'false', 'error' => 'Nothing found for Agent '.$params['AgentID'].' in Group '.$params['GroupID']);
	}
	$groupMembershipData = $db->loadAssoc();

	$query	= $db->getQuery(true);
	$query->select("BIT_OR(".$db->quoteName('#__opensim_grouprole.Powers').") AS GroupPowers");
	$query->from($db->quoteName('#__opensim_grouprolemembership'));
	$query->join("INNER",$db->quoteName('#__opensim_grouprole')." ON (".$db->quoteName('#__opensim_grouprole.GroupID')." = ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." AND ".$db->quoteName('#__opensim_grouprole.RoleID')." = ".$db->quoteName('#__opensim_grouprolemembership.RoleID').")");
	$query->where($db->quoteName('#__opensim_grouprolemembership.AgentID')." = ".$db->quote($params['AgentID']));
	$query->where($db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quote($params['GroupID']));
	$db->setQuery($query);
	$memberPower = $db->loadAssoc();

	$retval = array_merge($groupMembershipData,$memberPower);

	return $retval;
}

function getAgentGroupMemberships($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getAgentGroupMemberships");
	}
	$debug = varexport($params,TRUE);
	/*groupdebug($debug,"getAgentGroupMemberships");*/
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}
	global $uuidZero;

	if(!array_key_exists("AgentID",$params) || !$params['AgentID']) return array('succeed' => 'false', 'error' => 'No AgentID provided', 'params' => varexport($params, TRUE));

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query->select($db->quoteName('#__opensim_groupmembership.GroupID'));
	$query->select($db->quoteName('#__opensim_groupmembership.ListInProfile'));
	$query->from($db->quoteName('#__opensim_groupmembership'));
	$query->where($db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quote($params['AgentID']));


	$db->setQuery($query);
	$db->execute();

	$foundgroupmemberships = $db->getNumRows();
	if($foundgroupmemberships > 0) {
		$groupmemberships = $db->loadAssocList();
		$retval = array();
		foreach($groupmemberships AS $groupmembership) {
			if($groupmembership['GroupID'] == 0 && array_key_exists("RequestingAgentID",$params) && $params['RequestingAgentID'] != $params['AgentID']) {
				$canview = canAgentViewRoleMembers($params['RequestingAgentID'],$groupmembership['GroupID'],$uuidZero); // Owners should always be able to view members
//				debugzeile(varexport($canview,TRUE),"canview für ".$groupmembership['GroupID']);
				if(!$canview) continue;
			}
			$retval[$groupmembership['GroupID']] = _getAgentGroupMembership(array("GroupID" => $groupmembership['GroupID'], "AgentID" => $params['AgentID']));
		}
		return $retval;
	} else {
		return array('succeed' => 'false', 'error' => 'No Memberships found for AgentID '.$params['AgentID']);
	}
}

function canAgentViewRoleMembers($agentID,$groupID,$roleID) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport(func_get_args(),TRUE);
		debugzeile($debug,"Parameter canAgentViewRoleMembers");
	}

	global $membersVisibleTo;

	if($membersVisibleTo == 'All') return true;

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query->select("CASE WHEN MIN(".$db->quoteName('#__opensim_grouprolemembership.AgentID').") IS NOT NULL THEN 1 ELSE 0 END AS IsOwner");
	$query->from($db->quoteName('#__opensim_group'));
	$query->join("INNER",$db->quoteName('#__opensim_groupmembership')." ON(".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_groupmembership.GroupID')." AND ".$db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quote($agentID).")");
	$query->join("LEFT",$db->quoteName('#__opensim_grouprolemembership')." ON(".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." AND ".$db->quoteName('#__opensim_group.OwnerRoleID')." = ".$db->quoteName('#__opensim_grouprolemembership.AgentID')." AND ".$db->quoteName('#__opensim_grouprolemembership.AgentID')." = ".$db->quote($agentID).")");
	$query->where($db->quoteName('#__opensim_group.GroupID')." = ".$db->quote($groupID));
	$query->group($db->quoteName('#__opensim_group.GroupID'));

	$db->setQuery($query);
	$db->execute();
	$foundIsOwner = $db->getNumRows();
	if($foundIsOwner == 0) {
		return FALSE;
	}
	$viewMemberInfo = $db->loadAssoc();

	switch($membersVisibleTo) {
		case "Group":
			// if we get to here, there is at least one row, so they are members of the group
			return true;
		break;
		case "Owners":
		default:
			return $viewMemberInfo['IsOwner'];
		break;
	}
}

function getGroupMembers($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getGroupMembers");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}
	if(!array_key_exists("GroupID",$params) || !$params['GroupID']) {
		return array('succeed' => 'false', 'error' => 'No GroupID specified for getGroupMembers!');
	}

	global $groupPowers, $requestingAgentID, $opensim;

	$requestingAgentID	= (array_key_exists("requestingAgentID",$params))	? $params['requestingAgentID']:"";

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query->select($db->quoteName('#__opensim_groupmembership.AgentID'));
	$query->select($db->quoteName('#__opensim_groupmembership.Contribution'));
	$query->select($db->quoteName('#__opensim_groupmembership.ListInProfile'));
	$query->select($db->quoteName('#__opensim_groupmembership.AcceptNotices'));
	$query->select($db->quoteName('#__opensim_groupmembership.SelectedRoleID'));
	$query->select($db->quoteName('#__opensim_grouprole.Title'));
	$query->select("CASE WHEN ".$db->quoteName('#__opensim_grouprolemembership.AgentID')." IS NOT NULL THEN 1 ELSE 0 END AS IsOwner");
	$query->from($db->quoteName('#__opensim_group'));
	$query->join("INNER",$db->quoteName('#__opensim_groupmembership')." ON (".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_groupmembership.GroupID').")");
	$query->join("INNER",$db->quoteName('#__opensim_grouprole')." ON (".$db->quoteName('#__opensim_groupmembership.SelectedRoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID')." AND ".$db->quoteName('#__opensim_groupmembership.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID').")");
	$query->join("INNER",$db->quoteName('#__opensim_grouprole')." AS OwnerRole ON (".$db->quoteName('#__opensim_group.OwnerRoleID')." = ".$db->quoteName('OwnerRole.RoleID')." AND ".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('OwnerRole.GroupID').")");
	$query->join("LEFT",$db->quoteName('#__opensim_grouprolemembership')." ON (".$db->quoteName('#__opensim_group.OwnerRoleID')." = ".$db->quoteName('#__opensim_grouprolemembership.RoleID')." AND ".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." AND ".$db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quoteName('#__opensim_grouprolemembership.AgentID').")");
	$query->where($db->quoteName('#__opensim_group.GroupID')." = ".$db->quote($params['GroupID']));

	$db->setQuery($query);
	$db->execute();
	$foundgroupmembers = $db->getNumRows();
	if($foundgroupmembers == 0) {
		return array('succeed' => 'false', 'error' => 'No Group Members found for group '.$params['GroupID']);
	}
	$allMembers = $db->loadAssocList();

	$roleMembersVisibleBit = $groupPowers['RoleMembersVisible'];
	$canViewAllGroupRoleMembers = canAgentViewRoleMembers($requestingAgentID, $params['GroupID'], '');

	$memberResults = array();

	foreach($allMembers AS $memberInfo) {
		$agentID = $memberInfo['AgentID'];

		$query	= $db->getQuery(true);
		$query->select("BIT_OR(".$db->quoteName('#__opensim_grouprole.Powers').") AS AgentPowers");
		$query->select("(BIT_OR(".$db->quoteName('#__opensim_grouprole.Powers').") & ".intval($groupPowers['RoleMembersVisible']).") AS MemberVisible");
		$query->from($db->quoteName('#__opensim_grouprolemembership'));
		$query->join("INNER",$db->quoteName('#__opensim_grouprole')." ON (".$db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID')." AND ".$db->quoteName('#__opensim_grouprolemembership.RoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID').")");
		$query->where($db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quote($params['GroupID']));
		$query->where($db->quoteName('#__opensim_grouprolemembership.AgentID')." = ".$db->quote($memberInfo['AgentID']));

		$db->setQuery($query);
		$db->execute();
		$foundMemberPowers = $db->getNumRows();
		if($foundMemberPowers == 0) {
			if($canViewAllGroupRoleMembers || ($memberResults[$memberInfo['AgentID']] == $requestingAgent)) {
				$memberResults[$agentID] = array_merge($memberInfo,array('AgentPowers' => 0));
			}
		} else {
			$memberPowersInfo = $db->loadAssoc();
			if($memberPowersInfo['MemberVisible'] || $canViewAllGroupRoleMembers) {
				$memberResults[$agentID] = array_merge($memberInfo, $memberPowersInfo);
			}
		}
	}

	if (count($memberResults) == 0) {
		return array('succeed' => 'false', 'error' => 'No visible group members found for group '.$params['GroupID']);
	}

	foreach($memberResults AS $agentID => $memberResult) {
		$lastonline = $opensim->getUserLastOnline($agentID);
		if($lastonline == 0) $memberResults[$agentID]['OnlineStatus'] = "unknown";
		else $memberResults[$agentID]['OnlineStatus'] = date("m/d/Y",$lastonline);
	}

	return $memberResults;
}


function getAgentActiveMembership($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getAgentActiveMembership");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query
		->select($db->quoteName('#__opensim_group.GroupID'))
		->select($db->quoteName('#__opensim_group.Name')." AS GroupName")
		->select($db->quoteName('#__opensim_group.Charter'))
		->select($db->quoteName('#__opensim_group.InsigniaID'))
		->select($db->quoteName('#__opensim_group.FounderID'))
		->select($db->quoteName('#__opensim_group.MembershipFee'))
		->select($db->quoteName('#__opensim_group.OpenEnrollment'))
		->select($db->quoteName('#__opensim_group.ShowInList'))
		->select($db->quoteName('#__opensim_group.AllowPublish'))
		->select($db->quoteName('#__opensim_group.MaturePublish'))
		->select($db->quoteName('#__opensim_groupmembership.Contribution'))
		->select($db->quoteName('#__opensim_groupmembership.ListInProfile'))
		->select($db->quoteName('#__opensim_groupmembership.AcceptNotices'))
		->select($db->quoteName('#__opensim_groupmembership.SelectedRoleID'))
		->select($db->quoteName('#__opensim_grouprole.Title'))
		->select($db->quoteName('#__opensim_groupactive.ActiveGroupID'))
		->from($db->quoteName('#__opensim_groupactive'))
		->join("INNER",$db->quoteName('#__opensim_group')." ON (".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_groupactive.ActiveGroupID').")")
		->join("INNER",$db->quoteName('#__opensim_groupmembership')." ON (".$db->quoteName('#__opensim_group.GroupID')." = ".$db->quoteName('#__opensim_groupmembership.GroupID')." AND ".$db->quoteName('#__opensim_groupactive.AgentID')." = ".$db->quoteName('#__opensim_groupmembership.AgentID').")")
		->join("INNER",$db->quoteName('#__opensim_grouprole')." ON (".$db->quoteName('#__opensim_groupmembership.SelectedRoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID')." AND ".$db->quoteName('#__opensim_groupmembership.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID').")")
		->where($db->quoteName('#__opensim_groupactive.AgentID')." = ".$db->quote($params['AgentID']));

	$db->setQuery($query);
	$db->execute();
	$foundActiveMembership = $db->getNumRows();
	if($foundActiveMembership == 0) {
		return array('succeed' => 'false', 'error' => 'No active group membership found for agent '.$params['AgentID']);
	}
	$groupMembershipInfo = $db->loadAssoc();

	$query	= $db->getQuery(true);
	$query
		->select("BIT_OR(".$db->quoteName('#__opensim_grouprole.Powers').") AS GroupPowers")
		->from($db->quoteName('#__opensim_grouprolemembership'))
		->join("INNER",$db->quoteName('#__opensim_grouprole')." ON (".$db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID')." AND ".$db->quoteName('#__opensim_grouprolemembership.RoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID').")")
		->where($db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quote($groupMembershipInfo['GroupID']))
		->where($db->quoteName('#__opensim_grouprolemembership.AgentID')." = ".$db->quote($params['AgentID']));

	$db->setQuery($query);
	$groupPowersInfo = $db->loadAssoc();

	return array_merge($groupMembershipInfo,$groupPowersInfo);
}

function getAgentRoles($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getAgentRoles");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query
		->select($db->quoteName('#__opensim_grouprole.RoleID'))
		->select($db->quoteName('#__opensim_grouprole.GroupID'))
		->select($db->quoteName('#__opensim_grouprole.Title'))
		->select($db->quoteName('#__opensim_grouprole.Name'))
		->select($db->quoteName('#__opensim_grouprole.Description'))
		->select($db->quoteName('#__opensim_grouprole.Powers'))
		->select("CASE WHEN ".$db->quoteName('#__opensim_groupmembership.SelectedRoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID')." THEN 1 ELSE 0 END AS Selected")
		->from($db->quoteName('#__opensim_groupmembership'))
		->join("INNER",$db->quoteName('#__opensim_grouprolemembership')." ON (".$db->quoteName('#__opensim_groupmembership.GroupID')." = ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." AND ".$db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quoteName('#__opensim_grouprolemembership.AgentID').")")
		->join("INNER",$db->quoteName('#__opensim_grouprole')." ON (".$db->quoteName('#__opensim_grouprolemembership.RoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID')." AND ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID').")")
		->join("LEFT",$db->quoteName('#__opensim_groupactive')." ON (".$db->quoteName('#__opensim_groupactive.AgentID')." = ".$db->quoteName('#__opensim_groupmembership.AgentID').")")
		->where($db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quote($params['AgentID']));

	if(array_key_exists("GroupID",$params) && $params['GroupID']) {
		$query->where($db->quoteName('#__opensim_groupmembership.GroupID')." = ".$db->quote($params['GroupID']));
	}

	$db->setQuery($query);
	$db->execute();
	$foundAgentRoles = $db->getNumRows();
	if($foundAgentRoles == 0) {
		return array('succeed' => 'false', 'error' => 'No roles found for agent '.$params['AgentID']);
	}

	$agentRoles = $db->loadAssocList();

	$retval = array();
	foreach($agentRoles AS $role) {
		$ID = $role['GroupID'].$role['RoleID'];
		$retval[$ID] = $role;
	}

	return $retval;
}

function getGroupRoles($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getGroupRoles");
	}
	if(is_array($error = secureRequest($params, FALSE))) {
		return $error;
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query
		->select($db->quoteName('#__opensim_grouprole.RoleID'))
		->select($db->quoteName('#__opensim_grouprole.Name'))
		->select($db->quoteName('#__opensim_grouprole.Title'))
		->select($db->quoteName('#__opensim_grouprole.Description'))
		->select($db->quoteName('#__opensim_grouprole.Powers'))
		->select("COUNT(".$db->quoteName('#__opensim_grouprolemembership.AgentID').") AS Members")
		->from($db->quoteName('#__opensim_grouprole'))
		->join("LEFT",$db->quoteName('#__opensim_grouprolemembership')." ON (".$db->quoteName('#__opensim_grouprole.GroupID')." = ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." AND ".$db->quoteName('#__opensim_grouprole.RoleID')." = ".$db->quoteName('#__opensim_grouprolemembership.RoleID').")")
		->where($db->quoteName('#__opensim_grouprole.GroupID')." = ".$db->quote($params['GroupID']))
		->group($db->quoteName('#__opensim_grouprole.GroupID'))
		->group($db->quoteName('#__opensim_grouprole.Name'))
		->group($db->quoteName('#__opensim_grouprole.Title'))
		->group($db->quoteName('#__opensim_grouprole.Description'))
		->group($db->quoteName('#__opensim_grouprole.Powers'));

	$db->setQuery($query);
	$db->execute();
	$foundgrouproles = $db->getNumRows();
	if($foundgrouproles == 0) {
		return array('succeed' => 'false', 'error' => 'No roles found for group '.$params['GroupID']);
	}
	$grouproles = $db->loadAssocList();

	$retval = array();
	foreach($grouproles AS $role) {
		$RoleID = $role['RoleID'];
		$retval[$RoleID] = $role;
	}

	return $retval;
}

function getGroupRoleMembers($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getGroupRoleMembers");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}
	if(!array_key_exists("GroupID",$params) || !$params['GroupID']) {
		return array('succeed' => 'false', 'error' => 'No GroupID specified for getGroupRoleMembers');
	}

	global $requestingAgent, $groupPowers;
	$roleMembersVisibleBit = $groupPowers['RoleMembersVisible'];
	$requestingAgentID = (array_key_exists("requestingAgentID",$params) && $params['requestingAgentID']) ? $params['requestingAgentID']:$requestingAgent;
	$canViewAllGroupRoleMembers = canAgentViewRoleMembers($requestingAgentID, $params['GroupID'],'');

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query
		->select($db->quoteName('#__opensim_grouprole.RoleID'))
		->select($db->quoteName('#__opensim_grouprolemembership.AgentID'))
		->select("(".$db->quoteName('#__opensim_grouprole.Powers')." & ".intval($roleMembersVisibleBit).") AS MemberVisible")
		->from($db->quoteName('#__opensim_grouprole'))
		->join("INNER",$db->quoteName('#__opensim_grouprolemembership')." ON (".$db->quoteName('#__opensim_grouprole.GroupID')." = ".$db->quoteName('#__opensim_grouprolemembership.GroupID')." AND ".$db->quoteName('#__opensim_grouprole.RoleID')." = ".$db->quoteName('#__opensim_grouprolemembership.RoleID').")")
		->where($db->quoteName('#__opensim_grouprole.GroupID')." = ".$db->quote($params['GroupID']));

	$db->setQuery($query);
	$db->execute();
	$foundgrouprolemembers = $db->getNumRows();
	if($foundgrouprolemembers == 0) {
		return array('succeed' => 'false', 'error' => 'No role memberships found for group '.$params['GroupID']);
	}

	$grouprolemembers = $db->loadAssocList();

	$retval = array();
	foreach($grouprolemembers AS $member) {
		if($canViewAllGroupRoleMembers || $member['MemberVisible'] || ($member['AgentID'] == $requestingAgentID)) {
			$Key = $member['AgentID'].$member['RoleID'];
			$retval[$Key ] = $member;
		}
	}

	return $retval;
}

function setAgentGroupInfo($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter setAgentGroupInfo");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}
	if(!array_key_exists("AgentID",$params) || !array_key_exists("GroupID",$params) || !$params['AgentID'] || !$params['GroupID']) {
		return array('error' => "Invalid parameters for setAgentGroupInfo");
	}

	global $requestingAgent,$uuidZero;

	if(isset($requestingAgent) && ($requestingAgent != $uuidZero) && ($requestingAgent != $params['AgentID'])) {
		return array('error' => "Agent can only change their own group info");
	}

	$db		= JFactory::getDbo();
	$updateAgent = new stdClass();

	// GroupID and AgentID are required
	$updateAgent->GroupID	= $params["GroupID"];
	$updateAgent->AgentID	= $params["AgentID"];

	if(array_key_exists("SelectedRoleID",$params))	$updateAgent->SelectedRoleID	= $params["SelectedRoleID"];
	if(array_key_exists("AcceptNotices",$params))	$updateAgent->AcceptNotices		= $params["AcceptNotices"];
	if(array_key_exists("ListInProfile",$params))	$updateAgent->ListInProfile		= $params["ListInProfile"];

	$condition	= array("GroupID",
						"AgentID");

	$result = $db->updateObject('#__opensim_groupmembership', $updateAgent, $condition);

	if($result === TRUE) {
		return array('success' => 'true');
	} else {
		return array('error' => "Error updating info for agent ".$params["AgentID"]." in group ".$params['GroupID']);
	}
}

function getGroupNotices($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getGroupNotices");
	}
	if(is_array($error = secureRequest($params, FALSE))) {
		return $error;
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query
		->select($db->quoteName('#__opensim_groupnotice.GroupID'))
		->select($db->quoteName('#__opensim_groupnotice.NoticeID'))
		->select($db->quoteName('#__opensim_groupnotice.Timestamp'))
		->select($db->quoteName('#__opensim_groupnotice.FromName'))
		->select($db->quoteName('#__opensim_groupnotice.Subject'))
		->select($db->quoteName('#__opensim_groupnotice.Message'))
		->select($db->quoteName('#__opensim_groupnotice.BinaryBucket'))
		->from($db->quoteName('#__opensim_groupnotice'))
		->where($db->quoteName('#__opensim_groupnotice.GroupID')." = ".$db->quote($params['GroupID']));

	$db->setQuery($query);
	$db->execute();
	$foundnotices = $db->getNumRows();
	if($foundnotices == 0) {
		return array('succeed' => 'false', 'error' => 'No notices found');
	}

	$groupnotices = $db->loadAssocList();

	$retval = array();
	foreach($groupnotices AS $notice) {
		$NoticeID = $notice['NoticeID'];
		$retval[$NoticeID] = $notice;
	}

	return $retval;
}

function getGroupNotice($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getGroupNotice");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}

	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query
		->select($db->quoteName('#__opensim_groupnotice.GroupID'))
		->select($db->quoteName('#__opensim_groupnotice.NoticeID'))
		->select($db->quoteName('#__opensim_groupnotice.Timestamp'))
		->select($db->quoteName('#__opensim_groupnotice.FromName'))
		->select($db->quoteName('#__opensim_groupnotice.Subject'))
		->select($db->quoteName('#__opensim_groupnotice.Message'))
		->select($db->quoteName('#__opensim_groupnotice.BinaryBucket'))
		->from($db->quoteName('#__opensim_groupnotice'))
		->where($db->quoteName('#__opensim_groupnotice.NoticeID')." = ".$db->quote($params['NoticeID']));

	$db->setQuery($query);
	$db->execute();
	$foundnotices = $db->getNumRows();
	if($foundnotices == 0) {
		return array('succeed' => 'false', 'error' => 'Group notice '.$params['NoticeID'].' not found');
	}

	$retval = $db->loadAssoc();
	$retval['Subject'] = utf8_decode($retval['Subject']);
	$retval['Message'] = utf8_decode($retval['Message']);
	return $retval;
}

function addGroupNotice($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter addGroupNotice");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	$db			= JFactory::getDbo();

	$newNotice	= new stdClass();
	$newNotice->GroupID			= $params["GroupID"];
	$newNotice->NoticeID		= $params["NoticeID"];
	$newNotice->Timestamp		= $params["Timestamp"];
	$newNotice->FromName		= $params["FromName"];
	$newNotice->Subject			= utf8_encode($params["Subject"]);
	$newNotice->Message			= utf8_encode($params["Message"]);
	$newNotice->BinaryBucket	= $params["BinaryBucket"];

	$result = $db->insertObject('#__opensim_groupnotice', $newNotice);

	if($result !== TRUE) {
		return array('error' => "Error while saving notice ".$params["NoticeID"]);
	}
	return array('success' => 'true');
}


function addAgentToGroupInvite($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter addAgentToGroupInvite");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	$db				= JFactory::getDbo();

	/// 1. Remove any existing invites for this agent to this group
	$query = $db->getQuery(true);
	$conditions = array(
		$db->quoteName('GroupID').' = '.$db->quote($params['GroupID']), 
		$db->quoteName('AgentID').' = '.$db->quote($params['AgentID'])
	);
	$query->delete($db->quoteName('#__opensim_groupinvite'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();

	/// 2. Add new invite for this agent to this group for the specifide role

	$newinvite	= new stdClass();
	$newinvite->InviteID	= $params["InviteID"];
	$newinvite->GroupID		= $params["GroupID"];
	$newinvite->RoleID		= $params["RoleID"];
	$newinvite->AgentID		= $params["AgentID"];

	$result = $db->insertObject('#__opensim_groupinvite', $newinvite);

	if($result !== TRUE) {
		return array('error' => "Error while creating group invite");
	}

	return array('success' => 'true');
}

function getAgentToGroupInvite($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter getAgentToGroupInvite");
	}
	if(is_array($error = secureRequest($params,FALSE))) {
		return $error;
	}
	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);

	$query
		->select($db->quoteName('#__opensim_groupinvite.GroupID'))
		->select($db->quoteName('#__opensim_groupinvite.RoleID'))
		->select($db->quoteName('#__opensim_groupinvite.AgentID'))
		->from($db->quoteName('#__opensim_groupinvite'))
		->where($db->quoteName('#__opensim_groupinvite.InviteID')." = ".$db->quote($params['InviteID']));

	$db->setQuery($query);
	$db->execute();
	$foundgroup = $db->getNumRows();
	if($foundgroup == 1) {
		$inviteInfo = $db->loadAssoc();
		return array('success' => 'true', 'GroupID'=>$inviteInfo['GroupID'], 'RoleID'=>$inviteInfo['RoleID'], 'AgentID'=>$inviteInfo['AgentID']);
	} else {
		return array('succeed' => 'false', 'error' => 'Invitation not found');
	}
}

function removeAgentToGroupInvite($params) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport($params,TRUE);
		debugzeile($debug,"Parameter removeAgentToGroupInvite");
	}
	if(is_array($error = secureRequest($params,TRUE))) {
		return $error;
	}

	$db				= JFactory::getDbo();

	/// 1. Remove any existing invites for this agent to this group
	$query = $db->getQuery(true);
	$conditions = array(
		$db->quoteName('InviteID').' = '.$db->quote($params['InviteID'])
	);
	$query->delete($db->quoteName('#__opensim_groupinvite'));
	$query->where($conditions);
	$db->setQuery($query);
	$db->execute();

	return array('success' => 'true');
}

function secureRequest($parameter, $write = FALSE) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport(func_get_args(),TRUE);
		debugzeile($debug,"Parameter secureRequest");
	}
	global 	$groupWriteKey,$groupReadKey,$verifiedReadKey,$verifiedWriteKey,$groupRequireAgentAuthForWrite,$requestingAgent;
	global  $overrideAgentUserService;

	// for debugging
	if(md5($_SERVER["REMOTE_ADDR"]) == "8799c345f090d4ffe06980f41b509b10") return TRUE;

	// Cache this for access by other security functions
	if(!isset($parameter['RequestingAgentID'])) return array('error' => "Invalid RequestingAgentID specified", 'params' => varexport($parameter, TRUE));
	$requestingAgent = $parameter['RequestingAgentID'];


	if(isset($groupReadKey) && ($groupReadKey != '') && (!isset($verifiedReadKey) || ($verifiedReadKey !== TRUE))) {
		if(!isset($parameter['ReadKey']) || ($parameter['ReadKey'] != $groupReadKey )) {
			return array('error' => "Invalid (or No) read key specified", 'params' => varexport($parameter, TRUE));
		} else {
			$verifiedReadKey = TRUE;
		}
	}

	if(($write == TRUE) && isset($groupWriteKey) && ($groupWriteKey != '') && (!isset($verifiedWriteKey) || ($verifiedWriteKey !== TRUE))) {
		if(!isset($parameter['WriteKey']) || ($parameter['WriteKey'] != $groupWriteKey )) {
			return array('error' => "Invalid (or No) write key specified", 'params' => varexport($parameter, TRUE));
		} else {
			$verifiedWriteKey = TRUE;
		}
	}

	if(($write == TRUE) && isset($groupRequireAgentAuthForWrite) && ($groupRequireAgentAuthForWrite == TRUE)) {
		// Note: my brain can't do boolean logic this morning, so just putting this here instead of integrating with line above.
		// If the write key has already been verified for this request, don't check it again.  This comes into play with methods that call other methods, such as CreateGroup() which calls Addrole()
		if(isset($verifiedWriteKey) && ($verifiedWriteKey !== TRUE)) {
			return TRUE;
		}

		if(!isset($parameter['RequestingAgentID']) || !isset($parameter['RequestingAgentUserService']) || !isset($parameter['RequestingSessionID'])) {
			return array('error' => "Requesting AgentID and SessionID must be specified", 'params' => varexport($parameter, TRUE));
		}

		// NOTE: an AgentID and SessionID of $uuidZero will likely be a region making a request, that is not tied to a specific agent making the request.

		$UserService = $parameter['RequestingAgentUserService'];
		if(isset($overrideAgentUserService) && ($overrideAgentUserService != "")) {
			$UserService = $overrideAgentUserService;
		}

		$client = new xmlrpc_client($UserService);
		$client->return_type = 'phpvals';

		$verifyParams = new xmlrpcval(array('avatar_uuid' => new xmlrpcval($parameter['RequestingAgentID'], 'string'),
										    'session_id'  => new xmlrpcval($parameter['RequestingSessionID'], 'string')),
									  'struct');

		$message = new xmlrpcmsg("check_auth_session", array($verifyParams));
		$resp = $client->send($message, 5);
		if ($resp->faultCode()) {
			return array('error' => "Error validating AgentID and SessionID",'xmlrpcerror'=> $resp->faultString(),'params' => varexport($parameter, TRUE));
		}

		$verifyReturn = $resp->value();

		if(!isset($verifyReturn['auth_session']) || ($verifyReturn['auth_session'] != 'TRUE')) {
			return array('error' => "UserService.check_auth_session() did not return TRUE", 'userservice' => varexport($verifyReturn, TRUE),'params' => varexport($parameter, TRUE));
		}
	}
	return TRUE;
}

function checkGroupPermission($GroupID,$Permission) {
	if(_JOPENSIMGROUPDEBUG === TRUE) {
		$debug = varexport(func_get_args(),TRUE);
		debugzeile($debug,"Parameter checkGroupPermission");
	}
	global $groupEnforceGroupPerms, $requestingAgent, $uuidZero, $groupPowers;

	if(!isset($Permission) || ($Permission == 0)) {
		if(_JOPENSIMGROUPDEBUG === TRUE) {
			debugzeile('No Permission value specified for checkGroupPermission',"Return value of checkGroupPermission");
		}
		return array('error' => 'No Permission value specified for checkGroupPermission','Permission' => $Permission);
	}

	// If it isn't set to true, then always return true, otherwise verify they have perms
	if(!isset($groupEnforceGroupPerms) || ($groupEnforceGroupPerms != TRUE)) {
		return true;
	}

	if(!isset($requestingAgent) || ($requestingAgent == $uuidZero)) {
		if(_JOPENSIMGROUPDEBUG === TRUE) {
			debugzeile('Requesting agent was either not specified or not validated',"Return value of checkGroupPermission");
		}
		return array('error' => 'Requesting agent was either not specified or not validated.','requestingAgent' => $requestingAgent);
	}

	$params = array('AgentID' => $requestingAgent, 'GroupID' => $GroupID);
	$reqAgentMembership = getAgentGroupMembership($params);

	if(isset($reqAgentMembership['error'] )) {
		if(_JOPENSIMGROUPDEBUG === TRUE) {
			debugzeile('Could not get agent membership for group ...',"Return value of checkGroupPermission");
		}
		return array('error' => 'Could not get agent membership for group','params' => varexport($params, TRUE),'nestederror' => $reqAgentMembership['error']);
	}

	$PermMask	= $reqAgentMembership['GroupPowers'];
	$PermValue	= $Permission;

	$PermMasked = $PermMask & $PermValue;

	if($PermMasked != $Permission) {
		$permNames = array_flip($groupPowers);
		if(_JOPENSIMGROUPDEBUG === TRUE) {
			debugzeile('Agent does not have group power to ...',"Return value of checkGroupPermission");
		}
		return array('error' => 'Agent does not have group power to ' . $Permission .'('.$permNames[$Permission].')','PermMasked' => $PermMasked,'params' => varexport($params, TRUE),'permBitMaskSql' => $sql,'Permission' => $Permission);
	}

	if(_JOPENSIMGROUPDEBUG === TRUE) {
		debugzeile(TRUE,"Return value of checkGroupPermission");
	}

	return TRUE;
}
?>