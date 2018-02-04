<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

/*jimport('joomla.application.component.model');*/
require_once(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');

class opensimModelMap extends OpenSimModelOpenSim {

	var $_data;
	var $_data_ext;
	var $_regiondata = null;
	var $_settingsData;
	var $filename = "map.php";
	var $view = "map";
	var $_os_db;
	var $_osgrid_db;
	var $_db;
	var $mapquery;

	/**
	 * Pagination object
	 * @var object
	 */
	var $_pagination = null;
	/**
	 * Items total
	 * @var integer
	 */
	var $_total = null;

	public function __construct() {
		global $mainframe, $option;
		parent::__construct();
		$this->getSettingsData();

		$filter = JFactory::getApplication()->input->get('search');
		$this->mapquery = $this->opensim->getAllRegionsQuery($filter);

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int', FALSE);
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$this->_osgrid_db = $this->getOpenSimGridDB();
		if(!$this->_osgrid_db) {
			return FALSE;
		}
		$this->getMapData($filter,"regions.regionName","asc");
	}

	public function _buildQueryRegions($filter,$order,$direction) {
		$this->mapquery = $this->opensim->getAllRegionsQuery($filter,$order,$direction);
		return $this->mapquery;
	}

	public function getPagination() {
		if(!$this->_osgrid_db) return FALSE;
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	public function getTotal() {
		if(!$this->_osgrid_db) return FALSE;
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$this->_osgrid_db->setQuery($this->opensim->getAllRegionsQuery());
			$this->_osgrid_db->query();
			if($this->_osgrid_db->getErrorNum() > 0) {
				$errormsg = $this->_osgrid_db->getErrorNum().": ".stristr($this->_osgrid_db->getErrorMsg(),"sql=",TRUE)." in ".__FILE__." at line ".__LINE__;
				JFactory::getApplication()->enqueueMessage($errormsg,"error");
			}
			$this->_total = $this->_osgrid_db->getNumRows();
		}
		return $this->_total;
	}


	public function getMapData($filter,$order,$direction) {
		global $mainframe,$option;
		// Lets load the data if it doesn't already exist
		if (empty( $this->_settingsData )) $this->getSettingsData();
		if (!$this->_osgrid_db || $this->_osgrid_db->getErrorNum() > 0) {
			return FALSE;
		}

		$lim   = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int', FALSE);
		$lim0  = JFactory::getApplication()->input->get('limitstart', 0, '', 'int');

		$retval['settings'] = $this->_settingsData; // settings has only one line

		$opensim = $this->opensim;
		$query = $this->_buildQueryRegions($filter,$order,$direction);

		$this->_osgrid_db->setQuery($query,$lim0,$lim);
		$regiondata['regions'] = $this->_osgrid_db->loadAssocList();

		if(is_array($regiondata['regions'])) {
			foreach($regiondata['regions'] AS $key => $val) {
//				$debug = var_export($val,TRUE);
//				error_log($debug);
				$regiondata['regions'][$key]['posX'] = intval($regiondata['regions'][$key]['posX']);
				$regiondata['regions'][$key]['posY'] = intval($regiondata['regions'][$key]['posY']);
				$regiondata['regions'][$key]['maplink'] = str_replace("-","",$val['uuid']);
				$ownerdata = $opensim->getUserData($val['owner_uuid']);
				if(array_key_exists("firstname",$ownerdata) && array_key_exists("lastname",$ownerdata)) {
					$regiondata['regions'][$key]['ownername'] = $ownerdata['firstname']." ".$ownerdata['lastname'];
				} else {
					$regiondata['regions'][$key]['ownername'] = "n/a";
				}
				$mapinfo = $this->getMapInfo($val['uuid']);
				$regiondata['regions'][$key]['articleId'] = $mapinfo['articleId'];
				$regiondata['regions'][$key]['articleTitle'] = $this->getContentTitleFromId($mapinfo['articleId']);
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

	public function getArticle($regionUUID) { // diese Routine soll in einer nächsten Version einen Joomla Artikel den einzelnen Regionen zuordnen
		// TODO: checken ob $regionUUID in $this->regiondata vorhanden ist
		// andernfalls initialisierungsroutine anpassen
		// dann aus #__opensim_mapinfo lesen
		//
		// weitere noch notwendige Methoden:
		// setArticle($regionUUID)
		// deleteArticle($regionUUID)
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
		}
		return null;
	}

	public function getLocationRange() {
		if (!$this->_osgrid_db || $this->_osgrid_db->getErrorNum() > 0) {
			return FALSE;
		}
		$opensim = $this->opensim;
		$rangequery = $opensim->getRegionRangeQuery();
		$this->_osgrid_db->setQuery($rangequery);
		$regionrange = $this->_osgrid_db->loadAssoc();
		return $regionrange;
	}

	public function savedefault() {
		$region	= JFactory::getApplication()->input->get('region');
		$x		= JFactory::getApplication()->input->get('x');
		$y		= JFactory::getApplication()->input->get('y');
		$z		= JFactory::getApplication()->input->get('z',0);

		$data['defaulthome'] = $region;
		$data['mapstartX'] = intval($x / 2);
		$data['mapstartY'] = intval(256 - ($y / 2));
		$data['mapstartZ'] = intval($z);

		$regiondata	= $this->opensim->getRegionData($region);
		if($regiondata['sizeX'] > 256) $data['mapstartX'] = ($data['mapstartX']/256)*$regiondata['sizeX'];
		if($regiondata['sizeY'] > 256) $data['mapstartY'] = ($data['mapstartY']/256)*$regiondata['sizeY'];

		$jOpenSim = JComponentHelper::getComponent('com_opensim',TRUE);
		$comparams = JComponentHelper::getParams('com_opensim');
		$comparams->set('jopensim_userhome_region', $data['defaulthome']);
		$comparams->set('jopensim_userhome_x', $data['mapstartX']);
		$comparams->set('jopensim_userhome_y', $data['mapstartY']);
		$comparams->set('jopensim_userhome_z', $data['mapstartZ']);
		$componentid = JComponentHelper::getComponent('com_opensim')->id;
		$table = JTable::getInstance('extension');
		$table->load($componentid);
		$table->bind(array('params' => $comparams->toString()));
		// check for error
		if (!$table->check()) {
		    $this->setError('lastcreatedate: check: ' . $table->getError());
		    return false;
		}
		// Save to database
		if (!$table->store()) {
		    $this->setError('lastcreatedate: store: ' . $table->getError());
		    return false;
		}
		return true;
	}

	public function savemanual() {
		$data['defaulthome']	= JFactory::getApplication()->input->get('region');
		$data['mapstartX']		= JFactory::getApplication()->input->get('loc_x');
		$data['mapstartY']		= JFactory::getApplication()->input->get('loc_y');
		$data['mapstartZ']		= JFactory::getApplication()->input->get('loc_z',0);

		$jOpenSim = JComponentHelper::getComponent('com_opensim',TRUE);
		$comparams = JComponentHelper::getParams('com_opensim');
		$comparams->set('jopensim_userhome_region', $data['defaulthome']);
		$comparams->set('jopensim_userhome_x', $data['mapstartX']);
		$comparams->set('jopensim_userhome_y', $data['mapstartY']);
		$comparams->set('jopensim_userhome_z', $data['mapstartZ']);
		$componentid = JComponentHelper::getComponent('com_opensim')->id;
		$table = JTable::getInstance('extension');
		$table->load($componentid);
		$table->bind(array('params' => $comparams->toString()));
		// check for error
		if (!$table->check()) {
		    $this->setError('lastcreatedate: check: ' . $table->getError());
		    return false;
		}
		// Save to database
		if (!$table->store()) {
		    $this->setError('lastcreatedate: store: ' . $table->getError());
		    return false;
		}
		return true;
	}

	public function getMapInfo($regionUUID) {
		if(is_array($regionUUID)) {
			$region = $regionUUID[0];
		} else {
			$region = $regionUUID;
		}
		$retval = array();
		$query = sprintf("SELECT #__opensim_mapinfo.* FROM #__opensim_mapinfo WHERE regionUUID = '%s'",$region);
		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		if($db->getNumRows() == 1) {
			$retval = $db->loadAssoc();
			if($retval['articleId'] && $retval['articleId'] > 0) $retval['articleTitle'] = $this->getContentTitleFromId($retval['articleId']);
			else $retval['articleTitle'] = "";
		} else {
			$retval['regionUUID']	= $region;
			$retval['articleId']	= null;
			$retval['articleTitle'] = "";
			$retval['hidemap']		= 0;
			$retval['public']		= 0;
			$retval['guide']		= 0;
		}
		return $retval;
	}

	public function setMapInfo($data) {
		$query = sprintf("INSERT INTO #__opensim_mapinfo (regionUUID,articleId,hidemap,`public`,guide) VALUES ('%1\$s','%2\$d','%3\$d','%4\$d','%5\$d')
								ON DUPLICATE KEY UPDATE
							articleId = '%2\$d',
							`public` = '%4\$d',
							hidemap = '%3\$d',
							guide = '%5\$d'",
					$data['regionUUID'],
					$data['regionArticle'],
					$data['mapinvisible'],
					$data['mappublic'],
					$data['mapguide']);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function removeMapArticle($regionUUID) {
		$query = sprintf("UPDATE #__opensim_mapinfo SET articleId = NULL WHERE regionUUID = '%s'",$regionUUID);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function setVisible($regionUUID,$status) {
		$query = sprintf("INSERT INTO #__opensim_mapinfo (regionUUID,articleId,hidemap) VALUES ('%1\$s',NULL,'%2\$d')
								ON DUPLICATE KEY UPDATE
							hidemap = '%2\$d'",
					$regionUUID,
					$status);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function setPublic($regionUUID,$status) {
		$query = sprintf("INSERT INTO #__opensim_mapinfo (regionUUID,articleId,public) VALUES ('%1\$s',NULL,'%2\$d')
								ON DUPLICATE KEY UPDATE
							public = '%2\$d'",
					$regionUUID,
					$status);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function setGuide($regionUUID,$status) {
		$query = sprintf("INSERT INTO #__opensim_mapinfo (regionUUID,articleId,guide) VALUES ('%1\$s',NULL,'%2\$d')
								ON DUPLICATE KEY UPDATE
							guide = '%2\$d'",
					$regionUUID,
					$status);
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
	}

	public function updateMapconfig($data) {
		$savedata = array();
		if(array_key_exists("map_cache_age",$data))			$savedata['map_cache_age'] = $data['map_cache_age'];
		if(array_key_exists("mapcontainer_width",$data))	$savedata['mapcontainer_width'] = $data['mapcontainer_width'];
		if(array_key_exists("mapcontainer_height",$data))	$savedata['mapcontainer_height'] = $data['mapcontainer_height'];
		if(array_key_exists("mapcenter_offsetX",$data))		$savedata['mapcenter_offsetX'] = $data['mapcenter_offsetX'];
		if(array_key_exists("mapcenter_offsetY",$data))		$savedata['mapcenter_offsetY'] = $data['mapcenter_offsetY'];
		if(array_key_exists("map_defaultsize",$data))		$savedata['map_defaultsize'] = $data['map_defaultsize'];
		if(array_key_exists("map_minsize",$data))			$savedata['map_minsize'] = $data['map_minsize'];
		if(array_key_exists("map_maxsize",$data))			$savedata['map_maxsize'] = $data['map_maxsize'];
		if(array_key_exists("map_zoomstep",$data))			$savedata['map_zoomstep'] = $data['map_zoomstep'];
		if(count($savedata) == 9) { // all required values arrived
			$this->saveConfig($savedata);
			return TRUE;
		} else { // some error occured
			return FALSE;
		}
	}

	public function getArticles() {
		$retval = array();
		$query = "SELECT
						#__categories.title AS categorytitle,
						#__content.id AS articleid,
						#__content.title AS articletitle
					FROM
						#__categories,
						#__content
					WHERE
						#__categories.published = 1
					AND
						#__categories.access = 1
					AND
						#__categories.extension = 'com_content'
					AND
						#__content.state > 0
					AND
						#__categories.id = #__content.catid
					AND
						#__content.access = 1
					ORDER BY
						categorytitle,
						#__content.ordering";

		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();

		if($db->getNumRows() > 0) {
			$retval = $db->loadAssocList();
		}
		return $retval;
	}

	public function getCacheImages() {
		$images			= array();
		$cachefolder	= $this->checkCacheFolder();
		if($cachefolder['existing'] === TRUE) {
			$hdl=opendir($cachefolder['path']);
			while ($res = readdir ($hdl)) {
				if ($res == "." || $res == ".." || $res == "index.html" || !is_file($cachefolder['path'].DIRECTORY_SEPARATOR.$res)) continue;
				$images[] = $res;
			}
		}
		return $images;
	}
}
?>
