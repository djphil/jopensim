<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

/*jimport('joomla.application.component.model');*/

class opensimModelRegions extends JModelLegacy {
	public $filename = "regions.php";

	public $host;
	public $user;
	public $pass;
	public $dbname;
	public $port;

	public $osdbhost;
	public $osdbuser;
	public $osdbpasswd;
	public $osdbname;
	public $osdbport;

	public $osgriddbhost;
	public $osgriddbuser;
	public $osgriddbpasswd;
	public $osgriddbname;
	public $osgriddbport;

	public $regions	= array();
	public $unused	= array();
	public $db;

	public function __construct($config = array()) {
		parent::__construct($config);
		$input = JFactory::getApplication()->input;

		$app		= JFactory::getApplication();
		$limitstart	= $app->getUserStateFromRequest( 'regions_limitstart', 'limitstart', 0, 'int' );
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$orderby	= $app->getUserStateFromRequest( 'regions_filter_order', 'filter_order', 'regions.regionName', 'STR' );
		$orderdir	= $app->getUserStateFromRequest( 'regions_filter_order_Dir', 'filter_order_Dir', 'asc', 'STR' );
		$search		= $app->getUserStateFromRequest( 'regions_filter_search', 'filter_search', '', 'STR' );

		$this->setState('limit', $input->get('limit',$limit,'INT'));
		$this->setState('regions_limitstart', $input->get('limitstart',$limitstart,'INT'));
		$this->setState('regions_filter_order', $input->get('filter_order',$orderby,'STR'));
		$this->setState('regions_filter_order_Dir', $input->get('filter_order_Dir',$orderdir,'STR'));
		$this->setState('regions_filter_search', $input->get('filter_search',$search,'STR'));

		$params		= JComponentHelper::getParams('com_opensim');

		$this->osdbhost			= $params->get('opensim_dbhost');
		$this->osdbuser			= $params->get('opensim_dbuser');
		$this->osdbpasswd		= $params->get('opensim_dbpasswd');
		$this->osdbname			= $params->get('opensim_dbname');
		$this->osdbport			= $params->get('opensim_dbport');

		$this->osgriddbhost		= $params->get('opensimgrid_dbhost');
		$this->osgriddbuser		= $params->get('opensimgrid_dbuser');
		$this->osgriddbpasswd	= $params->get('opensimgrid_dbpasswd');
		$this->osgriddbname		= $params->get('opensimgrid_dbname');
		$this->osgriddbport		= $params->get('opensimgrid_dbport');

		$this->host		= ($this->osgriddbhost) ? $this->osgriddbhost:$this->osdbhost;
		$this->user		= ($this->osgriddbuser) ? $this->osgriddbuser:$this->osdbuser;
		$this->pass		= ($this->osgriddbpasswd) ? $this->osgriddbpasswd:$this->osdbpasswd;
		$this->dbname	= ($this->osgriddbname) ? $this->osgriddbname:$this->osdbname;
		$this->port		= ($this->osgriddbport) ? $this->osgriddbport:$this->osdbport;
		if(!$this->port) $this->port = 3306;

		$option = array(); //prevent problems
		$option['host']     = $this->host;		// Database host name
		$option['user']     = $this->user;		// User for database authentication
		$option['password'] = $this->pass;		// Password for database authentication
		$option['database'] = $this->dbname;	// Database name
		$option['port']		= $this->port;
		$option['prefix']   = '';				// Database prefix (may be empty)

		try {
			$this->db = JDatabaseDriver::getInstance($option);
			parent::setDbo($this->db);
			$this->getAllRegions();
			$this->getRegions();
		} catch (Exception $e) {
			$this->debug['ex'] = $e;
		}
	}

	public function externalDBerror($db = 'OpenSim DB') {
		return TRUE;
	}

	public function getPagination() {
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotal(), $this->getState('regions_limitstart'), $this->getState('limit') );
		return $this->_pagination;
	}

	public function getAllRegions() {
		$db = $this->db;
		$query = $db->getQuery(true);
		$query->select("regions.uuid");
		$query->from("regions");
		$query->order($this->getState('regions_filter_order')." ".$this->getState('regions_filter_order_Dir'));
		$db->setQuery((string)$query);
		$this->debugquery = $query;
		$this->regionuuids = $db->loadColumn();
		$this->checkUnused();
		$this->setState('_total',count($this->regionuuids));
		return count($this->regionuuids);
	}

	public function getTotal() {
		$db = $this->db;
		$query = $db->getQuery(true);
		$query->select("regions.uuid");
		$query->from("regions");
		$query->order($this->getState('regions_filter_order')." ".$this->getState('regions_filter_order_Dir'));
		$query = $this->searchRegions($query);
		$db->setQuery((string)$query);
		$this->debugquery = $query;
		$this->regionuuids = $db->loadColumn();
		$this->setState('_total',count($this->regionuuids));
		return count($this->regionuuids);
	}

	public function getRegions() {
		$db = $this->db;
		$query = $db->getQuery(true);
		$query->select("regions.*");
		$query->from("regions");
		$query->order($this->getState('regions_filter_order')." ".$this->getState('regions_filter_order_Dir'));
		$query = $this->searchRegions($query);
		$query->setLimit($this->getState('limit'),$this->getState('regions_limitstart'));
		$db->setQuery((string)$query);
		$this->debugquery = $query;
		$this->regions = $db->loadAssocList();

		return $this->regions;
	}

	public function checkUnused() {
		$this->unused = $this->getCacheImages();
		if(count($this->regionuuids) > 0) {
			foreach($this->regionuuids AS $regionuuid) {
				$used = array_search($regionuuid.".jpg",$this->unused);
				if($used !== FALSE) unset($this->unused[$used]);
			}
		}
		return $this->unused;
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

	public function checkCacheFolder() {
		$cachefolder = JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'regions';
		$retval['path'] = $cachefolder;
		if(is_dir($cachefolder)) {
			$retval['existing'] = TRUE;
			if(is_writable($cachefolder)) {
				$retval['writeable'] = TRUE;
			} else {
				$retval['writeable'] = FALSE;
			}
		} else {
			$retval['existing'] = FALSE;
		}
		return $retval;
	}

	public function searchRegions($query) {
		if ($this->getState('regions_filter_search') !== '' && $this->getState('regions_filter_search') !== null) {
			$db = $this->getDbo();
			$search = $this->getState('regions_filter_search');
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('regions.regionName LIKE ' . $search);
		}
		return $query;
	}

	public function getRegionState($state) {
		return $this->getState($state);
	}

	public function getMethods() {
		return get_class_methods($this);
	}

}
?>
