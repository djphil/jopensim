<?php
/**
 * @module      OpenSim Gridstatus (mod_opensim_gridstatus)
 * @copyright   Copyright (C) 2017 FoTo50 http://www.jopensim.com
 * @license     GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class ModOpenSimHelper {
	public $offlinecolor    = "#FF0000";
	public $onlinecolor     = "#00FF00";
	public $connectparams   = array();
	public $parameter       = array();
	public $opensim;
	public $_osgrid_db;
	public $regions;

	public function __construct($connect,$parameter) {
		if(is_array($connect)) {
			foreach($connect AS $key => $val) {
				$this->connectparams[$key] = $val;
			}
		}
		$this->setParameter($parameter);

		$this->initOpenSim();                       // generate the opensim object
		$this->_osgrid_db = $this->getOsGridDB();   // load the external DB in an object
	}

	public function initOpenSim() {
		$this->opensim = new opensim($this->connectparams['osgriddbhost'],$this->connectparams['osgriddbuser'],$this->connectparams['osgriddbpasswd'],$this->connectparams['osgriddbname'],$this->connectparams['osgriddbport']);
	}

	public function getComponentParameter() {
		$params = &JComponentHelper::getParams('com_opensim');
		$parameter['osgriddbhost']      = $params->get('opensimgrid_dbhost');
		$parameter['osgriddbuser']      = $params->get('opensimgrid_dbuser');
		$parameter['osgriddbpasswd']    = $params->get('opensimgrid_dbpasswd');
		$parameter['osgriddbname']      = $params->get('opensimgrid_dbname');
		$parameter['osgriddbport']      = $params->get('opensimgrid_dbport');
		$this->connectparams            = $parameter;
	}

	public function setParameter($parameter) {
		if (!is_array($parameter)) return FALSE;
		$this->parameter['lastDays']		= $parameter['lastDays'];
		$this->parameter['offlinecolor']	= $parameter['offlinecolor'];
		$this->parameter['onlinecolor']		= $parameter['onlinecolor'];
		$this->parameter['gridstatus']		= $parameter['gridstatus'];
		$this->parameter['hiddenregions']	= $parameter['hiddenregions'];
		
        $gridlines = 0;

        if ($parameter['showgridstatus']) $gridlines += 1;
		if ($parameter['showregions']) $gridlines += 2;
		if ($parameter['showlastvisitors']) $gridlines += 4;
		if ($parameter['showonline']) $gridlines += 8;
		if ($parameter['showtotalusers']) $gridlines += 16;
        if ($parameter['showonlinehg']) $gridlines += 32;
		$this->parameter['gridlines'] = $gridlines;
	}

	public function getOsGridDB() {
		return $this->opensim->_osgrid_db;
	}

	public function getStatus() {
		if (!$this->_osgrid_db) {
			$returnvalue['statusmsg'] = "<font color='".$this->offlinecolor."'>".JText::_('MOD_OPENSIM_GRIDSTATUS_OFFLINE')."</font>";
		} else {
			$zeroUID		= "00000000-0000-0000-0000-000000000000";
			$lastDays		= $this->parameter['lastDays'];
			$offlinecolor	= $this->offlinecolor;
			$onlinecolor	= $this->onlinecolor;
			$returnvalue    = array();

			$this->_osgrid_db->setQuery("SELECT uuid FROM regions");
			$this->regions = $regions = $this->_osgrid_db->loadColumn();
			
            if ($this->parameter['hiddenregions'] == 0) {
				$db = JFactory::getDbo();
				$query = "SELECT #__opensim_mapinfo.regionUUID FROM #__opensim_mapinfo WHERE #__opensim_mapinfo.hidemap = 1";
				$db->setQuery($query);
				$db->execute();
				$numrows = $db->getNumRows();
				if($numrows > 0) {
					$hiddenregions = $db->loadColumn();
					foreach($hiddenregions AS $hiddenregion) {
						$ishidden = array_search($hiddenregion,$regions);
						if($ishidden === FALSE) continue;
						else unset($regions[$ishidden]);
					}
				}
			}
			$returnvalue['totalregions'] = count($regions);

			if ($this->parameter['gridstatus'] == -1) $returnvalue['status'] = "offline";
			else if ($this->parameter['gridstatus'] == 1) $returnvalue['status'] = "online";
			else {
				if($returnvalue['totalregions'] > 0) $returnvalue['status'] = "online"; // Online Server needs more than 0 regions
				else  $returnvalue['status'] = "offline";
			}

			if ($returnvalue['status'] == "online") 
                $returnvalue['statusmsg'] = "<font color='".$this->parameter['onlinecolor']."'>".JText::_('MOD_OPENSIM_GRIDSTATUS_ONLINE')."</font>";
			else $returnvalue['statusmsg'] = "<font color='".$this->parameter['offlinecolor']."'>".JText::_('MOD_OPENSIM_GRIDSTATUS_OFFLINE')."</font>";

			$returnvalue['days'] = $lastDays;

			$this->_osgrid_db->setQuery(sprintf("SELECT COUNT(*) FROM Presence WHERE RegionID != '%s'",$zeroUID));
			$returnvalue['online'] = $this->_osgrid_db->loadResult();
            // Hypergrid users
			$this->_osgrid_db->setQuery(sprintf("SELECT COUNT(*) FROM Presence WHERE RegionID = '%s'", $zeroUID));
			$returnvalue['hgonline'] = $this->_osgrid_db->loadResult();

			$tage = sprintf("%d",$lastDays);
			$jetzt = time();
			$lastloggedin = $jetzt - 60*60*24*$tage;
			$this->_osgrid_db->setQuery("SELECT COUNT(*) FROM GridUser WHERE Login > '$lastloggedin' OR Logout > '$lastloggedin'");
			$returnvalue['lastonline'] = $this->_osgrid_db->loadResult();

			$returnvalue['totalusers'] = $this->opensim->countActiveUsers();

			$returnvalue['gridboxlines'] = $this->parameter['gridlines'];
		}
		return $returnvalue;
	} // end getStatus

} // end ModOpenSimHelper
?>
