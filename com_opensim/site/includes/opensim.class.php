<?php
/***********************************************************************

Class for OpenSimulator Joomla-Component

started 2010-08-30 by FoTo50 (Powerdesign) foto50@jopensim.com

 * @component jOpenSim Component
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;


The methods RemoteAdmin()/SendCommand() are inspired by the RemoteAdmin Class, available at http://code.google.com/p/opensimtools/wiki/RemoteAdminPHPClass and copyright (c) 2008, The New World Grid Regents http://www.newworldgrid.com and Contributors
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

		* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
		* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
		* Neither the name of the New World Grid nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


***********************************************************************/


		// How to instantiate a RemoteAdmin object ?
		// $opensim = new opensim();
		// $opensim->RemoteAdmin("mySimulatorURL", Port, "secret password")


		// How to send commands to remoteadmin plugin ?
		// $opensim->SendCommand('admin_broadcast', array('message' => 'Message to broadcast to all regions'));
		// $opensim->SendCommand('admin_shutdown');
		// Commands like admin_shutdown don't need params, so you can left the second SendCommand function param empty ;)

		// Example for error handling
		//
		// include('classes/RemoteAdmin.php');
		// $opensim = new opensim();
		// $opensim->RemoteAdmin('localhost', 9000, 'secret');
		// $retour = $opensim->SendCommand('admin_shutdown');
		// if ($retour === FALSE)
		// {
		//      echo 'ERROR';
		// }

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

class opensim {
	public static $version	= "0.3.0.13"; // current version
	public $_settingsdata	= array();
	// basic OpenSim database connection
	public $osdbhost;
	public $osdbuser;
	public $osdbpasswd;
	public $osdbname;
	public $osdbport;
	public $_os_db; // the object containing the connection to the external DB
	// OpenSim Grid database connection
	public $osgriddbhost;
	public $osgriddbuser;
	public $osgriddbpasswd;
	public $osgriddbname;
	public $osgriddbport;
	public $_osgrid_db; // the object containing the connection to the external (grid) DB
	public $connectionerror = FALSE; // Trigger to avoid double error messages
	public $silent = FALSE; // If TRUE, no error messages at all will be enqueued
	// for the remoteAdmin connection
	private $remoteAdminHost;
	private $remoteAdminPort;
	private $remoteAdminPass;
	// define tables and fields here to avoid changing all over if something changes later
	public $userquery;
	public $defaultval = array();
	public $zerouid = "00000000-0000-0000-0000-000000000000";
	// table and fields for general info from user
	public $usertable						= "UserAccounts";
	public $usertable_field_id				= "PrincipalID";
	public $usertable_field_ScopeID			= "ScopeID";
	public $usertable_field_firstname		= "FirstName";
	public $usertable_field_lastname		= "LastName";
	public $usertable_field_email			= "Email";
	public $usertable_field_ServiceURLs		= "ServiceURLs";
	public $usertable_field_created			= "Created";
	public $usertable_field_UserLevel		= "UserLevel";
	public $usertable_field_UserFlags		= "UserFlags";
	public $usertable_field_UserTitle		= "UserTitle";
	// table and fields for info about user in the grid
	public $gridtable						= "GridUser";
	public $gridtable_field_id				= "UserID";
	public $gridtable_field_homeregion		= "HomeRegionID";
	public $gridtable_field_homeposition	= "HomePosition";
	public $gridtable_field_homelookat		= "HomeLookAt";
	public $gridtable_field_online			= "Online";
	public $gridtable_field_login			= "Login";
	public $gridtable_field_logout			= "Logout";
	// table and fields for user athentification
	public $authtable						= "auth";
	public $authtable_field_id				= "UUID";
	public $authtable_field_passwordHash	= "passwordHash";
	public $authtable_field_passwordSalt	= "passwordSalt";
	public $authtable_field_webLoginKey		= "webLoginKey";
	public $authtable_field_accountType		= "accountType";
	// table and fields for friends
	public $friendstable					= "Friends";
	public $friendstable_field_id			= "PrincipalID";
	public $friendstable_friend				= "Friend";
	public $friendstable_flags				= "Flags";
	public $friendstable_offered			= "Offered";
	// region table
	public $regiontable						= "regions";
	public $regiontable_field_id			= "uuid";
	public $regiontable_regionname			= "regionName";
	public $regiontable_serverIP			= "serverIP";
	public $regiontable_serverPort			= "serverPort";
	public $regiontable_locationX			= "locX";
	public $regiontable_locationY			= "locY";
	// presence table
	public $presencetable					= "Presence";
	public $presencetable_field_id			= "UserID";
	public $presencetable_regionid			= "RegionID";
	public $presencetable_session			= "SessionID";
	public $presencetable_ssession			= "SecureSessionID";
	// land table
	public $landtable						= "land";
	public $landtable_field_id				= "UUID";
	public $landtable_RegionUUID			= "RegionUUID";
	public $landtable_LocalLandID			= "LocalLandID";
	public $landtable_Bitmap				= "Bitmap";
	public $landtable_Name					= "Name";
	public $landtable_Description			= "Description";
	public $landtable_OwnerUUID				= "OwnerUUID";
	public $landtable_IsGroupOwned			= "IsGroupOwned";
	public $landtable_Area					= "Area";
	public $landtable_AuctionID				= "AuctionID";
	public $landtable_Category				= "Category";
	public $landtable_ClaimDate				= "ClaimDate";
	public $landtable_ClaimPrice			= "ClaimPrice";
	public $landtable_GroupUUID				= "GroupUUID";
	public $landtable_SalePrice				= "SalePrice";
	public $landtable_LandStatus			= "LandStatus";
	public $landtable_LandFlags				= "LandFlags";
	public $landtable_LandingType			= "LandingType";
	public $landtable_MediaAutoScale		= "MediaAutoScale";
	public $landtable_MediaTextureUUID		= "MediaTextureUUID";
	public $landtable_MediaURL				= "MediaURL";
	public $landtable_MusicURL				= "MusicURL";
	public $landtable_PassHours				= "PassHours";
	public $landtable_PassPrice				= "PassPrice";
	public $landtable_SnapshotUUID			= "SnapshotUUID";
	public $landtable_UserLocationX			= "UserLocationX";
	public $landtable_UserLocationY			= "UserLocationY";
	public $landtable_UserLocationZ			= "UserLocationZ";
	public $landtable_UserLookAtX			= "UserLookAtX";
	public $landtable_UserLookAtY			= "UserLookAtY";
	public $landtable_UserLookAtZ			= "UserLookAtZ";
	public $landtable_AuthbuyerID			= "AuthbuyerID";
	public $landtable_OtherCleanTime		= "OtherCleanTime";
	public $landtable_Dwell					= "Dwell";

