<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

class opensimModelOpensim extends JModelItem {

	var $_data;
	var $_data_ext;
	var $_regiondata = null;
	var $_settingsData;
	var $filename = "opensim.php";
	var $view = "opensim";
	var $_os_db;
	var $_osgrid_db;
	var $_db;

	public function __construct() {
		parent::__construct();
		$params			= &JComponentHelper::getParams('com_opensim');
		$this->params	= $params;
		$osgriddbhost	= $params->get('opensimgrid_dbhost');
		$osgriddbuser	= $params->get('opensimgrid_dbuser');
		$osgriddbpasswd	= $params->get('opensimgrid_dbpasswd');
		$osgriddbname	= $params->get('opensimgrid_dbname');
		$osgriddbport	= $params->get('opensimgrid_dbport');
		$this->getSettingsData();
		$this->opensim		= new opensim($osgriddbhost,$osgriddbuser,$osgriddbpasswd,$osgriddbname,$osgriddbport);
		$this->userquery	= $this->opensim->getUserQuery(null);
		$this->_osgrid_db	= $this->getOpenSimGridDB();
		$this->getData();
	}

	public function getOpenSimGridDB() {
		return $this->opensim->_osgrid_db;
	}

	public function getSettingsData() {
		// Lets load the data if it doesn't already exist
		if (empty( $this->_settingsData )) {
			$settings = array();

			$params												= &JComponentHelper::getParams('com_opensim');
			$this->params										= $params;

			$settings['osdbhost']								= $params->get('opensimgrid_dbhost');
			$settings['osdbuser']								= $params->get('opensimgrid_dbuser');
			$settings['osdbpasswd']								= $params->get('opensimgrid_dbpasswd');
			$settings['osdbname']								= $params->get('opensimgrid_dbname');
			$settings['osdbport']								= $params->get('opensimgrid_dbport',3306);

			$settings['addons_messages']						= $params->get('addons_messages');
			$settings['addons_profile']							= $params->get('addons_profile');
			$settings['addons_groups']							= $params->get('addons_groups');
			$settings['addons_search']							= $params->get('addons_search');
			$settings['addons_inworldauth']						= $params->get('addons_inworldauth');
			$settings['addons_terminalchannel']					= $params->get('addons_terminalchannel');
			$settings['addons_identminutes']					= $params->get('addons_identminutes');
			$settings['addons_currency']						= $params->get('addons_currency');
			$settings['addons']									= $settings['addons_messages'] + ($settings['addons_profile']*2) + ($settings['addons_groups']*4) + ($settings['addons_inworldauth']*8) + ($settings['addons_search']*16) + ($settings['addons_currency']*32);

			$settings['loginscreen_image']						= $params->get('loginscreenbackground');
			$settings['loginscreen_boxborder_inline']			= $params->get('loginscreen_boxborder_inline');
			$settings['loginscreen_color']						= $params->get('jopensim_loginscreen_color_background_screen');
			$settings['loginscreen_msgbox_color']				= $params->get('jopensim_loginscreen_color_background_box');
			$settings['loginscreen_text_color']					= $params->get('jopensim_loginscreen_color_text');
			$settings['loginscreen_xdays']						= $params->get('loginscreen_show_uniquevisitors_days');

            // Added by djphil
			$settings['jopensim_loginscreen_stylebold']         = $params->get('jopensim_loginscreen_stylebold');
			$settings['jopensim_loginscreen_styleicon']         = $params->get('jopensim_loginscreen_styleicon');
			$settings['jopensim_loginscreen_color_links']       = $params->get('jopensim_loginscreen_color_links');
			$settings['jopensim_loginscreen_boxborder_title']   = $params->get('jopensim_loginscreen_boxborder_title');
            // End
                        
			$settings['loginscreen_offline_color']				= $params->get('jopensim_loginscreen_color_offline');
			$settings['loginscreen_online_color']				= $params->get('jopensim_loginscreen_color_online');
			$settings['hiddenregions']							= $params->get('loginscreen_show_counthidden');
			$settings['loginscreen_gridstatus']					= $params->get('loginscreen_gridstatus');
			$settings['loginscreen_box_gridstatus']				= $params->get('loginscreen_box_gridstatus');
			$settings['loginscreen_box_message']				= $params->get('loginscreen_box_message');
			$settings['loginscreen_box_regions']				= $params->get('loginscreen_box_regions');
			
			$settings['loginscreen_show_status']				= $params->get('loginscreen_show_status');
			$settings['loginscreen_show_regions']				= $params->get('loginscreen_show_regions');
			$settings['loginscreen_show_uniquevisitors']		= $params->get('loginscreen_show_uniquevisitors');
			$settings['loginscreen_show_totalusers']			= $params->get('loginscreen_show_totalusers');
			$settings['loginscreen_show_onlinenow']				= $params->get('loginscreen_show_onlinenow');

			$settings['loginscreen_msgbox_title']				= $params->get('loginscreen_messagebox_title');
			$settings['loginscreen_msgbox_message']				= $params->get('loginscreen_messagebox_content');
			$settings['loginscreen_msgbox_title_background']	= $params->get('jopensim_loginscreen_color_background_title');
			$settings['loginscreen_msgbox_title_text']			= $params->get('jopensim_loginscreen_color_text_title');

			$this->_settingsData = $settings;
		}
		return $this->_settingsData;
	}

