<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die();

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
	public $db;

	public function __construct($config = array()) {
		parent::__construct($config);
		$params					= JComponentHelper::getParams('com_opensim');

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
		$this->db = JDatabaseDriver::getInstance($option);
		parent::setDbo($this->db);
		$this->getRegions();
	}

	public function getRegions() {
		if(count($this->regions) == 0) {
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select("regions.*");
			$query->from("regions");
			$query->order("regions.regionName ASC");
			$query->where('regions.regionName NOT LIKE "http%"');
			$db->setQuery((string)$query);
			$this->regions = $db->loadAssocList();
		}
		return $this->regions;
	}

	public function getMethods() {
		return get_class_methods($this);
	}

}
?>