	// some debugging in here
	public $debug							= array();

	public function __construct($osdbhost,$osdbuser,$osdbpasswd,$osdbname,$osdbport = '3306', $silent = FALSE) {
		$this->osgriddbhost		= $osdbhost;
		$this->osgriddbuser		= $osdbuser;
		$this->osgriddbpasswd	= $osdbpasswd;
		$this->osgriddbname		= $osdbname;
		$this->osgriddbport		= $osdbport;
		$this->silent		= $silent;

		$this->loaddefaultvalues();

		$this->connect2osgrid();
	}

	public function connect2osgrid() {
		// check if another port is used
		if($this->osgriddbport && $this->osgriddbport != "3306") $externalhost = $this->osgriddbhost.":".$this->osgriddbport;
		else $externalhost = $this->osgriddbhost;

		$option['driver']   = 'mysql';					// Database driver name
		$option['host']     = $externalhost;			// Database host name and port
		$option['user']     = $this->osgriddbuser;		// User for database authentication
		$option['password'] = $this->osgriddbpasswd;	// Password for database authentication
		$option['database'] = $this->osgriddbname;		// Database name
		$option['prefix']   = '';						// Database prefix (may be empty)

		try {
			$osgrid_db = JDatabaseDriver::getInstance($option);

			$this->_osgrid_db = $osgrid_db;
			$test = $osgrid_db->connect();

			return $this->_osgrid_db;
		} catch (Exception $e) {
			if($this->silent === FALSE) {
				if($this->connectionerror === FALSE) {
					$message = $e->getMessage();
					$errormsg = JText::sprintf('JOPENSIM_ERROR_DB',$message);
					JFactory::getApplication()->enqueueMessage($errormsg,"error");
					$this->connectionerror = TRUE;
				}
			}
			$this->_osgrid_db = null;
			return null;
		}
	}

	public function externalDBerror($db = 'OpenSim DB') {
		return TRUE;
	}

	public function loaddefaultvalues() {
		// default values for usertable (needed for new user)
		$this->defaultval['usertable']['ScopeID']		= $this->zerouid;
		$this->defaultval['usertable']['ServiceURLs']	= "HomeURI= GatekeeperURI= InventoryServerURI= AssetServerURI=";
		$this->defaultval['usertable']['UserLevel']	= 0;
		$this->defaultval['usertable']['UserFlags']	= 0;
		$this->defaultval['usertable']['UserTitle']	= "";
		// default values for authtable (needed for new user)
		$this->defaultval['authtable']['webLoginKey']	= $this->zerouid;
		$this->defaultval['authtable']['accountType']	= "UserAccount";
	}

	public function getOsTableField($fieldname) {
		$fieldAndTable = array();
		$fieldAndTable['firstname']['table']		= $this->usertable;
		$fieldAndTable['firstname']['field']		= $this->usertable_field_firstname;
		$fieldAndTable['firstname']['userid']		= $this->usertable_field_id;
		$fieldAndTable['lastname']['table']			= $this->usertable;
		$fieldAndTable['lastname']['field']			= $this->usertable_field_lastname;
		$fieldAndTable['lastname']['userid']		= $this->usertable_field_id;
		$fieldAndTable['email']['table']			= $this->usertable;
		$fieldAndTable['email']['field']			= $this->usertable_field_email;
		$fieldAndTable['email']['userid']			= $this->usertable_field_id;
		$fieldAndTable['passwordHash']['table']		= $this->authtable;
		$fieldAndTable['passwordHash']['field']		= $this->authtable_field_passwordHash;
		$fieldAndTable['passwordHash']['userid']	= $this->authtable_field_id;
		$fieldAndTable['passwordSalt']['table']		= $this->authtable;
		$fieldAndTable['passwordSalt']['field']		= $this->authtable_field_passwordSalt;
		$fieldAndTable['passwordSalt']['userid']	= $this->authtable_field_id;

		if(isset($fieldAndTable[$fieldname])) return $fieldAndTable[$fieldname];
		else return null;
	}

	public function getUserQueryObject($filter = null, $order = null, $sort = null, $filterterm = null) {
		if(!is_object($this->_osgrid_db)) return FALSE;
		$query = $this->_osgrid_db->getQuery(true);
		$query->from($this->usertable);
		$selectfields = array(	$this->usertable.".".$this->usertable_field_firstname,
								$this->usertable.".".$this->usertable_field_lastname,
								$this->usertable.".".$this->usertable_field_id,
								$this->usertable.".".$this->usertable_field_email,
								$this->usertable.".".$this->usertable_field_created,
								$this->gridtable.".".$this->gridtable_field_login,
								$this->gridtable.".".$this->gridtable_field_logout);
		$selectas = array(		'firstname',
								'lastname',
								'userid',
								'email',
								'created',
								'last_login',
								'last_logout');
		$query->select($this->_osgrid_db->quoteName($selectfields,$selectas));
		$query->select("IF(ISNULL(".$this->presencetable.".".$this->presencetable_field_id."),'false','true') AS online");
		$query->join('LEFT',$this->gridtable.' ON (BINARY '.$this->usertable.".".$this->usertable_field_id." = BINARY ".$this->gridtable.".".$this->gridtable_field_id.')');
		$query->join('LEFT',$this->presencetable.' ON (BINARY '.$this->usertable.".".$this->usertable_field_id." = BINARY ".$this->presencetable.".".$this->presencetable_field_id.')');

		if($filter) {
			if(is_array($filter)) {
				foreach($filter AS $field => $term) {
					if($filterterm == null) $where[] = $this->usertable.".`".$field."` LIKE '%".$term."%'";
					else $where[] = $this->usertable.".`".$field."` = '".$term."'";
				}
			} else {
				if($filterterm == null) $filtering = " LIKE '%".$filter."%'";
				else $filtering = " = '".$filter."'";
				$searchfields = $this->usersearchfields();
				$where = array();
				foreach($searchfields AS $searchfield) $where[] = $searchfield.$filtering;
			}
			$query->where(implode(" OR ",$where));
		}
		if($order) {
			if(!$sort) $sort = "ASC";
			$query->order($this->_osgrid_db->quoteName($order)." ".$sort);
		}
		return $query;
	}

