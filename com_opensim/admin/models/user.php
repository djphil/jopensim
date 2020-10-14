<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class OpenSimModelUser extends OpenSimModelOpenSim {
	public $_settingsData;
	public $filename		= "user.php";
	public $view			= "user";
	public $userquery;
	/**
	 * Items total
	 * @var integer
	 */
	public $_total = null;

	/**
	 * Pagination object
	 * @var object
	 */
	public $_pagination = null;

	/**
	 * external DB object
	 * @var object
	 */
	public $_osgrid_db = null;

	public function __construct($config = array()) {
		parent::__construct($config);

		$input = JFactory::getApplication()->input;

		$app		= JFactory::getApplication();
		$limitstart	= $app->getUserStateFromRequest( 'users_limitstart', 'limitstart', 0, 'int' );
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$orderby	= $app->getUserStateFromRequest( 'users_filter_order', 'filter_order', 'UserAccounts.Created', 'STR' );
		$orderdir	= $app->getUserStateFromRequest( 'users_filter_order_Dir', 'filter_order_Dir', 'desc', 'STR' );
		$search		= $app->getUserStateFromRequest( 'users_filter_search', 'filter_search', '', 'STR' );

		$this->setState('limit', $input->get('limit',$limit,'INT'));
		$this->setState('users_limitstart', $input->get('limitstart',$limitstart,'INT'));
		$this->setState('users_filter_order', $input->get('filter_order',$orderby,'STR'));
		$this->setState('users_filter_order_Dir', $input->get('filter_order_Dir',$orderdir,'STR'));
		$this->setState('users_filter_search', $input->get('filter_search',$search,'STR'));

		$this->getSettingsData();

		$this->_osgrid_db = $this->getOpenSimGridDB();

		if($this->_osgrid_db) {
			$opensim = $this->opensim;
			$this->userquery = $opensim->getUserQuery($search);

		}
	}


	public function getData() {
		return $this->getUserDataList();
	}

	public function getTotal() {
		if(!$this->_osgrid_db) return FALSE;
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$this->_osgrid_db->setQuery($this->userquery);
			try {
				$this->_osgrid_db->execute();
				$this->_total = $this->_osgrid_db->getNumRows();
			} catch(Exception $e) {
				$errormsg = $e->getMessage();
				JFactory::getApplication()->enqueueMessage($errormsg,"error");
				$this->_total = 0;
			}
		}
		return $this->_total;
	}

	public function checkUserExists($firstname,$lastname,$uid = null) {
		if(empty($this->_osgrid_db)) $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$checkquery = $opensim->getCheckQuery($firstname,$lastname,$uid);
		$this->_osgrid_db->setQuery($checkquery);
		$this->_osgrid_db->execute();
		$existing = $this->_osgrid_db->getNumRows();
		if($existing > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getUUID() {
		$db		= JFactory::getDBO();
		$query	= "SELECT UUID()";
		$db->setQuery($query);
		$uuid = $db->loadResult();
		return $uuid;
	}

	public function getUserRelation($userid) {
		$db		= JFactory::getDBO();
		$query	= sprintf("SELECT joomlaID FROM #__opensim_userrelation WHERE opensimID = '%s'",$userid);
		$db->setQuery($query);
		$relations = $db->loadRow();
		return $relations;
	}

	public function getAllUserRelation() {
		$db		= JFactory::getDBO();
		$query	= "SELECT joomlaID FROM #__opensim_userrelation";
		$db->setQuery($query);
		$relations = $db->loadRowList();
		foreach($relations AS $relation) { // I want them in a simple array
			$retval[] = $relation[0];
		}
		return $retval;
	}

	public function getJoomlaRelationList($userid) {
		$db = JFactory::getDBO();
		$query = sprintf("SELECT 
							#__opensim_userrelation.opensimID,
							#__users.*
						FROM
							#__users LEFT JOIN #__opensim_userrelation ON #__users.id = #__opensim_userrelation.joomlaID
						WHERE
							#__opensim_userrelation.opensimID IS NULL
						OR
							#__opensim_userrelation.opensimID = '%s'",$userid);
		$db->setQuery($query);
		$joomlausers = $db->loadAssocList();
		return $joomlausers;
	}

	public function insertuser($newuser) {
		if(empty($this->_osgrid_db)) $this->getOpenSimDB();
		if(!$this->_osgrid_db) return FALSE;
		$newuser['homeregion'] = $this->_settingsData['defaulthome'];
		$newuser['homeposition'] = sprintf("<"."%f,%f,%f".">",$this->_settingsData['mapstartX'],$this->_settingsData['mapstartY'],$this->_settingsData['mapstartZ']);
		$newuser['homelookat'] = "<0,0,0>"; // have to figure out once how to set that exact
		$opensim = $this->opensim;
		$newuser['passwordSalt'] = md5($newuser['password']);
		$newuser['passwordHash'] = md5(md5($newuser['password']).":".$newuser['passwordSalt']);
		$insertquery = $opensim->getInsertUserQuery($newuser);
		$debug[] = $insertquery;
		$this->_osgrid_db->setQuery($insertquery['user']);
		$retval = $this->_osgrid_db->execute();
		$this->_osgrid_db->setQuery($insertquery['auth']);
		$retval = $this->_osgrid_db->execute();
		if($this->regionExists($newuser['homeregion'])) { // only add home region if set already
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
		return $retval;
	}

	public function updateuser($data) {
		if(empty($this->_osgrid_db)) $this->getOpenSimDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$updatequery	= $opensim->getUpdateUserQuery($data);
		if(is_array($updatequery) && count($updatequery) > 0) {
			foreach($updatequery AS $query) {
				$this->_osgrid_db->setQuery($query);
				$this->_osgrid_db->execute();
			}
			$this->repopulateavatars();
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function repopulateavatars() {
		$userlist = array();
		if(empty($this->_osgrid_db)) $this->getOpenSimDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$filter['usertable_field_UserLevel'] = -3;
		$query = $opensim->getUserListQuery($filter);
		$this->_osgrid_db->setQuery($query);
		$userlist = $this->_osgrid_db->loadAssocList();

		$db = JFactory::getDBO();
		$query = "TRUNCATE TABLE #__opensim_useravatars;";
		$db->setQuery($query);
		$db->execute();

		if(is_array($userlist) && count($userlist) > 0) {
			foreach($userlist AS $useravatar) {
				if($useravatar['UserTitle']) $avatarname = $useravatar['UserTitle'];
				else $avatarname = $useravatar['FirstName']." ".$useravatar['LastName'];
				$query = sprintf("INSERT INTO #__opensim_useravatars (userid,avatarname) VALUES ('%s','%s')",
					$useravatar['PrincipalID'],
					$avatarname);
				$db->setQuery($query);
				$db->execute();
			}
		}
		return $userlist;
	}

	public function checkAvatarProfileImage($userid) {
		if($this->_settingsData['getTextureEnabled'] == 1) {
			$db = JFactory::getDBO();
			$query	= sprintf("SELECT #__opensim_userprofile.image FROM #__opensim_userprofile WHERE #__opensim_userprofile.avatar_id = '%s'",$userid);
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			if($num_rows == 1) {
				$avatarimage = $db->loadResult();
				if($avatarimage != $this->opensim->zerouid) {
					$destimage	= JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'avatars'.DIRECTORY_SEPARATOR.$userid.".".$this->_settingsData['getTextureFormat'];
					if(substr($this->_settingsData['opensim_host'],0,4) != "http") $opensimhost = "http://".$this->_settingsData['opensim_host'];
					else $opensimhost		= $this->_settingsData['opensim_host'];
					$robust_port			= $this->_settingsData['robust_port'];
					$profileimage2nd= @file_get_contents($opensimhost.":".$robust_port."/CAPS/GetTexture/?texture_id=".$avatarimage."&format=".$this->_settingsData['getTextureFormat']);
					if($profileimage2nd) {
						file_put_contents($destimage,$profileimage2nd);
					}
				}
			}
		}
	}

	public function getonlinestatus($userid) {
		if(empty($this->_osgrid_db)) $this->getOpenSimDB();
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$query = $opensim->getOnlineStatusQuery($userid);
		$this->_osgrid_db->setQuery($query);
		$this->_osgrid_db->execute();
		$num_rows = $this->_osgrid_db->getNumRows();
		if($num_rows == 1) return TRUE;
		else return FALSE;
	}

	public function deleteUser($userid) {
		if($this->getonlinestatus($userid) === TRUE) return FALSE; // only delete user that arent currently online
		if(!$this->_osgrid_db) return FALSE;
		$opensim = $this->opensim;
		$deletequeries = $opensim->getdeletequeries($userid);
		if(is_array($deletequeries)) {
			$db = JFactory::getDBO();
			$query = sprintf("DELETE FROM #__opensim_userrelation WHERE opensimID = '%s'",$userid);
			$db->setQuery($query);
			$db->execute();
			$query = sprintf("DELETE FROM #__opensim_offlinemessages WHERE fromAgentID = '%1\$s' OR toAgentID = '%1\$s'",$userid);
			$db->setQuery($query);
			$db->execute();
			// Todo: delete queries in profile (partnering?), classified, grouptables, usersettings, etc...
			foreach($deletequeries AS $db => $dbquery) {
				if(is_object($this->$db)) {
					foreach($dbquery AS $query) {
						$this->$db->setQuery($query);
						$this->$db->execute();
					}
				}
			}
			return TRUE;
		}
	}

	public function userrelation($opensimid,$joomlaid,$method) {
		if($joomlaid == 0) return FALSE; // no Joomla User? no change!
		$db	= JFactory::getDBO();
		switch($method) {
			case "insert":
				$query = sprintf("INSERT INTO #__opensim_userrelation (joomlaID,opensimID) VALUES ('%d','%s')",$joomlaid,$opensimid);
			break;
			case "update":
				$query = sprintf("UPDATE #__opensim_userrelation SET joomlaID = '%d' WHERE opensimID = '%s'",$joomlaid,$opensimid);
			break;
			case "delete":
				$query = sprintf("DELETE FROM #__opensim_userrelation WHERE opensimID = '%s'",$opensimid);
			break;
		}
		$db->setQuery($query);
		$db->execute();
		return TRUE;
	}

	public function regionExists($regionID) {
		$opensim = $this->opensim;
		$query = $opensim->regionExistsQuery($regionID);
		$this->_osgrid_db->setQuery($query);
		$existing = $this->_osgrid_db->loadResult();
		if($existing == $regionID) return TRUE;
		else return FALSE;
	}

	public function setUserOffline($userid) {
		$opensim = $this->opensim;
		$opensim->setUserOffline($userid);
	}

	public function repairUserStatus() {
		$opensim = $this->opensim;
		$opensim->repairUserStatus();
	}

	public function getUserLevels() {
		$db = JFactory::getDBO();
		if($this->moneyEnabled === TRUE) $query = "SELECT #__opensim_userlevel.* FROM #__opensim_userlevel ORDER BY #__opensim_userlevel.userlevel ASC";
		else  $query = "SELECT #__opensim_userlevel.* FROM #__opensim_userlevel WHERE #__opensim_userlevel.userlevel != -2 ORDER BY #__opensim_userlevel.userlevel ASC";
		$db->setQuery($query);
		$userlevels = $db->loadAssocList();
		return $userlevels;
	}

	public function getUserParams() {
		$params	= $this->params;
		$retval['jopensim_usersetting_flag3']	= $params->get('jopensim_usersetting_flag3');
		$retval['jopensim_usersetting_flag4']	= $params->get('jopensim_usersetting_flag4');
		$retval['jopensim_usersetting_flag5']	= $params->get('jopensim_usersetting_flag5');
		$retval['jopensim_usersetting_flag9']	= $params->get('jopensim_usersetting_flag9');
		$retval['jopensim_usersetting_flag10']	= $params->get('jopensim_usersetting_flag10');
		$retval['jopensim_usersetting_flag11']	= $params->get('jopensim_usersetting_flag11');
		$retval['jopensim_usersetting_flag12']	= $params->get('jopensim_usersetting_flag12');
		$retval['jopensim_defaultuserlevel']	= intval($params->get('jopensim_defaultuserlevel'));
		$retval['jopensim_usersetting_title']	= $params->get('jopensim_usersetting_title');
		$retval['userflags'] = 	$retval['jopensim_usersetting_flag3'] +
								$retval['jopensim_usersetting_flag4'] +
								$retval['jopensim_usersetting_flag5'] +
								$retval['jopensim_usersetting_flag9'] +
								$retval['jopensim_usersetting_flag10'] +
								$retval['jopensim_usersetting_flag11'] +
								$retval['jopensim_usersetting_flag12'];
		return $retval;
	}


	public function getPagination() {
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotal(), $this->getState('users_limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}

	public function getUserState($state) {
		return $this->getState($state);
	}

	public function populateState() {
		$filter_order = JFactory::getApplication()->input->get('filter_order');
		$filter_order_Dir = JFactory::getApplication()->input->get('filter_order_Dir');
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		parent::populateState();
	}

}
?>
