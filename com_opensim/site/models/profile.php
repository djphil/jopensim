<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

require_once(JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelProfile extends OpenSimModelOpenSim {

	var $_data;
	var $_data_ext;
	var $_regiondata = null;
	var $_settingsData;
	var $filename = "profile.php";
	var $view = "profile";
	var $_os_db;
	var $_osgrid_db;
	var $_db;

	public function __construct() {
		parent::__construct();
		$this->getSettingsData();
		$this->_os_db = $this->getOpenSimDB();
		$this->_osgrid_db = $this->getOpenSimGridDB();
	}

	public function getUserProfile($uid) {
		$profiledata	= $this->getprofile($uid);
		$namequery = $this->opensim->getUserNameQuery($uid);
		$this->_osgrid_db->setQuery($namequery);
		$name = $this->_osgrid_db->loadAssoc();
		$profiledata['firstname']	= $name['firstname'];
		$profiledata['lastname']	= $name['lastname'];
		$profiledata['name']		= $name['firstname']." ".$name['lastname'];
		return $profiledata;
	}

}
?>