	public function usersearchfields() {
		return array(	$this->usertable.".".$this->usertable_field_firstname,
						$this->usertable.".".$this->usertable_field_lastname,
						$this->usertable.".".$this->usertable_field_id,
						$this->usertable.".".$this->usertable_field_email);
	}

	public function getUserQuery($filter = null, $order = null, $sort = null, $filterterm = null) {
		if($filterterm == null) {
			$term = "LIKE";
			$filter = "%".$filter."%";
		} else {
			$term = "=";
		}
		if(!$order) $order = $this->usertable_field_created;
		if(!$sort)  $sort = "DESC";
		if(is_array($filter)) {
			$where = "WHERE (";
			$filterarray = array();
			foreach($filter AS $feld => $wert) {
				if($filterterm == null) $wert = "%".$wert."%";
				$filterarray[] = sprintf("%1\$s.%2\$s %4\$s '%3\$s'",
									 $this->usertable,
									 $feld,
									 $wert,
									 $term);
			}
			$where .= implode(" OR ",$filterarray).")";
		} elseif($filter) {
			$where = sprintf("WHERE (%1\$s.%2\$s %5\$s '%4\$s' OR %1\$s.%3\$s %5\$s '%4\$s')",
						$this->usertable,
						$this->usertable_field_firstname,
						$this->usertable_field_lastname,
						$filter,
						$term);
		} else {
			$where = "";
		}
		$query = sprintf("SELECT
							%1\$s.%2\$s AS userid,
							%1\$s.%3\$s AS firstname,
							%1\$s.%4\$s AS lastname,
							%1\$s.%5\$s AS email,
							FROM_UNIXTIME(%1\$s.%6\$s,'%%Y-%%m-%%d %%H:%%i:%%s') AS created,
							%7\$s.%10\$s AS last_login,
							%7\$s.%11\$s AS last_logout,
		#					FROM_UNIXTIME(%7\$s.%10\$s,'%%Y-%%m-%%d %%H:%%i:%%s') AS last_login,
		#					FROM_UNIXTIME(%7\$s.%11\$s,'%%Y-%%m-%%d %%H:%%i:%%s') AS last_logout,
							IF(ISNULL(%12\$s.%13\$s),'false','true') AS online
						FROM
							%1\$s
								LEFT JOIN %7\$s ON CONVERT(CAST(%1\$s.%2\$s AS BINARY) USING utf8) COLLATE utf8_general_ci = CONVERT(CAST(%7\$s.%8\$s AS BINARY) USING utf8) COLLATE utf8_general_ci
								LEFT JOIN %12\$s ON CONVERT(CAST(%1\$s.%2\$s AS BINARY) USING utf8) COLLATE utf8_general_ci = CONVERT(CAST(%12\$s.%13\$s AS BINARY) USING utf8) COLLATE utf8_general_ci
						%19\$s",
			$this->usertable,
			$this->usertable_field_id,
			$this->usertable_field_firstname,
			$this->usertable_field_lastname,
			$this->usertable_field_email,
			$this->usertable_field_created,
			$this->gridtable,
			$this->gridtable_field_id,
			$this->gridtable_field_online,
			$this->gridtable_field_login,
			$this->gridtable_field_logout,
			$this->presencetable,
			$this->presencetable_field_id,
			$this->presencetable_regionid,
			$this->presencetable_session,
			$this->presencetable_ssession,
			$order,
			$sort,
			$where);
		$this->userquery = $query;
		return $query;
	}

	public function userGridStatusQuery($userid) {
		$query = sprintf("SELECT
								IF(%1\$s.%3\$s = 'True','1','0') AS online,
								IF(%1\$s.%4\$s > '0',FROM_UNIXTIME(%1\$s.%4\$s,'%%Y-%%m-%%d %%H:%%i:%%s'),NULL) AS last_login,
								IF(%1\$s.%5\$s > '0',FROM_UNIXTIME(%1\$s.%5\$s,'%%Y-%%m-%%d %%H:%%i:%%s'),NULL) AS last_logout
							FROM
								%1\$s
							WHERE
								%1\$s.%2\$s = '%6\$s'",
			$this->gridtable,
			$this->gridtable_field_id,
			$this->gridtable_field_online,
			$this->gridtable_field_login,
			$this->gridtable_field_logout,
			$userid);
		return $query;
	}

