<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelAuth extends OpenSimModelOpenSim {

	var $_data;
	var $_data_ext;
	var $_regiondata = null;
	var $_settingsData;
	var $filename = "auth.php";
	var $view = "auth";
	var $_os_db;
	var $_osgrid_db;
	var $_db;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
		$this->_os_db = $this->getOpenSimDB();
		$this->_osgrid_db = $this->getOpenSimGridDB();
	}

	public function ageVerify($uuid,$ishguser = FALSE) {
		if($ishguser === FALSE) {
			$userageverified = $this->opensim->getAgeVerified($uuid);
			if($userageverified === FALSE) {
				$joomlaid			= $this->opensimRelationReverse($uuid);
				$userageverified	= $this->checkAge($joomlaid);
			}
		} else { // check for HG user
			if($this->_settingsData['addons_authorizehg'] == 0) $userageverified = FALSE;
			else $userageverified	= $this->checkAgeHG($uuid);
		}
		return $userageverified;
	}

	public function checkAge($joomlaid) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('#__user_profiles.profile_value'));
		$query->from($db->quoteName('#__user_profiles'));
		$query->where($db->quoteName('#__user_profiles.user_id').' = '.$db->quote($joomlaid));
		$query->where($db->quoteName('#__user_profiles.profile_key').' = '.$db->quote("profile.dob"));
		$db->setQuery($query);
		$db->execute();
		$foundbirthday = $db->getNumRows();
		if($foundbirthday == 1) {
			$dob = trim($db->loadResult(),"\"");
			$currentTime	= new JDate('now -'.$this->_settingsData['auth_minage'].' year'); // age verified date and time
			$agetimestamp	= $currentTime->format('U');
			$birthday		= new JDate($dob);
			$birthtimestamp	= $birthday->format('U');
			if($birthtimestamp < $agetimestamp) { // user is now old enough :)
				$opensimUID	= $this->opensimRelation($joomlaid);
				$this->opensim->setAgeVerified($opensimUID);
				return TRUE;
			} else { // user is still not old enough
				return FALSE;
			}
		} else { // nothing found
			return FALSE;
		}
	}

	public function checkAgeHG($userid) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('#__opensim_hguser.ageverified'));
		$query->from($db->quoteName('#__opensim_hguser'));
		$query->where($db->quoteName('#__opensim_hguser.PrincipalID').' = '.$db->quote($userid));
		$db->setQuery($query);
		$db->execute();
		$foundHGbirthday = $db->getNumRows();
		if($foundHGbirthday == 1) {
			$age = $db->loadResult();
			if($age == 0) {
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			$this->createHGuser($userid);
			return FALSE;
		}
	}

	public function createHGuser($userid) {
		$clientinfo	= $this->getClientInfo($userid);
		$db		= JFactory::getDbo();

		$newHGMember	= new stdClass();
		$newHGMember->PrincipalID		= $userid;
		$newHGMember->userName			= $clientinfo['userName'];
		$newHGMember->grid				= $clientinfo['grid'];

		$result = $db->insertObject('#__opensim_hguser', $newHGMember);
	}

	public function getHGuser($userid) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select($db->quoteName('#__opensim_hguser.ageverified'));
		$query->from($db->quoteName('#__opensim_hguser'));
		$query->where($db->quoteName('#__opensim_hguser.PrincipalID').' = '.$db->quote($userid));
		$db->setQuery($query);
		$db->execute();
		$foundHGbirthday = $db->getNumRows();
		if($foundHGbirthday == 1) {
			$remoteip = $_SERVER['REMOTE_ADDR'];
			$this->updateHGuserIP($userid,$remoteip);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function updateHGuserIP($userid,$remoteip) {
		$db		= JFactory::getDbo();
		$updateHGuser	= new stdClass();
		$updateHGuser->PrincipalID			= $userid;
		$updateHGuser->remoteip				= $remoteip;
		$condition	= array("PrincipalID");
		$result = $db->updateObject('#__opensim_hguser', $updateHGuser, $condition);
	}

	public function confirmAgeHG($hguuid) {
		$remoteip		= $_SERVER['REMOTE_ADDR'];
		$now			= date("Y-m-d- H:i:s");
		$db				= JFactory::getDbo();
		$updateHGuser	= new stdClass();
		$updateHGuser->PrincipalID			= $hguuid;
		$updateHGuser->remoteip				= $remoteip;
		$updateHGuser->confirmdate			= $now;
		$updateHGuser->ageverified			= 1;
		$condition	= array("PrincipalID");
		$result = $db->updateObject('#__opensim_hguser', $updateHGuser, $condition);
	}
}
?>
