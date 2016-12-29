<?php
/**
 * @module      OpenSim Friends (mod_opensim_friends)
 * @copyright   Copyright (C) 2015 FoTo50 http://www.jopensim.com
 * @license     GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class ModOpenSimFriendsHelper {
	public $friendlist = array();
	public $os_uid;			// if present, here is the OS UID
	public $os_settings;	// general Settings from the Component
	public $_osgrid_db;		// OpenSim Database Object
	public $currentuser;	// the current logged in user
	public $opensim;
	public $onlinecolor		= "#00FF00"; // Default online color
	public $offlinecolor	= "#FF0000"; // Default offline color
	public $namelength		= 0;
	public $opensimmodel;

	public function __construct($namelength = 0) {
		$this->namelength = $namelength;
		$this->getSettings();						// fetch the general Settings from the component at the beginning
		$this->initOpenSim();						// generate the opensim object
		$this->_osgrid_db	= $this->getOSdb();		// load the external DB in an object
		$this->currentuser	=& JFactory::getUser();	// who is here now?
		$this->getUIDfromUser();					// and is he/she registered in OpenSim?
		$this->initFriendsList();					// Fill up the FriendList (if applicable)
	}

	public function getSettings() {
		jimport('joomla.application.component.model');
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_opensim/models');
		$this->opensimmodel	= JModelLegacy::getInstance('opensim','OpenSimModel');
		$this->os_settings = $this->opensimmodel->getSettingsData();
	}

	public function initOpenSim() {
		$this->opensim	= $this->opensimmodel->opensim;
	}

	public function getOSdb() {
		return $this->opensim->_osgrid_db;
	}

	public function getUIDfromUser() {
		$db =& JFactory::getDBO();
		$query = sprintf("SELECT opensimID FROM #__opensim_userrelation WHERE joomlaID = '%d'",$this->currentuser->id);
		$db->setQuery($query);
		$uuid = $db->loadResult();
		if(!$uuid) $this->os_uid = FALSE;
		else $this->os_uid = $uuid;
	}

	public function initFriendsList() {
		if(!$this->_osgrid_db) return $this->externalDBerror();

		$query = $this->opensim->getUserDataQuery($this->os_uid);
		$this->_osgrid_db->setQuery($query['friends']);
		$this->friendlist = $this->_osgrid_db->loadAssocList();
		if(is_array($this->friendlist)) {
			$friends[0] = array();
			$friends[1] = array();
			foreach($this->friendlist AS $key => $friend) {
				$onlinestatus = $this->opensim->getUserPresence($friend['friendid']);
				$friendnamequery = $this->opensim->getUserNameQuery($friend['friendid']);
				$this->_osgrid_db->setQuery($friendnamequery);
				$friendname = $this->_osgrid_db->loadAssoc();
				if(!is_array($friendname) || count($friendname) == 0) {
					// lets see if this is a HG visitor
					$hgtest = explode(";",$friend['friendid']);
					if(count($hgtest) == 4) { // yes, this is a HG user
						$friendname['firstname'] = $hgtest[2];
						$hghost = parse_url($hgtest[1]);
						$friendname['lastname'] = (array_key_exists("host",$hghost)) ? "@".$hghost['host']:"@".$hgtest[1];
					} else {
						// this UUID seems not to exists anymore, lets ignore it
						continue;
					}
				}
				$count = count($friends[$onlinestatus]);
				$friends[$onlinestatus][$count]['name']	= $this->prepareName($friendname['firstname']." ".$friendname['lastname']);
				$friends[$onlinestatus][$count]['uid']	= $friend['friendid'];
			}
		}
		if(is_array($friends[0])) sort($friends[0]);
		if(is_array($friends[1])) sort($friends[1]);
		$this->friendlist = $friends;
		return $friends;
	}

	public function prepareName($name) {
		if(($this->namelength > 0) && (strlen($name) > $this->namelength)) {
			$name = substr($name,0,$this->namelength)."&#x2026;";
		}
		return $name;
	}

	public function getFriendList() {
		if(count($this->friendlist) > 0) { // return the friends as an array
			return $this->friendlist;
		} else { // no friends found
			return null;
		}
	}

	public function externalDBerror() {
		JFactory::getApplication()->enqueueMessage(JText::_(ERROR_NOSIMDB),"error");
		return FALSE;
	}
} //end ModOpenSimFriendsHelper
?>