	public function getUserPresence($userid) {
		if(empty($this->_osgrid_db)) return FALSE;
		$hgtest = explode(";",$userid); // lets see if this comes from friendlist and it is maybe a hg user
		if(count($hgtest) == 4) { // yes, this is a HG user
			$userid = $hgtest[0];
		}
		$query = sprintf("SELECT * FROM %s WHERE %s = '%s' AND %s != '%s'",
			$this->presencetable,
			$this->presencetable_field_id,
			$userid,
			$this->presencetable_regionid,
			$this->zerouid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		if($this->_osgrid_db->getNumRows() > 0) return 1;
		else return 0;
	}

	public function setUserOffline($userid) {
		if(empty($this->_osgrid_db)) return FALSE;
		$query = sprintf("DELETE FROM %s WHERE %s = '%s'",
			$this->presencetable,
			$this->presencetable_field_id,
			$userid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		$query = sprintf("UPDATE %s SET %s = 'False' WHERE %s = '%s'",
			$this->gridtable,
			$this->gridtable_field_id,
			$this->gridtable_field_online,
			$userid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
	}

	public function repairUserStatus() {
		if(empty($this->_osgrid_db)) return FALSE;
		$query = sprintf("DELETE FROM %s WHERE %s = '%s'",
			$this->presencetable,
			$this->presencetable_regionid,
			$this->zerouid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		$query = sprintf("UPDATE
								%1\$s LEFT JOIN %4\$s ON (BINARY %1\$s.%2\$s = BINARY %4\$s.%5\$s AND %4\$s.%6\$s != '%7\$s')
							SET
								%1\$s.`%3\$s` = 'False'
							WHERE
								%4\$s.%5\$s IS NULL",
			$this->gridtable,
			$this->gridtable_field_id,
			$this->gridtable_field_online,
			$this->presencetable,
			$this->presencetable_field_id,
			$this->presencetable_regionid,
			$this->zerouid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
	}

	public function getUserData($userid) {
		if(empty($this->_osgrid_db)) return FALSE;

		$userdataqueries = $this->getUserDataQuery($userid);

		$this->_osgrid_db->setQuery($userdataqueries['userdata']);
		$userdata = $this->_osgrid_db->loadAssoc();
		$this->_osgrid_db->setQuery($userdataqueries['griddata']);
		$griddata = $this->_osgrid_db->loadAssoc();
		$this->_osgrid_db->setQuery($userdataqueries['authdata']);
		$authdata = $this->_osgrid_db->loadAssoc();

		if(!$userdata) $userdata = array();
		if(!$griddata) $griddata = array();
		if(!$authdata) $authdata = array();

		$retval = array_merge($userdata,$griddata,$authdata);
		return $retval;
	}

	public function getUserLastOnline($userid) {
		if(!is_object($this->_osgrid_db)) return "unknown"; // No database, we wont find out
		$db		= $this->_osgrid_db;
		$query	= $db->getQuery(TRUE);
		$query->select($db->quoteName($this->gridtable.".".$this->gridtable_field_login));
		$query->from($db->quoteName($this->gridtable));
		$query->where($db->quoteName($this->gridtable.".".$this->gridtable_field_id)." = ".$db->quote($userid));
		
		$db->setQuery($query);
		$lastlogin = $db->loadResult();
		return $lastlogin;
	}

	public function debugzeile($zeile,$function = "") {
		if(!$function) $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##########\n";
		else $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##### ".$function." ##########\n";
		$zeile = var_export($zeile,TRUE);
		$logfile = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."interface.log";
		$handle = fopen($logfile,"a+");
		$logzeile = $zeit.$zeile."\n\n";
		fputs($handle,$logzeile);
		fclose($handle);
	}


	public function getUserName($uuid,$part = null) {
		if(empty($this->_osgrid_db)) return FALSE;
		$query = $this->getUserNameQuery($uuid);
		$this->_osgrid_db->setQuery($query);
		$username = $this->_osgrid_db->loadAssoc();
		switch($part) {
			case "firstname":
				return $username['firstname'];
			break;
			case "lastname":
				return $username['lastname'];
			break;
			case "fullname":
			case "full":
				return $username['fullname'];
			break;
			default:
				return $username;
			break;
		}
	}

	public function getUserSettings($userid) {
		$db =& JFactory::getDBO();
		$query = sprintf("SELECT im2email,visible,timezone FROM #__opensim_usersettings WHERE uuid = '%s'",$userid);
		$db->setQuery($query);
		$settings = $db->loadAssoc();
		return $settings;
	}

	public function ownerLand($userid) {
		$db =& JFactory::getDBO();
		$query = $db->getQuery(TRUE);
		$query
			->select($db->quoteName('#__opensim_search_allparcels.parcelname'))
			->select($db->quoteName('#__opensim_search_allparcels.parcelUUID'))
			->from($db->quoteName('#__opensim_search_allparcels'))
			->where($db->quoteName('#__opensim_search_allparcels.ownerUUID')." = ".$db->quote($userid));

		$db->setQuery($query);
		$ownerParcels = $db->loadAssocList();
//		$ownerParcels = array();
//		if(count($ownerLand) > 0) {
//			foreach($ownerLand AS $land) $ownerParcels[] = $land[0];
//		}
//		$ownerParcels = array_unique($ownerParcels);
		return $ownerParcels;
	}

	public function groupLand4user($userid,$groupflags) {
		$db =& JFactory::getDBO();
		$groupflags = intval($groupflags);
		$query = $db->getQuery(TRUE);
		$query
			->select("DISTINCT(".$db->quoteName('#__opensim_grouprole.GroupID').") AS GroupID")
			->from($db->quoteName('#__opensim_groupmembership'))
			->from($db->quoteName('#__opensim_grouprole'))
			->from($db->quoteName('#__opensim_grouprolemembership'))
			->where($db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quote($userid))
			->where($db->quoteName('#__opensim_groupmembership.AgentID')." = ".$db->quoteName('#__opensim_grouprolemembership.AgentID'))
			->where($db->quoteName('#__opensim_grouprolemembership.RoleID')." = ".$db->quoteName('#__opensim_grouprole.RoleID'))
			->where($db->quoteName('#__opensim_grouprolemembership.GroupID')." = ".$db->quoteName('#__opensim_grouprole.GroupID'));
		if($groupflags == 0)	$query->where($db->quoteName('#__opensim_grouprole.Powers')." > 0");
		else					$query->where($db->quoteName('#__opensim_grouprole.Powers')." & ".$groupflags);

		$db->setQuery($query);
		$landgroupsarray = $db->loadColumn();
		$landgroups = array();
		$groupland = $this->groupLand($landgroupsarray);
//		$groupland['groups'] = $landgroupsarray;
//		$groupland['debug']['query'] = $query;
//		$groupland['debug']['landgroups'] = $landgroups;
		return $groupland;
	}

	public function groupLand($groupids) {
		$db = JFactory::getDBO();
		if(!is_array($groupids)) {
			if(!$groupids) return null;
			$query = $db->getQuery(TRUE);
			$query
				->select($db->quoteName('#__opensim_search_allparcels.parcelname'))
				->select($db->quoteName('#__opensim_search_allparcels.parcelUUID'))
				->from($db->quoteName('#__opensim_search_allparcels'))
				->where($db->quoteName('#__opensim_search_allparcels.groupUUID')." = ".$db->quote($groupids));

			$db->setQuery($query);
			$groupParcels = $db->loadAssocList();
		} else {
			if(count($groupids) == 0) return null;
			$groupParcels = array();
			foreach($groupids AS $groupid) {
				$query = $db->getQuery(TRUE);
				$query
					->select($db->quoteName('#__opensim_search_allparcels.parcelname'))
					->select($db->quoteName('#__opensim_search_allparcels.parcelUUID'))
					->from($db->quoteName('#__opensim_search_allparcels'))
					->where($db->quoteName('#__opensim_search_allparcels.groupUUID')." = ".$db->quote($groupid));

				$db->setQuery($query);
				$groupParcel = $db->loadAssocList();
				$groupParcels = array_merge($groupParcels,$groupParcel);
			}
		}

		return $groupParcels;
	}

	public function getPublicRegions() {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT regionUUID FROM #__opensim_mapinfo WHERE `public` = '1'");
		$db->setQuery($query);
		$publicregions = $db->loadColumn();
		return $publicregions;
	}

	public function getRegionLand($regionUid) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(TRUE);
		$query
			->select($db->quoteName('#__opensim_search_allparcels.parcelname'))
			->select($db->quoteName('#__opensim_search_allparcels.parcelUUID'))
			->from($db->quoteName('#__opensim_search_allparcels'))
			->where($db->quoteName('#__opensim_search_allparcels.regionUUID')." = ".$db->quote($regionUid));
		$db->setQuery($query);
		$publicLand = $db->loadAssocList();
		return $publicLand;
	}

	public function getLandInfo($landID) {
		if(empty($this->_os_db)) return FALSE;
		$query = sprintf("SELECT
								%2\$s AS landid,
								%3\$s AS regionid,
								%4\$s AS name,
								%5\$s AS description,
								%6\$s AS owneruuid,
								%7\$s AS isgroupowned,
								%8\$s AS m2,
								%9\$s AS landingX,
								%10\$s AS landingY,
								%11\$s AS landingZ,
								%12\$s AS lookingX,
								%13\$s AS lookingY,
								%14\$s AS lookingZ
							FROM %1\$s
							WHERE %1\$s.%2\$s= '%16\$s'
							ORDER BY %1\$s.%4\$s",
								$this->landtable,
								$this->landtable_field_id,
								$this->landtable_RegionUUID,
								$this->landtable_Name,
								$this->landtable_Description,
								$this->landtable_OwnerUUID,
								$this->landtable_IsGroupOwned,
								$this->landtable_Area,
								$this->landtable_UserLocationX,
								$this->landtable_UserLocationY,
								$this->landtable_UserLocationZ,
								$this->landtable_UserLookAtX,
								$this->landtable_UserLookAtY,
								$this->landtable_UserLookAtZ,
								$this->landtable_GroupUUID,
								$landID);
		$this->_os_db->setQuery($query);
		$this->_os_db->query();
		if($this->_os_db->getNumRows() == 1) {
			$retval = $this->_os_db->loadAssoc();
			return $retval;
		} else {
			return FALSE;
		}
	}

	public function globalPosition2regionPosition($pos) {
		if(!is_array($pos) || !array_key_exists('posX',$pos) || !array_key_exists('posY',$pos)) return FALSE; // wrong parameter count
		if(empty($this->_osgrid_db)) return FALSE;
		$locX		= intval($pos['posX'] / 256) * 256;
		$regionX	= $pos['posX'] - $locX;
		$locY		= intval($pos['posY'] / 256) * 256;
		$regionY	= $pos['posY'] - $locY;
		$query		= sprintf("SELECT %1\$s.%2\$s AS regionname FROM %1\$s WHERE %1\$s.%3\$s = '%5\$d' AND %1\$s.%4\$s = '%6\$d'",
						$this->regiontable,
						$this->regiontable_regionname,
						$this->regiontable_locationY,
						$this->regiontable_locationX,
						$locX,
						$locY);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		if($this->_osgrid_db->getNumRows() == 1) { // The region seems to be registered, lets get the info
			$region	= $this->_osgrid_db->loadAssoc();
		} else {
			return FALSE;
		}
		$retval['regionname']	= $region['regionname'];
		$retval['posX']			= $regionX;
		$retval['posY']			= $regionY;
		if(array_key_exists('posZ',$pos)) $retval['posZ'] = $pos['posZ'];
		else $retval['posZ']	= null;
		return $retval;
	}

	public function getGlobalPosition($landUID) {
		if(empty($this->_osgrid_db)) return FALSE;
		$landinfo = $this->getLandInfo($landUID);
		$ParcelX = intval($landinfo['landingX']);
		$ParcelY = intval($landinfo['landingY']);
		$ParcelZ = intval($landinfo['landingZ']);

		if($landinfo === FALSE) return FALSE;
		$query = $this->getAllRegionsQuery($landinfo['regionid']);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		if($this->_osgrid_db->getNumRows() == 1) { // The region seems to be registered, lets get up2date data from there
			$region					= $this->_osgrid_db->loadAssoc();
			$retval['globalX']		= $region['locX'] + $ParcelX;
			$retval['globalY']		= $region['locY'] + $ParcelY;
			$retval['globalZ']		= $ParcelZ;
			$retval['regionname']	= $region['regionName'];
		} else { // Region probably currently not online, lets try to get data from the search datasnapshot
			$db =& JFactory::getDBO();
			$query = sprintf("SELECT * FROM #__opensim_search_regions WHERE regionuuid = '%s'",$landinfo['regionid']);
			$db->setQuery($query);
			$db->query();
			if($db->getNumRows() == 1) { // We found at least something in the datasnapshot
				$region					= $db->loadAssoc();
				$retval['globalX']		= $region['locX'] + $ParcelX;
				$retval['globalY']		= $region['locY'] + $ParcelY;
				$retval['globalZ']		= $ParcelZ;
				$retval['regionname']	= $region['regionname'];
			} else { // Nothing found :(
				return FALSE;
			}
		}
		return $retval;
	}

	public function countActiveUsers() {
		if(empty($this->_osgrid_db)) return FALSE;
		$query = sprintf("SELECT COUNT(DISTINCT %1\$s.%2\$s) AS anzahl FROM %1\$s WHERE %1\$s.%3\$s >= 0",
					$this->usertable,
					$this->usertable_field_id,
					$this->usertable_field_UserLevel);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		if($this->_osgrid_db->getNumRows() == 1) { // The region seems to be registered, lets get up2date data from there
			$count	= $this->_osgrid_db->loadAssoc();
			return $count['anzahl'];
		} else {
			return FALSE;
		}
	}

	public function getUserListQuery($filter = null, $condition = "like") {
		if(is_array($filter)) {
			$where = array();
			foreach($filter AS $key => $val) {
				if(isset($this->$key) && $this->$key) {
					switch($condition) {
						case "exact":
							$where[] = $this->usertable.".".$this->$key." = '".$val."'";
						break;
						default:
							$where[] = $this->usertable.".".$this->$key." LIKE '%".$val."%'";
						break;
					}
				}
			}
		}
		if(count($where) > 0) {
			$wherestring = "WHERE ".implode(" AND ",$where);
		} else {
			$wherestring = "";
		}
		$query = sprintf("SELECT
								%1\$s.*
							FROM
								%1\$s
							%2\$s",
			$this->usertable,
			$wherestring);
		return $query;
	}

	public function getUserDataQuery($userid) {
		$query['userdata'] = sprintf("SELECT
											%1\$s.%2\$s AS uuid,
											%1\$s.%3\$s AS firstname,
											%1\$s.%4\$s AS lastname,
											CONCAT_WS(' ',%1\$s.%3\$s,%1\$s.%4\$s) AS name,
											%1\$s.%5\$s AS email,
											%1\$s.%7\$s AS userlevel,
											%1\$s.%8\$s AS userflags,
											%1\$s.%9\$s AS usertitle,
											FROM_UNIXTIME(%1\$s.%6\$s,'%%Y-%%m-%%d %%H:%%i:%%s') AS born
										FROM
											%1\$s
										WHERE
											%1\$s.%2\$s = '%10\$s'",
							$this->usertable,
							$this->usertable_field_id,
							$this->usertable_field_firstname,
							$this->usertable_field_lastname,
							$this->usertable_field_email,
							$this->usertable_field_created,
							$this->usertable_field_UserLevel,
							$this->usertable_field_UserFlags,
							$this->usertable_field_UserTitle,
							$userid);
		$query['griddata'] = sprintf("SELECT
											%1\$s.%3\$s AS last_login,
											%1\$s.%4\$s AS last_logout
										FROM
											%1\$s
										WHERE
											%1\$s.%2\$s = '%5\$s'",
							$this->gridtable,
							$this->gridtable_field_id,
							$this->gridtable_field_login,
							$this->gridtable_field_logout,
							$userid);
		$query['authdata'] = sprintf("SELECT
											%1\$s.%3\$s AS passwordSalt
										FROM
											%1\$s
										WHERE
											%1\$s.%2\$s = '%4\$s'",
							$this->authtable,
							$this->authtable_field_id,
							$this->authtable_field_passwordSalt,
							$userid);

		$query['friends'] = sprintf("SELECT
											%1\$s.%3\$s AS friendid
										FROM
											%1\$s
										WHERE
											%1\$s.%2\$s = '%5\$s'
										AND
											%1\$s.%4\$s & 1",
							$this->friendstable,
							$this->friendstable_field_id,
							$this->friendstable_friend,
							$this->friendstable_flags,
							$userid);
		return $query;
	}

	public function getUserNameQuery($uid) {
		$query = sprintf("SELECT
								%1\$s.%3\$s AS firstname,
								%1\$s.%4\$s AS lastname,
								CONCAT_WS(' ',%1\$s.%3\$s,%1\$s.%4\$s) AS fullname
							FROM
								%1\$s
							WHERE
								%1\$s.%2\$s = '%5\$s'",
			$this->usertable,
			$this->usertable_field_id,
			$this->usertable_field_firstname,
			$this->usertable_field_lastname,
			$uid);
		return $query;
	}

	public function getCheckQuery($firstname,$lastname,$uid = null) {
		$query = sprintf("SELECT * FROM
							%1\$s
						WHERE
							%1\$s.%2\$s = '%4\$s'
						AND
							%1\$s.%3\$s = '%5\$s'",
			$this->usertable,
			$this->usertable_field_firstname,
			$this->usertable_field_lastname,
			$firstname,
			$lastname);
		if($uid) $query .= sprintf("\nAND\n\t%1\$s.%2\$s != '%3\$s'",
			$this->usertable,
			$this->usertable_field_id,
			$uid);
		return $query;
	}

	public function getUpdateUserQuery($data) {
		if(!is_array($data) || !array_key_exists("PrincipalID",$data)) return FALSE; // No updates can be made without this
		$setarray = array();
		if(array_key_exists("ScopeID",$data))		$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_ScopeID,		$data['ScopeID']);
		if(array_key_exists("FirstName",$data))		$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_firstname,	$data['FirstName']);
		if(array_key_exists("LastName",$data))		$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_lastname,		$data['LastName']);
		if(array_key_exists("Email",$data))			$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_email,		$data['Email']);
		if(array_key_exists("ServiceURLs",$data))	$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_ServiceURLs,	$data['ServiceURLs']);
		if(array_key_exists("Created",$data))		$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_created,		$data['Created']);
		if(array_key_exists("UserLevel",$data))		$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_UserLevel,	$data['UserLevel']);
		if(array_key_exists("UserFlags",$data))		$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_UserFlags,	$data['UserFlags']);
		if(array_key_exists("UserTitle",$data))		$setarray[] = sprintf(" %s.%s = '%s'",$this->usertable,$this->usertable_field_UserTitle,	$data['UserTitle']);
		if(count($setarray) == 0) {
			return FALSE;
		} else {
			$set = implode(",\n\t",$setarray);
			$query['user'] = sprintf("UPDATE %1\$s SET \n\t%3\$s\nWHERE\n\t%1\$s.%2\$s = '%4\$s'",
										$this->usertable,
										$this->usertable_field_id,
										$set,
										$data['PrincipalID']);
		}
		if(array_key_exists("password",$data)) {
			$passwordSalt	= md5(time());
			$passwordHash	= md5(md5($data['password']).":".$passwordSalt);
			$query['auth']	= sprintf("UPDATE %1\$s SET %1\$s.%3\$s = '%6\$s', %1\$s.%4\$s = '%7\$s' WHERE %1\$s.%2\$s = '%5\$s'",
				$this->authtable,
				$this->authtable_field_id,
				$this->authtable_field_passwordHash,
				$this->authtable_field_passwordSalt,
				$data['PrincipalID'],
				$passwordHash,
				$passwordSalt);
		}
		return $query;
	}

	public function getInsertUserQuery($data) {
		if(!$data['uuid']) $data['uuid'] = $this->make_random_guid();
		$created = time();
		$query['user'] = sprintf("INSERT INTO %1\$s (%2\$s,%3\$s,%4\$s,%5\$s,%6\$s,%7\$s,%8\$s,%9\$s,%10\$s,%11\$s) VALUES ('%12\$s','%13\$s','%14\$s','%15\$s','%16\$s','%17\$s','%18\$s','%19\$s','%20\$s','%21\$s')",
				$this->usertable,
				$this->usertable_field_id,
				$this->usertable_field_ScopeID,
				$this->usertable_field_firstname,
				$this->usertable_field_lastname,
				$this->usertable_field_email,
				$this->usertable_field_ServiceURLs,
				$this->usertable_field_created,
				$this->usertable_field_UserLevel,
				$this->usertable_field_UserFlags,
				$this->usertable_field_UserTitle,
				$data['uuid'],
				$this->defaultval['usertable']['ScopeID'],
				mysql_real_escape_string($data['firstname']),
				mysql_real_escape_string($data['lastname']),
				$data['email'],
				$this->defaultval['usertable']['ServiceURLs'],
				$created,
				$this->defaultval['usertable']['UserLevel'],
				$this->defaultval['usertable']['UserFlags'],
				$this->defaultval['usertable']['UserTitle']);

		$query['auth'] = sprintf("INSERT INTO %1\$s (%2\$s,%3\$s,%4\$s,%5\$s,%6\$s) VALUES ('%7\$s','%8\$s','%9\$s','%10\$s','%11\$s')",
				$this->authtable,
				$this->authtable_field_id,
				$this->authtable_field_passwordHash,
				$this->authtable_field_passwordSalt,
				$this->authtable_field_webLoginKey,
				$this->authtable_field_accountType,
				$data['uuid'],
				$data['passwordHash'],
				$data['passwordSalt'],
				$this->defaultval['authtable']['webLoginKey'],
				$this->defaultval['authtable']['accountType']);

		$query['grid'] = sprintf("INSERT INTO %1\$s (%2\$s,%3\$s,%4\$s,%5\$s,%6\$s) VALUES ('%7\$s','%8\$s','%9\$s','%10\$s','%11\$s')",
				$this->gridtable,
				$this->gridtable_field_id,
				$this->gridtable_field_homeregion,
				$this->gridtable_field_homeposition,
				$this->gridtable_field_homelookat,
				$this->gridtable_field_online,
				$data['uuid'],
				$data['homeregion'],
				$data['homeposition'],
				$data['homelookat'],
				"False");

		return $query;
	}

	public function  getinventoryqueries($uuid) {
		$my_inventory = $this->make_random_guid();
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('My Inventory','8','1','$my_inventory','$uuid','".$this->zerouid."')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('Textures','0','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('Sounds','1','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('Calling Cards','2','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('Landmarks','3','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('Clothing','5','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('Objects','6','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							"VALUES ('Notecards','7','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							  "VALUES ('Scripts','10','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							  "VALUES ('Body Parts','13','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							  "VALUES ('Trash','14','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							  "VALUES ('Photo Album','15','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							  "VALUES ('Lost And Found','16','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							  "VALUES ('Animations','20','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		$query[] = 'INSERT INTO inventoryfolders (folderName,type,version,folderID,agentID,parentFolderID) '.
							  "VALUES ('Gestures','21','1','".$this->make_random_guid()."','$uuid','$my_inventory')";
		return $query;
	}

	public function getdeletequeries($userid) {
		$query['_osgrid_db'][]	= sprintf("DELETE FROM inventoryfolders	WHERE agentID = '%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM auth				WHERE UUID = '%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM GridUser			WHERE UserID = '%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM Avatars			WHERE PrincipalID='%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM Friends			WHERE PrincipalID='%1\$s' OR Friend='%1\$s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM tokens			WHERE UUID='%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM Presence			WHERE UserID='%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM inventoryfolders	WHERE agentID='%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM inventoryitems	WHERE avatarID='%s'",$userid);
		$query['_osgrid_db'][]	= sprintf("DELETE FROM UserAccounts		WHERE PrincipalID = '%s'",$userid);

		// Todo: delete queries in profile, classified, etc
		return $query;
	}

	public function getOnlineStatusQuery($userid) {
		$query = sprintf("SELECT * FROM %s WHERE %s = '%s'",
							$this->presencetable,
							$this->presencetable_field_id,
							$userid);
		return $query;
	}

	public function regionExistsQuery($regionID) {
		$query = sprintf("SELECT %1\$s.%2\$s FROM %1\$s WHERE %1\$s.%2\$s = '%3\$s'",
						$this->regiontable,
						$this->regiontable_field_id,
						$regionID);
		return $query;
	}

	public function  make_random_guid() {
		$uuid = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
	            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
	            mt_rand( 0, 0x0fff ) | 0x4000,
	            mt_rand( 0, 0x3fff ) | 0x8000,   
	            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		return $uuid;
	}

	public function getRegionData($regionUUID) {
		if(empty($this->_osgrid_db)) return FALSE;
		$regionquery = $this->getAllRegionsQuery($regionUUID);
		$this->_osgrid_db->setQuery($regionquery);
		$this->_osgrid_db->query();
		if($this->_osgrid_db->getNumRows() == 1) {
			$regiondata = $this->_osgrid_db->loadAssoc();
			return $regiondata;
		} else { // something went wrong :(
			return FALSE;
		}
	}

	public function getAllRegionsQuery($filter = null, $sort = null, $order = "ASC") {
		if(!$sort) {
			$orderby = sprintf("ORDER BY %1\$s.%2\$s %4\$s, %1\$s.%3\$s %4\$s",
						$this->regiontable,
						$this->regiontable_locationY,
						$this->regiontable_locationX,
						$order);
		} else {
			$orderby = sprintf("ORDER BY %1\$s %2\$s",
						$sort,
						$order);
		}
		if($filter) {
			$where = sprintf("WHERE (%1\$s.%2\$s LIKE '%%%4\$s%%' OR %1\$s.%3\$s LIKE '%%%4\$s%%')",
						$this->regiontable,
						$this->regiontable_field_id,
						$this->regiontable_regionname,
						$filter);
		} else {
			$where = "";
		}
		$query = sprintf("SELECT %1\$s.*, %1\$s.%2\$s / 256 AS posY, %1\$s.%3\$s / 256 AS posX FROM %1\$s %4\$s %5\$s",
						$this->regiontable,
						$this->regiontable_locationY,
						$this->regiontable_locationX,
						$where,
						$orderby);
		return $query;
	}

	public function getRegionRangeQuery() {
		$query = sprintf("SELECT
							MAX(%1\$s.%2\$s) AS maxX,
							MAX(%1\$s.%3\$s) AS maxY,
							MIN(%1\$s.%2\$s) AS minX,
							MIN(%1\$s.%3\$s) AS minY
						FROM
							regions",
				$this->regiontable,
				$this->regiontable_locationX,
				$this->regiontable_locationY);
		return $query;
	}

	public function countRegions() {
		if(empty($this->_osgrid_db)) return FALSE;
		$query = $this->getAllRegionsQuery();
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		return $this->_osgrid_db->getNumRows();
	}

	public function countPresence() {
		if(empty($this->_osgrid_db)) return FALSE;
		$query = sprintf("SELECT * FROM %s WHERE %s != '%s'",$this->presencetable,$this->presencetable_regionid,$this->zerouid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		return $this->_osgrid_db->getNumRows();
	}

	public function RemoteAdmin($sURL, $sPort, $pass) {
		$this->simulatorURL		= $sURL;	// String
		$this->simulatorPort	= $sPort;	// Integer
		$this->password			= $pass;
	}

	public function SendCommand($command, $params) {
		$paramsNames	= array_keys($params);
		$paramsValues	= array_values($params);

		// Building the XML data to pass to RemoteAdmin through XML-RPC ;)
		$xml = '<methodCall>
					<methodName>' . htmlspecialchars($command) . '</methodName>
					<params>
						<param>
							<value>
								<struct>
									<member>
										<name>password</name>
										<value><string>' . htmlspecialchars($this->password) . '</string></value>
									</member>';
		if (count($params) != 0) {
			for ($p = 0; $p < count($params); $p++) {
				$xml .= '<member><name>' . htmlspecialchars($paramsNames[$p]) . '</name>';
				$xml .= '<value>' . htmlspecialchars($paramsValues[$p]) . '</value></member>';
			}
		}
		$xml .= '				</struct>
							</value>
						</param>
					</params>
				</methodCall>';

		// Now building headers and sending the data ;)
		$host = $this->simulatorURL;
		$port = $this->simulatorPort;
		$timeout = 5; // Timeout in seconds

		$fp = fsockopen($host, $port, $errno, $errstr, $timeout);
		if (!$fp) {
			return FALSE; // If contacting host timeouts or impossible to create the socket, the method returns FALSE
		} else {
			fputs($fp, "POST / HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");
			fputs($fp, "Content-type: text/xml\r\n");
			fputs($fp, "Content-length: ". strlen($xml) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $xml);
			$res = "";
			while(!feof($fp)) {
				$res .= fgets($fp, 128);
			}
			fclose($fp);
			$response = substr($res, strpos($res, "\r\n\r\n"));

			// Now parsing the XML response from RemoteAdmin ;)
			$retval = array();

			$suchmuster = "/<name>([^<]*)<[^>]*>[^value>]*value>[^<]*<([^>]*)>([^<]*)/";
			preg_match_all($suchmuster,$response,$treffer,PREG_SET_ORDER);
			/*return $treffer;*/
			
			if(is_array($treffer) && count($treffer) > 0) {
				foreach($treffer AS $key => $val) {
					$retval[$val[1]] = $val[3];
				}
			}
			return $retval;
		}

	}

	public function parseOSxml($xmlstring,$typ = 'messaging') {
		$ergebnis = array();
		switch($typ) {
			case "messaging":
				$suchmuster = "/<([a-zA-Z]+)>([^<]*)<(\/[^>]*)>/"; // sollte xml requests von opensim an com_opensim (interface) zerlegen können
				preg_match_all($suchmuster,$xmlstring,$treffer,PREG_SET_ORDER);
				if(is_array($treffer) && count($treffer) > 0) {
					foreach($treffer AS $wert) $ergebnis[$wert[1]] = $wert[2];
				}
				$ergebnis['original'] = $xmlstring;
				return $ergebnis;
			break;
			case "method": // gibt den Namen der Methode retour
				$suchmuster = '/\<methodName\>([^\<]*)/';
				preg_match_all($suchmuster,$xmlstring,$treffer);
				if(count($treffer) == 2) {
					if(isset($treffer[1][0])) return $treffer[1][0];
					else return FALSE;
				}
				else return FALSE;
			break;
			default:
				$suchmuster = '/\<'.$typ.'\>([^\<]*)/';
				preg_match_all($suchmuster,$xmlstring,$treffer);
				if(count($treffer) == 2) return $treffer;
				else return $typ;
			break;
		}
	}

	public function osversion() {
		return self::$version;
	}

	public function checkversion() {
		$versionfile	= "http://www.jopensim.com/opensim/version3.txt";
		$recentversion	= @file_get_contents($versionfile);
		if(!$recentversion) return JText::_('UPDATEINFONOTAVAILABLE');
		$versioncheck	= version_compare(self::$version,trim($recentversion));
		if($versioncheck < 0) {
			return JText::sprintf('UPDATEVERSION',$recentversion);
		} elseif($versioncheck > 0) {
			return "<i class='icon-warning-circle' style='color:orange;'></i>PreRelease?";
		} else {
			return JText::_('UP2DATE');
		}
//		elseif(trim($recentversion) == self::$version) return JText::_('UP2DATE');
//		else return JText::sprintf('UPDATEVERSION',$recentversion);
	}

	public function checkRegionIP($ip) {

		if(md5($ip) == "8799c345f090d4ffe06980f41b509b10") return TRUE; // for debugging
		$suchmuster = '/^([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$/'; // IP adresse?
		$query = sprintf("SELECT DISTINCT(%s) AS hosts FROM %s",
					$this->regiontable_serverIP,
					$this->regiontable);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->query();
		$hosts = $this->_osgrid_db->loadAssocList();
		$regionhosts = array();
		foreach($hosts AS $host) {
			preg_match($suchmuster,$host['hosts'],$treffer);
			if(count($treffer) > 0) $regionhosts[] = $host['hosts'];
			else $regionhosts[] = gethostbyname($host['hosts']);
		}
		if(in_array($ip,$regionhosts)) return TRUE;
		else return FALSE;
	}

	public function moneyEnabled() {
		return TRUE;
	}

	public function getversion() {
		return self::$version;
	}

	public function __destruct() {
	}
}

if(!function_exists("debugprint")) {
	function debugprint($variable,$desc="",$exit=0) { // I hope I will not forget to remove this function once I dont need it anymore ;) <-- looks like I will ALWAYS need it ;)
		echo "<div align='center'><div align='left'><pre class='debug'>\n\n\n";
		if ($desc) echo "########## $desc ##########\n\n";
		print_r($variable);
		echo "</pre></div></div>\n";
		if ($exit == 1) exit;
	}
}
?>