	public function convert2rgba($color,$alpha) {
		$default = 'rgb(0,0,0)';
 
		// Return default if no color provided
		if(empty($color)) return $default;
 
		// Sanitize $color if "#" is provided
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
				return $default;
		}
 
		// Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);
 
		// Check if opacity is set(rgba or rgb)
		if($alpha){
			if(abs($alpha) > 1)
			$alpha = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$alpha.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}
 
		// Return rgb(a) color string
		return $output;
	}

	public function removehidden($regionarray) {
		foreach($regionarray AS $key => $region) {
			if($region['hidemap'] == 1) unset($regionarray[$key]);
		}
		return $regionarray;
	}

	public function _buildQueryRegions($filter = null, $sort = null, $order = "ASC") {
		$opensim = $this->opensim;
		$query = $opensim->getAllRegionsQuery($filter, $sort, $order);
		return $query;
	}

	public function getData() {
		// Lets load the data if it doesn't already exist
		if (empty( $this->_settingsData )) $this->getSettingsData();
		if (!$this->_osgrid_db || JError::isError($this->_osgrid_db) || $this->_osgrid_db->getErrorNum() > 0) {
			return FALSE;
		}

		$retval['settings'] = $this->_settingsData; // settings has only one line

		$query = $this->_buildQueryRegions(null,"regionName");

		$this->_osgrid_db->setQuery($query);
		$regiondata['regions'] = $this->_osgrid_db->loadAssocList();

		if(is_array($regiondata['regions'])) {
			foreach($regiondata['regions'] AS $key => $val) {
				$mapinfo = $this->getMapInfo($val['uuid']);
				$regiondata['regions'][$key]['maplink'] = str_replace("-","",$val['uuid']);
				$regiondata['regions'][$key]['articleId'] = $mapinfo['articleId'];
				$regiondata['regions'][$key]['hidemap'] = $mapinfo['hidemap'];
			}
		}
		
		$this->_regiondata = $regiondata['regions'];
		$retval = array_merge($retval,$regiondata);
		return $retval;
	}

	public function getRegionName($maplink) {
		if(empty($this->_regiondata)) $this->getData();
		if(is_array($this->_regiondata)) {
			foreach($this->_regiondata AS $region) {
				if($region['maplink'] == $maplink) return $region['regionName'];
			}
			return "not found";
		} else {
			return FALSE;
		}
	}

	public function getRegionUid($maplink) {
		if(empty($this->_regiondata)) $this->getData();
		if(is_array($this->_regiondata)) {
			foreach($this->_regiondata AS $region) {
				if($region['maplink'] == $maplink) return $region['uuid'];
			}
			return "not found";
		} else {
			return FALSE;
		}
	}

	public function getRegionAtLocation($locX,$locY) {
		if(!is_array($this->_regiondata)) return FALSE;
		foreach($this->_regiondata AS $region) {
			if($region['locX'] == $locX && $region['locY'] == $locY && $region['hidemap'] == 0) return $region;
			/*if($region['locX'] == $locX && $region['locY'] == $locY) return $region;*/
		}
		return null;
	}

	public function getRegionsInRow($locY) {
		if(!is_array($this->_regiondata)) return FALSE;
		$counter = 0;
		foreach($this->_regiondata AS $region) {
			if($region['locY'] == $locY && $region['hidemap'] == 0) $counter++;
		}
		return $counter;
	}

	public function getRegionsInColumn($locX) {
		if(!is_array($this->_regiondata)) return FALSE;
		$counter = 0;
		foreach($this->_regiondata AS $region) {
			if($region['locX'] == $locX && $region['hidemap'] == 0) $counter++;
		}
		return $counter;
	}

	public function getLocationRange() {
		if (!$this->_osgrid_db || JError::isError($this->_osgrid_db) || $this->_osgrid_db->getErrorNum() > 0) {
			return FALSE;
		}
		$opensim = $this->opensim;
		$rangequery = $opensim->getRegionRangeQuery();
		$this->_osgrid_db->setQuery($rangequery);
		$regionrange = $this->_osgrid_db->loadAssoc();
		if(count($regionrange) > 0 && $regionrange['maxX'] && $regionrange['maxY'] && $regionrange['minX'] && $regionrange['minY']) return $regionrange;
		else return FALSE;
	}

	public function getMapInfo($regionUUID) {
		$retval = array();
		$query = sprintf("SELECT * FROM #__opensim_mapinfo WHERE regionUUID = '%s'",$regionUUID);
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		if($db->getNumRows() == 1) {
			$retval = $db->loadAssoc();
		} else {
			$retval['regionUUID'] = $regionUUID;
			$retval['articleId'] = null;
			$retval['hidemap'] = 0;
		}
		return $retval;
	}

	public function getGridStatus() {
		if(!$this->_osgrid_db) {
			$returnvalue['statusmsg'] = "<span style='color:#".$this->_settingsData['loginscreen_offline_color']."'>".JText::_('OFFLINE')."</span>";
		} else {
			$zeroUID		= "00000000-0000-0000-0000-000000000000";
			$lastDays		= $this->_settingsData['loginscreen_xdays'];
			$offlinecolor	= $this->_settingsData['loginscreen_offline_color'];
			$onlinecolor	= $this->_settingsData['loginscreen_online_color'];
			$returnvalue = array();

			$this->_osgrid_db->setQuery("SELECT uuid FROM regions");
			$regions = $this->_osgrid_db->loadColumn();
			if(intval($this->_settingsData['hiddenregions']) == 0) {
				$db = JFactory::getDbo();
				$query = "SELECT #__opensim_mapinfo.regionUUID FROM #__opensim_mapinfo WHERE #__opensim_mapinfo.hidemap = 1";
				$db->setQuery($query);
				$hiddenregions = $db->loadColumn();
				if(is_array($hiddenregions)) {
					$numrows = count($hiddenregions);
				} else {
					$numrows = 0;
				}
				$this->_settingsData['debug1'] = $numrows;
				if($numrows > 0) {
					$db->setQuery($query);
					$this->_settingsData['debug2'] = $hiddenregions;
					foreach($hiddenregions AS $hiddenregion) {
						$ishidden = array_search($hiddenregion,$regions);
						if($ishidden === FALSE) continue;
						else unset($regions[$ishidden]);
					}
				}
			}
			$returnvalue['totalregions'] = count($regions);

			if($this->_settingsData['loginscreen_gridstatus'] == -1) $returnvalue['status'] = "offline";
			elseif($this->_settingsData['loginscreen_gridstatus'] == 1) $returnvalue['status'] = "online";
			else {
				if($returnvalue['totalregions'] > 0)  $returnvalue['status'] = "online"; // Online Server needs more than 0 regions
				else  $returnvalue['status'] = "offline";
			}

			if($returnvalue['status'] == "online") $returnvalue['statusmsg'] = "<span class='jopensim_gridstatus' style='color:".$onlinecolor.";'>".JText::_('ONLINE')."</span>";
			else $returnvalue['statusmsg'] = "<span class='jopensim_gridstatus' style='color:".$offlinecolor."'>".JText::_('OFFLINE')."</span>";

			if(!$lastDays) $lastDays = $this->_settingsData['loginscreen_xdays'];
			$returnvalue['days'] = $lastDays;

			$this->_osgrid_db->setQuery(sprintf("SELECT COUNT(*) FROM Presence WHERE RegionID != '%s'",$zeroUID));
			$returnvalue['online'] = $this->_osgrid_db->loadResult();

			$tage = sprintf("%d",$lastDays);
			$jetzt = time();
			$lastloggedin = $jetzt - 60*60*24*$tage;
			$this->_osgrid_db->setQuery("SELECT COUNT(*) FROM GridUser WHERE Login > '$lastloggedin' OR Logout > '$lastloggedin'");
			$returnvalue['lastonline']		= $this->_osgrid_db->loadResult();

			$returnvalue['loginscreen_show_status']			= $this->_settingsData['loginscreen_show_status'];
			$returnvalue['loginscreen_show_regions']		= $this->_settingsData['loginscreen_show_regions'];
			$returnvalue['loginscreen_show_uniquevisitors']	= $this->_settingsData['loginscreen_show_uniquevisitors'];
			$returnvalue['loginscreen_show_totalusers']		= $this->_settingsData['loginscreen_show_totalusers'];
			$returnvalue['loginscreen_show_onlinenow']		= $this->_settingsData['loginscreen_show_onlinenow'];
			$returnvalue['hiddenregions']					= $this->_settingsData['hiddenregions'];
		}
		return $returnvalue;
	} //end getStatus
}
?>
